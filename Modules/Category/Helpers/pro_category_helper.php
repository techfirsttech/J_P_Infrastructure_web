<?php

use Modules\Category\Models\Category;
use Modules\Products\Models\Product;

function pro_category_delete_check($id)
{
    $product = Product::where('category_id', $id)->count();
    if ($product > 0) {
        return false;
    } else {
        return true;
    }
}

function product_category_view()
{
    $category = Category::where('is_parent', '1')->where('type', 'product')->get();
    $type = '';
    return view('category::common_category', compact('category', 'type'));
}
