<?php

namespace Modules\LeadBot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadBotTask extends Model
{
    use HasFactory;

    protected $table = 'lead_bot_tasks';

    protected $fillable = [
        'lead_bot_id',
        'task_type',
        'task_name',
        'task_order',
        'task_config',
        'company_id',
    ];

    protected $casts = [
        'task_config' => 'array',
    ];

    public function leadBot()
    {
        return $this->belongsTo(LeadBot::class);
    }
}

