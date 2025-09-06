<?php

namespace Modules\PaymentMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\ExpenseMaster\Models\ExpenseMaster;
use Modules\IncomeMaster\Models\IncomeMaster;
use Modules\PaymentMaster\Models\PaymentMaster;
use Modules\PaymentMaster\Models\PaymentTransfer;

class PaymentMasterApiController extends Controller
{

    public function index(Request $request)
    {
        try {
            $query = PaymentTransfer::select(
                'payment_transfers.id',
                'payment_transfers.supervisor_id as from_user_id',
                'payment_transfers.to_supervisor_id as to_user_id',
                'payment_transfers.amount',
                'payment_transfers.remark',
                'payment_transfers.site_id',
                // DB::raw("DATE_FORMAT(payment_transfers.date, '%d-%m-%Y') as date"),
                'user.name as from_user_name',
                'to_user.name as to_user_name',
                'site_masters.site_name',
            )
                ->leftJoin('users as user', 'user.id', '=', 'payment_transfers.supervisor_id')
                ->leftJoin('users as to_user', 'to_user.id', '=', 'payment_transfers.to_supervisor_id')
                ->leftJoin('site_masters','site_masters.id', '=','payment_transfers.site_id');
            $user = Auth::user();
            $role = $user->roles->first();

            if ($role && $role->name === 'Supervisor') {
                $query->where('payment_transfers.supervisor_id', $user->id);
            }

            if ($request->filled('to_user_id')) {
                $query->where('payment_transfers.to_supervisor_id', $request->to_user_id);
            }

            if ($request->filled('site_id')) {
                $query->where('payment_transfers.site_id', $request->site_id);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('payment_transfers.created_at', [$startDate, $endDate]);
            } elseif ($request->filled('start_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $query->where('payment_transfers.created_at', '>=', $startDate);
            } elseif ($request->filled('end_date')) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->where('payment_transfers.created_at', '<=', $endDate);
            }



            // $totalAmount = (clone $query)->sum('income_masters.amount');

            $paymentTransfer = $query->orderBy('payment_transfers.id', 'DESC')->simplePaginate(30);

            // $formattedIncomeMaster = collect($incomeMaster->items())->map(function ($item) {
            //     if (floor($item->amount) == $item->amount) {
            //         $item->amount = (int) $item->amount;  // 100.000 → 100
            //     } else {
            //         $item->amount = (float) $item->amount;  // 100.50 → 100.5
            //     }
            //     return $item;
            // });

            return response([
                'status' => true,
                'message' => 'Payment Transfer List',
                // 'total_amount' => floor($totalAmount) == $totalAmount ? (int) $totalAmount : (float) $totalAmount,
                'payment_transfer' => $paymentTransfer->items()
            ], 200);
        } catch (\Exception $e) {
            dd($e);
            return response([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                // 'error' => $e->getMessage()
            ], 200);
        }
    }


    public function create()
    {
        return view('paymentmaster::create');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'amount' => 'required',
        ], [

            'amount.required' => __('sitemaster::message.amount_is_required.'),
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {
            $yearID = getSelectedYear();
            $expenseMaster = new ExpenseMaster();
            $expenseMaster->supervisor_id = Auth::id();
            $expenseMaster->amount = $request->amount;
            $expenseMaster->remark = $request->remark;
            $expenseMaster->date = now()->toDateString();
            $expenseMaster->year_id = $yearID;
            $result = $expenseMaster->save();

            $paymentMaster = new PaymentMaster();
            $paymentMaster->supervisor_id = $expenseMaster->supervisor_id;
            $expenseMaster->to_supervisor_id = $request->to_supervisor_id;
            $paymentMaster->model_type = "Expense";
            $paymentMaster->model_id = $expenseMaster->id;
            $paymentMaster->amount = $expenseMaster->amount;
            $paymentMaster->status = "Debit";
            $paymentMaster->remark = $expenseMaster->remark;
            $paymentMaster->date = (!empty($expenseMaster->date)) ? date('Y-m-d', strtotime($expenseMaster->date)) : null;;
            $paymentMaster->year_id = $yearID;;
            $paymentMaster->save();

            $incomeMaster = new IncomeMaster();
            $incomeMaster->site_id = $request->site_id;
            $incomeMaster->supervisor_id = $request->to_supervisor_id;
            $incomeMaster->amount = $request->amount;
            $incomeMaster->remark = $request->remark;
            $incomeMaster->date = now()->toDateString();
            // $incomeMaster->date = (!empty($request->date)) ? date('Y-m-d', strtotime($request->date)) : null;
            $incomeMaster->year_id = $yearID;
            $result = $incomeMaster->save();


            $paymentMaster = new PaymentMaster();
            $paymentMaster->supervisor_id = $incomeMaster->supervisor_id;
            $paymentMaster->model_type = "Income";
            $paymentMaster->model_id = $incomeMaster->id;
            $paymentMaster->amount = $incomeMaster->amount;
            $paymentMaster->status = "Credit";
            $paymentMaster->remark = $incomeMaster->remark;
            $paymentMaster->date = now()->toDateString();
            // $paymentMaster->date = (!empty($incomeMaster->date)) ? date('Y-m-d', strtotime($incomeMaster->date)) : null;;
            $paymentMaster->year_id = $yearID;;
            $paymentMaster->save();

            $paymentTransfer = new PaymentTransfer();
            $paymentTransfer->supervisor_id = Auth::id();
            $paymentTransfer->site_id = $request->site_id;
            $paymentTransfer->to_supervisor_id = $request->to_supervisor_id;
            $paymentTransfer->amount = $incomeMaster->amount;
            $paymentTransfer->remark = $request->remark;
            $paymentTransfer->year_id = $yearID;;
            $paymentTransfer->save();

            if ($result) {
                DB::commit();
                return response()->json(['status' => true, 'message' => 'Payment Transfer successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Payment can not Transfer.'], 200);
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }


    public function show($id)
    {
        return view('paymentmaster::show');
    }


    public function edit($id)
    {
        return view('paymentmaster::edit');
    }
    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
