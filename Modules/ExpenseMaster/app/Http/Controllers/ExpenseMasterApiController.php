<?php

namespace Modules\ExpenseMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\ExpenseCategory\Models\ExpenseCategory;
use Modules\ExpenseMaster\Models\ExpenseMaster;
use Modules\PaymentMaster\Models\PaymentMaster;

class ExpenseMasterApiController extends Controller
{

    public function index()
    {
        return view('expensemaster::index');
    }

    public function create()
    {
        return view('expensemaster::create');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer|exists:site_masters,id',
            'expense_category_id' => 'required|integer|exists:expense_categories,id',
            'amount' => 'required',
        ], [
            'site_id.required' => __('sitemaster::message.site_id_is_required.'),
            'site_id.integer' => __('sitemaster::message.site_id_must_be_an_integer.'),
            'site_id.exists' => __('sitemaster::message.the_selected_site_master_id_does_not_exist.'),

            'expense_category_id.required' => __('sitemaster::message.category_id_is_required.'),
            'expense_category_id.integer' => __('sitemaster::message.category_id_must_be_an_integer.'),
            'expense_category_id.exists' => __('sitemaster::message.the_selected_category_id_does_not_exist.'),

            'amount.required' => __('sitemaster::message.amount_is_required.'),
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 400);
        }

        DB::beginTransaction();
        try {
            $yearID = getSelectedYear();
            $expenseMaster = new ExpenseMaster();
            $expenseMaster->site_id = $request->site_id;
            $expenseMaster->supervisor_id = $request->supervisor_id;
            $expenseMaster->expense_category_id = $request->expense_category_id;
            $expenseMaster->amount = $request->amount;
            $expenseMaster->document = $request->document;
            $expenseMaster->remark = $request->remark;
            $expenseMaster->year_id = $yearID;
            $result = $expenseMaster->save();

            $paymentMaster = new PaymentMaster();
            $paymentMaster->site_id = $expenseMaster->site_id;
            $paymentMaster->supervisor_id = $expenseMaster->supervisor_id;
            $paymentMaster->model_type = "Expense";
            $paymentMaster->model_id = $expenseMaster->id;
            $paymentMaster->amount = $expenseMaster->amount;
            $paymentMaster->status = "Debit";
            $paymentMaster->remark = $expenseMaster->remark;
            $paymentMaster->year_id = $yearID;;
            $paymentMaster->save();


            // DB::commit();
            if ($result) {
                DB::commit();
                return response()->json(['status' => true, 'message' => 'Site created successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Site can not create.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logToCustomFile($e);
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }


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

    public function expenseCategoryDropdown()
    {
        try {
            $expenseCategoryDropdown = ExpenseCategory::select('id','expense_category_name')->get();
            return response(['status' => true, 'message' => 'Expense Category Dropdown', 'expense_category_dropdown' => $expenseCategoryDropdown], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
}
