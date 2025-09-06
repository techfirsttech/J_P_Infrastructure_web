<?php

namespace Modules\SiteMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\City\Models\City;
use Modules\Contractor\Models\Contractor;
use Modules\Country\Models\Country;
use Modules\ExpenseMaster\Models\ExpenseMaster;
use Modules\IncomeMaster\Models\IncomeMaster;
use Modules\RawMaterialMaster\Models\RawMaterialMaster;
use Modules\RawMaterialMaster\Models\RawMaterialStock;
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
                    if ($row->id == 18) {

                        $edit = '';
                        $delete = '';
                    } else {
                        $edit = 'site-master-edit';
                        $delete = 'site-master-delete';
                    }
                    $assign = ''; //'assign-user-list';
                    $showURL = "";
                    $editURL = route('sitemaster.edit', $row->id);
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL', 'assign'));
                })
                ->editColumn('site_name', function ($row) {
                    return '<a href="javascript:void(0);" class="view" data-id="' . $row->id . '">' . $row->site_name . '</a>';
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
        // $supervisor = User::get();
        $supervisor = User::select('id', 'name')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'supervisor');
            })
            ->orderBy('id', 'DESC')
            ->get();
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
            // $status = SiteMasterStatus::where('status_name', 'Approved')->first();
            // $statusID = $status->id;
            $siteMaster = new SiteMaster();
            $siteMaster->site_name = ucwords($request->site_name);
            $siteMaster->address = $request->address;
            $siteMaster->country_id = $countryID;
            $siteMaster->state_id = $request->state_id;
            $siteMaster->city_id = $request->city_id;
            $siteMaster->pincode = $request->pincode;
            // $siteMaster->site_master_status_id = $statusID;

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
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try again');
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $query = SiteMaster::with(['supervisors:id,name'])
                ->select(
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
                    'site_master_statuses.status_name'
                )
                ->leftJoin('countries', 'countries.id', '=', 'site_masters.country_id')
                ->leftJoin('states', 'states.id', '=', 'site_masters.state_id')
                ->leftJoin('cities', 'cities.id', '=', 'site_masters.city_id')
                ->leftJoin('site_master_statuses', 'site_master_statuses.id', '=', 'site_masters.site_master_status_id')
                ->where('site_masters.id', $id)
                ->first();
            if (!is_null($query)) {
                $data['html'] = view('sitemaster::model', compact('query'))->render();
                return response()->json($data);
            } else {
                return response()->json(['status_code' => 403, 'message' => 'Site not found.']);
            }
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $site = SiteMaster::findOrFail($id);
        $siteSupervisor = SiteSupervisor::where('site_master_id', $site->id)->get();

        $supervisor = User::role('Supervisor')->get();
        $supervisor_ids = $siteSupervisor->pluck('user_id')->toArray();
        $state = State::all();
        $cities = City::where('state_id', $site->state_id)->get(); // Load based on selected state

        return view('sitemaster::edit', compact('site', 'supervisor', 'state', 'cities', 'siteSupervisor', 'supervisor_ids'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => [
                'required',
                Rule::unique('site_masters')->ignore($id)->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
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
            $siteMaster = SiteMaster::findOrFail($id);
            $country = Country::where('name', 'India')->first();
            $countryID = $country ? $country->id : null;

            $siteMaster->site_name = ucwords($request->site_name);
            $siteMaster->address = $request->address;
            $siteMaster->country_id = $countryID;
            $siteMaster->state_id = $request->state_id;
            $siteMaster->city_id = $request->city_id;
            $siteMaster->pincode = $request->pincode;
            $siteMaster->save();

            // Update supervisors
            // First delete old
            SiteSupervisor::where('site_master_id', $siteMaster->id)->delete();

            // Then re-insert new ones
            $userID = $request->user_id;
            if ($userID) {
                foreach ($userID as $user) {
                    SiteSupervisor::create([
                        'site_master_id' => $siteMaster->id,
                        'user_id' => $user,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('sitemaster.index')->with('success', 'Site updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e); // You can uncomment for debugging
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $siteMaster = SiteMaster::select('id')->where('id', $id)->first();
            if (!is_null($siteMaster)) {
                $income = IncomeMaster::Where('site_id', $siteMaster->id)->count();
                $expense = ExpenseMaster::Where('site_id', $siteMaster->id)->count();
                $rawMaterialStock = RawMaterialStock::Where('site_id', $siteMaster->id)->count();
                $contractor = Contractor::Where('site_id', $siteMaster->id)->count();
                if ($income == 0 || $expense == 0 || $rawMaterialStock == 0 || $contractor == 0) {
                    $siteMaster->delete();
                    return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                } else {
                    return response()->json(['status_code' => 201, 'message' => 'This Site already use in another module.']);
                }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Site not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
