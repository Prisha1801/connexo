<?php

namespace Modules\Wpbox\Models;

use App\Models\Company;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoRetargetLog extends Model
{
    protected $table = 'autoretarget_logs';

    protected $fillable = ['autoretarget_campaign_id', 'campaign_id', 'contact_id', 'autoretarget_message_id', 'sent_at', 'status', 'response'];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function autoretargetCampaign()
    {
        return $this->belongsTo(AutoRetargetCampaign::class, 'autoretarget_campaign_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function message()
    {
        return $this->belongsTo(AutoRetargetMessage::class, 'autoretarget_message_id');
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
