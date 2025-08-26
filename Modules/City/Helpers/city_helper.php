<?php

use Modules\Customer\Models\Customer;
use Modules\Customer\Models\Customerlocation;
use Modules\Supplier\Models\Supplier;
use Modules\Supplier\Models\Supplierlocation;

function city_delete_check($id)
{
    // $supplier = Supplierlocation::where('city_id', $id)->count();
    // $customer = Customerlocation::where('city_id', $id)->count();
    // if ($supplier > 0 || $customer > 0) {
    //     return false;
    // } else {
        return true;
    // }
}
