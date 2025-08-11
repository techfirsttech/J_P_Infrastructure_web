<?php

namespace Modules\Unit\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Unit\Models\Unit;
use Modules\Unit\Models\UnitGravity;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:unit-list|unit-create|unit-edit|unit-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:unit-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:unit-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:unit-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(Unit::select('id', 'name', 'unit_value'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $flag = unit_delete_check($row->id);
                    $show = '';
                    $edit = true ? 'unit-edit' : '';
                    $delete = $flag ? 'unit-delete' : '';
                    $showURL = "";
                    $editURL = route('unit.edit', $row->id);
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
                })
                ->editColumn('name', function ($row) {
                    return '<a href="javascript:void(0);" class="view" data-id="' . $row->id . '">' . $row->name . '</a>';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('unit::index');
        }
    }

    public function create()
    {
        $unit = Unit::select('id', 'name')->get();
        return view('unit::create', compact('unit'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', Rule::unique('units')->where(function ($query) use ($request) {
                if (!is_null($request->id)) {
                    return $query->where([['deleted_at', null], ['id', '!=', $request->id]]);
                } else {
                    return $query->where('deleted_at', null);
                }
            })],
        ], [
            'name.required' => __('unit::message.enter_unit'),
            'name.unique' => __('unit::message.enter_unique_unit'),
        ]);
        if ($validator->fails()) {
            $response = ['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            $unit = new Unit();
            $unit->name = $request->name;
            $unit->unit_value = $request->unit_value;
            $result = $unit->save();
            DB::commit();
            if (!is_null($result)) {
                if (isset($request->child_id) && !empty($request->child_id)) {
                    foreach ($request->child_id as $key => $item) {
                        if (!is_null($request->child_id[$key])) {
                            if ($request->child_id[$key] != '' && $request->child_id[$key] != 0 && $request->segment_value[$key] != '') {
                                $unitGravity = new UnitGravity();
                                $unitGravity->unit_id = $unit->id;
                                $unitGravity->child_id = $request->child_id[$key];
                                $unitGravity->unit_value = $request->segment_value[$key];
                                $unitGravity->save();
                            }
                        }
                    }
                }
                return response()->json(['status_code' => 200, 'data' => route('unit.index'), 'message' => 'Unit added successfully.']);
            } else {
                return response()->json(['status_code' => 403, 'message' => 'Unit added failed']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function show(Unit $unit)
    {
        $unit = Unit::with('unitGravity.unit')->where('id', $unit->id)->first();
        if (!is_null($unit)) {
            $data['html'] = view('unit::modal', compact('unit'))->render();
            return response()->json($data);
        } else {
            return response()->json(['status_code' => 403, 'message' => 'Unit not found.']);
        }
    }

    public function edit($id)
    {
        try {
            $unit = Unit::with('unitGravity')->where('id', $id)->first();
            if (!is_null($unit)) {
                if (unit_delete_check($unit->id)) {
                    $unit_name = Unit::select('id', 'name')->where('id', '!=', $id)->get();
                    return view('unit::edit', compact('unit', 'unit_name'));
                } else {
                    return redirect()->back()->with('warning', 'This unit already use in another module.');
                }
            } else {
                return redirect()->back()->with('warning', 'Unit not found.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function update(Request $request, Unit $unit)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', Rule::unique('units')->where(function ($query) use ($request) {
                if (!is_null($request->id)) {
                    return $query->where([['deleted_at', null], ['id', '!=', $request->id]]);
                } else {
                    return $query->where('deleted_at', null);
                }
            })],
        ], [
            'name.required' => __('unit::message.enter_unit'),
            'name.unique' => __('unit::message.enter_unique_unit'),
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            $unit = Unit::where('id', $request->id)->first();
            $unit->name = $request->name;
            $unit->unit_value = $request->unit_value;
            $result = $unit->save();
            DB::commit();
            if (!is_null($result)) {
                $unitGravity = UnitGravity::where('unit_id', $unit->id)->delete();
                foreach ($request->child_id as $key => $item) {
                    if ($request->child_id[$key] != '' && $request->child_id[$key] != 0 && $request->segment_value[$key] != '') {
                        $unitGravity = new UnitGravity();
                        $unitGravity->unit_id = $unit->id;
                        $unitGravity->child_id = $request->child_id[$key];
                        $unitGravity->unit_value = $request->segment_value[$key];
                        $unitGravity->save();
                    }
                }
                return response()->json(['status_code' => 200, 'data' => route('unit.index'), 'message' => 'Unit updated successfully.']);
            } else {
                return response()->json(['status_code' => 403, 'message' => 'Unit updated failed']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function destroy($id)
    {
        try {
            $unit = Unit::select('id')->where('id', $id)->first();
            if (!is_null($unit)) {
                if (unit_delete_check($unit->id)) {
                    $unit->delete();
                    return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                } else {
                    return response()->json(['status_code' => 201, 'message' => 'This unit already use in another module.']);
                }
                return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Unit not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function unitItemDelete(Request $request)
    {
        try {
            $query = UnitGravity::where('unit_id', $request->unit_id)->where('child_id', $request->id)->delete();
            if ($query) {
                return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
            } else {
                return response()->json(['status_code' => 403, 'message' => 'Child Unit not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
