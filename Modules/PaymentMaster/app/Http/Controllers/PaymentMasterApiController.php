<?php

namespace Modules\PaymentMaster\Http\Controllers;

use App\Http\Controllers\Controller;
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

    public function index()
    {
        return view('paymentmaster::index');
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
