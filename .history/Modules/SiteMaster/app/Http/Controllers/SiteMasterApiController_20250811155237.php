<?php

namespace Modules\SiteMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SiteMaster\Models\SiteMaster;

class SiteMasterApiController extends Controller
{

    public function index()
    {
        try {
            $siteMaster = SiteMaster::select(
            'site_masters.id',
            'site_masters.site_name',
            'site_masters.address',
            'site_masters.pincode',
            'site_masters.country_id',
            'site_masters.state_id',
            'site_masters.city_id',
            'site_masters.site_master_status_id',
            'site_masters.created_at',
            'site_masters.created_by',
            'countries.name as country_name',
            'cities.name as city_name',
            'states.name as state_name',
            'site_master_statuses.status_name',
        )
            ->leftJoin('countries', 'countries.id', '=', 'site_masters.country_id')
            ->leftJoin('states', 'states.id', '=', 'site_masters.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'site_masters.city_id')
            ->leftJoin('site_master_statuses', 'site_master_statuses.id', '=', 'site_masters.site_master_status_id');
        ->get();
            $state = State::select('id', 'name')->where('country_id', $county)
                ->orderBy('id', 'DESC')
                ->get();
            return response(['status' => true, 'message' => 'State List', 'result' => $state], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function create()
    {
        return view('sitemaster::create');
    }
    public function store(Request $request) {}

    public function show($id)
    {
        return view('sitemaster::show');
    }

    public function edit($id)
    {
        return view('sitemaster::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
