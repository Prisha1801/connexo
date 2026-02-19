<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    
    protected $table = 'wa_templates';
    public $guarded = [];
    protected static function booted(){
        // static::addGlobalScope(new CompanyScope);

        // static::creating(function ($model){
        //   $company_id=session('company_id',null);
        //     if($company_id){
        //         $model->company_id=$company_id;
        //     }
        // });
    }
}



