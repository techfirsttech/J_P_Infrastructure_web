<?php

namespace Modules\RawMaterialCategory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\RawMaterialCategory\Database\Factories\RawMaterialCategoryFactory;

class RawMaterialCategory extends BaseModel
{
    use HasFactory;
    public $table = 'raw_material_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id',
        'material_category_name',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    // protected static function newFactory(): RawMaterialCategoryFactory
    // {
    //     // return RawMaterialCategoryFactory::new();
    // }
}
