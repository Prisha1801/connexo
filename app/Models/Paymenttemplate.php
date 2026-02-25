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
     * Return the active payment configuration string based on payment_type.
     * 0 = WhatsApp Pay, 1 = Razorpay, 2 = PayU, 3 = Zaakpay, 4 = Meta Pay.
     */
    public function getActivePaymentConfig()
    {
        $type = (int) ($this->payment_type ?? 0);
        switch ($type) {
            case 1:
                return $this->payment_configuration_other ?? '';
            case 2:
                return $this->payment_configuration_payu ?? '';
            case 3:
                return $this->payment_configuration_zaakpay ?? '';
            case 4:
                return $this->payment_configuration_meta ?? '';
            default:
                return $this->payment_configuration ?? '';
        }
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
