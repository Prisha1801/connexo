<?php

namespace App\Models;

use App\Traits\HasConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogProduct extends Model
{
    use HasConfig;
    use HasFactory;
    use SoftDeletes;

    protected $modelName = "App\Models\CatalogProduct";

    protected $table = 'catalog_products';

    protected $guarded = [];
}
