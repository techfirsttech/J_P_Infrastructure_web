<?php

namespace Modules\Currency\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencies';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = ['currency_name', 'currency_symbol'];
}
