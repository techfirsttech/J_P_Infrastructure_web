<?php

namespace Modules\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Category\Models\Category;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:category-list|category-create|category-edit|category-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        if (request()->ajax()) {
            $query = Category::select('categories.*', 'parent.name as parent_name')
                ->leftJoin('categories as parent', 'categories.parent_id', '=', 'parent.id')
                ->where('categories.type', 'product');
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $show = '';
                    $edit = 'category-edit';
                    $delete = 'category-delete';
                    $showURL = "";
                    $editURL = "";
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            $type = '';
            return view('category::index', compact('type'));
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => ['required', Rule::unique('categories','name')->where(function ($query) use ($request) {
                if (!is_null($request->id)) {
                    return $query->where([['type', 'product'], ['deleted_at', null], ['id', '!=', $request->id]]);
                } else {
                    return $query->where([['type', 'product'], ['deleted_at', null]]);
                }
            })],
        ], [
            'category_name.required' => __('category::message.enter_name'),
            'category_name.unique' => __('category::message.enter_unique_name')
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->id)) {
                $category = Category::where('id', $request->id)->first();
                $msg = ' updated ';
            } else {
                $category = new Category();
                $msg = ' added ';
            }

            $category->type = 'product';
            $category->name = $request->category_name;
            $category->parent_id = empty($request->is_parent) ? $request->parent_id : null;
            $category->is_parent = !empty($request->is_parent) ? 1 : 0;
            $result = $category->save();

            if (!is_null($result)) {
                DB::commit();
                $dropdown = [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
                return response()->json(['status_code' => 200, 'message' => 'Category' . $msg . 'successfully.', 'dropdown' => $dropdown]);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Category' . $msg . 'failed.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function show(Category $category)
    {
        //
    }

    public function edit($id)
    {
        try {
            $category = Category::where('id', $id)->first();
            if (!is_null($category)) {
                // if (pro_category_delete_check($category->id)) {
                    return response()->json(['status_code' => 200, 'message' => 'Edit category', 'result' => $category]);
                // } else {
                //     return response()->json(['status_code' => 201, 'message' => 'This category already use in another module.']);
                // }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Category not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function update(Request $request, Category $category)
    {
        //
    }

    public function destroy($id)
    {
        try {
            // if (pro_category_delete_check($id)) {
                $parent = Category::where('parent_id', $id)->first();
                if (is_null($parent)) {
                    $category = Category::findOrFail($id);
                    $category->delete();
                    return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                } else {
                    return response()->json(['status_code' => 201, 'message' => 'You can`t delete this category because it is already in use.']);
                }
            // } else {
            //     return response()->json(['status_code' => 201, 'message' => 'This Category existing in another module.']);
            // }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function byParentId($id)
    {
        $category = Category::select('id', 'name')->where('parent_id', $id)->get();
        $response = ['status_code' => 200, 'result' => $category];
        return response()->json($response);
    }
}
