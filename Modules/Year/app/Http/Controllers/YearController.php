<?php

namespace Modules\Year\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Year\Models\Year;
use Yajra\DataTables\Facades\DataTables;

class YearController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:year-list', ['only' => ['index']]);
        $this->middleware('permission:year-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:year-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:year-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Year::select('id', 'name', DB::raw("CASE WHEN set_default = 1 THEN 'Yes' ELSE 'No' END as set_default")))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $flag = year_delete_check($row->id);
                    $show = '';
                    $edit = ($flag && $row->set_default != 'Yes') ? 'year-edit' : '';
                    $delete = ($flag && $row->set_default != 'Yes') ? 'year-delete' : '';
                    $showURL = "";
                    $editURL = "";
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('year::index');
        }
    }

    public function create()
    {
        //return view('year::create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', Rule::unique('years')->where(function ($query) use ($request) {
                return $query->where('deleted_at', null);
            })],
        ], [
            'name.required' => __('year::message.enter_year'),
            'name.unique' => __('year::message.enter_unique_year'),
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $defaultSet = !empty($request->set_default) ? 1 : 0;
            if ($defaultSet == 1) {
                Year::query()->update(['set_default' => 0]);
            }
            $year = new Year();
            $year->name = $request->name;
            $year->set_default = $defaultSet;
            $year->save();
            DB::commit();
            return response()->json(['status_code' => 200, 'message' => 'Year added successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function show($id)
    {
        //return view('year::show');
    }

    public function edit($id)
    {
        try {
            $year = Year::select('id', 'name', 'set_default')->where('id', $id)->first();
            if (!is_null($year)) {
                if (year_delete_check($year->id)) {
                    return response()->json(['status_code' => 200, 'message' => 'Edit Year.', 'result' => $year]);
                } else {
                    return response()->json(['status_code' => 201, 'message' => 'This year already use in another module.']);
                }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Year not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', Rule::unique('years')->where(function ($query) use ($id) {
                return $query->where([['deleted_at', null], ['id', '!=', $id]]);
            })],
        ], [
            'name.required' => __('year::message.enter_year'),
            'name.unique' => __('year::message.enter_unique_year'),
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $year = Year::where('id', $id)->first();
            if (!is_null($year)) {
                $defaultSet = !empty($request->set_default) ? 1 : 0;
                if ($defaultSet == 1) {
                    Year::query()->update(['set_default' => 0]);
                }

                $year->name = $request->name;
                $year->set_default = $defaultSet;
                $year->save();
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'Year updated successfully.']);
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Year not found.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function destroy($id)
    {
        try {
            $year = Year::select('id')->where('id', $id)->first();
            if (!is_null($year)) {
                if (year_delete_check($year->id)) {
                    $year->delete();
                    return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                } else {
                    return response()->json(['status_code' => 201, 'message' => 'This year already use in another module.']);
                }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Year not found.']);
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
