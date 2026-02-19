<?php

namespace Modules\FlowGround\Services;

use Modules\FlowGround\Models\FlowGroundField;

class FlowJsonBuilder
{
    public function build(int $flowGroundId): array
    {
        $fields = FlowGroundField::where('flow_ground_id', $flowGroundId)
            ->orderBy('position')
            ->get();

        $components = [];

        foreach ($fields as $field) {
            $components[] = [
                'type' => 'TextInput',
                'name' => $field->name,
                'label' => $field->label,
                'required' => (bool) $field->required,
            ];
        }

        return [
            'version' => '7.3',
            'screens' => [[
                'id' => 'form',
                'title' => 'Form',
                'components' => $components,
                'action' => ['name' => 'submit'],
            ]],
        ];
    }
}
