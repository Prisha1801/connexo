<?php

namespace Modules\Wpbox\Models;

use App\Models\Company;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoRetargetMessage extends Model
{

    protected $table = 'autoretarget_messages';
    protected $fillable = [
        'autoretarget_campaign_id', 
        'campaign_id', 
        'delay_days', 
        'send_time', 
        'order', 
        'is_active'
    ];

    public function autoretargetCampaign(): BelongsTo
    {
        return $this->belongsTo(AutoRetargetCampaign::class, 'autoretarget_campaign_id');
    }

    // Add relationship to the Campaign model
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

     public function company()
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());

        static::creating(function ($model) {
            $company_id = session('company_id', null);
            if ($company_id) {
                $model->company_id = $company_id;
            }
        });
    }
}