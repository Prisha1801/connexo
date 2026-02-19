<?php

namespace Modules\FlowGround\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlowGround extends Model
{
    protected $table = 'flow_grounds';

    protected $fillable = [
        'uuid',
        'company_id',
        'name',
        'description',
        'categories',
        'status',
        'meta_flow_id',
        'created_by',
    ];

    protected $casts = [
        'uuid' => 'string',
        'categories' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(FlowGroundVersion::class, 'flow_ground_id');
    }
}
