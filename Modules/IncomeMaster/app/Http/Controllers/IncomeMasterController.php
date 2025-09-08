<?php

namespace Modules\IncomeMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\IncomeMaster\Models\IncomeMaster;
use Modules\Party\Models\Party;
use Modules\PaymentMaster\Models\PaymentMaster;
use Modules\SiteMaster\Models\SiteMaster;
use Modules\User\Models\User;
use Yajra\DataTables\Facades\DataTables;

class IncomeMasterController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:income-master-list|income-master-create', ['only' => ['index', 'store']]);
        $this->middleware('permission:income-master-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:income-master-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:income-master-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = IncomeMaster::select(
            'income_masters.id',
            'income_masters.user_id',
            'income_masters.site_id',
            'income_masters.supervisor_id',
            'income_masters.amount',
            'income_masters.remark',
            DB::raw("DATE_FORMAT(income_masters.date, '%d-%m-%Y') as date"),
            'site_masters.site_name',
            'parties.party_name',
            'supervisor.name as supervisor_name',
        )
            ->leftJoin('site_masters', 'income_masters.site_id', '=', 'site_masters.id')
            ->leftJoin('users as supervisor', 'supervisor.id', '=', 'income_masters.supervisor_id')
            ->leftJoin('parties', 'parties.id', '=', 'income_masters.party_id')
            ->when(role_supervisor(), function ($q) {
                return $q->where('user_id', Auth::id());
            })
            ->when(!empty($request->site_id) && $request->site_id !== 'All', function ($query) use ($request) {
                $query->where('income_masters.site_id', $request->site_id);
            })
            ->when(!empty($request->supervisor_id) && $request->supervisor_id !== 'All', function ($query) use ($request) {
                $query->where('income_masters.supervisor_id', $request->supervisor_id);
            })
            ->when(!empty($request->s_date) || !empty($request->e_date), function ($query) use ($request) {
                $startDate = !empty($request->s_date)
                    ? date('Y-m-d 00:00:00', strtotime($request->s_date))
                    : null;

                $endDate = !empty($request->e_date)
                    ? date('Y-m-d 23:59:59', strtotime($request->e_date))
                    : ($startDate ? date('Y-m-d 23:59:59', strtotime($request->s_date)) : null);

                if ($startDate && $endDate) {
                    $query->whereBetween('income_masters.date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->where('income_masters.date', '>=', $startDate);
                } elseif ($endDate) {
                    $query->where('income_masters.date', '<=', $endDate);
                }
            })->orderBy('income_masters.date', 'DESC');

        // $query = IncomeMaster::select(
        //         'income_masters.id',
        //         'income_masters.user_id',
        //         'income_masters.site_id',
        //         'income_masters.supervisor_id',
        //         'income_masters.amount',
        //         'income_masters.remark',
        //         DB::raw("DATE_FORMAT(income_masters.date, '%d-%m-%Y') as date"),
        //         'site_masters.site_name',
        //         'supervisor.name as supervisor_name',
        //         'user.name as user_name',
        //     )
        //         ->leftJoin('site_masters', 'income_masters.site_id', '=', 'site_masters.id')
        //         ->leftJoin('users as supervisor', 'supervisor.id', '=', 'income_masters.supervisor_id')
        //         ->leftJoin('users as user', 'user.id', '=', 'income_masters.user_id');

        //     $user = Auth::user();
        //     $role = $user->roles->first();

        //     if ($role && $role->name === 'Supervisor') {
        //         $query->where('income_masters.user_id', $user->id);
        //     }

        //     if ($request->filled('supervisor_id')) {
        //         $query->where('income_masters.supervisor_id', $request->supervisor_id);
        //     }

        //     if ($request->filled('site_id')) {
        //         $query->where('income_masters.site_id', $request->site_id);
        //     }

        //     if ($request->filled('start_date') && $request->filled('end_date')) {
        //         $startDate = Carbon::parse($request->start_date)->startOfDay();
        //         $endDate = Carbon::parse($request->end_date)->endOfDay();
        //         $query->whereBetween('income_masters.created_at', [$startDate, $endDate]);
        //     } elseif ($request->filled('start_date')) {
        //         $startDate = Carbon::parse($request->start_date)->startOfDay();
        //         $query->where('income_masters.created_at', '>=', $startDate);
        //     } elseif ($request->filled('end_date')) {
        //         $endDate = Carbon::parse($request->end_date)->endOfDay();
        //         $query->where('income_masters.created_at', '<=', $endDate);
        //     }

        //      $incomeMaster = $query->orderBy('income_masters.id', 'DESC')->get();
        if (request()->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $flag = true;
                    $show = '';
                    $edit = true ? 'income-master-edit' : '';
                    $delete = $flag ? 'income-master-delete' : '';
                    $showURL = "";
                    $editURL = "";
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
                })
                ->editColumn('amount', function ($row) {
                    return number_format($row->amount, 2);
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            $siteMaster = SiteMaster::get();
            $supervisor = User::select('id', 'name')
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'supervisor');
                })
                ->orderBy('id', 'DESC')
                ->get();
            $party = Party::select('id', 'party_name')->get();
            return view('incomemaster::index', compact('siteMaster', 'supervisor', 'party'));
        }
    }
    // public function index()
    // {
    //     $incomeMaster = IncomeMaster::select(
    //         'income_masters.id',
    //         'income_masters.user_id',
    //         'income_masters.site_id',
    //         'income_masters.supervisor_id',
    //         'income_masters.amount',
    //         'income_masters.remark',
    //         'site_masters.site_name',
    //         'supervisor.name as supervisor_name',
    //         'user.name as user_name',
    //     )
    //         ->leftJoin('site_masters', 'income_masters.site_id', '=', 'site_masters.id')
    //         ->leftJoin('users as supervisor', 'supervisor.id', '=', 'income_masters.supervisor_id')
    //         ->leftJoin('users as user', 'user.id', '=', 'income_masters.user_id')
    //         ->orderBy('income_masters.id', 'DESC')
    //         ->get();
    //     if (request()->ajax()) {
    //         return DataTables::of($incomeMaster)
    //             ->addIndexColumn()
    //             ->addColumn('action', function ($row) {
    //                 $flag = true;
    //                 $show = '';
    //                 $edit = true ? 'income-master-edit' : '';
    //                 $delete = $flag ? 'income-master-delete' : '';
    //                 $showURL = "";
    //                 $editURL = "";
    //                 return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
    //             })
    //             ->escapeColumns([])
    //             ->make(true);
    //     } else {
    //         $siteMaster = SiteMaster::get();
    //         $supervisor = User::select('id', 'name')
    //             ->whereHas('roles', function ($q) {
    //                 $q->where('name', 'supervisor');
    //             })
    //             ->orderBy('id', 'DESC')
    //             ->get();
    //         return view('incomemaster::index',compact('siteMaster','supervisor'));
    //     }
    // }

    public function create()
    {
        return view('incomemaster::create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer|exists:site_masters,id',
            'supervisor_id' => 'required|integer|exists:users,id',
            'amount' => 'required',
        ], [
            'site_id.required' => __('incomemaster::message.site_id_is_required.'),
            'site_id.integer' => __('incomemaster::message.site_id_must_be_an_integer.'),
            'site_id.exists' => __('incomemaster::message.the_selected_site_master_id_does_not_exist.'),

            'supervisor_id.required' => __('incomemaster::message.supervisor_id_is_required.'),
            'supervisor_id.integer' => __('incomemaster::message.supervisor_id_must_be_an_integer.'),
            'supervisor_id.exists' => __('incomemaster::message.the_selected_supervisor_id_does_not_exist.'),

            'amount.required' => __('incomemaster::message.amount_is_required.'),
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            if (!is_null($request->id)) {
                $incomeMaster = IncomeMaster::where('id', $request->id)->first();
                $paymentMaster = PaymentMaster::where([['model_type', 'Income'], ['model_id', $incomeMaster->id]])->first();
                $msg = ' updated ';
            } else {
                $incomeMaster = new IncomeMaster();
                $paymentMaster = new PaymentMaster();
                $msg = ' added ';
            }
            $yearID = getSelectedYear();
            $incomeMaster->user_id = Auth::id();
            $incomeMaster->site_id = $request->site_id;
            $incomeMaster->supervisor_id = $request->supervisor_id;
            $incomeMaster->party_id = $request->party_id;
            $incomeMaster->amount = $request->amount;
            $incomeMaster->remark = $request->remark;
            $incomeMaster->date = (!empty($request->date)) ? date('Y-m-d', strtotime($request->date)) : null;
            $incomeMaster->year_id = $yearID;
            $result = $incomeMaster->save();

            $paymentMaster->site_id = $incomeMaster->site_id;
            $paymentMaster->supervisor_id = $incomeMaster->supervisor_id;
            $paymentMaster->model_type = "Income";
            $paymentMaster->model_id = $incomeMaster->id;
            $paymentMaster->amount = $incomeMaster->amount;
            $paymentMaster->status = "Credit";
            $paymentMaster->remark = $incomeMaster->remark;
            $paymentMaster->date = (!empty($incomeMaster->date)) ? date('Y-m-d', strtotime($incomeMaster->date)) : null;
            $paymentMaster->year_id = $yearID;
            $paymentMaster->save();

            if (!is_null($result)) {
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'Income' . $msg . 'successfully.']);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Income' . $msg . 'failed.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function show($id)
    {
        return view('incomemaster::show');
    }

    public function edit($id)
    {
        try {
            $incomeMaster = IncomeMaster::select('id', 'site_id', 'supervisor_id', 'party_id', 'amount', 'remark', DB::raw("DATE_FORMAT(date, '%d-%m-%Y') as date"))->where('id', $id)->first();
            if (!is_null($incomeMaster)) {
                return response()->json(['status_code' => 200, 'message' => 'Edit Income ', 'result' => $incomeMaster]);
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Income not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function update(Request $request, $id) {}

    public function destroy($id)
    {
        try {
            $incomeMaster = IncomeMaster::select('id')->where('id', $id)->first();
            if (!is_null($incomeMaster)) {
                //  if (storage_delete_check($incomeMaster->id)) {
                PaymentMaster::where([['model_type', 'Income'], ['model_id', $id]])->delete();
                $incomeMaster->delete();
                return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                // } else {
                //     return response()->json(['status_code' => 201, 'message' => 'This Income already use in another module.']);
                // }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Income not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
