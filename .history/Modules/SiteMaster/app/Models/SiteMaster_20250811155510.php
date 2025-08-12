<?php

namespace Modules\SiteMaster\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\SiteMaster\Database\Factories\SiteMasterFactory;

class SiteMaster extends BaseModel
{
    use HasFactory;


     public $table = 'cities';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'name', 'state_id', 'country_id'];


    // protected static function newFactory(): SiteMasterFactory
    // {
    //     // return SiteMasterFactory::new();
    // }
}
