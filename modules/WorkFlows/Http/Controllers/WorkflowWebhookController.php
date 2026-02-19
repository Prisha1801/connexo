<?php

namespace Modules\WorkFlows\Http\Controllers;

use Illuminate\Http\Request;
use Modules\WorkFlows\Models\Workflow;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CallWebhookApiService;
use App\Services\ContactService;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Modules\LeadManager\Models\Lead;
use Modules\WorkFlows\Models\WorkflowWebhookData;

class WorkflowWebhookController extends Controller
{
    public function handleWebhook(Request $request, $token)
    {
        $workflow = Workflow::with('tasks')->where('webhook_token', $token)->first();

        if (!$workflow) {
            return response()->json(['error' => 'Invalid webhook token'], 404);
        }

        $payload = $request->all();

        // 1. Get headers from HTTP request
        $headers = collect($request->headers->all())
            ->mapWithKeys(function ($values, $key) {
                return [strtolower($key) => $values[0] ?? ''];
            })
            ->toArray();

        // 2. If payload contains "headers", merge/override with them
        if (isset($payload['headers']) && is_array($payload['headers'])) {
            foreach ($payload['headers'] as $key => $value) {
                $headers[strtolower($key)] = $value;
            }
            // Optional: If the actual useful data is inside payload['body'], use that instead
            if (isset($payload['body']) && is_array($payload['body'])) {
                $payload = $payload['body'];
            }
        }

        $webhookData = WorkflowWebhookData::create([
            'workflow_id' => $workflow->id,
            'payload' => $payload,
            'headers' => $headers,
            'company_id' => $workflow->company_id,
        ]);

        $context = [
            'body' => $payload,
            'headers' => $headers,
        ];

        $whatsAppService = app(WhatsAppService::class);
        $callApiService = app(CallWebhookApiService::class);

        foreach ($workflow->tasks as $task) {
            switch ($task->task_type) {
                case 'create_contact':
                    $this->processCreateContactTask($task, $payload, $workflow->company_id);
                    break;
                case 'send_whatsapp':
                    $config = $this->parseTaskConfig($task);
                    $whatsAppService->sendCampaignMessage($config, $payload, $workflow->company_id);
                    break;
                case 'call_api':
                    $config = $this->parseTaskConfig($task);
                    $callApiService->execute($config, $context, $task->id);
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook processed successfully',
            'data' => $webhookData,
        ]);
    }

    private function processCreateContactTask($task, array $payload, int $companyId)
    {
        // Handle both array and JSON string configurations
        $config = $this->parseTaskConfig($task);

        // FIX: Properly parse custom fields structure
        $customFields = $this->parseCustomFieldsConfig($config['custom_fields'] ?? []);

        // Merge with proper defaults
        $config = array_merge(
            [
                'name_variable' => '',
                'name_static' => '',
                'phone' => '',
                'add_groups' => [],
                'remove_groups' => [],
                'custom_fields' => $config['custom_fields'] ?? [],
                'add_custom_fields' => 0,
                'create_lead'     => 0,
                'agent_id'        => null,
            ],
            $config,
        );

        Log::debug('Processing create contact task', [
            'task_id' => $task->id,
            'config' => $config,
            'payload_keys' => array_keys($payload),
        ]);

        try {
            $service = new ContactService();
            $contact = $service->createContact($payload, $companyId, $config);

            if (!empty($config['create_lead'])) {
                $leadData = [
                    'company_id'  => $companyId,
                    'contact_id'  => $contact->id,
                    'stage'       => 'New',
                    'notifications' => 1,
                ];

                $lead = Lead::firstOrCreate(
                    ['company_id' => $companyId, 'contact_id' => $contact->id],
                    $leadData
                );
            }
            
            Log::info('Contact created via webhook', [
                'contact_id' => $contact->id,
                'task_id' => $task->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Contact creation failed', [
                'error' => $e->getMessage(),
                'task_id' => $task->id,
                'payload' => $payload,
                'config' => $config,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Safely parse task configuration from various formats
     */
    // private function parseTaskConfig($task): array
    // {
    //     // Handle already decoded arrays
    //     if (is_array($task->task_config)) {
    //         return $task->task_config;
    //     }

    //     // Handle JSON strings
    //     if (is_string($task->task_config)) {
    //         return json_decode($task->task_config, true) ?? [];
    //     }

    //     // Handle config attribute as fallback
    //     if (is_array($task->config)) {
    //         return $task->config;
    //     }

    //     if (is_string($task->config)) {
    //         return json_decode($task->config, true) ?? [];
    //     }

    //     Log::warning('No valid task config found', [
    //         'task_id' => $task->id,
    //         'task_config_type' => gettype($task->task_config),
    //         'config_type' => gettype($task->config),
    //     ]);

    //     return [];
    // }

    private function parseTaskConfig($task): array
    {
        // Check if task_config is already an array
        if (is_array($task->task_config)) {
            return $task->task_config;
        }

        // Handle JSON string in task_config
        if (is_string($task->task_config) && !empty($task->task_config)) {
            $decoded = json_decode($task->task_config, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // Fallback to config attribute if task_config is invalid
        if (is_array($task->config)) {
            return $task->config;
        }

        // Handle JSON string in config
        if (is_string($task->config) && !empty($task->config)) {
            $decoded = json_decode($task->config, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // Log detailed warning if no valid config found
        $this->logInvalidConfig($task);

        return [];
    }

    private function logInvalidConfig($task)
    {
        $context = [
            'task_id' => $task->id,
            'task_type' => $task->task_type ?? 'unknown',
            'task_config_type' => gettype($task->task_config),
            'task_config_value' => is_string($task->task_config) ? substr($task->task_config, 0, 100) : $task->task_config,
            'config_type' => gettype($task->config),
            'config_value' => is_string($task->config) ? substr($task->config, 0, 100) : $task->config,
            'json_error' => json_last_error_msg(),
        ];

        Log::warning('No valid task configuration found', $context);
    }

    /**
     * Parse the custom fields configuration into a proper key-value structure
     */
    private function parseCustomFieldsConfig(array $customFieldsConfig): array
    {
        $parsed = [];
        $currentField = null;

        foreach ($customFieldsConfig as $item) {
            // Start new field definition
            if (isset($item['field_id'])) {
                if ($currentField) {
                    $parsed[] = $currentField;
                }
                $currentField = [
                    'field_id' => $item['field_id'],
                    'value_variable' => '',
                    'value_static' => '',
                ];
            }
            // Add value definition
            elseif ($currentField) {
                if (isset($item['value_variable'])) {
                    $currentField['value_variable'] = $item['value_variable'];
                }
                if (isset($item['value_static'])) {
                    $currentField['value_static'] = $item['value_static'];
                }
            }
        }

        // Add the last field
        if ($currentField) {
            $parsed[] = $currentField;
        }

        return $parsed;
    }

    public function fetchWebhookResponse($workflowId)
    {
        $webhookData = WorkflowWebhookData::where('workflow_id', $workflowId)->whereNotNull('payload')->latest()->first();

        if (!$webhookData) {
            return response()->json(['message' => 'No webhook response captured yet.'], 404);
        }

        $payload = (array) $webhookData->payload;
        $headers = (array) $webhookData->response;
        $existingMappedData = (array) ($webhookData->mapped_data ?? []);

        $newMappedData = $this->generateMappedData($payload, $headers, $existingMappedData);

        $webhookData->update(['mapped_data' => $newMappedData]);

        $responseData = array_map(function ($item) {
            return ['label' => $item['label'], 'value' => $item['value']];
        }, $newMappedData);

        return response()->json(array_values($responseData));
    }

    private function generateMappedData($payload, $headers, $existingMappedData)
    {
        $this->processNestedData($payload, $existingMappedData);
        $this->processNestedData($headers, $existingMappedData);
        return $existingMappedData;
    }

    private function processNestedData($data, &$result, $path = '')
    {
        if (!is_iterable($data)) {
            $this->addFinalValue($path, $data, $result);
            return;
        }

        foreach ($data as $key => $value) {
            $currentPath = $path ? "$path.$key" : $key;

            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->processNestedData($decoded, $result, $currentPath);
                    continue;
                }
            }

            if (is_iterable($value)) {
                $this->processNestedData($value, $result, $currentPath);
                continue;
            }

            $this->addFinalValue($currentPath, $value, $result);
        }
    }

    private function addFinalValue($fullPath, $value, &$result)
    {
        if (!isset($result[$fullPath])) {
            $result[$fullPath] = [
                'key' => $fullPath,
                'label' => $this->generateLabel($fullPath),
                'value' => $value,
            ];
        }
    }

    private function generateLabel($key)
    {
        return ucwords(str_replace(['_', '.'], ' ', $key));
    }

    private function processSendWhatsappTask($task, array $payload, int $companyId)
    {
        $config = $this->parseTaskConfig($task);

        // Extract phone number (static or variable-based)
        $phone = $config['wa_phone_static'] ?? '';
        if (!$phone && isset($config['wa_phone_variable'])) {
            $phone = data_get($payload, $config['wa_phone_variable'], '');
        }

        // Validate required parameters
        if (empty($phone)) {
            Log::error('Missing phone number for WhatsApp task', ['task_id' => $task->id]);
            return;
        }

        if (empty($config['campaign_id'])) {
            Log::error('Missing campaign ID for WhatsApp task', ['task_id' => $task->id]);
            return;
        }

        try {
            // Get company token
            $company = Company::find($companyId);
            $token = $company->getConfig('plain_token', '');

            // Process payload template
            $waPayload = $this->processTemplate($config['wa_payload'] ?? '{}', $payload);

            // Build request data
            $requestData = [
                'campaing_id' => $config['campaign_id'],
                'token' => $token,
                'phone' => $phone,
                'data' => json_decode($waPayload, true) ?: [],
            ];

            // Send to WhatsApp service
            $this->sendWhatsAppRequest($requestData);

            Log::info('WhatsApp task processed', [
                'task_id' => $task->id,
                'phone' => $phone,
                'campaign_id' => $config['campaign_id'],
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp sending failed', [
                'error' => $e->getMessage(),
                'task_id' => $task->id,
                'payload' => $payload,
                'config' => $config,
            ]);
        }
    }
}
