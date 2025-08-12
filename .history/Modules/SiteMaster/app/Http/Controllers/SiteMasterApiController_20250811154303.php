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
            $siteMaster = SiteMaster::select('id','site_name','address','pincode','country_id','state_id','city_id','site_master_status_id');
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
