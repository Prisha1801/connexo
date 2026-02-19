<?php

namespace Modules\LeadBot\Services;

class WebhookVariableExtractor
{
    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $headers
     * @param array<string, array{key:string,label:string,value:mixed}> $existingMappedData
     * @return array<string, array{key:string,label:string,value:mixed}>
     */
    public function buildMappedData(array $payload, array $headers, array $existingMappedData = []): array
    {
        $mapped = $existingMappedData;
        $this->walk($payload, $mapped);
        $this->walk($headers, $mapped, 'headers');
        return $mapped;
    }

    /**
     * @param mixed $data
     * @param array<string, array{key:string,label:string,value:mixed}> $out
     */
    private function walk($data, array &$out, string $prefix = ''): void
    {
        if (!is_iterable($data)) {
            $this->addValue($prefix, $data, $out);
            return;
        }

        foreach ($data as $key => $value) {
            $path = $prefix === '' ? (string) $key : ($prefix . '.' . $key);

            // If we receive JSON strings, try to decode (common for webhook bodies)
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $this->walk($decoded, $out, $path);
                    continue;
                }
            }

            if (is_iterable($value)) {
                $this->walk($value, $out, $path);
                continue;
            }

            $this->addValue($path, $value, $out);
        }
    }

    /**
     * @param array<string, array{key:string,label:string,value:mixed}> $out
     * @param mixed $value
     */
    private function addValue(string $path, $value, array &$out): void
    {
        $path = trim($path);
        if ($path === '' || isset($out[$path])) {
            return;
        }

        $out[$path] = [
            'key' => $path,
            'label' => $this->labelize($path),
            'value' => $value,
        ];
    }

    private function labelize(string $key): string
    {
        return ucwords(str_replace(['_', '.'], ' ', $key));
    }
}

