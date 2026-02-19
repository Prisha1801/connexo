<?php

namespace Modules\FlowGround\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowGroundVersion extends Model
{
    protected $table = 'flow_ground_versions';

    protected $fillable = [
        'flow_ground_id',
        'version',
        'flow_json',
        'synced_to_meta',
    ];

    protected $casts = [
        'flow_json'      => 'array',
        'synced_to_meta' => 'boolean',
        'version'        => 'integer',
    ];

    public function flowGround(): BelongsTo
    {
        return $this->belongsTo(FlowGround::class, 'flow_ground_id');
    }
}
