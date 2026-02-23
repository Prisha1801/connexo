<?php

namespace Modules\Wpbox\Models;

use App\Models\Company;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutoRetargetCampaign extends Model
{
    protected $table = 'autoretarget_campaigns';

    protected $fillable = ['name', 'description', 'is_active'];

    public function messages(): HasMany
    {
        return $this->hasMany(AutoRetargetMessage::class, 'autoretarget_campaign_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AutoRetargetLog::class, 'autoretarget_campaign_id');
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
