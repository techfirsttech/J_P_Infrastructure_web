<?php

namespace Modules\SiteMaster\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\SiteMaster\Database\Factories\SiteSupervisorFactory;

class SiteSupervisor extends  Model
{
    use HasFactory;
     public $table = 'site_supervisors';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'site_master_id', 'user_id'];


    // protected static function newFactory(): SiteSupervisorFactory
    // {
    //     // return SiteSupervisorFactory::new();
    // }
}
