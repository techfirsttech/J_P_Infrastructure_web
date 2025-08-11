<?php

namespace Modules\Category\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\BaseModel;

class Category extends BaseModel
{
    use HasFactory;

    protected $table = 'categories';
    protected $primarykey = 'id';
    protected $guarded = ['id'];

    // public function parent()
    // {
    //     return $this->belongsTo(Category::class, 'parent_id');
    // }

    // public function raw_material()
    // {
    //     return $this->hasMany(RawMaterial::class, 'category_id', 'id')->select('id', 'category_id', 'name', 'current_stock');
    // }
}
