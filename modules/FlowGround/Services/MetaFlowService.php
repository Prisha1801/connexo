<?php

namespace Modules\FlowGround\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MetaFlowService
{
    protected $company;
    protected string $baseUrl = 'https://graph.facebook.com/v21.0';
    protected string $token;
    protected string $wabaId;

    public function __construct($company)
    {
        if (! $company) {
            throw new RuntimeException('Company context is required');
        }

        $this->company = $company;

        $this->wabaId = (string) $company->getConfig(
            'whatsapp_business_account_id'
        );

        $this->token = (string) $company->getConfig(
            'whatsapp_permanent_access_token'
        );

        $this->ensureConfig();
    }

    /**
     * Ensure WhatsApp config exists for company
     */
    protected function ensureConfig(): void
    {
        if (empty($this->token) || empty($this->wabaId)) {
            throw new RuntimeException(
                'WhatsApp configuration missing for this company'
            );
        }
    }

    /**
     * Base Meta API request
     */
    protected function request(string $method, string $endpoint, array $data = [])
    {
        $response = Http::withToken($this->token)
            ->acceptJson()
            ->$method($this->baseUrl . $endpoint, $data);

        return $response->json();
    }

    /**
     * Create WhatsApp Flow
     */
    public function createFlow(array $flowJson): array
    {
        return $this->request('post', "/{$this->wabaId}/flows", [
            'name' => 'Flow-' . now()->timestamp,
            'categories' => ['OTHER'],
            'flow_json' => json_encode($flowJson, JSON_UNESCAPED_UNICODE),
        ]);
    }

    /**
     * Publish WhatsApp Flow
     */
    public function publishFlow(string $flowId): array
    {
        return $this->request('post', "/{$flowId}/publish");
    }

    public function deleteFlow(string $flowId): array
    {
        try {
            $response = Http::withToken($this->token)
                ->delete($this->baseUrl . '/' . $flowId);

            return [
                'status' => $response->status(),
                'content' => $response->json(),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 500,
                'content' => $e->getMessage(),
            ];
        }
    }

    public function deprecateFlow(string $flowId): array
    {
        try {
            $response = Http::withToken($this->token)
                ->post($this->baseUrl . '/' . $flowId . '/deprecate');

            return [
                'status' => $response->status(),
                'content' => $response->json(),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 500,
                'content' => $e->getMessage(),
            ];
        }
    }
}