<?php

namespace Modules\RawMaterialCategory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Modules\RawMaterialCategory\Models\RawMaterialCategory;

class RawMaterialCategoryApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('rawmaterialcategory::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('rawmaterialcategory::create');
    }

    /**
     * Store a newly created resource in storage.
     */
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
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {
            // if (!is_null($request->id)) {
            //     $rawMaterialCategory = RawMaterialCategory::where('id', $request->id)->first();
            //     $msg = ' updated ';
            // } else {
                $rawMaterialCategory = new RawMaterialCategory();
            //     $msg = ' added ';
            // }
            $rawMaterialCategory->material_category_name = $request->material_category_name;
            $result = $rawMaterialCategory->save();
            DB::commit();
            if ($result) {
                DB::commit();
                return response()->json(['status' => true, 'message' => 'Material Category created successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Material Category can not create.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('rawmaterialcategory::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('rawmaterialcategory::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
