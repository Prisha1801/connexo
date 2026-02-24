<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Contacts\Models\Contact;

class CTWAAdsClickLead extends Model
{
    use HasFactory;

    protected $table = 'ctwa_ads_click_leads';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'contact_id',
        'source_url',
        'source_id',
        'source_type',
        'wa_id',
    ];

    /**
     * Relationships
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}