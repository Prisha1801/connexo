<?php

namespace Modules\LeadBot\Http\Controllers;

use App\Models\FacebookLeads;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\LeadBot\Models\LeadBot;
use Modules\LeadBot\Models\LeadBotWebhookData;
use Modules\LeadBot\Services\LeadBotTaskExecutor;
use Modules\LeadBot\Services\WebhookVariableExtractor;

class LeadBotWebhookController extends Controller
{
    public function __construct(
        protected WebhookVariableExtractor $extractor,
        protected LeadBotTaskExecutor $executor
    ) {
    }

    public function handle(Request $request, string $token)
    {
        $bot = LeadBot::with('tasks')->where('webhook_token', $token)->first();

        if (!$bot) {
            return response()->json(['error' => 'Invalid webhook token'], 404);
        }

        $payload = $request->all();

        // Normalize headers to simple key/value
        $headers = collect($request->headers->all())
            ->mapWithKeys(function ($values, $key) {
                return [strtolower($key) => $values[0] ?? ''];
            })
            ->toArray();

        // Support the "headers/body" wrapper some sources send
        if (isset($payload['headers']) && is_array($payload['headers'])) {
            foreach ($payload['headers'] as $k => $v) {
                $headers[strtolower((string) $k)] = is_scalar($v) ? (string) $v : json_encode($v);
            }
            if (isset($payload['body']) && is_array($payload['body'])) {
                $payload = $payload['body'];
            }
        }

        $webhookRow = LeadBotWebhookData::create([
            'lead_bot_id' => $bot->id,
            'payload' => $payload,
            'headers' => $headers,
            'company_id' => $bot->company_id,
            'success' => false,
        ]);

        // Build/update mapped variables
        $mapped = $this->extractor->buildMappedData($payload, $headers, (array) ($webhookRow->mapped_data ?? []));
        $webhookRow->mapped_data = $mapped;

        // Execute tasks
        $results = $this->executor->executeAll($bot, $payload, $headers);
        $webhookRow->task_results = $results;

        $allOk = collect($results)->every(fn ($r) => (bool) ($r['success'] ?? false));
        $webhookRow->success = $allOk;
        $webhookRow->response = json_encode(['results' => $results], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $webhookRow->save();

        return response()->json([
            'success' => true,
            'lead_bot_id' => $bot->id,
            'webhook_data_id' => $webhookRow->id,
            'results' => $results,
        ]);
    }

    /**
     * Fetch latest webhook mapped variables (for UI "Capture webhook response").
     */
    public function latestVariables(LeadBot $leadBot)
    {
        $source = (string) request()->query('source', '');

        $row = LeadBotWebhookData::where('lead_bot_id', $leadBot->id)->latest()->first();

        // If no webhook exists yet, allow pulling a sample lead from CRM for Meta/Facebook
        if ((!$row || (!$row->payload && !$row->headers)) && ($this->isMetaCrmApp($leadBot->app_id) || $source === 'crm')) {
            $payload = $this->getLatestMetaLeadPayload();
            if (!$payload) {
                return response()->json(['message' => 'No Meta/Facebook leads found in CRM yet.'], 404);
            }

            $row = LeadBotWebhookData::create([
                'lead_bot_id' => $leadBot->id,
                'payload' => $payload,
                'headers' => [],
                'company_id' => $leadBot->company_id,
                'success' => false,
            ]);
        }

        if (!$row || (!$row->payload && !$row->headers)) {
            return response()->json(['message' => 'No webhook response captured yet.'], 404);
        }

        $mapped = $this->extractor->buildMappedData(
            (array) ($row->payload ?? []),
            (array) ($row->headers ?? []),
            (array) ($row->mapped_data ?? [])
        );

        $row->mapped_data = $mapped;
        $row->save();

        $responseData = array_map(function ($item) {
            return [
                'key' => $item['key'] ?? '',
                'label' => $item['label'] ?? '',
                'value' => $item['value'] ?? '',
            ];
        }, array_values($mapped));

        return response()->json($responseData);
    }

    protected function isMetaCrmApp(?string $appId): bool
    {
        $appId = strtolower((string) $appId);
        return in_array($appId, ['meta', 'facebook', 'facebook_leads', 'fb_leads', 'fbleads'], true);
    }

    /**
     * Pull the latest Meta/Facebook lead already stored in CRM (facebook_leads table).
     * @return array<string,mixed>|null
     */
    protected function getLatestMetaLeadPayload(): ?array
    {
        $requestedId = request()->query('lead_id');
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        // If staff is logged in, use the owner user_id (same idea as sidebar context)
        $ownerUserId = $user->id;
        try {
            if (method_exists($user, 'hasRole') && $user->hasRole('staff') && $user->company?->user_id) {
                $ownerUserId = (int) $user->company->user_id;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $q = FacebookLeads::where('user_id', $ownerUserId);
        if ($requestedId !== null && $requestedId !== '') {
            $rid = (int) $requestedId;
            if ($rid > 0) {
                $q->where('id', $rid);
            }
        }
        $lead = $q->orderByDesc('created_time')->orderByDesc('id')->first();

        if (!$lead) {
            return null;
        }

        $payload = $lead->toArray();

        // Make sure any JSON strings become arrays for nested mapping
        foreach (['field_data', 'all_field_data'] as $k) {
            if (isset($payload[$k]) && is_string($payload[$k])) {
                $decoded = json_decode($payload[$k], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $payload[$k] = $decoded;
                }
            }
        }

        // Provide a consistent top-level structure used by task templating
        return [
            'lead' => $payload,
            'source' => 'crm',
            'app' => 'meta',
        ];
    }
}

