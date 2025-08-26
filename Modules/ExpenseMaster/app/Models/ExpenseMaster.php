<?php

namespace Modules\ExpenseMaster\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\ExpenseMaster\Database\Factories\ExpenseMasterFactory;

class ExpenseMaster extends BaseModel
{
    use HasFactory;

    public $table = 'expense_masters';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'site_id', 'supervisor_id', 'date','expense_category_id','amount','document','remark','year_id','created_by','updated_by','deleted_by'];

    // protected static function newFactory(): ExpenseMasterFactory
    // {
    //     // return ExpenseMasterFactory::new();ExpenseMaster ExpenseMaster
    // }
}
