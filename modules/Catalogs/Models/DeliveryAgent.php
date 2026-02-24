<?php

namespace Modules\Catalogs\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAgent extends Model
{
    use HasFactory;

    protected $table = 'delivery_agents';

    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            $companyId = session('company_id', null);
            if ($companyId) {
                $model->company_id = $companyId;
            }
        });
    }
}

