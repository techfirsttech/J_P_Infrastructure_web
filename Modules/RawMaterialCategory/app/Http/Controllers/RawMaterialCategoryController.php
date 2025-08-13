<?php

namespace Modules\RawMaterialCategory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\RawMaterialCategory\Models\RawMaterialCategory;
use Yajra\DataTables\Facades\DataTables;

class RawMaterialCategoryController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:material-category-list|material-category-create', ['only' => ['index', 'store']]);
        $this->middleware('permission:material-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:material-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:material-category-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(RawMaterialCategory::select('id', 'material_category_name'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $flag = true;
                    $show = '';
                    $edit = true ? 'material-category-edit' : '';
                    $delete = $flag ? 'material-category-delete' : '';
                    $showURL = "";
                    $editURL = "";
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('rawmaterialcategory::index');
        }
        // return view('rawmaterialcategory::index');
    }

    public function create()
    {
        return view('rawmaterialcategory::create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_category_name' => ['required', Rule::unique('raw_material_categories')->where(function ($query) use ($request) {
                if (!is_null($request->id)) {
                    return $query->where([['deleted_at', null], ['id', '!=', $request->id]]);
                } else {
                    return $query->where([['deleted_at', null]]);
                }
            })],
        ], [
            'material_category_name.required' => __('rawmaterialcategory::message.enter_material_category_name'),
            'material_category_name.unique' => __('rawmaterialcategory::message.enter_unique_material_category_name')
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->id)) {
                $rawMaterialCategory = RawMaterialCategory::where('id', $request->id)->first();
                $msg = ' updated ';
            } else {
                $rawMaterialCategory = new RawMaterialCategory();
                $msg = ' added ';
            }
            $rawMaterialCategory->material_category_name = $request->material_category_name;
            $result = $rawMaterialCategory->save();
            if (!is_null($result)) {
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'Material Category' . $msg . 'successfully.']);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Material Category' . $msg . 'failed.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function show($id)
    {
        return view('rawmaterialcategory::show');
    }

    public function edit($id)
    {
        try {
            $rawMaterialCategory = RawMaterialCategory::where('id', $id)->first();
            if (!is_null($rawMaterialCategory)) {
                return response()->json(['status_code' => 200, 'message' => 'Edit material category ', 'result' => $rawMaterialCategory]);
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Material category not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
    public function update(Request $request, $id) {}

    public function destroy($id)
    {
        try {
            $rawMaterialCategory = RawMaterialCategory::select('id')->where('id', $id)->first();
            if (!is_null($rawMaterialCategory)) {
                //  if (storage_delete_check($rawMaterialCategory->id)) {
                $rawMaterialCategory->delete();
                return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                // } else {
                //     return response()->json(['status_code' => 201, 'message' => 'This Expense Category already use in another module.']);
                // }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Material category not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
