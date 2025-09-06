<?php

namespace Modules\IncomeMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\IncomeMaster\Models\IncomeMaster;
use Modules\PaymentMaster\Models\PaymentMaster;

class IncomeMasterApiController extends Controller
{

    // public function index()
    // {
    //     try {
    //         $incomeMaster = IncomeMaster::select(
    //             'income_masters.id',
    //             'income_masters.user_id',
    //             'income_masters.site_id',
    //             'income_masters.supervisor_id',
    //             'income_masters.amount',
    //             'income_masters.remark',
    //             'site_masters.site_name',
    //             'supervisor.name as supervisor_name',
    //             'user.name as user_name',
    //         )
    //             ->leftJoin('site_masters', 'income_masters.site_id', '=', 'site_masters.id')
    //             ->leftJoin('users as supervisor', 'supervisor.id', '=', 'income_masters.supervisor_id')
    //             ->leftJoin('users as user', 'user.id', '=', 'income_masters.user_id')
    //             ->orderBy('income_masters.id', 'DESC')
    //             ->simplePaginate(12);
    //         return response(['status' => true, 'message' => 'Income Master List', 'income_master' => $incomeMaster->items()], 200);
    //     } catch (Exception $e) {
    //         return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
    //     }
    // }

    public function index(Request $request)
    {
        try {
            $query = IncomeMaster::select(
                'income_masters.id',
                'income_masters.user_id',
                'income_masters.site_id',
                'income_masters.supervisor_id',
                'income_masters.amount',
                'income_masters.remark',
                'income_masters.party_id',
                DB::raw("DATE_FORMAT(income_masters.date, '%d-%m-%Y') as date"),
                'site_masters.site_name',
                'parties.party_name',
                'supervisor.name as supervisor_name',
                'user.name as user_name',
            )
                ->leftJoin('site_masters', 'income_masters.site_id', '=', 'site_masters.id')
                ->leftJoin('users as supervisor', 'supervisor.id', '=', 'income_masters.supervisor_id')
                ->leftJoin('users as user', 'user.id', '=', 'income_masters.user_id')
                ->leftJoin('parties', 'parties.id', '=', 'income_masters.party_id');

            $user = Auth::user();
            $role = $user->roles->first();

            // if ($role && $role->name === 'Supervisor') {
            //     $query->where('income_masters.user_id', $user->id);
            // }
            if ($role && $role->name === 'Supervisor') {
                $query->where(function ($q) use ($user) {
                    $q->where('income_masters.user_id', $user->id)
                        ->orWhere('income_masters.supervisor_id', $user->id);
                });
            }

            if ($request->filled('supervisor_id')) {
                $query->where('income_masters.supervisor_id', $request->supervisor_id);
            }

            if ($request->filled('site_id')) {
                $query->where('income_masters.site_id', $request->site_id);
            }
            if ($request->filled('party_id')) {
                $query->where('income_masters.party_id', $request->party_id);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('income_masters.date', [$startDate, $endDate]);
            } elseif ($request->filled('start_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $query->where('income_masters.date', '>=', $startDate);
            } elseif ($request->filled('end_date')) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->where('income_masters.date', '<=', $endDate);
            }



            $totalAmount = (clone $query)->sum('income_masters.amount');

            $incomeMaster = $query->orderBy('income_masters.id', 'DESC')->simplePaginate(30);

            $formattedIncomeMaster = collect($incomeMaster->items())->map(function ($item) {
                if (floor($item->amount) == $item->amount) {
                    $item->amount = (int) $item->amount;  // 100.000 → 100
                } else {
                    $item->amount = (float) $item->amount;  // 100.50 → 100.5
                }
                return $item;
            });

            return response([
                'status' => true,
                'message' => 'Income Master List',
                'total_amount' => floor($totalAmount) == $totalAmount ? (int) $totalAmount : (float) $totalAmount,
                'income_master' => $formattedIncomeMaster
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                // 'error' => $e->getMessage()
            ], 200);
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
            $yearID = getSelectedYear();
            $incomeMaster = new IncomeMaster();
            $incomeMaster->user_id = Auth::id();
            $incomeMaster->site_id = $request->site_id;
            $incomeMaster->party_id = $request->party_id;
            $incomeMaster->supervisor_id = $request->supervisor_id;
            $incomeMaster->amount = $request->amount;
            $incomeMaster->remark = $request->remark;
            $incomeMaster->date = (!empty($request->date)) ? date('Y-m-d', strtotime($request->date)) : null;
            $incomeMaster->year_id = $yearID;
            $result = $incomeMaster->save();

            $paymentMaster = new PaymentMaster();
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


            // DB::commit();
            if ($result) {
                DB::commit();
                return response()->json(['status' => true, 'message' => 'Income add successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Income can not added.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logToCustomFile($e);
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function show($id)
    {
        return view('incomemaster::show');
    }

    public function edit($id)
    {
        return view('incomemaster::edit');
    }

    public function update(Request $request)
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
            $yearID = getSelectedYear();
            $incomeMaster = IncomeMaster::where('id', $request->id)->first();
            $incomeMaster->site_id = $request->site_id;
            $incomeMaster->supervisor_id = $request->supervisor_id;
            $incomeMaster->party_id = $request->party_id;
            $incomeMaster->amount = $request->amount;
            $incomeMaster->remark = $request->remark;
            $incomeMaster->year_id = $yearID;


            $result = $incomeMaster->save();

            $paymentMaster = PaymentMaster::where([['model_type', 'Income'], ['model_id', $incomeMaster->id]])->first();
            $paymentMaster->site_id = $incomeMaster->site_id;
            $paymentMaster->supervisor_id = $incomeMaster->supervisor_id;
            $paymentMaster->model_type = "Income";
            $paymentMaster->model_id = $incomeMaster->id;
            $paymentMaster->amount = $incomeMaster->amount;
            $paymentMaster->status = "Credit";
            $paymentMaster->remark = $incomeMaster->remark;
            $paymentMaster->year_id = $yearID;;
            $paymentMaster->save();
            // DB::commit();
            if ($result) {
                DB::commit();
                return response()->json(['status' => true, 'message' => 'Income update successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Income can not added.'], 200);
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            $this->logToCustomFile($e);
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:income_masters,id',
        ], [
            'id.required' => 'ID is required.',
            'id.integer' => 'ID must be an integer.',
            'id.exists' => 'The selected Income Master ID does not exist.',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }
        try {
            $incomeMaster = IncomeMaster::where('id', $request->id)->first();
            if (!is_null($incomeMaster)) {
                PaymentMaster::where([['model_type', 'Income'], ['model_id', $incomeMaster->id]])->delete();
                $incomeMaster->delete();
                $response = ['status' => true, 'message' => 'Income deleted successfully.'];
            } else {
                $response = ['status' => false, 'message' => 'Income not found.'];
            }
            return response($response, 200);
        } catch (\Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
}
