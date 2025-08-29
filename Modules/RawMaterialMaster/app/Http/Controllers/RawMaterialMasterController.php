<?php

namespace Modules\RawMaterialMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\RawMaterialCategory\Models\RawMaterialCategory;
use Modules\RawMaterialMaster\Models\RawMaterialMaster;
use Modules\RawMaterialMaster\Models\RawMaterialStock;
use Modules\RawMaterialMaster\Models\RawMaterialStockTransaction;
use Modules\SiteMaster\Models\SiteMaster;
use Modules\StockTransfer\Models\StockTransfer;
use Modules\Unit\Models\Unit;
use Modules\User\Models\User;
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

    public function destroy(Request $request)
    {
        try {
            $rawMaterialMaster = RawMaterialMaster::where('id', $request->id)->first();
            $rawMaterialMaster->delete();
            return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function materialTransaction(Request $request)
    {
        $query = RawMaterialStockTransaction::select(
            'raw_material_stock_transactions.id',
            'raw_material_stock_transactions.material_id',
            'raw_material_stock_transactions.material_stock_id',
            'raw_material_stock_transactions.site_id',
            'raw_material_stock_transactions.supervisor_id',
            'raw_material_stock_transactions.supplier_id',
            'raw_material_stock_transactions.quantity',
            'raw_material_stock_transactions.unit_id',
            'raw_material_stock_transactions.price',
            'raw_material_stock_transactions.description',
            'raw_material_stock_transactions.type',
            'raw_material_stock_transactions.remark',
            'raw_material_stock_transactions.created_by',
            'raw_material_masters.material_name',
            'site_masters.site_name',
            'users.name as supervisor_name',
            'suppliers.supplier_name',
            'units.name as unit_name'
        )
            ->leftJoin('raw_material_masters', 'raw_material_stock_transactions.material_id', '=', 'raw_material_masters.id')
            ->leftJoin('site_masters', 'site_masters.id', '=', 'raw_material_stock_transactions.site_id')
            ->leftJoin('users', 'users.id', '=', 'raw_material_stock_transactions.supervisor_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'raw_material_stock_transactions.supplier_id')
            ->leftJoin('units', 'units.id', '=', 'raw_material_stock_transactions.unit_id');

        $user = Auth::user();
        $role = $user->roles->first();

        if ($role && $role->name === 'Supervisor') {
            $query->where('raw_material_stock_transactions.created_by', $user->id);
        }

        if ($request->filled('material_id')) {
            $query->where('raw_material_stock_transactions.material_id', $request->material_id);
        }

        if ($request->filled('site_id')) {
            $query->where('raw_material_stock_transactions.site_id', $request->site_id);
        }

        if ($request->filled('supervisor_id')) {
            $query->where('raw_material_stock_transactions.supervisor_id', $request->supervisor_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('raw_material_stock_transactions.supplier_id', $request->supplier_id);
        }

        if ($request->filled('type')) {
            $query->where('raw_material_stock_transactions.type', $request->type);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('raw_material_stock_transactions.created_at', [$start, $end]);
        } elseif ($request->filled('start_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $query->where('raw_material_stock_transactions.created_at', '>=', $start);
        } elseif ($request->filled('end_date')) {
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->where('raw_material_stock_transactions.created_at', '<=', $end);
        }

        $data = $query->orderBy('raw_material_stock_transactions.id', 'DESC');


        if (request()->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $show = '';
                    $edit = '';
                    $delete = '';
                    $assign = ''; //'assign-user-list';
                    $showURL = "";
                    $editURL = "";
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL', 'assign'));
                })
                ->editColumn('type', function ($row) {
                    if ($row->type == 'In') {
                        return '<span class="badge bg-label-success ">In</span>';
                    } elseif ($row->type == 'Out') {
                        return '<span class="badge bg-label-danger ">Out</span>';
                    }
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            $materials = RawMaterialMaster::select('id', 'material_name')->orderBy('material_name','asc')->get();
            $sites = SiteMaster::select('id', 'site_name')->orderBy('site_name','asc')->get();
            $supervisors = User::role('Supervisor')->select('id', 'name')->orderBy('name','asc')->get();

            return view('rawmaterialmaster::transaction', compact('materials', 'sites', 'supervisors'));
            // return view('rawmaterialmaster::transaction');
        }
    }


    public function stockTransferList(Request $request)
    {
        $query = StockTransfer::select(
            'stock_transfers.id',
            'stock_transfers.material_id',
            'stock_transfers.material_stock_id',
            'stock_transfers.from_site_id',
            'stock_transfers.supervisor_id',
            'stock_transfers.to_site_id',
            'stock_transfers.quantity',
            'stock_transfers.unit_id',
            'stock_transfers.remark',

            'raw_material_masters.material_name',
            'from.site_name as from_site_name',
            'to.site_name as to_site_name',
            'users.name as supervisor_name',
            'units.name as unit_name'
        )
            ->leftJoin('raw_material_masters', 'stock_transfers.material_id', '=', 'raw_material_masters.id')
            ->leftJoin('site_masters as from', 'from.id', '=', 'stock_transfers.from_site_id')
            ->leftJoin('site_masters as to', 'to.id', '=', 'stock_transfers.to_site_id')
            ->leftJoin('users', 'users.id', '=', 'stock_transfers.supervisor_id')
            ->leftJoin('units', 'units.id', '=', 'stock_transfers.unit_id');

        $user = Auth::user();
        $role = $user->roles->first();

        if ($role && $role->name === 'Supervisor') {
            $query->where('stock_transfers.created_by', $user->id);
        }

        if ($request->filled('from_site_id')) {
            $query->where('stock_transfers.from_site_id', $request->from_site_id);
        }

        if ($request->filled('to_site_id')) {
            $query->where('stock_transfers.to_site_id', $request->to_site_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('stock_transfers.created_at', [$start, $end]);
        } elseif ($request->filled('start_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $query->where('stock_transfers.created_at', '>=', $start);
        } elseif ($request->filled('end_date')) {
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->where('stock_transfers.created_at', '<=', $end);
        }

        $data = $query->orderBy('stock_transfers.id', 'DESC');

        if (request()->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $show = '';
                    $edit = '';
                    $delete = '';
                    $assign = '';;
                    $showURL = "";
                    $editURL = "";
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL', 'assign'));
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('rawmaterialmaster::stock-transfer-list');
        }
    }
}
