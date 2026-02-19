<?php

namespace Modules\FlowGround\Services;

use Modules\FlowGround\Models\FlowGround;
use Modules\FlowGround\Models\FlowGroundSubmission;

class FlowAnalyticsService
{
    public function summaryByCompany(int $companyId): array
    {
        $flows = FlowGround::where('company_id', $companyId)->pluck('id');

        return [
            'total' => FlowGroundSubmission::whereIn('flow_ground_id', $flows)->count(),

            'completed' => FlowGroundSubmission::whereIn('flow_ground_id', $flows)
                ->where('status', 'completed')->count(),

            'failed' => FlowGroundSubmission::whereIn('flow_ground_id', $flows)
                ->where('status', 'failed')->count(),

            'today' => FlowGroundSubmission::whereIn('flow_ground_id', $flows)
                ->whereDate('created_at', now())->count(),
        ];
    }
}