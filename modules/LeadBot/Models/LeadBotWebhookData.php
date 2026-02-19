<?php

namespace Modules\LeadBot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadBotWebhookData extends Model
{
    use HasFactory;

    protected $table = 'lead_bot_webhook_data';

    protected $fillable = [
        'lead_bot_id',
        'payload',
        'headers',
        'mapped_data',
        'task_results',
        'response',
        'success',
        'company_id',
    ];

    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
        'mapped_data' => 'array',
        'task_results' => 'array',
        'success' => 'bool',
    ];

    public function leadBot()
    {
        return $this->belongsTo(LeadBot::class);
    }
}

