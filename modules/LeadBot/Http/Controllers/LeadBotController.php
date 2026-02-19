<?php

namespace Modules\LeadBot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FacebookLeads;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Contacts\Models\Field;
use Modules\Contacts\Models\Group;
use Modules\LeadBot\Models\LeadBot;
use Modules\LeadBot\Models\LeadBotTask;
use Modules\LeadBot\Models\LeadBotWebhookData;
use Modules\Wpbox\Models\AutoRetargetCampaign;
use Modules\Wpbox\Models\Campaign;

class LeadBotController extends Controller
{
    public function index()
    {
        $bots = LeadBot::orderByDesc('id')->paginate(10);
        $totalBots = LeadBot::count();
        $uniqueApps = LeadBot::select('app_id')->distinct()->count('app_id');

        return view('lead-bot::index', compact('bots', 'totalBots', 'uniqueApps'));
    }

    public function create()
    {
        return view('lead-bot::create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'app_id' => 'required|string|max:100',
            'trigger_event' => 'required|string|max:255',
        ]);

        $bot = LeadBot::create([
            'name' => $validated['name'],
            'app_id' => $validated['app_id'],
            'trigger_event' => $validated['trigger_event'],
            'webhook_token' => Str::random(40),
            'company_id' => auth()->user()->company_id ?? session('company_id'),
        ]);

        return redirect()
            ->route('lead-bot.edit', $bot->id)
            ->with('success', 'LeadBot created successfully!')
            ->with('webhook_url', url("/api/lead-bot/webhook/{$bot->webhook_token}"));
    }

    public function edit(LeadBot $leadBot)
    {
        $leadBot->load('tasks');

        $latestWebhook = LeadBotWebhookData::where('lead_bot_id', $leadBot->id)
            ->whereNotNull('mapped_data')
            ->latest()
            ->first();

        $mappedDataArray = [];
        if ($latestWebhook && $latestWebhook->mapped_data) {
            $mappedDataArray = collect($latestWebhook->mapped_data)
                ->map(function ($item, $key) {
                    return [
                        'key' => $key,
                        'label' => $item['label'] ?? $key,
                        'value' => $item['value'] ?? null,
                    ];
                })
                ->values()
                ->all();
        }

        $groups = Group::get();
        $contactFields = Field::pluck('name', 'id')->toArray();
        $whatsappCampaigns = Campaign::where('is_api', true)->get();
        //$autoretargetCampaigns = AutoRetargetCampaign::where('is_active', true)->get();

        $agents = collect();
        try {
            if ($leadBot->company_id) {
                $agents = User::where('company_id', $leadBot->company_id)->get(['id', 'name']);
            }
        } catch (\Throwable $e) {
            $agents = collect();
        }

        $crmMetaLeads = collect();
        if (in_array(strtolower((string) $leadBot->app_id), ['meta', 'facebook', 'facebook_leads', 'fb_leads', 'fbleads'], true) && auth()->check()) {
            $user = auth()->user();
            $ownerUserId = $user->id;
            try {
                if (method_exists($user, 'hasRole') && $user->hasRole('staff') && $user->company?->user_id) {
                    $ownerUserId = (int) $user->company->user_id;
                }
            } catch (\Throwable $e) {
                // ignore
            }

            $crmMetaLeads = FacebookLeads::where('user_id', $ownerUserId)
                ->orderByDesc('created_time')
                ->orderByDesc('id')
                ->limit(25)
                ->get(['id', 'lead_id', 'full_name', 'phone_number', 'campaign_name', 'platform', 'created_time']);
        }

        return view('lead-bot::edit', [
            'leadBot' => $leadBot,
            'latestWebhook' => $latestWebhook,
            'mappedDataArray' => $mappedDataArray,
            'groups' => $groups,
            'contactFields' => $contactFields,
            'whatsappCampaigns' => $whatsappCampaigns,
            'crmMetaLeads' => $crmMetaLeads,
            // 'autoretargetCampaigns' => $autoretargetCampaigns,
            'agents' => $agents,
        ]);
    }

    public function update(Request $request, LeadBot $leadBot)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'app_id' => 'required|string|max:100',
            'trigger_event' => 'required|string|max:255',
            'tasks' => 'required|array|min:1',
            'tasks.*.task_type' => 'required|string|in:create_contact,send_whatsapp,call_api',
            'tasks.*.task_name' => 'sometimes|nullable|string|max:255',
            // WorkFlows-compatible order field name
            'tasks.*.order' => 'required|integer|min:0',
            'tasks.*.task_config' => 'required|array',
            'tasks.*.id' => 'nullable|integer|exists:lead_bot_tasks,id',
        ]);

        DB::transaction(function () use ($leadBot, $validated) {
            $leadBot->update([
                'name' => $validated['name'],
                'app_id' => $validated['app_id'],
                'trigger_event' => $validated['trigger_event'],
            ]);

            $keptIds = [];
            foreach ($validated['tasks'] as $taskData) {
                $base = [
                    'task_type' => $taskData['task_type'],
                    'task_name' => ($taskData['task_name'] ?? null) ?: null,
                    'task_order' => (int) $taskData['order'],
                    'task_config' => $taskData['task_config'] ?? [],
                    'company_id' => $leadBot->company_id,
                ];

                if (!empty($taskData['id'])) {
                    $task = LeadBotTask::where('lead_bot_id', $leadBot->id)->findOrFail($taskData['id']);
                    $task->update($base);
                    $keptIds[] = $task->id;
                } else {
                    $task = $leadBot->tasks()->create(array_merge($base, [
                        'lead_bot_id' => $leadBot->id,
                    ]));
                    $keptIds[] = $task->id;
                }
            }

            LeadBotTask::where('lead_bot_id', $leadBot->id)->whereNotIn('id', $keptIds)->delete();
        });

        return redirect()->back()->with('success', 'LeadBot updated successfully');
    }

    public function destroy(LeadBot $leadBot)
    {
        $leadBot->delete();
        return redirect()->route('lead-bot.index')->with('success', 'LeadBot deleted.');
    }
}

