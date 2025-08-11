<?php

namespace Modules\Year\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

class Year extends BaseModel
{
    use HasFactory;

    public $tables = 'years';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['name', 'set_default'];
}
