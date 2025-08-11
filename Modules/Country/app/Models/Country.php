<?php

namespace Modules\Country\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\Country\Database\Factories\CountryFactory;

class Country extends BaseModel
{
    use HasFactory;

    public $table = 'countries';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id',
        'name',
        'code',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
