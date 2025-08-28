<?php

namespace Modules\StockTransfer\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\RawMaterialMaster\Models\RawMaterialStock;
use Modules\RawMaterialMaster\Models\RawMaterialStockTransaction;
use Modules\StockTransfer\Models\StockTransfer;

class StockTransferApiController extends Controller
{

    public function index(Request $request)
    {
        try {
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

            $data = $query->orderBy('stock_transfers.id', 'DESC')->simplePaginate(30);

            // Format quantity and price to remove decimals
            $formatted = collect($data->items())->map(function ($item) {
                $item['quantity'] = intval($item['quantity']);
                return $item;
            });

            return response([
                'status' => true,
                'message' => 'Raw Material Stock Transaction List',
                'stock_transfer' => $formatted
            ], 200);
        } catch (\Exception $e) {
            dd($e);
            return response([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                // 'error' => $e->getMessage(), // Remove in production
            ], 200);
        }
    }


    public function create()
    {
        return view('stocktransfer::create');
    }


    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'from_site_id' => 'required|integer|exists:site_masters,id',
    //         'to_site_id' => 'required|integer|exists:site_masters,id',
    //         'type' => 'required|in:In,Out',
    //         'material' => 'required|array|min:1',
    //         'material.*.material_id' => 'required|integer',
    //         'material.*.quantity' => 'required|numeric|min:1',
    //         'material.*.unit_id' => 'required|integer|exists:units,id',
    //         'material.*.price' => 'nullable|numeric',
    //         'material.*.description' => 'nullable|string'
    //     ], [
    //         'material.*.material_id.required' => 'Material ID is required.',
    //         'material.*.quantity.required' => 'Quantity is required.',
    //         'material.*.type.required' => 'Type (In/Out) is required.',
    //         'material.*.unit_id.required' => 'Unit ID is required.',
    //         'material.*.price.numeric' => 'Price must be a valid number.',
    //         'material.*.description.string' => 'Description must be a valid string.'
    //     ]);


    //     if ($validator->fails()) {
    //         $error = $validator->errors();
    //         $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
    //         return response()->json($response, 422);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $yearID = getSelectedYear();

    //         foreach ($request->material as $material) {
    //             $rawMaterialStock = RawMaterialStock::where([
    //                 ['site_id', $request->site_id],
    //                 ['supervisor_id', $request->supervisor_id],
    //                 ['material_id', $material['material_id']]
    //             ])->first();

    //             if ($rawMaterialStock) {
    //                 if ($request->type === 'In') {
    //                     $rawMaterialStock->quantity += $material['quantity'];
    //                 } elseif ($request->type === 'Out') {
    //                     if ($rawMaterialStock->quantity < $material['quantity']) {
    //                         DB::rollBack();
    //                         return response()->json(['status' => false, 'message' => 'Not enough stock for material'], 200);
    //                     }
    //                     $rawMaterialStock->quantity -= $material['quantity'];
    //                 }
    //             } else {
    //                 if ($request->type === 'Out') {
    //                     DB::rollBack();
    //                     return response()->json(['status' => false, 'message' => 'Stock not available for material '], 200);
    //                 }

    //                 $rawMaterialStock = new RawMaterialStock();
    //                 $rawMaterialStock->quantity = $material['quantity'];
    //                 $rawMaterialStock->site_id = $request->site_id;
    //                 $rawMaterialStock->supervisor_id = $request->supervisor_id;
    //                 $rawMaterialStock->supplier_id = $request->supplier_id ?? null;
    //                 $rawMaterialStock->material_id = $material['material_id'];
    //                 $rawMaterialStock->unit_id = $material['unit_id'];
    //                 $rawMaterialStock->year_id = $yearID;

    //                 $rawMaterialStock->price = $material['price'];
    //             }

    //             $rawMaterialStock->save();

    //             $rawMaterialStockTransaction = new RawMaterialStockTransaction();
    //             $rawMaterialStockTransaction->material_id = $rawMaterialStock->material_id;
    //             $rawMaterialStockTransaction->material_stock_id = $rawMaterialStock->id;
    //             $rawMaterialStockTransaction->site_id = $rawMaterialStock->site_id;
    //             $rawMaterialStockTransaction->supervisor_id = $rawMaterialStock->supervisor_id;
    //             $rawMaterialStockTransaction->supplier_id = $request->supplier_id ?? null;
    //             $rawMaterialStockTransaction->quantity = $material['quantity'];
    //             $rawMaterialStockTransaction->unit_id = $rawMaterialStock->unit_id;
    //             $rawMaterialStockTransaction->type = $request->type;
    //             $rawMaterialStockTransaction->remark = $request->remark;

    //             $rawMaterialStockTransaction->price = $material['price'];
    //             $rawMaterialStockTransaction->description = $material['description'];

    //             $rawMaterialStockTransaction->save();
    //         }

    //         DB::commit();
    //         return response()->json(['status' => true, 'message' => 'Raw Material Stock updated successfully.'], 200);
    //     } catch (\Exception $e) {

    //         DB::rollBack();
    //         return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 500);
    //     }
    // }

    public function store(Request $request)
    {
        // dd( $request->from_site_id);
        $validator = Validator::make($request->all(), [
            'from_site_id' => 'required|integer|exists:site_masters,id',
            'to_site_id' => 'required|integer|exists:site_masters,id|different:from_site_id',
            'supervisor_id' => 'required|integer|exists:users,id',
            'material' => 'required|array|min:1',
            'material.*.material_id' => 'required|integer',
            'material.*.quantity' => 'required|numeric|min:1',
            'material.*.unit_id' => 'required|integer|exists:units,id',
            'material.*.description' => 'nullable|string'
        ], [
            'to_site_id.different' => 'From site and To site cannot be the same.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $yearID = getSelectedYear();

            foreach ($request->material as $material) {

                // 🔹 From Site Stock Check
                $fromStock = RawMaterialStock::where([
                    ['site_id', $request->from_site_id],
                    ['material_id', $material['material_id']],
                    ['unit_id', $material['unit_id']]
                ])->first();

                if (!$fromStock || $fromStock->quantity < $material['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Not enough stock at From Site for material ID: ' . $material['material_id']
                    ], 400);
                }

                // 🔹 Deduct From Site
                $fromStock->quantity -= $material['quantity'];
                $fromStock->save();

                // 🔹 Add To Site Stock (create if not exist)
                $toStock = RawMaterialStock::firstOrNew([
                    'site_id' => $request->to_site_id,
                    'material_id' => $material['material_id'],
                    'unit_id' => $material['unit_id'],
                    'year_id' => $yearID
                ]);
                $toStock->quantity = ($toStock->quantity ?? 0) + $material['quantity'];
                $toStock->supervisor_id = $request->supervisor_id;
                $toStock->save();

                // 🔹 Log Transfer-Out Transaction
                RawMaterialStockTransaction::create([
                    'material_id' => $material['material_id'],
                    'material_stock_id' => $fromStock->id,
                    'site_id' => $request->from_site_id,
                    'supervisor_id' => $request->supervisor_id,
                    'quantity' => $material['quantity'],
                    'unit_id' => $material['unit_id'],
                    'type' => 'Out',
                    'remark' => $request->remark ?? null,
                    'description' => $material['description'] ?? null
                ]);

                // 🔹 Log Transfer-In Transaction
                RawMaterialStockTransaction::create([
                    'material_id' => $material['material_id'],
                    'material_stock_id' => $toStock->id,
                    'site_id' => $request->to_site_id,
                    'supervisor_id' => $request->supervisor_id,
                    'quantity' => $material['quantity'],
                    'unit_id' => $material['unit_id'],
                    'type' => 'In',
                    'remark' => $request->remark ?? null,
                    'description' => $material['description'] ?? null
                ]);

                StockTransfer::create([
                    'material_id' => $material['material_id'],
                    'material_stock_id' => $toStock->id,
                    'from_site_id' => $request->from_site_id,
                    'supervisor_id' => $request->supervisor_id,
                    'to_site_id' => $request->to_site_id,
                    'quantity' => $material['quantity'],
                    'unit_id' => $material['unit_id'],
                    'remark' => $material['description'] ?? null
                ]);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Stock transferred successfully.'], 200);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function show($id)
    {
        return view('stocktransfer::show');
    }

    public function edit($id)
    {
        return view('stocktransfer::edit');
    }


    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
