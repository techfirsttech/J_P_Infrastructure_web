<?php

namespace Modules\Supplier\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Supplier\Models\Supplier;

class SupplierApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $supplier = Supplier::select('id', 'supplier_name','supplier_code')->get();
            return response(['status' => true, 'message' => 'Supplier Dropdown', 'supplier_dropdown' => $supplier], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
        // return view('supplier::index');
    }


    public function create()
    {
        return view('supplier::create');
    }

      public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_name' => ['required', Rule::unique('suppliers')->where(function ($query) use ($request) {
                if (!is_null($request->id)) {
                    return $query->where([['deleted_at', null], ['id', '!=', $request->id]]);
                } else {
                    return $query->where([['deleted_at', null]]);
                }
            })],
        ], [
            'supplier_name.required' => __('supplier::message.enter_supplier_name'),
            'supplier_name.unique' => __('supplier::message.enter_unique_supplier_name')
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {

            $yearID = getSelectedYear();
            $supplier = new Supplier();
            $supplier->supplier_name = $request->supplier_name;
            // $contractor->mobile = $request->mobile;
            $supplier->year_id = $yearID;
            $result = $supplier->save();
            DB::commit();
            if ($result) {
                DB::commit();
                return response()->json(['status' => true, 'message' => 'Supplier created successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Supplier can not create.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function show($id)
    {
        return view('supplier::show');
    }


    public function edit($id)
    {
        return view('supplier::edit');
    }

    public function update(Request $request, $id) {}


    public function destroy($id) {}
}
