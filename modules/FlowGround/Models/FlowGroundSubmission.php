<?php

namespace Modules\FlowGround\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlowGroundSubmission extends Model
{
    use HasFactory;

    protected $table = 'flow_ground_submissions';

    protected $fillable = [
        'flow_ground_id',
        'phone',
        'payload',
        'response_json',
        'status',
        'error_message',

        'meta_flow_id',
        'meta_flow_version',
        'meta_flow_revision',
        'meta_flow_status',
        'meta_flow_error_message',
        'meta_flow_error_code',
        'meta_flow_error_details',
    ];

    protected $casts = [
        'payload' => 'array',
        'response_json' => 'array',
        'meta_flow_error_details' => 'array',
    ];

    public function flowGround(): BelongsTo
    {
        return $this->belongsTo(FlowGround::class, 'flow_ground_id');
    }
}
