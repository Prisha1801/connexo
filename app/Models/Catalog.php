<?php

namespace App\Models;

use App\Traits\HasConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Catalog extends Model
{
    use HasConfig;
    use HasFactory;
    use SoftDeletes;

    protected $modelName = "App\Models\Catalog";

    protected $table = 'catalogs';

    protected $fillable = [
        'catalog_id',
        'name',
        'company_id',
    ];
}
