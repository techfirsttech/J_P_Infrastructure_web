<?php

namespace Modules\SiteMaster\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\SiteMaster\Database\Factories\SiteMasterFactory;

class SiteMaster extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): SiteMasterFactory
    // {
    //     // return SiteMasterFactory::new();
    // }
}
