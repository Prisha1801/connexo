<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledCampaign extends Model
{
    use HasFactory;
    
    protected $table = 'scheduled_campaigns';
    
    protected $fillable = [
        'campaign_name',
        'lead_name',
        'lead_phone',
        'template_id',
        'user_id',
        'scheduled_at',
        'status',
        'response_log',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(WaTemplate::class, 'template_id');
    }
}
