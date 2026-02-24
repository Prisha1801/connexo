<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookAdAccount extends Model
{
    protected $table = 'facebook_ad_accounts';

    protected $fillable = [
        'company_id', 'account_id', 'account_name',
        'campaign_id', 'campaign_name',
        'adset_id', 'adset_name',
        'ad_id', 'ad_name',
        'created_at', 'updated_at'
    ];

    public $timestamps = true;
}
