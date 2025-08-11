<?php

use Modules\City\Models\City;

function state_delete_check($id)
{
    $city = City::where('state_id', $id)->count();
    if ($city > 0) {
        return false;
    } else {
        return true;
    }
}
