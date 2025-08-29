<?php

namespace Modules\SiteMaster\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;
use Modules\User\Models\User;

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

    protected $fillable = ['id', 'site_name', 'address', 'pincode', 'country_id', 'state_id', 'city_id', 'site_master_status_id', 'created_by', 'updated_by', 'deleted_by'];


    // protected static function newFactory(): SiteMasterFactory
    // {
    //     // return SiteMasterFactory::new();
    // }

    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'site_supervisors', 'site_master_id', 'user_id');
    }
}
