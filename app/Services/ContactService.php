<?php

namespace App\Services;

use App\Scopes\CompanyScope;
use Illuminate\Support\Arr;
use Modules\Contacts\Models\Contact;

class ContactService
{
    /**
     * Create or update a contact from webhook payload and task config.
     *
     * @param array $payload Webhook request body (used for variable resolution)
     * @param int $companyId
     * @param array $config Task config: phone, name_variable, name_static, add_groups, remove_groups, tags, custom_fields, etc.
     * @return Contact|null
     */
    public function createContact(array $payload, int $companyId, array $config): ?Contact
    {
        $context = array_merge($payload, ['body' => $payload]);

        $phone = $this->resolvePhone($config, $context);
        if (empty($phone)) {
            return null;
        }

        $phone = preg_replace('/\s+/', '', $phone);
        $phone = ltrim($phone, '+');

        $name = $this->resolveString(
            $config['name_variable'] ?? null,
            $config['name_static'] ?? null,
            $context
        );
        $name = is_string($name) ? $this->renderTemplate($name, $context) : $name;
        $name = trim((string) ($name ?: $phone));

        /** @var Contact|null $contact */
        $contact = Contact::withoutGlobalScope(CompanyScope::class)
            ->withTrashed()
            ->where('company_id', $companyId)
            ->where('phone', $phone)
            ->first();

        if (!$contact) {
            $contact = new Contact();
            $contact->company_id = $companyId;
            $contact->phone = $phone;
        } elseif ($contact->trashed()) {
            $contact->restore();
        }

        $contact->name = $name;

        if (!empty($config['assign_to_user']) || !empty($config['agent_id'])) {
            $userId = (int) ($config['assign_to_user'] ?? $config['agent_id'] ?? 0);
            if ($userId && \Schema::hasColumn($contact->getTable(), 'user_id')) {
                $contact->user_id = $userId;
            }
        }

        $contact->saveQuietly();

        $this->syncGroups($contact, $config);
        $this->syncTags($contact, $config);
        $this->syncCustomFields($contact, $config, $context);

        return $contact;
    }

    protected function resolvePhone(array $config, array $context): string
    {
        $phone = $config['phone'] ?? '';
        if (is_string($phone) && trim($phone) !== '') {
            return $this->renderTemplate($phone, $context);
        }
        return (string) $this->resolveString(
            $config['phone_variable'] ?? null,
            $config['phone_static'] ?? null,
            $context
        );
    }

    protected function syncGroups(Contact $contact, array $config): void
    {
        $addGroups = array_values(array_filter(Arr::wrap($config['add_groups'] ?? [])));
        $removeGroups = array_values(array_filter(Arr::wrap($config['remove_groups'] ?? [])));

        if (!empty($addGroups)) {
            $contact->groups()->syncWithoutDetaching($addGroups);
        }
        if (!empty($removeGroups)) {
            $contact->groups()->detach($removeGroups);
        }
    }

    protected function syncTags(Contact $contact, array $config): void
    {
        $tags = trim((string) ($config['tags'] ?? ''));
        if ($tags === '') {
            return;
        }
        if (\Schema::hasColumn($contact->getTable(), 'tags')) {
            $contact->tags = $tags;
            $contact->saveQuietly();
        }
    }

    protected function syncCustomFields(Contact $contact, array $config, array $context): void
    {
        if (empty($config['add_custom_fields'])) {
            return;
        }

        $customFields = $this->parseCustomFields($config['custom_fields'] ?? []);

        foreach ($customFields as $row) {
            $fieldId = (int) ($row['field_id'] ?? 0);
            if (!$fieldId) {
                continue;
            }

            $value = $this->resolveString(
                $row['value_variable'] ?? null,
                $row['value_static'] ?? null,
                $context
            );
            $value = is_string($value) ? $this->renderTemplate($value, $context) : $value;
            $value = is_scalar($value) ? (string) $value : json_encode($value);
            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }

            try {
                if ($contact->fields()->where('fields.id', $fieldId)->exists()) {
                    $contact->fields()->updateExistingPivot($fieldId, ['value' => $value]);
                } else {
                    $contact->fields()->attach($fieldId, ['value' => $value]);
                }
            } catch (\Throwable $e) {
                // Skip if pivot structure differs
            }
        }
    }

    protected function parseCustomFields(array $items): array
    {
        $parsed = [];
        $current = null;

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            if (isset($item['field_id'])) {
                if ($current) {
                    $parsed[] = $current;
                }
                $current = [
                    'field_id' => $item['field_id'],
                    'value_variable' => $item['value_variable'] ?? '',
                    'value_static' => $item['value_static'] ?? '',
                ];
            } elseif ($current) {
                if (isset($item['value_variable'])) {
                    $current['value_variable'] = $item['value_variable'];
                }
                if (isset($item['value_static'])) {
                    $current['value_static'] = $item['value_static'];
                }
            }
        }
        if ($current) {
            $parsed[] = $current;
        }
        return $parsed;
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
