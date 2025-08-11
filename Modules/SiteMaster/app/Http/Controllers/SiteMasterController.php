<?php

namespace Modules\SiteMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\City\Models\City;
use Modules\Country\Models\Country;
use Modules\SiteMaster\Models\SiteMaster;
use Modules\SiteMaster\Models\SiteMasterStatus;
use Modules\SiteMaster\Models\SiteSupervisor;
use Modules\State\Models\State;
use Modules\User\Models\User;
use Modules\User\Models\UserProfile;
use Yajra\DataTables\Facades\DataTables;

class SiteMasterController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:site-master-list|site-master-create|site-master-edit|site-master-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:site-master-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:site-master-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:site-master-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
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
        // ->get();

        if (request()->ajax()) {
            return DataTables::of($siteMaster)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $show = '';
                    $edit = 'site-master-edit';
                    $delete = 'site-master-delete';
                    $assign = ''; //'assign-user-list';
                    $showURL = "";
                    $editURL = route('sitemaster.edit', $row->id);
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL', 'assign'));
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('sitemaster::index');
        }
    }
    // {
    //     return view('sitemaster::index');
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $array_role = ['Super Admin'];
        // $roleMaster = Role::whereNotIn('name', $array_role)->pluck('name', 'name')->all();
        $supervisor = User::get();
        $country = Country::where('name', 'India')->first();
        $state = State::where('country_id', $country->id)->get();
        $city = City::where('country_id', $country->id)->get();
        return view('sitemaster::create', compact('supervisor', 'state', 'city'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('sitemaster::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $siteMaster = SiteMaster::where('id', $id)->first();
        // $user = User::find($userProfile->user_id);
        // $userRole = $user->roles->pluck('name')->toArray();
        // $locations = Location::get();
        return view('sitemaster::edit', compact('siteMaster'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $userProfile = UserProfile::where('id', $request->user_profile_id)->first();
        $user = User::where('id', $userProfile->user_id)->first();
        $validator = Validator::make($request->all(), [
            'mobile' => [
                'required',
                Rule::unique('site_masters')->where(function ($query) use ($request) {
                    return $query->where('deleted_at', '=', null);
                })->ignore($user->id),
            ],
            'username' => [
                'required',
                Rule::unique('site_masters')->where(function ($query) use ($request) {
                    return $query->where('deleted_at', '=', null);
                })->ignore($user->id),
            ],
            'email' => [
                Rule::unique('site_masters')->where(function ($query) use ($request) {
                    return $query->where('deleted_at', '=', null);
                })->ignore($user->id),
            ],
            'password' => 'nullable|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'confirm_password' => 'nullable|same:password',
            'roles' => 'required'
        ], [
            'mobile.required' => __('user::message.enter_mobile'),
            'mobile.numeric' => __('user::message.enter_mobile'),
            'mobile.digits' => __('user::message.enter_digits'),
            'username.required' => __('user::message.enter_username'),
            'password.min' => __('user::message.enter_password_min'),
            'password.regex' => __('user::message.enter_password_regex'),
            'roles' => __('user::message.select_designation')
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $user = User::where('id', $userProfile->user_id)->first();
            $user->name = ucwords($request->firstname) . ' ' . ucwords($request->lastname);
            $user->email = strtolower($request->email);
            $user->mobile = $request->mobile;
            $user->username = $request->username;
            // $user->status = 'Active';
            $user->status = $request->status;

            $user->save();
            $user->syncRoles([]);
            $user->assignRole($request->input('roles'));

            $userProfile->firstname = ucwords($request->firstname);
            $userProfile->lastname = ucwords($request->lastname);
            $userProfile->date_of_birth = (!empty($request->dateofbirth)) ? date('Y-m-d', strtotime($request->dateofbirth)) : null;
            $result = $userProfile->save();
            if ($result) {
                DB::commit();
                return redirect()->route('sitemaster.index')->with('success', 'User updated successfully');
            } else {
                DB::rollback();
                return redirect()->back()->with('warning', 'User updated failed');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try again');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $userProfile = UserProfile::where('id', $id)->first();
            $user = User::findOrFail($userProfile->user_id);
            $user->delete();
            $userProfile->delete();
            return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}