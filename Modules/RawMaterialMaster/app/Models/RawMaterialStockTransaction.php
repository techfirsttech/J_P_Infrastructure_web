<?php

namespace Modules\RawMaterialMaster\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\RawMaterialMaster\Database\Factories\RawMaterialStockTransactionFactory;

class RawMaterialStockTransaction extends BaseModel
{
    use HasFactory;
    public $table = 'raw_material_stock_transactions';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'material_id','material_stock_id', 'site_id', 'supervisor_id', 'supplier_id','description', 'quantity','price','total', 'unit_id', 'type','remark','year_id','created_by', 'updated_by', 'deleted_by'];



    // protected static function newFactory(): RawMaterialStockTransactionFactory
    // {
    //     // return RawMaterialStockTransactionFactory::new();
    // }
}
