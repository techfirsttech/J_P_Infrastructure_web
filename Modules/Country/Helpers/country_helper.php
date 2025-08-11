<?php

use Modules\State\Models\State;

function country_delete_check($id)
{
    $state = State::where('country_id', $id)->count();
    if ($state > 0) {
        return false;
    } else {
        return true;
    }
}
