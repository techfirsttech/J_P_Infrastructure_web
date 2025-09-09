<?php

namespace Modules\PaymentMaster\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

class PaymentMaster extends  BaseModel
{
    use HasFactory;

    public $table = 'payment_masters';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = ['id', 'site_id', 'supervisor_id', 'to_supervisor_id', 'model_type', 'amount', 'status', 'remark', 'year_id', 'date', 'created_by', 'updated_by', 'deleted_by'];
}
