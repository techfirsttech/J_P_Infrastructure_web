<?php

namespace Modules\SiteMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $user = Auth::user();
            $role = $user->roles->first()->name ?? '';
            $query = SiteMaster::select(
                'site_masters.id',
                'site_masters.site_name',
                'site_masters.address',
                'site_masters.pincode',
                'site_masters.country_id',
                'site_masters.state_id',
                'site_masters.city_id',
                'site_masters.site_master_status_id',
                'countries.name as country_name',
                'cities.name as city_name',
                'states.name as state_name',
                'site_master_statuses.status_name',
            )
                ->leftJoin('countries', 'countries.id', '=', 'site_masters.country_id')
                ->leftJoin('states', 'states.id', '=', 'site_masters.state_id')
                ->leftJoin('cities', 'cities.id', '=', 'site_masters.city_id')
                ->leftJoin('site_master_statuses', 'site_master_statuses.id', '=', 'site_masters.site_master_status_id')
                ->orderBy('site_masters.id', 'DESC');
            if ($role === 'Supervisor') {
                $siteIds = SiteSupervisor::where('user_id', $user->id)->pluck('site_master_id')->toArray();
                $query->whereIn('site_masters.id', $siteIds);
            }
            $siteMasters = $query->simplePaginate(12);

            foreach ($siteMasters as $site) {
                $site->supervisors = SiteSupervisor::where('site_master_id', $site->id)
                    ->leftJoin('users', 'site_supervisors.user_id', 'users.id')
                    ->select('site_supervisors.user_id', 'users.name')
                    ->get();
            }
            return response(['status' => true, 'message' => 'Site Master List', 'result' => $siteMasters->items()], 200);
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
        // dd($request->site_name);
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
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
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

            $userID = $request->users;
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
                return response()->json(['status' => true, 'message' => 'Site created successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Site can not create.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
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

    public function update(Request $request)
    {
        // dd($request->site_name);
        $validator = Validator::make($request->all(), [
            'site_name' => [
                'required',
                // Rule::unique('site_masters')->where(function ($query) use ($request) {
                //     return $query->where('deleted_at', '=', null);
                // })
            ],
        ], [
            'site_name.required' => __('sitemaster::message.enter_site_name'),
            // 'site_name.unique' => __('sitemaster::message.enter_unique_site_name'),

        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {
            $country = Country::where('name', 'India')->first();
            $countryID = $country->id;
            $status = SiteMasterStatus::where('status_name', 'Approved')->first();
            $statusID = $status->id;
            $siteMaster = SiteMaster::where('id', $request->id)->first();
            $siteMaster->site_name = ucwords($request->site_name);
            $siteMaster->address = $request->address;
            $siteMaster->country_id = $countryID;
            $siteMaster->state_id = $request->state_id;
            $siteMaster->city_id = $request->city_id;
            $siteMaster->pincode = $request->pincode;
            $siteMaster->site_master_status_id = $statusID;

            $result = $siteMaster->save();


            SiteSupervisor::where('site_master_id', $request->id)->delete();
            $userID = $request->users;
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
                return response()->json(['status' => true, 'message' => 'Site Update successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Site can not update.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logToCustomFile($e);
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }


    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:site_masters,id',
        ], [
            'id.required' => 'ID is required.',
            'id.integer' => 'ID must be an integer.',
            'id.exists' => 'The selected Site Master ID does not exist.',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }
        try {
            $siteMaster = SiteMaster::where('id', $request->id)->first();
            if (!is_null($siteMaster)) {
                SiteSupervisor::where('site_master_id', $siteMaster->id)->delete();
                $siteMaster->delete();
                $response = ['status' => true, 'message' => 'Site deleted successfully.'];
            } else {
                $response = ['status' => false, 'message' => 'Site not found.'];
            }
            return response($response, 200);
        } catch (\Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    // public function siteDropdown()
    // {
    //     try {
    //         $siteDropdown = SiteMaster::select('id', 'site_name')->get();
    //         foreach ($siteDropdown as $site) {
    //             $site->supervisors = SiteSupervisor::where('site_master_id', $site->id)
    //                 ->leftJoin('users', 'site_supervisors.user_id', 'users.id')
    //                 ->select('site_supervisors.user_id', 'users.name')
    //                 ->get();
    //         }

    //         return response(['status' => true, 'message' => 'Site Dropdown', 'site_dropdown' => $siteDropdown], 200);
    //     } catch (Exception $e) {
    //         return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
    //     }
    // }

    public function siteDropdown(Request $request)
    {
        try {
            $userId = $request->input('supervisor_id');
            $query = SiteMaster::select('id', 'site_name');
            if ($userId) {
                $query->whereIn('id', function ($subQuery) use ($userId) {
                    $subQuery->select('site_master_id')->from('site_supervisors')->where('user_id', $userId);
                });
            }
            $siteDropdown = $query->orderBy('site_name','asc')->get();
            return response(['status' => true, 'message' => 'Site Dropdown', 'site_dropdown' => $siteDropdown], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
}
