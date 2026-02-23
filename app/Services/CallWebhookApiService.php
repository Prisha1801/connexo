<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CallWebhookApiService
{
    /**
     * Execute HTTP request based on task config and context.
     *
     * @param array $config Task config: url, http_method, data, auth_type, headers, params, etc.
     * @param array $context ['body' => array, 'headers' => array]
     * @param int|null $taskId
     * @return array Response info
     */
    public function execute(array $config, array $context, ?int $taskId = null): array
    {
        $body = $context['body'] ?? [];
        $reqHeaders = $context['headers'] ?? [];
        $ctx = array_merge($body, ['body' => $body, 'headers' => $reqHeaders]);

        $method = strtoupper((string) ($config['http_method'] ?? $config['method'] ?? 'POST'));
        if (!in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $method = 'POST';
        }

        $url = (string) ($config['url'] ?? '');
        $url = $this->renderTemplate($url, $ctx);
        if ($url === '') {
            return ['success' => false, 'error' => 'url is required'];
        }

        $headers = $this->headersFromConfig($config, $ctx);
        $query = $this->paramsFromConfig($config, $ctx);

        $requestBody = null;
        $bodyRaw = $config['data'] ?? $config['body'] ?? null;
        if (is_string($bodyRaw) && trim($bodyRaw) !== '') {
            $bodyString = $this->renderTemplate($bodyRaw, $ctx);
            $decoded = json_decode($bodyString, true);
            $requestBody = json_last_error() === JSON_ERROR_NONE ? $decoded : $bodyString;
        }

        $client = Http::timeout((int) ($config['timeout'] ?? 20));

        $authType = strtolower((string) ($config['auth_type'] ?? 'none'));
        if ($authType === 'basic') {
            $u = $this->resolveString(
                $config['basic_auth_username_variable'] ?? null,
                $config['basic_auth_username_static'] ?? null,
                $ctx
            );
            $p = $this->resolveString(
                $config['basic_auth_password_variable'] ?? null,
                $config['basic_auth_password_static'] ?? null,
                $ctx
            );
            $client = $client->withBasicAuth((string) $u, (string) $p);
        } elseif ($authType === 'bearer') {
            $t = $this->resolveString(
                $config['bearer_token_variable'] ?? null,
                $config['bearer_token_static'] ?? null,
                $ctx
            );
            $client = $client->withToken((string) $t);
        }

        if (!empty($headers)) {
            $client = $client->withHeaders($headers);
        }

        $options = array_filter([
            'query' => !empty($query) ? $query : null,
            'json' => is_array($requestBody) ? $requestBody : null,
            'body' => is_string($requestBody) ? $requestBody : null,
        ], fn ($v) => $v !== null);

        $response = $client->send($method, $url, $options);

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'response' => Str::limit($response->body(), 5000, '...'),
        ];
    }

    protected function headersFromConfig(array $config, array $context): array
    {
        $keys = Arr::wrap($config['headers_key'] ?? []);
        $vars = Arr::wrap($config['headers_value_variable'] ?? []);
        $statics = Arr::wrap($config['headers_value_static'] ?? []);

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

    protected function paramsFromConfig(array $config, array $context): array
    {
        $keys = Arr::wrap($config['params_key'] ?? []);
        $vars = Arr::wrap($config['params_value_variable'] ?? []);
        $statics = Arr::wrap($config['params_value_static'] ?? []);

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
