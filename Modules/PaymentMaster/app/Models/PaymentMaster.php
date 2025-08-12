<?php

namespace Modules\PaymentMaster\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\PaymentMaster\Database\Factories\PaymentMasterFactory;

class PaymentMaster extends  BaseModel
{
    use HasFactory;

    public $table = 'payment_masters';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'site_id', 'supervisor_id', 'model_type','model_type','amount','status','remark','year_id','created_by','updated_by','deleted_by'];


    // protected static function newFactory(): PaymentMasterFactory
    // {
    //     // return PaymentMasterFactory::new();
    // }
}
