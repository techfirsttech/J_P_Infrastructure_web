<?php

namespace Modules\Country\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Country\Models\Country;
use Yajra\DataTables\Facades\DataTables;

class CountryController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:country-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:country-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:country-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:country-delete', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(Country::query())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $flag = country_delete_check($row->id);
                    $show = '';
                    $edit = $flag ? 'country-edit' : '';
                    $delete = $flag ? 'country-delete' : '';
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
            return view('country::index');
        }
    }

    public function create()
    {
        return view('country::create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', Rule::unique('countries')->where(function ($query) use ($request) {
                if (!is_null($request->id)) {
                    return $query->where([['deleted_at', null], ['id', '!=', $request->id]]);
                } else {
                    return $query->where('deleted_at', null);
                }
            })],
        ], [
            'name.required' => __('country::message.enter_name'),
            'name.unique' => __('country::message.enter_unique_name')
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->id)) {
                $country = Country::where('id', $request->id)->first();
                $response = ['status_code' => 200, 'data' => route('country.index'), 'message' => 'Country updated successfully.'];
            } else {
                $country = new Country();
                $response = ['status_code' => 200, 'data' => route('country.index'), 'message' => 'Country added successfully.'];
            }
            $country->name = $request->name;
            $country->code = $request->code;
            $result = $country->save();

            if (!is_null($result)) {
                DB::commit();
                return response()->json($response);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Country added failed.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function show($id)
    {
        return view('country::show');
    }

    public function edit($id)
    {
        try {
            $country = Country::where('id', $id)->first();
            if (!is_null($country)) {
                if (country_delete_check($country->id)) {
                    return response()->json(['status_code' => 200,  'result' => $country]);
                } else {
                    return response()->json(['status_code' => 201, 'message' => 'This country already use in another module.']);
                }
            } else {
                return response()->json(['status_code' => 201,  'message' => 'Country not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function update(Request $request, $id) {}

    public function destroy($id)
    {
        try {
            $country = Country::select('id')->where('id', $id)->first();
            if (!is_null($country)) {
                if (country_delete_check($country->id)) {
                    $country->delete();
                    return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                } else {
                    return response()->json(['status_code' => 201, 'message' => 'This country already use in another module.']);
                }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Country not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
