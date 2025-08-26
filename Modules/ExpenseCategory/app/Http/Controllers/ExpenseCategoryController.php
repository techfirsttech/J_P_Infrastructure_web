<?php

namespace Modules\ExpenseCategory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\ExpenseCategory\Models\ExpenseCategory;
use Modules\ExpenseCategory\Models\ExpenseCategoryStatus;
use Yajra\DataTables\Facades\DataTables;

class ExpenseCategoryController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:expense-category-list|expense-category-create', ['only' => ['index', 'store']]);
        $this->middleware('permission:expense-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:expense-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:expense-category-delete', ['only' => ['destroy']]);
    }

    public function index()
    {

        $status = ExpenseCategoryStatus::select('id', 'expense_category_status_name', 'color_class')->get();
        // if (request()->ajax()) {
        //     return DataTables::of(ExpenseCategory::select('id', 'expense_category_name', 'expense_category_status_id'))
        //         ->addIndexColumn()
        //         ->addColumn('action', function ($row) {
        //             $flag = true;
        //             $show = '';
        //             $edit = true ? 'expense-category-edit' : '';
        //             $delete = $flag ? 'expense-category-delete' : '';
        //             $showURL = "";
        //             $editURL = "";
        //             return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
        //         })
        //         ->addColumn('expense_category_status_id', function ($row) use ($status) {
        //             if ($row->expense_category_status_name == 'Pending') {
        //                 $dropDown = '<div class="dropdown">
        //                         <button class="btn px-2 py-1 btn-outline-' . $row->color_class . ' dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        //                             ' . $row->expense_category_status_name . '
        //                         </button>
        //                         <ul class="dropdown-menu">';
        //                 foreach ($status as $ecs) {
        //                     if ($ecs->id == $row->o_acceptance_status_id) {
        //                         $dropDown .= '<li><a class="dropdown-item change-status active bg-' . $ecs->color_class . ' " href="javascript:void(0);"  data-id="' . $row->id . '" data-status="' . $ecs->id . '" >' . $ecs->expense_category_status_name . '</a></li>';
        //                     } else {
        //                         $dropDown .= '<li><a class="dropdown-item change-status" href="javascript:void(0);" data-id="' . $row->id . '" data-status="' . $ecs->id . '">' . $ecs->status_name . '</a></li>';
        //                     }
        //                 }
        //                 $dropDown .= '</ul>
        //                     </div>';
        //                 return $dropDown;
        //             } else {
        //                 return '<span class="badge bg-label-' . $row->color_class . '">' . $row->expense_category_status_name . '</span>';
        //             }
        //         })
        //         ->escapeColumns([])
        //         ->make(true);
        // } else {
        //     $status = ExpenseCategoryStatus::select('id', 'expense_category_status_name')->get();
        //     return view('expensecategory::index', compact('status'));
        // }
        if (request()->ajax()) {
            return DataTables::of(
                ExpenseCategory::with('expense_category_status:id,expense_category_status_name,color_class')
                    ->select('id', 'expense_category_name', 'expense_category_status_id')
            )
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $flag = true;
                    $show = '';
                    $edit = true ? 'expense-category-edit' : '';
                    $delete = $flag ? 'expense-category-delete' : '';
                    $showURL = "";
                    $editURL = "";
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
                })
                ->addColumn('expense_category_status_id', function ($row) use ($status) {

                    $statusName = $row->expense_category_status->expense_category_status_name ?? '';
                    $colorClass = $row->expense_category_status->color_class ?? '';

                    // if ($statusName === 'Pending') {
                    $dropDown = '<div class="dropdown">
                    <button class="btn px-2 py-1 btn-outline-' . $colorClass . ' dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        ' . $statusName . '
                    </button>
                    <ul class="dropdown-menu">';

                    foreach ($status as $ecs) {
                        if ($ecs->id == $row->expense_category_status_id) {
                            $dropDown .= '<li><a class="dropdown-item change-status active bg-' . $ecs->color_class . '" href="javascript:void(0);" data-id="' . $row->id . '" data-status="' . $ecs->id . '">' . $ecs->expense_category_status_name . '</a></li>';
                        } else {
                            $dropDown .= '<li><a class="dropdown-item change-status" href="javascript:void(0);" data-id="' . $row->id . '" data-status="' . $ecs->id . '">' . $ecs->expense_category_status_name . '</a></li>';
                        }
                    }

                    $dropDown .= '</ul></div>';
                    return $dropDown;
                    // } else {
                    //     return '<span class="badge bg-label-' . $colorClass . '">' . $statusName . '</span>';
                    // }
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            $status = ExpenseCategoryStatus::select('id', 'expense_category_status_name')->get();
            return view('expensecategory::index', compact('status'));
        }
    }


    public function statusChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:expense_categories,id',
            'status' => 'required|integer|exists:expense_category_statuses,id',
        ], [
            'id.required' => 'ID is required.',
            'id.integer' => 'ID must be an integer.',
            'id.exists' => 'The enter expense categories ID does not exist.',
            'status.required' => 'Enter expense categories status',
            'status.integer' => 'Enter expense categories status',
            'status.exists' => 'The enter expense categories status does not exist.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }

        try {
            ExpenseCategory::where('id', $request->id)->update(['expense_category_status_id' => $request->status]);
            return response()->json(['status_code' => 200, 'message' => 'Status change successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }




    public function create()
    {
        return view('expensecategory::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_category_name' => ['required', Rule::unique('expense_categories')->where(function ($query) use ($request) {
                if (!is_null($request->id)) {
                    return $query->where([['deleted_at', null], ['id', '!=', $request->id]]);
                } else {
                    return $query->where([['deleted_at', null]]);
                }
            })],
            // 'expense_category_status_id' => ['required']
        ], [
            // 'expense_category_status_id.required' => __('expensecategory::message.choose_expense_category_status'),
            'expense_category_name.required' => __('expensecategory::message.enter_expense_category_name'),
            'expense_category_name.unique' => __('expensecategory::message.enter_unique_expense_category_name')
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->id)) {
                $expenseCategory = ExpenseCategory::where('id', $request->id)->first();
                $msg = ' updated ';
            } else {
                $expenseCategory = new ExpenseCategory();
                $msg = ' added ';
            }
            $expenseCategory->expense_category_name = $request->expense_category_name;
            // $expenseCategory->expense_category_status_id = $request->expense_category_status_id;
            $result = $expenseCategory->save();
            if (!is_null($result)) {
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'Expense Category' . $msg . 'successfully.']);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Expense Category' . $msg . 'failed.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('expensecategory::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $expenseCategory = ExpenseCategory::where('id', $id)->first();
            if (!is_null($expenseCategory)) {
                return response()->json(['status_code' => 200, 'message' => 'Edit expense category ', 'result' => $expenseCategory]);
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Expense category not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $expenseCategory = ExpenseCategory::select('id')->where('id', $id)->first();
            if (!is_null($expenseCategory)) {
                //  if (storage_delete_check($expenseCategory->id)) {
                $expenseCategory->delete();
                return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                // } else {
                //     return response()->json(['status_code' => 201, 'message' => 'This Expense Category already use in another module.']);
                // }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Expense Category not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
