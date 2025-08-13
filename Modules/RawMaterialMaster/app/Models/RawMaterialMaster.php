<?php

namespace Modules\RawMaterialMaster\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\RawMaterialMaster\Database\Factories\RawMaterialMasterFactory;

class RawMaterialMaster extends BaseModel
{
    use HasFactory;
     public $table = 'raw_material_masters';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'material_category_id', 'material_name', 'material_code','unit_id','alert_quantity','tax','created_by','updated_by','deleted_by'];



    // protected static function newFactory(): RawMaterialMasterFactory
    // {
    //     // return RawMaterialMasterFactory::new();
    // }
}
