<?php

namespace Modules\StockTransfer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\StockTransfer\Database\Factories\StockTransferFactory;

class StockTransfer extends BaseModel
{
    use HasFactory;
    public $table = 'stock_transfers';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'material_id','material_stock_id', 'from_site_id', 'supervisor_id', 'to_site_id','to_supervisor_id','remark', 'quantity','unit_id', 'year_id','created_by', 'updated_by', 'deleted_by'];

    // protected static function newFactory(): StockTransferFactory
    // {
    //     // return StockTransferFactory::new();
    // }
}
