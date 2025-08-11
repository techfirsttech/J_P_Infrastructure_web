<?php

namespace Modules\State\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Country\Models\Country;
use Modules\User\Models\BaseModel;

class State extends BaseModel
{
    use HasFactory;

    public $table = 'states';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id',
        'name',
        'code',
        'country_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
