<?php

namespace Modules\CTWA\Models;

use Illuminate\Database\Eloquent\Model;

class CtwaMessage extends Model
{
    protected $fillable = [
        'campaign_id',
        'sender_id',
        'receiver_id',
        'message',
        'message_type',
        'received_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(CtwaCampaign::class, 'campaign_id');
    }
}
