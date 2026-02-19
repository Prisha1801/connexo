<?php

namespace Modules\WorkFlows\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\WorkFlows\Models\WorkflowTask;

class WorkflowTaskController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'workflow_id' => 'required|exists:workflows,id',
            'webhook_id' => 'nullable|exists:workflow_webhooks,id',
            'task_type' => 'required|string',
            'parameters' => 'required|json',
        ]);

        WorkflowTask::create($request->all());

        return response()->json(['message' => 'Task created successfully']);
    }
}
