<?php

namespace Modules\Labour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\Labour\Database\Factories\LabourFactory;

class Labour extends  BaseModel
{
    use HasFactory;

    public $table = 'labours';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'supervisor_id','site_id', 'contractor_id','labour_name', 'daily_wage','mobile','address','status','user_id','year_id','created_by','updated_by','deleted_by'];


    // protected static function newFactory(): LabourFactory
    // {
    //     // return LabourFactory::new();
    // }
}
