<?php

namespace Modules\CTWA\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CtwaAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ad_name',
        'ad_id',
        'adset_id',
        'campaign_id',
        'page_id',
        'whatsapp_phone_number',
        'cta_url',
        'prefilled_message',
        'message',
        'geo_targeting',
        'age_from',
        'age_to',
        'gender',
        'daily_budget',
        'start_date',
        'end_date',
        'media_type',
        'media_id',
        'interests',
    ];

    protected $casts = [
        'geo_targeting' => 'array',
        'interests' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}
