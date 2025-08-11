<?php

namespace Modules\ExpenseCategory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\ExpenseCategory\Database\Factories\ExpenseCategoryStatusFactory;

class ExpenseCategoryStatus extends BaseModel
{
    use HasFactory;
    public $table = 'expense_category_statuses';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id',
        'expense_category_status_name',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    // protected static function newFactory(): ExpenseCategoryStatusFactory
    // {
    //     // return ExpenseCategoryStatusFactory::new();
    // }
}
