<?php

namespace Modules\ExpenseMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\ExpenseMaster\Models\ExpenseMaster;
use Modules\PaymentMaster\Models\PaymentMaster;
use Modules\SiteMaster\Models\SiteMaster;
use Modules\User\Models\User;
use Yajra\DataTables\Facades\DataTables;

class ExpenseMasterController extends Controller
{

    public function index(Request $request)
    {
        $query = ExpenseMaster::select(
            'expense_masters.id',
            'expense_masters.site_id',
            'expense_masters.supervisor_id',
            'expense_masters.expense_category_id',
            'expense_masters.amount',
            'expense_masters.status',
            'expense_masters.document',
            'expense_masters.remark',
            'site_masters.site_name',
            'expense_categories.expense_category_name',
            'users.name as supervisor_name',
        )
            ->leftJoin('site_masters', 'expense_masters.site_id', '=', 'site_masters.id')
            ->leftJoin('users', 'users.id', '=', 'expense_masters.supervisor_id')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expense_masters.expense_category_id');

        $user = Auth::user();
        $role = $user->roles->first();

        if ($role && $role->name === 'Supervisor') {
            $query->where('expense_masters.supervisor_id', $user->id);
        }

        if ($request->filled('supervisor_id')) {
            $query->where('expense_masters.supervisor_id', $request->supervisor_id);
        }
        if ($request->filled('site_id')) {
            $query->where('expense_masters.site_id', $request->site_id);
        }
        if ($request->filled('expense_category_id')) {
            $query->where('expense_masters.expense_category_id', $request->expense_category_id);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('expense_masters.created_at', [$startDate, $endDate]);
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('expense_masters.created_at', '>=', $startDate);
        } elseif ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('expense_masters.created_at', '<=', $endDate);
        }



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
                ->editColumn('status', function ($row) {

                    $html = '<div class="">';
                    $active1 =  $active2  = $active3 = '';
                    if ($row->status == "Approve") {
                        $btn = "btn-outline-success";
                        $title = "Approve";
                        $active1 = 'active bg-success';
                    }
                    if ($row->status == "Hold") {
                        $btn = "btn-outline-warning";
                        $title = "Hold";
                        $active2 = 'active bg-warning';
                    }
                    if ($row->status == "Reject") {
                        $btn = "btn-outline-danger";
                        $title = "Reject";
                        $active3 = 'active bg-danger';
                    }
                    $html .= '<div class="dropdown">
                                <button class="btn px-2 py-1 ' . $btn . ' dropdown-toggle" type="button"  data-bs-toggle="dropdown" aria-expanded="false">
                                    ' . $title . '
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item status  ' . $active1 . '" href="javascript:void(0);" data-id="' . $row->id . '" data-value="Approve">Approve</a></li>
                                    <li><a class="dropdown-item status  ' . $active2 . '" href="javascript:void(0);" data-id="' . $row->id . '" data-value="Hold">Hold</a></li>
                                    <li><a class="dropdown-item status  ' . $active3 . '" href="javascript:void(0);" data-id="' . $row->id . '" data-value="Reject">Reject</a></li>
                                </ul>
                            </div>';
                    return $html;
                })
                ->editColumn('document', function ($row) {
                    if ($row->document) {
                        $url = url('expense/document/' . $row->document);
                        return '<a href="' . $url . '" target="_blank">
                    <img src="' . $url . '" alt="Document" height="40" />
                </a>';
                    } else {
                        return '';
                    }
                })
                // ->editColum('document', function ($row) {})
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('expensemaster::index');
        }
    }


    //    public function statusChange(Request $request)
    //     {
    //         $validator = Validator::make($request->all(), [
    //             'id' => 'required|integer|exists:expense_masters,id',
    //             'status' => 'required',
    //         ], [
    //             'id.required' => 'ID is required.',
    //             'id.integer' => 'ID must be an integer.',
    //             'id.exists' => 'The enter expense master ID does not exist.',
    //             'status.required' => 'Enter expense status',
    //         ]);
    //         if ($validator->fails()) {
    //             return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
    //         }
    //         try {
    //             ExpenseMaster::where('id', $request->id)->update(['status' => $request->status]);
    //             return response()->json(['status_code' => 200, 'message' => 'Status change successfully.']);
    //         } catch (\Exception $e) {
    //             return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
    //         }
    //     }

    public function statusChange(Request $request)
    {
        ExpenseMaster::where('id', $request->id)->update(['status' => $request->status]);
        if ($request->status != "Approve") {
            PaymentMaster::where([['model_type', 'Expense'], ['model_id', $request->id]])->delete();
        } else {
            // PaymentMaster::where([['model_type','Expense'],['model_id',$request->id]])->delete();

        }
        $response = ['status' => true, 'message' => 'Status change successfully.'];
        $response = ['data' => route('expensemaster.index'), 'status' => true, 'message' => ' Status change successfully.'];
        return response()->json($response);
    }


    public function create()
    {
        return view('expensemaster::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('expensemaster::show');
    }

    public function edit($id)
    {
        return view('expensemaster::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}



    public function paymentLedger(Request $request)
    {
        $query = PaymentMaster::select(
            'payment_masters.id',
            'payment_masters.site_id',
            'payment_masters.supervisor_id',
            'payment_masters.model_type',
            'payment_masters.model_id',
            'payment_masters.amount',
            'payment_masters.status',
            DB::raw("DATE_FORMAT(payment_masters.date, '%d-%m-%Y') as date"),
            'site_masters.site_name',
            'users.name as supervisor_name',
        )
            ->leftJoin('site_masters', 'payment_masters.site_id', '=', 'site_masters.id')
            ->leftJoin('users', 'users.id', '=', 'payment_masters.supervisor_id');
        $user = Auth::user();
        $role = $user->roles->first();

        if ($role && $role->name === 'Supervisor') {
            $query->where('payment_masters.supervisor_id', $user->id);
        }

        if ($request->filled('supervisor_id')) {
            $query->where('payment_masters.supervisor_id', $request->supervisor_id);
        }
        if ($request->filled('site_id')) {
            $query->where('payment_masters.site_id', $request->site_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('payment_masters.created_at', [$startDate, $endDate]);
        } elseif ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('payment_masters.created_at', '>=', $startDate);
        } elseif ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('payment_masters.created_at', '<=', $endDate);
        }

        $totalExpense = (clone $query)->where('model_type', 'Expense')->sum('payment_masters.amount');
        $totalIncome = (clone $query)->where('model_type', 'Income')->sum('payment_masters.amount');
        $closingBalance = $totalIncome - $totalExpense;

        $payment = $query->orderBy('payment_masters.id', 'DESC');


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
                ->addColumn('credit', function ($row) {
                    return ($row->status == 'Credit') ? number_format($row->amount, 2) : '-';
                })
                ->editColumn('date', function ($row) {
                    return $row->date ?? '-';
                })
                ->addColumn('debit', function ($row) {
                    return ($row->status == 'Debit') ? number_format($row->amount, 2) : '-';
                })
                // ->addColumn('credit', function ($row) {
                //     if ($row->status == 'Credit') {
                //         return '<span class"text-success">' . $row->amount . '</span>';
                //     }
                //     return '-';
                //     // return $row->status == 'Credit' ? $row->amount : '-';
                // })
                // ->addColumn('debit', function ($row) {
                //     return $row->status == 'Debit' ? $row->amount : '-';
                // })
                ->escapeColumns([])
                ->make(true);
        } else {
            $sites = SiteMaster::all();
            $supervisors = User::whereHas('roles', fn($q) => $q->where('name', 'Supervisor'))->get();

            return view('expensemaster::ledger', compact('sites', 'supervisors', 'totalExpense', 'totalIncome', 'closingBalance'));
        }
    }
}
