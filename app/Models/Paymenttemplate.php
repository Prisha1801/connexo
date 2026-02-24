<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use App\Traits\HasConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paymenttemplate extends Model
{
    use HasConfig;
    use HasFactory;

    protected $modelName = 'App\Models\Paymenttemplate';

    protected $table = 'setting_catalogs';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'company_id',
    ];

    /**
     * Relations
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Booted model events
     */
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
