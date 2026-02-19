<?php

namespace Modules\LeadBot\Services;

use App\Scopes\CompanyScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Contacts\Models\Contact;
use Modules\LeadBot\Models\LeadBot;
use Modules\LeadBot\Models\LeadBotTask;
use Modules\Wpbox\Models\Campaign;
use Modules\Wpbox\Models\Contact as WpboxContact;

class LeadBotTaskExecutor
{
    public function __construct(
        protected WhatsAppCampaignSender $whatsAppSender
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     * @param array<string,mixed> $headers
     * @return array<int, array<string,mixed>>
     */
    public function executeAll(LeadBot $bot, array $payload, array $headers): array
    {
        // WorkFlows-style templates expect payload keys at top-level ({{phone_number}})
        // while still supporting explicit paths ({{body.phone_number}}, {{headers.x}})
        $context = array_merge($payload, [
            'body' => $payload,
            'headers' => $headers,
        ]);

        $results = [];
        foreach ($bot->tasks()->get() as $task) {
            $results[] = $this->executeTask($bot, $task, $context);
        }
        return $results;
    }

    /**
     * @param array{body: array<string,mixed>, headers: array<string,mixed>} $context
     * @return array<string,mixed>
     */
    protected function executeTask(LeadBot $bot, LeadBotTask $task, array $context): array
    {
        try {
            return match ($task->task_type) {
                'create_contact' => $this->runCreateContact($bot, $task, $context),
                'send_whatsapp' => $this->runSendWhatsapp($bot, $task, $context),
                'call_api' => $this->runCallApi($bot, $task, $context),
                default => [
                    'task_id' => $task->id,
                    'task_type' => $task->task_type,
                    'success' => false,
                    'error' => 'Unsupported task type',
                ],
            };
        } catch (\Throwable $e) {
            return [
                'task_id' => $task->id,
                'task_type' => $task->task_type,
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param array{body: array<string,mixed>, headers: array<string,mixed>} $context
     * @return array<string,mixed>
     */
    protected function runCreateContact(LeadBot $bot, LeadBotTask $task, array $context): array
    {
        $cfg = is_array($task->task_config) ? $task->task_config : [];

        $phone = '';
        if (isset($cfg['phone']) && is_string($cfg['phone']) && trim($cfg['phone']) !== '') {
            $phone = $this->renderTemplate($cfg['phone'], $context);
        } else {
            $phone = (string) $this->resolveString($cfg['phone_variable'] ?? null, $cfg['phone_static'] ?? null, $context);
        }
        $phone = preg_replace('/\s+/', '', (string) $phone);
        $phone = ltrim($phone ?? '', '+');

        if (!$phone) {
            return [
                'task_id' => $task->id,
                'task_type' => $task->task_type,
                'success' => false,
                'error' => 'Phone is required',
            ];
        }

        $name = $this->resolveString($cfg['name_variable'] ?? null, $cfg['name_static'] ?? null, $context);
        $name = is_string($name) ? $this->renderTemplate($name, $context) : $name;
        $name = trim((string) ($name ?: $phone));

        $companyId = (int) ($bot->company_id ?? 0);

        /** @var Contact|null $contact */
        $contact = Contact::withoutGlobalScope(CompanyScope::class)
            ->withTrashed()
            ->where('company_id', $companyId ?: null)
            ->where('phone', $phone)
            ->first();

        if (!$contact) {
            $contact = new Contact();
            $contact->company_id = $companyId ?: null;
            $contact->phone = $phone;
        } elseif (method_exists($contact, 'trashed') && $contact->trashed()) {
            $contact->restore();
        }

        $contact->name = $name;

        // Assign to agent/user (contacts.user_id exists in this app)
        if (!empty($cfg['assign_to_user'])) {
            $contact->user_id = (int) $cfg['assign_to_user'];
        }
        $contact->save();

        // Groups
        $addGroups = array_values(array_filter(Arr::wrap($cfg['add_groups'] ?? [])));
        $removeGroups = array_values(array_filter(Arr::wrap($cfg['remove_groups'] ?? [])));
        if (!empty($addGroups)) {
            $contact->groups()->syncWithoutDetaching($addGroups);
        }
        if (!empty($removeGroups)) {
            $contact->groups()->detach($removeGroups);
        }

        // Custom fields (WorkFlows compatible toggle)
        if (!empty($cfg['add_custom_fields'])) {
            $customFields = Arr::wrap($cfg['custom_fields'] ?? []);
            foreach ($customFields as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $fieldId = (int) ($row['field_id'] ?? 0);
                if (!$fieldId) {
                    continue;
                }

                $value = $this->resolveString($row['value_variable'] ?? null, $row['value_static'] ?? null, $context);
                $value = is_string($value) ? $this->renderTemplate($value, $context) : $value;
                $value = is_scalar($value) ? (string) $value : json_encode($value);
                $value = trim((string) $value);
                if ($value === '') {
                    continue;
                }

                if ($contact->fields()->where('fields.id', $fieldId)->exists()) {
                    $contact->fields()->updateExistingPivot($fieldId, ['value' => $value]);
                } else {
                    $contact->fields()->attach($fieldId, ['value' => $value]);
                }
            }
        }

        // Create lead (optional; only if Lead model exists in this codebase)
        $leadCreated = false;
        if (!empty($cfg['create_lead'])) {
            $leadClass = '\\Modules\\LeadManager\\Models\\Lead';
            if (class_exists($leadClass)) {
                try {
                    /** @var \Illuminate\Database\Eloquent\Model $leadClass */
                    $leadClass::firstOrCreate(
                        ['company_id' => $companyId ?: null, 'contact_id' => $contact->id],
                        [
                            'company_id' => $companyId ?: null,
                            'contact_id' => $contact->id,
                            'stage' => 'New',
                            'notifications' => 1,
                        ]
                    );
                    $leadCreated = true;
                } catch (\Throwable $e) {
                    // ignore if module isn't installed/compatible
                }
            }
        }

        return [
            'task_id' => $task->id,
            'task_type' => $task->task_type,
            'success' => true,
            'contact_id' => $contact->id,
            'phone' => $phone,
            'lead_created' => $leadCreated,
        ];
    }

    /**
     * @param array{body: array<string,mixed>, headers: array<string,mixed>} $context
     * @return array<string,mixed>
     */
    protected function runSendWhatsapp(LeadBot $bot, LeadBotTask $task, array $context): array
    {
        $cfg = is_array($task->task_config) ? $task->task_config : [];

        $campaignId = (int) ($cfg['campaign_id'] ?? 0);
        if (!$campaignId) {
            return [
                'task_id' => $task->id,
                'task_type' => $task->task_type,
                'success' => false,
                'error' => 'campaign_id is required',
            ];
        }

        $phone = '';
        if (isset($cfg['wa_phone']) && is_string($cfg['wa_phone']) && trim($cfg['wa_phone']) !== '') {
            $phone = $this->renderTemplate($cfg['wa_phone'], $context);
        } else {
            $phone = (string) $this->resolveString($cfg['wa_phone_variable'] ?? null, $cfg['wa_phone_static'] ?? null, $context);
        }
        $phone = preg_replace('/\s+/', '', (string) $phone);
        $phone = ltrim($phone ?? '', '+');
        if (!$phone) {
            return [
                'task_id' => $task->id,
                'task_type' => $task->task_type,
                'success' => false,
                'error' => 'WhatsApp phone is required',
            ];
        }

        $companyId = (int) ($bot->company_id ?? 0);

        /** @var Campaign $campaign */
        $campaign = Campaign::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $companyId ?: null)
            ->where('is_api', true)
            ->findOrFail($campaignId);

        /** @var WpboxContact $contact */
        $contact = WpboxContact::withoutGlobalScope(CompanyScope::class)
            ->withTrashed()
            ->where('company_id', $companyId ?: null)
            ->where('phone', $phone)
            ->first();

        if (!$contact) {
            $contact = new WpboxContact();
            $contact->company_id = $companyId ?: null;
            $contact->phone = $phone;
            $contact->name = $phone;
            $contact->save();
        } elseif (method_exists($contact, 'trashed') && $contact->trashed()) {
            $contact->restore();
        }

        // Provide runtime variables for template rendering (Campaign::setParameter uses $contact->extra_value)
        $extra = $context;
        if (!empty($cfg['wa_payload']) && is_string($cfg['wa_payload'])) {
            $payloadString = $this->renderTemplate($cfg['wa_payload'], $context);
            $decoded = json_decode($payloadString, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $extra = array_merge($extra, $decoded);
            }
        }
        $contact->extra_value = $extra;

        $message = $campaign->makeMessages(null, $contact);
        if (!$message) {
            return [
                'task_id' => $task->id,
                'task_type' => $task->task_type,
                'success' => false,
                'error' => 'Failed to create message for campaign',
            ];
        }

        $this->whatsAppSender->send($message);

        return [
            'task_id' => $task->id,
            'task_type' => $task->task_type,
            'success' => true,
            'message_id' => $message->id,
            'wa_message_id' => $message->fb_message_id,
        ];
    }

    /**
     * @param array{body: array<string,mixed>, headers: array<string,mixed>} $context
     * @return array<string,mixed>
     */
    protected function runCallApi(LeadBot $bot, LeadBotTask $task, array $context): array
    {
        $cfg = is_array($task->task_config) ? $task->task_config : [];

        $method = strtoupper((string) ($cfg['http_method'] ?? ($cfg['method'] ?? 'POST')));
        if (!in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $method = 'POST';
        }

        $url = (string) ($cfg['url'] ?? '');
        $url = $this->renderTemplate($url, $context);
        if ($url === '') {
            return [
                'task_id' => $task->id,
                'task_type' => $task->task_type,
                'success' => false,
                'error' => 'url is required',
            ];
        }

        $headers = $this->headersFromConfig($cfg, $context);
        $query = $this->paramsFromConfig($cfg, $context);

        $body = null;
        $bodyRaw = $cfg['data'] ?? ($cfg['body'] ?? null);
        if (is_string($bodyRaw) && trim($bodyRaw) !== '') {
            $bodyString = $this->renderTemplate($bodyRaw, $context);
            $decoded = json_decode($bodyString, true);
            $body = json_last_error() === JSON_ERROR_NONE ? $decoded : $bodyString;
        }

        $client = Http::timeout((int) ($cfg['timeout'] ?? 20));

        // Auth types (WorkFlows compatible)
        $authType = strtolower((string) ($cfg['auth_type'] ?? 'none'));
        if ($authType === 'basic') {
            $u = $this->resolveString($cfg['basic_auth_username_variable'] ?? null, $cfg['basic_auth_username_static'] ?? null, $context);
            $p = $this->resolveString($cfg['basic_auth_password_variable'] ?? null, $cfg['basic_auth_password_static'] ?? null, $context);
            $client = $client->withBasicAuth((string) $u, (string) $p);
        } elseif ($authType === 'bearer') {
            $t = $this->resolveString($cfg['bearer_token_variable'] ?? null, $cfg['bearer_token_static'] ?? null, $context);
            $client = $client->withToken((string) $t);
        }

        if (!empty($headers)) {
            $client = $client->withHeaders($headers);
        }

        $response = $client->send($method, $url, array_filter([
            'query' => $query,
            'json' => is_array($body) ? $body : null,
            'body' => is_string($body) ? $body : null,
        ], fn ($v) => $v !== null));

        return [
            'task_id' => $task->id,
            'task_type' => $task->task_type,
            'success' => $response->successful(),
            'status' => $response->status(),
            'response' => Str::limit($response->body(), 5000, '...'),
        ];
    }

    /**
     * WorkFlows format: headers_key[], headers_value_variable[], headers_value_static[]
     * New format: headers: [{key,value_variable,value_static}]
     * @param array<string,mixed> $cfg
     * @param array<string,mixed> $context
     * @return array<string,string>
     */
    protected function headersFromConfig(array $cfg, array $context): array
    {
        if (!empty($cfg['headers']) && is_array($cfg['headers'])) {
            return $this->kvRowsToAssoc($cfg['headers'], $context);
        }

        $keys = Arr::wrap($cfg['headers_key'] ?? []);
        $vars = Arr::wrap($cfg['headers_value_variable'] ?? []);
        $statics = Arr::wrap($cfg['headers_value_static'] ?? []);

        $out = [];
        $count = max(count($keys), count($vars), count($statics));
        for ($i = 0; $i < $count; $i++) {
            $k = trim((string) ($keys[$i] ?? ''));
            if ($k === '') {
                continue;
            }
            $v = $this->resolveString($vars[$i] ?? null, $statics[$i] ?? null, $context);
            if (is_array($v) || is_object($v)) {
                $v = json_encode($v);
            }
            $v = $this->renderTemplate((string) $v, $context);
            $out[$k] = (string) $v;
        }
        return $out;
    }

    /**
     * WorkFlows format: params_key[], params_value_variable[], params_value_static[]
     * New format: query: [{key,value_variable,value_static}]
     * @param array<string,mixed> $cfg
     * @param array<string,mixed> $context
     * @return array<string,string>
     */
    protected function paramsFromConfig(array $cfg, array $context): array
    {
        if (!empty($cfg['query']) && is_array($cfg['query'])) {
            return $this->kvRowsToAssoc($cfg['query'], $context);
        }

        $keys = Arr::wrap($cfg['params_key'] ?? []);
        $vars = Arr::wrap($cfg['params_value_variable'] ?? []);
        $statics = Arr::wrap($cfg['params_value_static'] ?? []);

        $out = [];
        $count = max(count($keys), count($vars), count($statics));
        for ($i = 0; $i < $count; $i++) {
            $k = trim((string) ($keys[$i] ?? ''));
            if ($k === '') {
                continue;
            }
            $v = $this->resolveString($vars[$i] ?? null, $statics[$i] ?? null, $context);
            if (is_array($v) || is_object($v)) {
                $v = json_encode($v);
            }
            $v = $this->renderTemplate((string) $v, $context);
            $out[$k] = (string) $v;
        }
        return $out;
    }

    /**
     * @param array<int, mixed> $rows
     * @param array{body: array<string,mixed>, headers: array<string,mixed>} $context
     * @return array<string,string>
     */
    protected function kvRowsToAssoc($rows, array $context): array
    {
        $out = [];
        foreach (Arr::wrap($rows) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $key = trim((string) ($row['key'] ?? ''));
            if ($key === '') {
                continue;
            }
            $val = $this->resolveString($row['value_variable'] ?? null, $row['value_static'] ?? null, $context);
            if (is_array($val) || is_object($val)) {
                $val = json_encode($val);
            }
            $val = $this->renderTemplate((string) $val, $context);
            $out[$key] = (string) $val;
        }
        return $out;
    }

    /**
     * Resolve either a variable path (context) or fallback static value.
     * @param mixed $variable
     * @param mixed $static
     * @param array{body: array<string,mixed>, headers: array<string,mixed>} $context
     * @return mixed
     */
    protected function resolveString($variable, $static, array $context)
    {
        $variable = is_string($variable) ? trim($variable) : '';
        if ($variable !== '') {
            $val = data_get($context, $variable);
            if ($val !== null && $val !== '') {
                return $val;
            }
        }
        return $static;
    }

    /**
     * Replace {{path}} tokens with data_get(context, path).
     * @param array{body: array<string,mixed>, headers: array<string,mixed>} $context
     */
    protected function renderTemplate(string $template, array $context): string
    {
        if ($template === '' || !str_contains($template, '{{')) {
            return $template;
        }

        return preg_replace_callback('/\{\{\s*([^\}]+)\s*\}\}/', function ($m) use ($context) {
            $path = trim($m[1] ?? '');
            if ($path === '') {
                return '';
            }
            $value = data_get($context, $path);
            if ($value === null) {
                return '';
            }
            if (is_array($value) || is_object($value)) {
                return json_encode($value);
            }
            return (string) $value;
        }, $template) ?? $template;
    }
}

