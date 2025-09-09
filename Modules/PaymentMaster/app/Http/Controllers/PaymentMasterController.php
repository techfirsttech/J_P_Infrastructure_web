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
use Modules\SiteMaster\Models\SiteMaster;
use Modules\User\Models\User;
use Yajra\DataTables\Facades\DataTables;

class PaymentMasterController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentTransfer::select(
            'payment_transfers.id',
            'payment_transfers.supervisor_id',
            'payment_transfers.to_supervisor_id',
            'payment_transfers.amount',
            'payment_transfers.remark',
            DB::raw("DATE_FORMAT(payment_transfers.created_at, '%d-%m-%Y') as date"),
            'user.name as from_user_name',
            'to_user.name as to_user_name',
        )
            ->leftJoin('users as user', 'user.id', '=', 'payment_transfers.supervisor_id')
            ->leftJoin('users as to_user', 'to_user.id', '=', 'payment_transfers.to_supervisor_id');

        $user = Auth::user();
        $role = $user->roles->first();

        if ($role && $role->name === 'Supervisor') {
            $query->where('payment_transfers.supervisor_id', $user->id);
        }

        if ($request->filled('supervisor_id')) {
            $query->where('payment_transfers.supervisor_id', $request->supervisor_id);
        }
        if ($request->filled('to_supervisor_id')) {
            $query->where('payment_transfers.to_supervisor_id', $request->to_supervisor_id);
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


        if (request()->ajax()) {
            return DataTables::of($query)
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
                // ->editColum('document', function ($row) {})
                ->escapeColumns([])
                ->make(true);
        } else {
            $supervisor = User::select('id', 'name')->get();
            $site = SiteMaster::select('id','site_name')->get();
            $toSupervisor = User::select('id', 'name')->get();
            $to_supervisors = User::select('id', 'name')->where('id', '!=', Auth::id())->get();
            return view('paymentmaster::index', compact('supervisor', 'toSupervisor', 'to_supervisors','site'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('paymentmaster::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to_supervisor_id' => 'required|integer|exists:users,id',
            'amount' => 'required',
        ], [

            'to_supervisor_id.required' => __('expensemaster::message.supervisor_id_is_required.'),
            'to_supervisor_id.integer' => __('expensemaster::message.supervisor_id_must_be_an_integer.'),
            'to_supervisor_id.exists' => __('expensemaster::message.the_selected_supervisor_id_does_not_exist.'),

            'amount.required' => __('expensemaster::message.amount_is_required.'),
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
            // $expenseMaster->supervisor_id = Auth::id();
            $expenseMaster->supervisor_id = $request->supervisor_id;
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
            $expenseMaster->site_id = $incomeMaster->site_id;
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
            // $paymentTransfer->supervisor_id = Auth::id();
            $paymentTransfer->supervisor_id = $request->supervisor_id;
            $paymentTransfer->site_id = $request->site_id;
            $paymentTransfer->to_supervisor_id = $request->to_supervisor_id;
            $paymentTransfer->amount = $incomeMaster->amount;
            $paymentTransfer->remark = $request->remark;
            $paymentTransfer->year_id = $yearID;;
            $paymentTransfer->save();

            if (!is_null($result)) {
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'Payment transfer successfully.']);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Payment transfer failed.']);
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }


    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('paymentmaster::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('paymentmaster::edit');
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
