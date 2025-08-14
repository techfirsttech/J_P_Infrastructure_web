<?php

namespace Modules\Supplier\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\City\Models\City;
use Modules\Country\Models\Country;
use Modules\State\Models\State;
use Modules\User\Models\BaseModel;

class Supplier extends BaseModel
{
    use HasFactory;
    protected $table = 'suppliers';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['supplier_name', 'supplier_code', 'company_name','mobile', 'contact_number', 'contact_person_name', 'contact_person_number', 'email', 'gst_number','gst_apply','address_line_1', 'address_line_2', 'address_line_3', 'term_condition', 'country_id', 'state_id', 'city_id'];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
