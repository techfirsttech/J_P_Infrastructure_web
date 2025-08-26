<?php

namespace Modules\Supplier\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Country\Models\Country;
use Modules\Supplier\Models\Supplier;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:supplier-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:supplier-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:supplier-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:supplier-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(Supplier::select('id', 'supplier_name', 'supplier_code', 'mobile', 'contact_number', 'gst_number'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    // $flag = supplier_delete_check($row->id);
                    $show = '';
                    $edit =  'supplier-edit';
                    $delete = 'supplier-delete';
                    $showURL = "";
                    $editURL = route('supplier.edit', $row->id);
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
                })
                ->editColumn('supplier_name', function ($row) {
                    return '<a href="javascript:void(0);" class="view" data-id="' . $row->id . '">' . $row->supplier_name . ' (' . $row->supplier_code . ')</a>';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            $country = Country::select('id', 'name')->get();
            return view('supplier::index', compact('country'));
        }
    }

    public function create()
    {
        $country = Country::select('id', 'name', 'code')->get();
        return view('supplier::create', compact('country'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_name' => ['required',  Rule::unique('suppliers')->where(function ($query) {
                return $query->where('deleted_at', null);
            })]
        ], [
            'supplier_name.required' => __('supplier::message.enter_supplier_name'),
            'supplier_name.unique' => __('supplier::message.enter_unique_supplier_name'),
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            $yearID = getSelectedYear();

            $data = [
                'supplier_code' => $request->supplier_code,
                'supplier_name' => ucwords($request->supplier_name),
                'mobile' => $request->mobile,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
                'gst_number' => $request->gst_number,
                'gst_apply' => !empty($request->gst_apply) ? 1 : 0,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_number' => $request->contact_person_number,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'address_line_3' => $request->address_line_3,
                'term_condition' => $request->term_condition,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'year_id' => $yearID,

            ];
            $result = Supplier::create($data);
            if (!is_null($result)) {
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'Supplier added successfully.', 'data' => route('supplier.index')]);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Supplier added failed.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function show($id)
    {
        try {
            $query = Supplier::with('country', 'state', 'city')->select('id', 'supplier_code', 'supplier_name', 'mobile', 'contact_number', 'email', 'gst_number', 'gst_apply', 'contact_person_name', 'contact_person_number', 'address_line_1', 'address_line_2', 'address_line_3', 'term_condition', 'country_id', 'state_id', 'city_id')->where('id', $id)->first();
            if (!is_null($query)) {
                $data['html'] = view('supplier::model', compact('query'))->render();
                return response()->json($data);
            } else {
                return response()->json(['status_code' => 403, 'message' => 'Supplier not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function edit($id)
    {
        try {
            $supplier = Supplier::select('id', 'supplier_code', 'supplier_name', 'mobile', 'contact_number', 'email', 'gst_number', 'gst_apply', 'contact_person_name', 'contact_person_number', 'address_line_1', 'address_line_2', 'address_line_3', 'term_condition', 'country_id', 'state_id', 'city_id')->where('id', $id)->first();
            if (!is_null($supplier)) {
                // if (supplier_delete_check($supplier->id)) {
                $country = Country::select('id', 'name', 'code')->get();
                return view('supplier::edit', compact('country', 'supplier'));
                // } else {
                //     return redirect()->back()->with('warning', 'This supplier already use in another module.');
                // }
            } else {
                return redirect()->back()->with('warning', 'Supplier not found.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'supplier_name' => ['required', Rule::unique('suppliers')->where(function ($query) use ($id) {
                return $query->where([['deleted_at', null], ['id', '!=', $id]]);
            })],

        ], [
            'supplier_name.required' => __('supplier::message.enter_supplier_name'),
            'supplier_name.unique' => __('supplier::message.enter_unique_supplier_name'),
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            $supplier = Supplier::where('id', $id)->first();
            $data = [
                'supplier_code' => $request->supplier_code,
                'supplier_name' => ucwords($request->supplier_name),
                'mobile' => $request->mobile,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
                'gst_number' => $request->gst_number,
                'gst_apply' => !empty($request->gst_apply) ? 1 : 0,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_number' => $request->contact_person_number,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'address_line_3' => $request->address_line_3,
                'term_condition' => $request->term_condition,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
            ];
            $result = Supplier::findOrFail($supplier->id)->update($data);
            if (!is_null($result)) {
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'Supplier updated successfully.', 'data' => route('supplier.index')]);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Supplier updated failed.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function destroy($id)
    {
        try {
            $supplier = Supplier::select('id')->where('id', $id)->first();
            if (!is_null($supplier)) {
                // if (supplier_delete_check($supplier->id)) {
                //     $supplier->delete();
                //     return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                // } else {
                //     return response()->json(['status_code' => 201, 'message' => 'This supplier already use in another module.']);
                // }
                $supplier->delete();
                return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Supplier not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
