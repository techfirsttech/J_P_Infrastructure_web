<?php

namespace Modules\Party\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

// use Modules\Party\Database\Factories\PartyFactory;

class Party extends BaseModel
{
    use HasFactory;
     public $table = 'parties';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = ['id', 'party_name','created_by','updated_by','deleted_by'];



    // protected static function newFactory(): PartyFactory
    // {
    //     // return PartyFactory::new();
    // }
}
