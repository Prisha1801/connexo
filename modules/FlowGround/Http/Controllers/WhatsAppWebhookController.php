<?php

namespace Modules\FlowGround\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\FlowGround\Models\FlowGround;
use Modules\FlowGround\Models\FlowGroundSubmission;
use Modules\FlowGround\Services\FlowJsonBuilder;
use Modules\FlowGround\Services\FlowAnalyticsService;

class WhatsAppWebhookController extends Controller
{
    protected FlowJsonBuilder $flowJsonBuilder;
    protected FlowAnalyticsService $flowAnalyticsService;

    public function __construct(
        FlowJsonBuilder $flowJsonBuilder,
        FlowAnalyticsService $flowAnalyticsService
    ) {
        $this->flowJsonBuilder = $flowJsonBuilder;
        $this->flowAnalyticsService = $flowAnalyticsService;
    }

    public function handle(Request $request)
    {
        $rawPayload = $request->all();
        $message = data_get($rawPayload, 'entry.0.changes.0.value.messages.0');

        if (! $message || ($message['type'] ?? null) !== 'interactive') {
            return response()->json(['status' => 'ignored']);
        }

        $flowReply = data_get($message, 'interactive.flow_reply');
        if (! $flowReply) {
            return response()->json(['status' => 'ignored']);
        }

        // 🔹 Resolve Flow
        $flow = FlowGround::with('company')
            ->where('meta_flow_id', $flowReply['flow_id'])
            ->first();

        if (! $flow || ! $flow->company) {
            FlowGroundSubmission::create([
                'flow_ground_id' => $flow->id ?? null,
                'phone' => $message['from'] ?? null,
                'payload' => $rawPayload,
                'response_json' => [],
                'status' => 'failed',
                'error_message' => 'Company or Flow not found',
                'meta_flow_id' => $flowReply['flow_id'],
                'meta_flow_status' => 'company_not_resolved',
            ]);

            return response()->json(['status' => 'company_not_found']);
        }

        // ✅ Success
        FlowGroundSubmission::create([
            'flow_ground_id' => $flow->id,
            'phone' => $message['from'],
            'payload' => $rawPayload,
            'response_json' => $flowReply['response_json'],
            'status' => 'completed',

            'meta_flow_id' => $flowReply['flow_id'],
            'meta_flow_version' => $flowReply['flow_version'] ?? null,
            'meta_flow_revision' => $flowReply['flow_revision'] ?? null,
            'meta_flow_status' => 'submitted',
        ]);

        return response()->json(['screen' => 'success']);
    }
}
