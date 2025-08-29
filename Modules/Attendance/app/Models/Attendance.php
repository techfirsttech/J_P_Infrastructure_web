<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Contractor\Models\Contractor;
use Modules\Labour\Models\Labour;
use Modules\SiteMaster\Models\SiteMaster;
use Modules\User\Models\BaseModel;
use Modules\User\Models\User;

class Attendance extends BaseModel
{
    use HasFactory;
    public $table = 'attendances';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['id', 'site_id', 'contractor_id', 'supervisor_id', 'labour_id', 'type', 'amount', 'year_id', 'date'];


    public function site()
    {
        return $this->hasOne(SiteMaster::class, 'id', 'site_id')->select('id', 'site_name');
    }
    public function contractor()
    {
        return $this->hasOne(Contractor::class, 'id', 'contractor_id')->select('id', 'contractor_name');
    }
    public function labour()
    {
        return $this->hasOne(Labour::class, 'id', 'labour_id')->select('id', 'labour_name');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'supervisor_id')->select('id', 'name');
    }
}
