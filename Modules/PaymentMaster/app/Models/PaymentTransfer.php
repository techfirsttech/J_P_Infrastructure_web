<?php

namespace Modules\PaymentMaster\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\PaymentMaster\Database\Factories\PaymentTransferFactory;

class PaymentTransfer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): PaymentTransferFactory
    // {
    //     // return PaymentTransferFactory::new();
    // }
}
