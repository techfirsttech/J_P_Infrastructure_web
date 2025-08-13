<?php

namespace Modules\RawMaterialMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\RawMaterialCategory\Models\RawMaterialCategory;
use Modules\RawMaterialMaster\Models\RawMaterialMaster;
use Modules\Unit\Models\Unit;
use Yajra\DataTables\Facades\DataTables;

class RawMaterialMasterController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:material-master-list|material-master-create|material-master-edit|material-master-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:material-master-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:material-master-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:material-master-delete', ['only' => ['destroy']]);
    }


    public function index()
    {
        $rawMaterialMaster = RawMaterialMaster::select(
            'raw_material_masters.id',
            'raw_material_masters.material_category_id',
            'raw_material_masters.material_name',
            'raw_material_masters.material_code',
            'raw_material_masters.unit_id',
            'raw_material_masters.alert_quantity',
            'raw_material_masters.tax',
            'raw_material_masters.created_at',
            'raw_material_masters.created_by',

            'raw_material_categories.material_category_name',
            'units.name as unit_name'
        )
            ->leftJoin('raw_material_categories', 'raw_material_categories.id', '=', 'raw_material_masters.material_category_id')
            ->leftJoin('units', 'units.id', '=', 'raw_material_masters.unit_id');


        if (request()->ajax()) {
            return DataTables::of($rawMaterialMaster)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $show = '';
                    $edit = 'material-master-edit';
                    $delete = 'material-master-delete';
                    $assign = ''; //'assign-user-list';
                    $showURL = "";
                    $editURL = route('rawmaterialmaster.edit', $row->id);
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL', 'assign'));
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('rawmaterialmaster::index');
        }
    }


    public function create()
    {


        $rawMaterialCategory = RawMaterialCategory::select('id', 'material_category_name')->get();
        $unit = Unit::select('id', 'name')->get();
        return view('rawmaterialmaster::create', compact('rawMaterialCategory', 'unit'));
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_name' => [
                'required',
                Rule::unique('raw_material_masters')->where(function ($query) use ($request) {
                    return $query->where('deleted_at', '=', null);
                })
            ],
        ], [
            'material_name.required' => __('rawmaterialmaster::message.enter_material_name'),
            'material_name.unique' => __('rawmaterialmaster::message.enter_unique_material_name'),

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $rawMaterialMaster = new RawMaterialMaster();
            $rawMaterialMaster->material_name = ucwords($request->material_name);
            $rawMaterialMaster->material_category_id = $request->material_category_id;
            $rawMaterialMaster->material_code = $request->material_code;
            $rawMaterialMaster->unit_id = $request->unit_id;
            $rawMaterialMaster->alert_quantity = $request->alert_quantity;
            $rawMaterialMaster->tax = $request->tax;
            $result = $rawMaterialMaster->save();
            DB::commit();

            if ($result) {
                DB::commit();
                return response()->json(['status_code' => 200,  'data' => route('rawmaterialmaster.index'), 'message' => 'Raw Material added successfully.']);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Raw Material added failed.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }


    public function show($id)
    {
        return view('rawmaterialmaster::show');
    }

    public function edit($id)
    {
        $unit = Unit::get();
        $rawMaterialCategory = RawMaterialCategory::get();
        $rawMaterialMaster = RawMaterialMaster::where('id', $id)->first();
        return view('rawmaterialmaster::edit', compact('rawMaterialMaster', 'unit', 'rawMaterialCategory'));
    }
    public function update(Request $request, $id)
    {
        $rawMaterialMaster = RawMaterialMaster::where('id', $request->id)->first();
        $validator = Validator::make($request->all(), [
            'material_name' => [
                'required',
                Rule::unique('raw_material_masters')->where(function ($query) use ($request) {
                    return $query->where('deleted_at', '=', null);
                })->ignore($rawMaterialMaster->id),
            ]
        ], [
            'material_name.required' => __('rawmaterialmaster::message.enter_material_name'),
            'material_name.unique' => __('rawmaterialmaster::message.enter_unique_material_name'),

        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $rawMaterialMaster = RawMaterialMaster::where('id', $request->id)->first();
            $rawMaterialMaster->material_name = ucwords($request->material_name);
            $rawMaterialMaster->material_category_id = $request->material_category_id;
            $rawMaterialMaster->material_code = $request->material_code;
            $rawMaterialMaster->unit_id = $request->unit_id;
            $rawMaterialMaster->alert_quantity = $request->alert_quantity;
            $rawMaterialMaster->tax = $request->tax;
            $result = $rawMaterialMaster->save();
            if ($result) {
                DB::commit();
                return response()->json(['status_code' => 200,  'data' => route('rawmaterialmaster.index'), 'message' => 'Raw Material updated successfully.']);

            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Raw Material updated failed.']);

            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

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
