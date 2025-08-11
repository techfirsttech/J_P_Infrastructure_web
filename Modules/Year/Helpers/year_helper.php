<?php

use Modules\Purchase\Models\Purchase;

function year_delete_check($id)
{
    $purchase = Purchase::where('year_id', $id)->count();
    if ($purchase > 0) {
        return false;
    } else {
        return true;
    }
}
