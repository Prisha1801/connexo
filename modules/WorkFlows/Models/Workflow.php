<?php

namespace Modules\WorkFlows\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workflow extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'app_id', 'trigger_event', 'webhook_token', 'company_id'];

    public function webhooks()
    {
        return $this->hasMany(WorkflowWebhookData::class);
    }

    public function tasks()
    {
        return $this->hasMany(WorkflowTask::class);
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
