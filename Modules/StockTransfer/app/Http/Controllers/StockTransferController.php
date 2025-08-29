<?php

namespace Modules\StockTransfer\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\RawMaterialMaster\Models\RawMaterialStock;
use Modules\SiteMaster\Models\SiteMaster;
use Modules\StockTransfer\Models\StockTransfer;
use Modules\User\Models\User;
use Yajra\DataTables\Facades\DataTables;

class StockTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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

        // if ($request->filled('start_date') && $request->filled('end_date')) {
        //     $start = Carbon::parse($request->start_date)->startOfDay();
        //     $end = Carbon::parse($request->end_date)->endOfDay();
        //     $query->whereBetween('stock_transfers.created_at', [$start, $end]);
        // } elseif ($request->filled('start_date')) {
        //     $start = Carbon::parse($request->start_date)->startOfDay();
        //     $query->where('stock_transfers.created_at', '>=', $start);
        // } elseif ($request->filled('end_date')) {
        //     $end = Carbon::parse($request->end_date)->endOfDay();
        //     $query->where('stock_transfers.created_at', '<=', $end);
        // }

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
            $sites = SiteMaster::get();
            return view('stocktransfer::index', compact('sites'));
        }
    }
    public function stockList(Request $request)
    {
        $query = RawMaterialStock::select(
            'raw_material_stocks.id',
            'raw_material_stocks.material_id',
            'raw_material_stocks.site_id',
            'raw_material_stocks.supervisor_id',
            'raw_material_stocks.supplier_id',
            'raw_material_stocks.price',
            'raw_material_stocks.quantity',
            'raw_material_stocks.unit_id',
            'raw_material_stocks.created_by',
            'raw_material_masters.material_name',
            'site_masters.site_name',
            'users.name as supervisor_name',
            'suppliers.supplier_name',
            'units.name as unit_name',
        )
            ->leftJoin('raw_material_masters', 'raw_material_stocks.material_id', '=', 'raw_material_masters.id')
            ->leftJoin('site_masters', 'site_masters.id', '=', 'raw_material_stocks.site_id')
            ->leftJoin('users', 'users.id', '=', 'raw_material_stocks.supervisor_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'raw_material_stocks.supplier_id')
            ->leftJoin('units', 'units.id', '=', 'raw_material_stocks.unit_id')
            ->orderBy('raw_material_stocks.id', 'DESC');
        $user = Auth::user();
        $role = $user->roles->first();

        if ($role && $role->name === 'Supervisor') {
            $query->where('raw_material_stocks.created_by', $user->id);
        }

        if ($request->filled('site_id')) {
            $query->where('raw_material_stocks.site_id', $request->site_id);
        }

        if ($request->filled('supervisor_id')) {
            $query->where('raw_material_stocks.supervisor_id', $request->supervisor_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('raw_material_stocks.created_at', [$start, $end]);
        } elseif ($request->filled('start_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $query->where('raw_material_stocks.created_at', '>=', $start);
        } elseif ($request->filled('end_date')) {
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->where('raw_material_stocks.created_at', '<=', $end);
        }

        $data = $query->orderBy('raw_material_stocks.id', 'DESC');

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
            $sites = SiteMaster::get();
            $supervisor = User::get();
            return view('stocktransfer::stock', compact('sites','supervisor'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('stocktransfer::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('stocktransfer::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('stocktransfer::edit');
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
