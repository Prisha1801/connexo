<?php

namespace Modules\WorkFlows\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkflowTask extends Model
{
    use HasFactory;

    protected $fillable = ['workflow_id', 'task_type', 'task_name', 'task_config', 'company_id'];

    protected $casts = [
        'task_config' => 'array',
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
}


