<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\Attendance\Database\Factories\AttendanceFactory;

class Attendance extends BaseModel
{
    use HasFactory;

    public $table = 'attendances';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'site_id', 'supervisor_id','labour_id','type', 'amount', 'year_id','created_by','updated_by','deleted_by'];

    // protected static function newFactory(): AttendanceFactory
    // {
    //     // return AttendanceFactory::new();
    // }
}
