<?php

namespace Modules\Catalogs\Models;

use App\Scopes\CompanyScope;
use App\Traits\HasConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    public $guarded = [];


    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            $company_id = session('company_id', null);
            if ($company_id) {
                $model->company_id = $company_id;
            }
        });
    }
}
