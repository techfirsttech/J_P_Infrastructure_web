<?php

namespace Modules\City\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Country\Models\Country;
use Modules\State\Models\State;
use Modules\User\Models\BaseModel;

class City extends BaseModel
{
    use HasFactory;

    public $table = 'cities';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'name', 'state_id', 'country_id'];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
