<?php

namespace Modules\SiteMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Country\Models\Country;
use Modules\SiteMaster\Models\SiteMaster;
use Modules\SiteMaster\Models\SiteMasterStatus;
use Modules\SiteMaster\Models\SiteSupervisor;

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
                // 'site_masters.created_at',
                // 'site_masters.created_by',
                'countries.name as country_name',
                'cities.name as city_name',
                'states.name as state_name',
                'site_master_statuses.status_name',
            )
                ->leftJoin('countries', 'countries.id', '=', 'site_masters.country_id')
                ->leftJoin('states', 'states.id', '=', 'site_masters.state_id')
                ->leftJoin('cities', 'cities.id', '=', 'site_masters.city_id')
                ->leftJoin('site_master_statuses', 'site_master_statuses.id', '=', 'site_masters.site_master_status_id')
                ->get();
            return response(['status' => true, 'message' => 'Site Master List', 'result' => $siteMaster], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function create()
    {
        return view('sitemaster::create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => [
                'required',
                Rule::unique('site_masters')->where(function ($query) use ($request) {
                    return $query->where('deleted_at', '=', null);
                })
            ],
        ], [
            'site_name.required' => __('sitemaster::message.enter_site_name'),
            'site_name.unique' => __('sitemaster::message.enter_unique_site_name'),

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $country = Country::where('name', 'India')->first();
            $countryID = $country->id;
            $status = SiteMasterStatus::where('status_name', 'Approved')->first();
            $statusID = $status->id;
            $siteMaster = new SiteMaster();
            $siteMaster->site_name = ucwords($request->site_name);
            $siteMaster->address = $request->address;
            $siteMaster->country_id = $countryID;
            $siteMaster->state_id = $request->state_id;
            $siteMaster->city_id = $request->city_id;
            $siteMaster->pincode = $request->pincode;
            $siteMaster->site_master_status_id = $statusID;

            $result = $siteMaster->save();

            $userID = $request->user_id;
            if ($userID) {
                foreach ($userID as $userKey => $user) {
                    $siteSupervisor = new SiteSupervisor();
                    $siteSupervisor->site_master_id =  $siteMaster->id;
                    $siteSupervisor->user_id =  $user;
                    $siteSupervisor->save();
                }
            }
            DB::commit();
            if ($result) {
                DB::commit();
                return redirect()->route('sitemaster.index')->with('success', 'Site added successfully');
            } else {
                DB::rollback();
                return redirect()->back()->with('warning', 'SIte added failed');
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try again');
        }
    }

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
