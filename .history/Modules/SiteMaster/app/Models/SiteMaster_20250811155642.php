<?php

namespace Modules\SiteMaster\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\SiteMaster\Database\Factories\SiteMasterFactory;

class SiteMaster extends BaseModel
{
    use HasFactory;
     public $table = 'site_masters';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'site_name', 'address', 'pincode','country_id','state_id','city_id'];


    // protected static function newFactory(): SiteMasterFactory
    // {
    //     // return SiteMasterFactory::new();
    // }
}
