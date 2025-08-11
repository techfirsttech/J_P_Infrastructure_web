<?php

namespace Modules\Setting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';
    protected $fillable = ['id', 'company_name', 'tag_line', 'favicon', 'logo', 'logo_dark', 'gst_number', 'pancard_number', 'tan_number', 'country_id', 'state_id', 'city_id'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
