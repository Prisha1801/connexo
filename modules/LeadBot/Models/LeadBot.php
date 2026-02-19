<?php

namespace Modules\LeadBot\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadBot extends Model
{
    use HasFactory;

    protected $table = 'lead_bots';

    protected $fillable = [
        'name',
        'app_id',
        'trigger_event',
        'webhook_token',
        'company_id',
    ];

    public function tasks()
    {
        return $this->hasMany(LeadBotTask::class)->orderBy('task_order');
    }

    public function webhookData()
    {
        return $this->hasMany(LeadBotWebhookData::class)->latest();
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope());

        static::creating(function (self $model) {
            $companyId = session('company_id', null);
            if ($companyId) {
                $model->company_id = $companyId;
            } elseif (auth()->check() && auth()->user()?->company_id) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }
}

