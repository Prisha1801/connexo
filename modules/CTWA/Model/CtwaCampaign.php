<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CtwaCampaign extends Model
{
    protected $fillable = [
        'company_id', 'page_id', 'sender_id', 'sender_name'
    ];

    public function messages()
    {
        return $this->hasMany(CtwaMessage::class, 'campaign_id');
    }
}


// class CtwaCampaign extends Model
// {
//     protected $fillable = [
//         'campaign_name',
//         'ad_account_id',
//         'page_id',
//         'whatsapp_number',
//         'message_template',
//         'objective',
//         'status',
//         'ad_creative',
//         'adset_data',
//         'response',
//         'phone',
//         'message',
//         'received_at',
//     ];

//     protected $casts = [
//         'ad_creative' => 'array',
//         'adset_data' => 'array',
//         'response' => 'array',
//     ];
// }