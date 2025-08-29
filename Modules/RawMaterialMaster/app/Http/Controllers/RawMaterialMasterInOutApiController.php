<?php

namespace Modules\RawMaterialMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\RawMaterialMaster\Models\RawMaterialMaster;
use Modules\RawMaterialMaster\Models\RawMaterialStock;
use Modules\RawMaterialMaster\Models\RawMaterialStockTransaction;
use Modules\Unit\Models\Unit;

class RawMaterialMasterInOutApiController extends Controller
{

    // public function index()
    // {
    //     try {
    //         $rawMaterialStockTransaction = RawMaterialStockTransaction::select(
    //             'raw_material_stock_transactions.id',
    //             'raw_material_stock_transactions.material_id',
    //             'raw_material_stock_transactions.material_stock_id',
    //             'raw_material_stock_transactions.site_id',
    //             'raw_material_stock_transactions.supervisor_id',
    //             'raw_material_stock_transactions.supplier_id',
    //             'raw_material_stock_transactions.quantity',
    //             'raw_material_stock_transactions.unit_id',
    //             'raw_material_stock_transactions.price',
    //             'raw_material_stock_transactions.description',
    //             'raw_material_stock_transactions.type',
    //             'raw_material_stock_transactions.remark',
    //             'raw_material_stock_transactions.created_by',
    //             'raw_material_masters.material_name',
    //             'site_masters.site_name',
    //             'users.name as supervisor_name',
    //             'suppliers.supplier_name',
    //             'units.name as unit_name',
    //         )
    //             ->leftJoin('raw_material_masters', 'raw_material_stock_transactions.material_id', '=', 'raw_material_masters.id')
    //             ->leftJoin('site_masters', 'site_masters.id', '=', 'raw_material_stock_transactions.site_id')
    //             ->leftJoin('users', 'users.id', '=', 'raw_material_stock_transactions.supervisor_id')
    //             ->leftJoin('suppliers', 'suppliers.id', '=', 'raw_material_stock_transactions.supplier_id')
    //             ->leftJoin('units', 'units.id', '=', 'raw_material_stock_transactions.unit_id')
    //             ->orderBy('raw_material_stock_transactions.id', 'DESC')
    //             ->simplePaginate(12);


    //         return response(['status' => true, 'message' => 'Raw Material Stock Transaction List', 'material_in_out' => $rawMaterialStockTransaction->items()], 200);
    //     } catch (Exception $e) {
    //         return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
    //     }
    // }

    public function index(Request $request)
    {
        // try {
        //     $query = RawMaterialStockTransaction::select(
        //         'raw_material_stock_transactions.id',
        //         'raw_material_stock_transactions.material_id',
        //         'raw_material_stock_transactions.material_stock_id',
        //         'raw_material_stock_transactions.site_id',
        //         'raw_material_stock_transactions.supervisor_id',
        //         'raw_material_stock_transactions.supplier_id',
        //         'raw_material_stock_transactions.quantity',
        //         'raw_material_stock_transactions.unit_id',
        //         'raw_material_stock_transactions.price',
        //         'raw_material_stock_transactions.description',
        //         'raw_material_stock_transactions.type',
        //         'raw_material_stock_transactions.remark',
        //         'raw_material_stock_transactions.created_by',
        //         'raw_material_masters.material_name',
        //         'site_masters.site_name',
        //         'users.name as supervisor_name',
        //         'suppliers.supplier_name',
        //         'units.name as unit_name'
        //     )
        //         ->leftJoin('raw_material_masters', 'raw_material_stock_transactions.material_id', '=', 'raw_material_masters.id')
        //         ->leftJoin('site_masters', 'site_masters.id', '=', 'raw_material_stock_transactions.site_id')
        //         ->leftJoin('users', 'users.id', '=', 'raw_material_stock_transactions.supervisor_id')
        //         ->leftJoin('suppliers', 'suppliers.id', '=', 'raw_material_stock_transactions.supplier_id')
        //         ->leftJoin('units', 'units.id', '=', 'raw_material_stock_transactions.unit_id');

        //     $user = Auth::user();
        //     $role = $user->roles->first();

        //     if ($role && $role->name === 'Supervisor') {
        //         $query->where('raw_material_stock_transactions.created_by', $user->id);
        //     }

        //     if ($request->filled('material_id')) {
        //         $query->where('raw_material_stock_transactions.material_id', $request->material_id);
        //     }

        //     if ($request->filled('site_id')) {
        //         $query->where('raw_material_stock_transactions.site_id', $request->site_id);
        //     }

        //     if ($request->filled('supervisor_id')) {
        //         $query->where('raw_material_stock_transactions.supervisor_id', $request->supervisor_id);
        //     }

        //     if ($request->filled('supplier_id')) {
        //         $query->where('raw_material_stock_transactions.supplier_id', $request->supplier_id);
        //     }

        //     if ($request->filled('type')) {
        //         $query->where('raw_material_stock_transactions.type', $request->type);
        //     }

        //     if ($request->filled('start_date') && $request->filled('end_date')) {
        //         $start = Carbon::parse($request->start_date)->startOfDay();
        //         $end = Carbon::parse($request->end_date)->endOfDay();
        //         $query->whereBetween('raw_material_stock_transactions.created_at', [$start, $end]);
        //     } elseif ($request->filled('start_date')) {
        //         $start = Carbon::parse($request->start_date)->startOfDay();
        //         $query->where('raw_material_stock_transactions.created_at', '>=', $start);
        //     } elseif ($request->filled('end_date')) {
        //         $end = Carbon::parse($request->end_date)->endOfDay();
        //         $query->where('raw_material_stock_transactions.created_at', '<=', $end);
        //     }

        //     $data = $query->orderBy('raw_material_stock_transactions.id', 'DESC')->simplePaginate(12);

        //     return response([
        //         'status' => true,
        //         'message' => 'Raw Material Stock Transaction List',
        //         'material_in_out' => $data->items()
        //     ], 200);
        // } catch (\Exception $e) {
        //     return response([
        //         'status' => false,
        //         'message' => 'Something went wrong. Please try again.',
        //         'error' => $e->getMessage(), // remove in production
        //     ], 200);
        // }

        try {
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

            $data = $query->orderBy('raw_material_stock_transactions.id', 'DESC')->simplePaginate(12);

            // Format quantity and price to remove decimals
            $formatted = collect($data->items())->map(function ($item) {
                $item['quantity'] = intval($item['quantity']);
                $item['price'] = intval($item['price']);
                return $item;
            });

            return response([
                'status' => true,
                'message' => 'Raw Material Stock Transaction List',
                'material_in_out' => $formatted
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                // 'error' => $e->getMessage(), // Remove in production
            ], 200);
        }
    }


    public function create()
    {
        return view('rawmaterialmaster::create');
    }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'site_id' => 'required|integer|exists:site_masters,id',
    //         'supervisor_id' => 'required|integer|exists:users,id',

    //     ], [
    //         'site_id.required' => __('rawmaterialmaster::message.site_id_is_required.'),
    //         'site_id.integer' => __('rawmaterialmaster::message.site_id_must_be_an_integer.'),
    //         'site_id.exists' => __('rawmaterialmaster::message.the_selected_site_master_id_does_not_exist.'),

    //         'supervisor_id.required' => __('rawmaterialmaster::message.supervisor_id_is_required.'),
    //         'supervisor_id.integer' => __('rawmaterialmaster::message.supervisor_id_must_be_an_integer.'),
    //         'supervisor_id.exists' => __('rawmaterialmaster::message.the_selected_supervisor_id_does_not_exist.'),
    //     ]);

    //     if ($validator->fails()) {
    //         $error = $validator->errors();
    //         $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
    //         return response()->json($response, 422);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $yearID = getSelectedYear();
    //         $rawMaterialStock = RawMaterialStock::where([['site_id', $request->site_id], ['supervisor_id', $request->supervisor_id], ['material_id', $request->material_id]])->first();
    //         if ($rawMaterialStock) {
    //             if ($request->type === 'In') {
    //                 $rawMaterialStock->quantity += $request->quantity;
    //             } elseif ($request->type === 'Out') {
    //                 if ($rawMaterialStock->quantity < $request->quantity) {
    //                     return response()->json(['status' => false, 'message' => 'Not enough stock available.'], 422);
    //                 }
    //                 $rawMaterialStock->quantity -= $request->quantity;
    //             }
    //         } else {
    //             if ($request->type === 'Out') {
    //                 return response()->json(['status' => false, 'message' => 'Stock not available to issue.'], 422);
    //             }
    //             $rawMaterialStock = new RawMaterialStock();
    //             $rawMaterialStock->quantity = $request->quantity;
    //         }

    //         $rawMaterialStock->site_id       = $request->site_id;
    //         $rawMaterialStock->supervisor_id = $request->supervisor_id;
    //         $rawMaterialStock->supplier_id   = $request->supplier_id;
    //         $rawMaterialStock->material_id   = $request->material_id;
    //         $rawMaterialStock->unit_id       = $request->unit_id;
    //         $rawMaterialStock->year_id       = $yearID;
    //         $result = $rawMaterialStock->save();

    //         $rawMaterialStockTransaction = new RawMaterialStockTransaction();
    //         $rawMaterialStockTransaction->material_id = $rawMaterialStock->material_id;
    //         $rawMaterialStockTransaction->material_stock_id = $rawMaterialStock->id;
    //         $rawMaterialStockTransaction->site_id = $rawMaterialStock->site_id;
    //         $rawMaterialStockTransaction->supervisor_id = $rawMaterialStock->supervisor_id;
    //         $rawMaterialStockTransaction->supplier_id = $rawMaterialStock->supplier_id;
    //         $rawMaterialStockTransaction->quantity = $request->quantity;
    //         $rawMaterialStockTransaction->unit_id = $rawMaterialStock->unit_id;
    //         $rawMaterialStockTransaction->type = $request->type;
    //         $rawMaterialStockTransaction->remark = $request->remark;
    //         $rawMaterialStockTransaction->save();
    //         DB::commit();
    //         if ($result) {
    //             DB::commit();
    //             return response()->json(['status' => true, 'message' => 'Raw Material Stock created successfully.'], 200);
    //         } else {
    //             DB::rollBack();
    //             return response(['status' => false, 'message' => 'Raw Material Stock can not create.'], 200);
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
    //     }
    // }
    // ============

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer|exists:site_masters,id',
            'type' => 'required|in:In,Out',
            'material' => 'required|array|min:1',
            'material.*.material_id' => 'required|integer',
            'material.*.quantity' => 'required|numeric|min:1',
            'material.*.unit_id' => 'required|integer|exists:units,id',
            'material.*.price' => 'nullable|numeric',
            'material.*.description' => 'nullable|string'
        ], [
            'material.*.material_id.required' => 'Material ID is required.',
            'material.*.quantity.required' => 'Quantity is required.',
            'material.*.type.required' => 'Type (In/Out) is required.',
            'material.*.unit_id.required' => 'Unit ID is required.',
            'material.*.price.numeric' => 'Price must be a valid number.',
            'material.*.description.string' => 'Description must be a valid string.'
        ]);


        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {
            $yearID = getSelectedYear();

            foreach ($request->material as $material) {
                $rawMaterialStock = RawMaterialStock::where([
                    ['site_id', $request->site_id],
                    ['supervisor_id', $request->supervisor_id],
                    ['material_id', $material['material_id']]
                ])->first();

                if ($rawMaterialStock) {
                    if ($request->type === 'In') {
                        $rawMaterialStock->quantity += $material['quantity'];
                    } elseif ($request->type === 'Out') {
                        if ($rawMaterialStock->quantity < $material['quantity']) {
                            DB::rollBack();
                            return response()->json(['status' => false, 'message' => 'Not enough stock for material' ], 200);
                        }
                        $rawMaterialStock->quantity -= $material['quantity'];
                    }
                } else {
                    if ($request->type === 'Out') {
                        DB::rollBack();
                        return response()->json(['status' => false, 'message' => 'Stock not available for material ' ], 200);
                    }

                    $rawMaterialStock = new RawMaterialStock();
                    $rawMaterialStock->quantity = $material['quantity'];
                    $rawMaterialStock->site_id = $request->site_id;
                    $rawMaterialStock->supervisor_id = $request->supervisor_id;
                    $rawMaterialStock->supplier_id = $request->supplier_id ?? null;
                    $rawMaterialStock->material_id = $material['material_id'];
                    $rawMaterialStock->unit_id = $material['unit_id'];
                    $rawMaterialStock->year_id = $yearID;

                    $rawMaterialStock->price = $material['price'];
                }

                $rawMaterialStock->save();

                $rawMaterialStockTransaction = new RawMaterialStockTransaction();
                $rawMaterialStockTransaction->material_id = $rawMaterialStock->material_id;
                $rawMaterialStockTransaction->material_stock_id = $rawMaterialStock->id;
                $rawMaterialStockTransaction->site_id = $rawMaterialStock->site_id;
                $rawMaterialStockTransaction->supervisor_id = $rawMaterialStock->supervisor_id;
                $rawMaterialStockTransaction->supplier_id = $request->supplier_id ?? null;
                $rawMaterialStockTransaction->quantity = $material['quantity'];
                $rawMaterialStockTransaction->unit_id = $rawMaterialStock->unit_id;
                $rawMaterialStockTransaction->type = $request->type;
                $rawMaterialStockTransaction->remark = $request->remark;

                $rawMaterialStockTransaction->price = $material['price'];
                $rawMaterialStockTransaction->description = $material['description'];

                $rawMaterialStockTransaction->save();
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Raw Material Stock updated successfully.'], 200);
        } catch (\Exception $e) {

            DB::rollBack();
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 500);
        }
    }


    // ================
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'site_id' => 'required|integer|exists:site_masters,id',
    //         'supervisor_id' => 'required|integer|exists:users,id',
    //         'supplier_id' => 'required|integer|exists:suppliers,id',
    //         'type' => 'required|in:In,Out',
    //         'material' => 'required|array|min:1',
    //         'material.*.material_id' => 'required|integer|exists:materials,id',
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
    //             // Check if stock exists
    //             $rawMaterialStock = RawMaterialStock::where([
    //                 ['site_id', $request->site_id],
    //                 ['supervisor_id', $request->supervisor_id],
    //                 ['material_id', $material['material_id']]
    //             ])->first();

    //             if ($rawMaterialStock) {
    //                 // Handle "In" and "Out" logic
    //                 if ($request->type === 'In') {
    //                     $rawMaterialStock->quantity += $material['quantity'];
    //                 } elseif ($request->type === 'Out') {
    //                     if ($rawMaterialStock->quantity < $material['quantity']) {
    //                         DB::rollBack();
    //                         return response()->json(['status' => false, 'message' => 'Not enough stock for material: ' . $material['material_id']], 422);
    //                     }
    //                     $rawMaterialStock->quantity -= $material['quantity'];
    //                 }
    //             } else {
    //                 // If stock doesn't exist, create new entry
    //                 if ($request->type === 'Out') {
    //                     DB::rollBack();
    //                     return response()->json(['status' => false, 'message' => 'Stock not available for material: ' . $material['material_id']], 422);
    //                 }

    //                 $rawMaterialStock = new RawMaterialStock();
    //                 $rawMaterialStock->quantity = $material['quantity'];
    //                 $rawMaterialStock->site_id = $request->site_id;
    //                 $rawMaterialStock->supervisor_id = $request->supervisor_id;
    //                 $rawMaterialStock->supplier_id = $request->supplier_id;
    //                 $rawMaterialStock->material_id = $material['material_id'];
    //                 $rawMaterialStock->unit_id = $material['unit_id'];
    //                 $rawMaterialStock->year_id = $yearID;

    //                 // Add price and description to RawMaterialStock
    //                 $rawMaterialStock->price = $material['price'];
    //                 $rawMaterialStock->description = $material['description'];
    //             }

    //             // Save RawMaterialStock
    //             $rawMaterialStock->save();

    //             // Create transaction for each material
    //             $rawMaterialStockTransaction = new RawMaterialStockTransaction();
    //             $rawMaterialStockTransaction->material_id = $rawMaterialStock->material_id;
    //             $rawMaterialStockTransaction->material_stock_id = $rawMaterialStock->id;
    //             $rawMaterialStockTransaction->site_id = $rawMaterialStock->site_id;
    //             $rawMaterialStockTransaction->supervisor_id = $rawMaterialStock->supervisor_id;
    //             $rawMaterialStockTransaction->supplier_id = $rawMaterialStock->supplier_id;
    //             $rawMaterialStockTransaction->quantity = $material['quantity'];
    //             $rawMaterialStockTransaction->unit_id = $rawMaterialStock->unit_id;
    //             $rawMaterialStockTransaction->type = $request->type;
    //             $rawMaterialStockTransaction->remark = $request->remark;

    //             // Add price and description to RawMaterialStockTransaction
    //             $rawMaterialStockTransaction->price = $material['price'];
    //             $rawMaterialStockTransaction->description = $material['description'];

    //             // Save RawMaterialStockTransaction
    //             $rawMaterialStockTransaction->save();
    //         }

    //         DB::commit();
    //         return response()->json(['status' => true, 'message' => 'Raw Material Stock updated successfully.'], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
    //     }
    // }



    public function show($id)
    {
        return view('rawmaterialmaster::show');
    }

    public function edit($id)
    {
        return view('rawmaterialmaster::edit');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer|exists:site_masters,id',
            'supervisor_id' => 'required|integer|exists:users,id',

        ], [
            'site_id.required' => __('rawmaterialmaster::message.site_id_is_required.'),
            'site_id.integer' => __('rawmaterialmaster::message.site_id_must_be_an_integer.'),
            'site_id.exists' => __('rawmaterialmaster::message.the_selected_site_master_id_does_not_exist.'),

            'supervisor_id.required' => __('rawmaterialmaster::message.supervisor_id_is_required.'),
            'supervisor_id.integer' => __('rawmaterialmaster::message.supervisor_id_must_be_an_integer.'),
            'supervisor_id.exists' => __('rawmaterialmaster::message.the_selected_supervisor_id_does_not_exist.'),
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {
            $yearID = getSelectedYear();
            $rawMaterialStock = new RawMaterialStock();
            $rawMaterialStock->site_id = $request->site_id;
            $rawMaterialStock->supervisor_id = $request->supervisor_id;
            $rawMaterialStock->supplier_id =  $request->supplier_id;
            $rawMaterialStock->material_id =  $request->material_id;
            $rawMaterialStock->quantity = $request->quantity;
            $rawMaterialStock->unit_id = $request->unit_id;
            $rawMaterialStock->year_id = $yearID;
            $result = $rawMaterialStock->save();

            if ($rawMaterialStock) {
                $rawMaterialStockTransaction = new RawMaterialStockTransaction();
                $rawMaterialStockTransaction->material_id = $rawMaterialStock->material_id;
                $rawMaterialStockTransaction->material_stock_id = $rawMaterialStock->id;
                $rawMaterialStockTransaction->site_id = $rawMaterialStock->site_id;
                $rawMaterialStockTransaction->supervisor_id = $rawMaterialStock->supervisor_id;
                $rawMaterialStockTransaction->supplier_id = $rawMaterialStock->supplier_id;
                $rawMaterialStockTransaction->quantity = $rawMaterialStock->quantity;
                $rawMaterialStockTransaction->unit_id = $rawMaterialStock->unit_id;
                $rawMaterialStockTransaction->type = $request->type;
                $rawMaterialStockTransaction->remark = $request->remark;
                $rawMaterialStockTransaction->save();
            }
            DB::commit();
            if ($result) {
                DB::commit();
                return response()->json(['status' => true, 'message' => 'Raw Material Stock created successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Raw Material Stock can not create.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:raw_material_stock_transactions,id',
        ], [
            'id.required' => 'ID is required.',
            'id.integer' => 'ID must be an integer.',
            'id.exists' => 'The selected Raw Material ID does not exist.',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }
        try {
            $rawMaterialStockTransaction = RawMaterialStockTransaction::where('id', $request->id)->first();
            if (!is_null($rawMaterialStockTransaction)) {
                // SiteSupervisor::where('site_master_id', $siteMaster->id)->delete();
                $rawMaterialStockTransaction->delete();
                $response = ['status' => true, 'message' => 'Raw Material Stock Transaction deleted successfully.'];
            } else {
                $response = ['status' => false, 'message' => 'Raw Material Stock Transaction not found.'];
            }
            return response($response, 200);
        } catch (\Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function rawMaterialDropdown(Request $request)
    {

        try {
            $rawMaterialMaster = RawMaterialMaster::select('id', 'material_category_id', 'material_name', 'material_code')->orderBy('material_name','asc')->get();
            return response(['status' => true, 'message' => 'Raw Material Dropdown', 'raw_material_dropdown' => $rawMaterialMaster], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
    public function unitDropdown(Request $request)
    {

        try {
            $unit = Unit::select('id', 'name')->orderBy('name','asc')->get();
            return response(['status' => true, 'message' => 'Unit Dropdown', 'unit_dropdown' => $unit], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function materialStock(Request $request)
    {
        try {
            $rawMaterialStock = RawMaterialStock::select(
                'raw_material_stocks.id',
                'raw_material_stocks.material_id',
                'raw_material_stocks.site_id',
                'raw_material_stocks.supervisor_id',
                'raw_material_stocks.supplier_id',
                'raw_material_stocks.quantity',
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
                ->orderBy('raw_material_stocks.id', 'DESC')
                ->simplePaginate(12);

            return response(['status' => true, 'message' => 'Raw Material Stock List', 'material_stock' => $rawMaterialStock->items()], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
}
