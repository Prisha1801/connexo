<?php

namespace Modules\WorkFlows\Http\Controllers;

use Illuminate\Http\Request;
use Modules\WorkFlows\Models\Workflow;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\Contacts\Models\Field;
use Modules\Contacts\Models\Group;
use Modules\WorkFlows\Models\WorkflowTask;
use Modules\WorkFlows\Models\WorkflowWebhookData;
use Modules\WorkFlows\Services\WorkflowService;
use Modules\Wpbox\Models\AutoRetargetCampaign;
use Modules\Wpbox\Models\Campaign; // Added for WhatsApp campaigns

class WorkflowController extends Controller
{
    protected $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    public function index()
    {
        $query = Workflow::orderBy('id', 'desc');
        $workflows = $query->paginate(10);
        return view('work-flows::index', compact('workflows'));
    }

    public function create()
    {
        return view('work-flows::create');
    }

    public function edit($id)
    {
        $workflow = Workflow::with('tasks')->findOrFail($id);

        $webhookResponse = WorkflowWebhookData::where('workflow_id', $workflow->id)->whereNotNull('mapped_data')->latest()->first();

        // In the create method, add this line to get AutoRetarget campaigns
        $autoretargetCampaigns = AutoRetargetCampaign::where('is_active', true)->get();

        // Convert the mapped_data object to an array of objects for the dropdowns
        $mappedDataArray = [];
        if ($webhookResponse && $webhookResponse->mapped_data) {
            // Remove json_decode since it's already an array
            $mappedDataArray = collect($webhookResponse->mapped_data)
                ->map(function ($item, $key) {
                    return [
                        'key' => $key, // Original key like "phone"
                        'label' => $item['label'], // Human-readable label
                        'value' => $item['value'], // Example value
                    ];
                })
                ->values()
                ->all();
        }
        $groups = Group::get();

        // Fetch agents for this company
        $agents = User::where('company_id', $workflow->company_id)->get();
        $contactFields = Field::pluck('name', 'id')->toArray();
        // Get WhatsApp campaigns for the WhatsApp task
        $whatsappCampaigns = Campaign::where('is_api', true)->get();

        return view('work-flows::edit', [
            'groups' => $groups,
            'contactFields' => $contactFields,
            'workflow' => $workflow,
            'webhookResponse' => $webhookResponse,
            'mappedDataArray' => $mappedDataArray, // For dropdowns in tasks
            'whatsappCampaigns' => $whatsappCampaigns, // Pass campaigns to view
            'autoretargetCampaigns' => $autoretargetCampaigns,
            'agents' => $agents, // Pass agents to view
        ]);
    }

    public function destroy(Workflow $workflow)
    {
        $workflow->delete();
        return redirect()->route('workflows.index')->with('success', 'Workflow deleted.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'workflowname' => 'required|string|max:255',
            'app_id' => 'required|string',
            'trigger_event' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $workflow = $this->workflowService->createWorkflow($request->all());
            DB::commit();

            return redirect()
                ->route('workflows.edit', $workflow->id)
                ->with('success', 'Workflow created successfully!')
                ->with('webhook_url', url("/api/webhook/{$workflow->webhook_token}"));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate workflow data
        $validated = $request->validate([
            'workflowname' => 'required|string|max:255',
            'app_id' => 'required|string',
            'trigger_event' => 'required|string',
            'tasks' => 'required|array|min:1',
            'tasks.*.task_type' => 'required|string|in:create_contact,send_whatsapp,call_api',
            'tasks.*.task_name' => 'sometimes|string|max:255',
            'tasks.*.order' => 'required|integer',
            'tasks.*.task_config' => 'required|array',
            'tasks.*.id' => 'sometimes|integer|exists:workflow_tasks,id',
        ]);

        // Update workflow
        $workflow = Workflow::findOrFail($id);
        $workflow->update([
            'name' => $validated['workflowname'],
            'app_id' => $validated['app_id'],
            'trigger_event' => $validated['trigger_event'],
        ]);

        // Process tasks
        $taskIds = [];
        foreach ($validated['tasks'] as $taskData) {
            // Prepare base data for both update/create
            $baseData = [
                'task_type' => $taskData['task_type'],
                'task_config' => $taskData['task_config'],
                'order' => $taskData['order'],
            ];

            // Conditionally add task_name if it exists and is non-empty
            if (isset($taskData['task_name']) && $taskData['task_name'] !== '') {
                $baseData['task_name'] = $taskData['task_name'];
            }
            // If task_name is missing or empty, it won't be included

            if (isset($taskData['id'])) {
                // Update existing task with baseData (may or may not include task_name)
                $task = WorkflowTask::find($taskData['id']);
                $task->update($baseData);
                $taskIds[] = $task->id;
            } else {
                // Create new task - handle missing/empty task_name
                $createData = array_merge($baseData, [
                    'workflow_id' => $workflow->id,
                    'company_id' => $workflow->company_id,
                ]);

                // Add default name if task_name wasn't included
                // if (!array_key_exists('task_name', $createData)) {
                //     $createData['task_name'] = 'Task: ' . $taskData['task_type'];
                // }

                $newTask = WorkflowTask::create($createData);
                $taskIds[] = $newTask->id;
            }
        }

        // Delete removed tasks
        WorkflowTask::where('workflow_id', $workflow->id)->whereNotIn('id', $taskIds)->delete();

        return redirect()->back()->with('success', 'Workflow updated successfully');
    }
}
