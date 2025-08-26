<?php

namespace Modules\IncomeMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\IncomeMaster\Models\IncomeMaster;
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

    public function index()
    {
        $incomeMaster = IncomeMaster::select(
            'income_masters.id',
            'income_masters.user_id',
            'income_masters.site_id',
            'income_masters.supervisor_id',
            'income_masters.amount',
            'income_masters.remark',
            'site_masters.site_name',
            'supervisor.name as supervisor_name',
            'user.name as user_name',
        )
            ->leftJoin('site_masters', 'income_masters.site_id', '=', 'site_masters.id')
            ->leftJoin('users as supervisor', 'supervisor.id', '=', 'income_masters.supervisor_id')
            ->leftJoin('users as user', 'user.id', '=', 'income_masters.user_id')
            ->orderBy('income_masters.id', 'DESC')
            ->get();
        if (request()->ajax()) {
            return DataTables::of($incomeMaster)
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
            return view('incomemaster::index',compact('siteMaster','supervisor'));
        }
    }

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
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
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
            $paymentMaster->date = (!empty($incomeMaster->date)) ? date('Y-m-d', strtotime($incomeMaster->date)) : null;;
            $paymentMaster->year_id = $yearID;;
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
            $incomeMaster = IncomeMaster::where('id', $id)->first();
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
