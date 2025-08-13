<?php

namespace Modules\ExpenseMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\ExpenseCategory\Models\ExpenseCategory;
use Modules\ExpenseMaster\Models\ExpenseMaster;
use Modules\PaymentMaster\Models\PaymentMaster;
use Illuminate\Support\Str;

class ExpenseMasterApiController extends Controller
{

    public function index()
    {
        try {
            $expenseMaster = ExpenseMaster::select(
                'expense_masters.id',
                'expense_masters.site_id',
                'expense_masters.supervisor_id',
                'expense_masters.expense_category_id',
                'expense_masters.amount',
                'expense_masters.document',
                'expense_masters.remark',
                'site_masters.site_name',
                'expense_categories.expense_category_name',
                'users.name as supervisor_name',
            )
                ->leftJoin('site_masters', 'expense_masters.site_id', '=', 'site_masters.id')
                ->leftJoin('users', 'users.id', '=', 'expense_masters.supervisor_id')
                ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expense_masters.expense_category_id')
                 ->orderBy('expense_masters.id', 'DESC')
                ->simplePaginate(12);
            return response(['status' => true, 'message' => 'Expense Master List', 'expense_master' => $expenseMaster->items()], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
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
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {
            $yearID = getSelectedYear();
            $expenseMaster = new ExpenseMaster();
            $expenseMaster->site_id = $request->site_id;
            // $expenseMaster->supervisor_id = $request->supervisor_id;
            $expenseMaster->supervisor_id = Auth::id();
            $expenseMaster->expense_category_id = $request->expense_category_id;
            $expenseMaster->amount = $request->amount;
            $expenseMaster->remark = $request->remark;
            $expenseMaster->year_id = $yearID;
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $base64 = 'data:image/' . $file->extension() . ';base64,' . base64_encode(file_get_contents($file));
                $uploadResponse = imageUploadFromBase64([
                    'base64' => $base64,
                    'fileName' => 'expense-document',
                    'folder' => 'upload/expense/documents',
                    'thumfolder' => 'upload/expense/documents/thumbs',
                ]);
                if ($uploadResponse) {
                    $expenseMaster->document = $uploadResponse['original'];
                }
            }
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
                return response()->json(['status' => true, 'message' => 'Expense add successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Expense can not added.'], 200);
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

    public function update(Request $request)
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
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {
            $yearID = getSelectedYear();
            $expenseMaster = ExpenseMaster::where('id', $request->id)->first();
            $expenseMaster->site_id = $request->site_id;
            $expenseMaster->supervisor_id = Auth::id();
            $expenseMaster->expense_category_id = $request->expense_category_id;
            $expenseMaster->amount = $request->amount;
            // $expenseMaster->document = $request->document;
            $expenseMaster->remark = $request->remark;
            $expenseMaster->year_id = $yearID;

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $base64 = 'data:image/' . $file->extension() . ';base64,' . base64_encode(file_get_contents($file));
                $uploadResponse = imageUploadFromBase64([
                    'base64' => $base64,
                    'fileName' => 'expense-document',
                    'folder' => 'upload/expense/documents',
                    'thumfolder' => 'upload/expense/documents/thumbs',
                ]);
                if ($uploadResponse) {
                    $expenseMaster->document = $uploadResponse['original'];
                }
            }
            $result = $expenseMaster->save();

            $paymentMaster = PaymentMaster::where([['model_type', 'Expense'], ['model_id', $expenseMaster->id]])->first();
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
                return response()->json(['status' => true, 'message' => 'Expense add successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Expense can not added.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logToCustomFile($e);
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:expense_masters,id',
        ], [
            'id.required' => 'ID is required.',
            'id.integer' => 'ID must be an integer.',
            'id.exists' => 'The selected Expense Master ID does not exist.',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 400);
        }
        try {
            $expenseMaster = ExpenseMaster::where('id', $request->id)->first();
            if (!is_null($expenseMaster)) {
                PaymentMaster::where([['model_type', 'Expense'], ['model_id', $expenseMaster->id]])->delete();
                $expenseMaster->delete();
                $response = ['status' => true, 'message' => 'Expense deleted successfully.'];
            } else {
                $response = ['status' => false, 'message' => 'Expense not found.'];
            }
            return response($response, 200);
        } catch (\Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function expenseCategoryDropdown()
    {
        try {
            $expenseCategoryDropdown = ExpenseCategory::select('id', 'expense_category_name')->get();
            return response(['status' => true, 'message' => 'Expense Category Dropdown', 'expense_category_dropdown' => $expenseCategoryDropdown], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
}
