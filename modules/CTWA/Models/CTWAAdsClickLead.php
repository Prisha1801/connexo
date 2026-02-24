<?php

namespace Modules\CTWA\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CTWAAdsClickLead extends Model
{
    use HasFactory;

    protected $table = 'ctwa_ads_click_leads';

    protected $fillable = [
        'company_id',
        'contact_id',
        'source_url',
        'source_id',
        'source_type',
        'wa_id',
        'meta_lead_id',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    public function contact()
    {
        return $this->belongsTo(\Modules\Contacts\Models\Contact::class, 'contact_id');
    }
}
