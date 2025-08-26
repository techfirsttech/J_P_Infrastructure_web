<?php

namespace Modules\Contractor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\Contractor\Database\Factories\ContractorFactory;

class Contractor extends BaseModel
{
    use HasFactory;

    public $table = 'contractors';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'site_id','contractor_name','mobile','year_id','created_by','updated_by','deleted_by'];


    // protected static function newFactory(): ContractorFactory
    // {
    //     // return ContractorFactory::new();
    // }
}
