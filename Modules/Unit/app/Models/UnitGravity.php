<?php

namespace Modules\Unit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitGravity extends Model
{
     use HasFactory;

    protected $table = 'unit_gravities';
    protected $fillable = ['unit_id', 'child_id', 'unit_value'];

    public function unit()
    {
        return $this->hasOne(Unit::class, 'id', 'child_id');
    }
}
