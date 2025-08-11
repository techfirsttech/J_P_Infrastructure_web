<?php

namespace Modules\SiteMaster\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\SiteMaster\Database\Factories\SiteMasterStatusFactory;

class SiteMasterStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): SiteMasterStatusFactory
    // {
    //     // return SiteMasterStatusFactory::new();
    // }
}
