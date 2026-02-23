<?php

namespace App\Services;

use App\Scopes\CompanyScope;
use Illuminate\Support\Arr;
use Modules\LeadBot\Services\WhatsAppCampaignSender;
use Modules\Wpbox\Models\Campaign;
use Modules\Wpbox\Models\Contact as WpboxContact;

class WhatsAppService
{
    public function __construct(
        protected WhatsAppCampaignSender $sender
    ) {}

    /**
     * Send WhatsApp campaign message from workflow task config and payload.
     *
     * @param array $config Task config: campaign_id, wa_phone, wa_payload, etc.
     * @param array $payload Webhook payload (for variable resolution)
     * @param int $companyId
     */
    public function sendCampaignMessage(array $config, array $payload, int $companyId): void
    {
        $context = array_merge($payload, ['body' => $payload]);

        $campaignId = (int) ($config['campaign_id'] ?? 0);
        if (!$campaignId) {
            return;
        }

        $phone = $this->resolvePhone($config, $context);
        if (empty($phone)) {
            return;
        }

        $phone = preg_replace('/\s+/', '', $phone);
        $phone = ltrim($phone, '+');

        $campaign = Campaign::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $companyId)
            ->where('is_api', true)
            ->find($campaignId);

        if (!$campaign) {
            return;
        }

        $contact = WpboxContact::withoutGlobalScope(CompanyScope::class)
            ->withTrashed()
            ->where('company_id', $companyId)
            ->where('phone', $phone)
            ->first();

        if (!$contact) {
            $contact = new WpboxContact();
            $contact->company_id = $companyId;
            $contact->phone = $phone;
            $contact->name = $phone;
            $contact->save();
        } elseif ($contact->trashed()) {
            $contact->restore();
        }

        $extra = $context;
        if (!empty($config['wa_payload']) && is_string($config['wa_payload'])) {
            $payloadString = $this->renderTemplate($config['wa_payload'], $context);
            $decoded = json_decode($payloadString, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $extra = array_merge($extra, $decoded);
            }
        }
        $contact->extra_value = $extra;

        $message = $campaign->makeMessages(null, $contact);
        if ($message) {
            $this->sender->send($message);
        }
    }

    protected function resolvePhone(array $config, array $context): string
    {
        $phone = $config['wa_phone'] ?? '';
        if (is_string($phone) && trim($phone) !== '') {
            return $this->renderTemplate($phone, $context);
        }
        return (string) $this->resolveString(
            $config['wa_phone_variable'] ?? null,
            $config['wa_phone_static'] ?? null,
            $context
        );
    }

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
