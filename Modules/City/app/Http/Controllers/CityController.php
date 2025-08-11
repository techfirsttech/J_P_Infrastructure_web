<?php

namespace Modules\City\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\City\Models\City;
use Modules\Country\Models\Country;
use Modules\State\Models\State;
use Yajra\DataTables\Facades\DataTables;

class CityController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:city-list|city-create|city-edit|city-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:city-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:city-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:city-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(City::with('state:id,name,code', 'country:id,name,code'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $flag = city_delete_check($row->id);
                    $show = '';
                    $edit = true ? 'city-edit' : '';
                    $delete = $flag ? 'city-delete' : '';
                    $showURL = "";
                    $editURL = "";
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? ($row->created_at)->format('d-m-Y') : '-';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            $country = Country::select('id', 'name', 'code')->get();
            $state = State::select('id', 'name', 'code',)->get();
            return view('city::index', compact('state', 'country'));
        }
    }

    public function create()
    {
        return view('city::create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', Rule::unique('cities')->where(function ($query) use ($request) {
                if (!is_null($request->id)) {
                    return $query->where([['deleted_at', null], ['state_id', '=', $request->state_id], ['id', '!=', $request->id]]);
                } else {
                    return $query->where([['deleted_at', null], ['state_id', '=', $request->state_id]]);
                }
            })],
            'state_id' => ['required'],
            'country_id' => ['required']
        ], [
            'name.required' => __('city::message.enter_name'),
            'name.unique' => __('city::message.enter_unique_name'),
            'state_id.required' => __('city::message.select_state'),
            'country_id.required' => __('city::message.select_country'),
        ]);
        if ($validator->fails()) {
            $response = ['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->id)) {
                $city = City::where('id', $request->id)->first();
                $msg = ' updated ';
            } else {
                $city = new City();
                $msg = ' added ';
            }
            $city->country_id = $request->country_id;
            $city->name = $request->name;
            $city->state_id = $request->state_id;
            $result = $city->save();
            DB::commit();
            if (!is_null($result)) {
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'City' . $msg . 'successfully.']);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'City' . $msg . 'failed.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function show(Request $request)
    {
        try {
            $cityData = City::where('state_id', $request->id)->select('id', 'name')->get();
            if ($cityData->count() > 0) {
                return response()->json(array('status_code' => 200, 'result' => $cityData));
            } else {
                return response()->json(array('status_code' => 403, 'message' => 'City List Not Found'));
            }
        } catch (\Exception $e) {
            return response()->json(array('status_code' => 500, 'message' => 'Something went wrong. Please try again.'));
        }
    }
    public function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($cities);
    }


    public function getStates($country_id)
    {
        $states = State::where('country_id', $country_id)->get();
        return response()->json($states);
    }

    public function edit($id)
    {
        try {
            $city = City::where('id', $id)->first();
            if (!is_null($city)) {
                if (city_delete_check($city->id)) {
                    return response()->json(['status_code' => 200, 'message' => 'Edit City', 'result' => $city]);
                } else {
                    return response()->json(['status_code' => 201, 'message' => 'This city already use in another module.']);
                }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'City not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function update(Request $request, City $city)
    {
        //
    }

    public function destroy($id)
    {
        try {
            $city = City::select('id')->where('id', $id)->first();
            if (!is_null($city)) {
                if (city_delete_check($city->id)) {
                    $city->delete();
                    return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                } else {
                    return response()->json(['status_code' => 201, 'message' => 'This city already use in another module.']);
                }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'City not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }


    public function byCountryId($id)
    {
        $cities = City::select('id', 'name')->where('state_id', $id)->get();
        $response = ['status_code' => 200, 'result' => $cities];
        return response()->json($response);
    }
}