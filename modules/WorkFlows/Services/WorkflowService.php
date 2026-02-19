<?php

namespace Modules\WorkFlows\Services;

use Modules\WorkFlows\Models\Workflow;
use Illuminate\Support\Str;

class WorkflowService
{
    public function createWorkflow($data)
    {
        return Workflow::create([
            'name' => $data['workflowname'],
            'app_id' => $data['app_id'],
            'trigger_event' => $data['trigger_event'],
            'webhook_token' => Str::random(40),
            'company_id' => auth()->user()->company_id,
        ]);
    }
}
