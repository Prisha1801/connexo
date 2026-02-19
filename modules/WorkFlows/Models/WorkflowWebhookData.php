<?php

namespace Modules\WorkFlows\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkflowWebhookData extends Model
{
    use HasFactory;

    protected $fillable = ['workflow_id', 'payload', 'mapped_data', 'response', 'success', 'company_id'];

    protected $casts = [
        'payload' => 'array',
        'mapped_data' => 'array',
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
}
