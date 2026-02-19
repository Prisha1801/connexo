<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookAd extends Model
{
    use HasFactory;

    protected $table = 'facebook_ads';

    protected $fillable = [
        'user_id',
        'campaign_id',
        'campaign_name',
        'ad_id',
        'ad_name',
        'status',
        'ad_created_at', 
        'ad_account',
        'creative',
    ];


    protected $casts = [
        'creative' => 'array',
    ];
    
    // Optional: define relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
