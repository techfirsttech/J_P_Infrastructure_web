<?php

namespace Modules\Unit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\BaseModel;

class Unit extends Model
{
    use HasFactory;
    protected $table = 'units';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['id', 'name', 'unit_value'];

    public function unitGravity()
    {
        return $this->hasMany(UnitGravity::class, 'unit_id', 'id');
    }
}

