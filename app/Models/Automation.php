<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Automation extends Model
{
    use HasFactory;

    protected $table = 'automations';

    protected $fillable = [
        'user_id',
        'ad_account_id',
        'campaign_id',
        'adset_id',
        'ad_id',
        'template_id',
        'status',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
}