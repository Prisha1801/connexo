<?php

namespace Modules\CTWA\Models;

use Illuminate\Database\Eloquent\Model;

class CtwaCampaign extends Model
{
    protected $fillable = [
        'company_id',
        'page_id',
        'sender_id',
        'sender_name',
    ];

    public function messages()
    {
        return $this->hasMany(CtwaMessage::class, 'campaign_id');
    }
}
