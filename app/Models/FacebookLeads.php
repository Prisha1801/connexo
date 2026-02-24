<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookLeads extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'ad_account_id',
        'ad_account_name',
        'adset_id',
        'adset_name',
        'campaign_id',
        'campaign_name',
        'ad_id',
        'ad_name',
        'created_time',
        'platform',
        'your_requirement',
        'full_name',
        'phone_number',
        'email',
        'city',
        'all_field_data',
        'field_data',
        'whatsapp_sent_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'all_field_data' => 'array',
        'created_time' =>'datetime'
    ];
    
}
