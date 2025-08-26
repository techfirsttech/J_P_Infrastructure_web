<?php

namespace Modules\RawMaterialMaster\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\RawMaterialMaster\Database\Factories\RawMaterialStockFactory;

class RawMaterialStock extends BaseModel
{
    use HasFactory;
    public $table = 'raw_material_stocks';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'material_id', 'site_id', 'supervisor_id', 'supplier_id', 'quantity', 'unit_id', 'year_id','created_by', 'updated_by', 'deleted_by'];



    // protected static function newFactory(): RawMaterialStockFactory
    // {
    //     // return RawMaterialStockFactory::new();
    // }
}
