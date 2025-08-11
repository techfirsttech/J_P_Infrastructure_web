<?php

namespace Modules\ExpenseCategory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\ExpenseCategory\Database\Factories\ExpenseCategoryFactory;

class ExpenseCategory extends BaseModel
{
    use HasFactory;
    public $table = 'expense_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id',
        'expense_category_name',
        'expense_category_status_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    // protected static function newFactory(): ExpenseCategoryFactory
    // {
    //     // return ExpenseCategoryFactory::new();
    // }

    public function expense_category_status()
    {
        return $this->belongsTo(ExpenseCategoryStatus::class, 'expense_category_status_id');
    }
}
