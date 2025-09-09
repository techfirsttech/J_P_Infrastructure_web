<?php

namespace Modules\ExpenseMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\ExpenseMaster\Models\ExpenseMaster;
use Modules\PaymentMaster\Models\PaymentMaster;
use Modules\SiteMaster\Models\SiteMaster;
use Modules\User\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\ExpenseCategory\Models\ExpenseCategory;
use Yajra\DataTables\Facades\DataTables;

class ExpenseMasterController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:expense-master-list|expense-master-create', ['only' => ['index', 'store']]);
        $this->middleware('permission:expense-master-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:expense-master-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:expense-master-delete', ['only' => ['destroy']]);
    }

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
            DB::raw("DATE_FORMAT(expense_masters.date, '%d-%m-%Y') as date"),
            'site_masters.site_name',
            'expense_categories.expense_category_name',
            'supervisor.name as supervisor_name',
        )
            ->leftJoin('site_masters', 'expense_masters.site_id', '=', 'site_masters.id')
            ->leftJoin('users as supervisor', 'supervisor.id', '=', 'expense_masters.supervisor_id')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expense_masters.expense_category_id')
            ->when(role_supervisor(), function ($q) {
                return $q->where('user_id', Auth::id());
            })
            ->when(!empty($request->expense_category_id) && $request->expense_category_id !== 'All', function ($query) use ($request) {
                $query->where('expense_masters.expense_category_id', $request->expense_category_id);
            })
            ->when(!empty($request->site_id) && $request->site_id !== 'All', function ($query) use ($request) {
                $query->where('expense_masters.site_id', $request->site_id);
            })
            ->when(!empty($request->supervisor_id) && $request->supervisor_id !== 'All', function ($query) use ($request) {
                $query->where('expense_masters.supervisor_id', $request->supervisor_id);
            })
            ->when(!empty($request->s_date) || !empty($request->e_date), function ($query) use ($request) {
                $startDate = !empty($request->s_date)
                    ? date('Y-m-d 00:00:00', strtotime($request->s_date))
                    : null;

                $endDate = !empty($request->e_date)
                    ? date('Y-m-d 23:59:59', strtotime($request->e_date))
                    : ($startDate ? date('Y-m-d 23:59:59', strtotime($request->s_date)) : null);

                if ($startDate && $endDate) {
                    $query->whereBetween('expense_masters.date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->where('expense_masters.date', '>=', $startDate);
                } elseif ($endDate) {
                    $query->where('expense_masters.date', '<=', $endDate);
                }
            })->orderBy('expense_masters.date', 'DESC');

        if (request()->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $show = '';
                    $edit = 'expense-master-edit';
                    $delete = 'expense-master-delete';
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
                        $filePath = public_path('expense/document/' . $row->document);
                        if (file_exists($filePath)) {
                            $url = asset('expense/document/' . $row->document);
                            $extension = strtolower(pathinfo($row->document, PATHINFO_EXTENSION));
                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                return '<a href="' . $url . '" target="_blank"><img src="' . $url . '" alt="Document" width="40" height="40" /></a>';
                            }
                            if ($extension === 'pdf') {
                                return '<a href="' . $url . '" target="_blank"><i class="fa fa-file-pdf h2 mb-0"></i></a>';
                            }
                        }
                    }
                    return '';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            $expenseCategory = ExpenseCategory::select('id', 'expense_category_name')->get();
            $siteMaster = SiteMaster::get();
            $supervisor = User::select('id', 'name')
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'supervisor');
                })
                ->orderBy('id', 'DESC')
                ->get();
            return view('expensemaster::index', compact('expenseCategory', 'siteMaster', 'supervisor'));
        }
    }


    public function statusChange(Request $request)
    {
        ExpenseMaster::where('id', $request->id)->update(['status' => $request->status]);
        if ($request->status != "Approve") {
            PaymentMaster::where([['model_type', 'Expense'], ['model_id', $request->id]])->delete();
        } else {
            // PaymentMaster::where([['model_type','Expense'],['model_id',$request->id]])->update('deleted_at' => Null);
            PaymentMaster::where([['model_type', 'Expense'], ['model_id', $request->id]])->restore();
        }
        $response = ['status' => true, 'message' => 'Status change successfully.'];
        $response = ['data' => route('expensemaster.index'), 'status' => true, 'message' => ' Status change successfully.'];
        return response()->json($response);
    }


    public function create()
    {
        return view('expensemaster::create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer|exists:site_masters,id',
            'supervisor_id' => 'required|integer|exists:users,id',
            'amount' => 'required',
            'document' => 'nullable|file|mimes:jpeg,jpg,png,gif,pdf|max:4096',
        ], [
            'site_id.required' => __('expensemaster::message.site_id_is_required.'),
            'site_id.integer' => __('expensemaster::message.site_id_must_be_an_integer.'),
            'site_id.exists' => __('expensemaster::message.the_selected_site_master_id_does_not_exist.'),

            'supervisor_id.required' => __('expensemaster::message.supervisor_id_is_required.'),
            'supervisor_id.integer' => __('expensemaster::message.supervisor_id_must_be_an_integer.'),
            'supervisor_id.exists' => __('expensemaster::message.the_selected_supervisor_id_does_not_exist.'),

            'amount.required' => __('expensemaster::message.amount_is_required.'),

            'document.file' => __('expensemaster::message.must_be_file'),
            'document.mimes' => __('expensemaster::message.file_of_type'),
            'document.max' => __('expensemaster::message.file_size'),
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            if (!is_null($request->id)) {
                $expenseMaster = ExpenseMaster::where('id', $request->id)->first();
                $paymentMaster = PaymentMaster::where([['model_type', 'Expense'], ['model_id', $expenseMaster->id]])->first();
                $msg = ' updated ';
            } else {
                $expenseMaster = new ExpenseMaster();
                $paymentMaster = new PaymentMaster();
                $msg = ' added ';
            }
            $yearID = getSelectedYear();
            $expenseMaster->site_id = $request->site_id;
            $expenseMaster->supervisor_id = $request->supervisor_id;
            $expenseMaster->expense_category_id = $request->expense_category_id;
            $expenseMaster->amount = $request->amount;
            $expenseMaster->remark = $request->remark;
            $expenseMaster->date = (!empty($request->date)) ? date('Y-m-d', strtotime($request->date)) : null;
            $expenseMaster->year_id = $yearID;

            if ($request->hasFile('document')) {
                if ($expenseMaster->document) {
                    @unlink(public_path('expense/document/' . $expenseMaster->document));
                    @unlink(public_path('expense/document/thumbnail/' . $expenseMaster->document));
                }

                $expenseMaster->document = $this->uploadToPublicFolder(
                    $request->file('document'),
                    'EXP',
                    'expense/document',
                    'expense/document/thumbnail'
                );
            }

            $result = $expenseMaster->save();


            $paymentMaster->site_id = $expenseMaster->site_id;
            $paymentMaster->supervisor_id = $expenseMaster->supervisor_id;
            $paymentMaster->model_type = "Expense";
            $paymentMaster->model_id = $expenseMaster->id;
            $paymentMaster->amount = $expenseMaster->amount;
            $paymentMaster->status = "Debit";
            $paymentMaster->remark = $expenseMaster->remark;
            $paymentMaster->date = (!empty($expenseMaster->date)) ? date('Y-m-d', strtotime($expenseMaster->date)) : null;;
            $paymentMaster->year_id = $yearID;;
            $paymentMaster->save();

            if (!is_null($result)) {
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'Expense' . $msg . 'successfully.']);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Expense' . $msg . 'failed.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    private function uploadToPublicFolder($file, $imageName, $folder, $thumbFolder)
    {
        $originalExtension = strtolower($file->getClientOriginalExtension());
        $filename = Str::slug($imageName) . '-' . time() . '.' . $originalExtension;

        $originalPath = public_path($folder);
        $thumbPath = public_path($thumbFolder);

        if (!File::exists($originalPath)) {
            File::makeDirectory($originalPath, 0755, true);
        }

        if (!File::exists($thumbPath)) {
            File::makeDirectory($thumbPath, 0755, true);
        }

        $file->move($originalPath, $filename);

        return $filename;
    }

    public function show($id)
    {
        return view('expensemaster::show');
    }

    public function edit($id)
    {
        try {
            $expenseMaster = ExpenseMaster::where('id', $id)->first();
            if (!is_null($expenseMaster)) {
                return response()->json(['status_code' => 200, 'message' => 'Edit Expense ', 'result' => $expenseMaster]);
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Expense not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function update(Request $request, $id) {}

    public function destroy($id)
    {
        // dd($id);
        try {
            $expenseMaster = ExpenseMaster::select('id')->where('id', $id)->first();
            if (!is_null($expenseMaster)) {
                //  if (storage_delete_check($expenseMaster->id)) {
                PaymentMaster::where([['model_type', 'Expense'], ['model_id', $id]])->delete();
                $expenseMaster->delete();
                return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                // } else {
                //     return response()->json(['status_code' => 201, 'message' => 'This Expense already use in another module.']);
                // }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'expenseMaster not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

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
            'payment_masters.remark',
            DB::raw("DATE_FORMAT(payment_masters.date, '%d-%m-%Y') as date"),
            'site_masters.site_name',
            'supervisor.name as supervisor_name',
        )
            ->leftJoin('site_masters', 'payment_masters.site_id', '=', 'site_masters.id')
            ->leftJoin('users as supervisor', 'supervisor.id', '=', 'payment_masters.supervisor_id')
            ->when(role_supervisor(), function ($q) {
                return $q->where('user_id', Auth::id());
            })
            ->when(!empty($request->site_id) && $request->site_id !== 'All', function ($query) use ($request) {
                $query->where('payment_masters.site_id', $request->site_id);
            })
            ->when(!empty($request->supervisor_id) && $request->supervisor_id !== 'All', function ($query) use ($request) {
                $query->where('payment_masters.supervisor_id', $request->supervisor_id);
            })
            ->when(!empty($request->s_date) || !empty($request->e_date), function ($query) use ($request) {
                $startDate = !empty($request->s_date)
                    ? date('Y-m-d 00:00:00', strtotime($request->s_date))
                    : null;

                $endDate = !empty($request->e_date)
                    ? date('Y-m-d 23:59:59', strtotime($request->e_date))
                    : ($startDate ? date('Y-m-d 23:59:59', strtotime($request->s_date)) : null);

                if ($startDate && $endDate) {
                    $query->whereBetween('payment_masters.date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->where('payment_masters.date', '>=', $startDate);
                } elseif ($endDate) {
                    $query->where('payment_masters.date', '<=', $endDate);
                }
            })->orderBy('payment_masters.date', 'DESC');

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
                ->escapeColumns([])
                ->make(true);
        } else {
            $siteMaster = SiteMaster::orderBy('site_name', 'ASC')->get();
            $supervisor = User::whereHas('roles', fn($q) => $q->where('name', 'Supervisor'))->orderBy('name', 'ASC')->get();
            return view('expensemaster::ledger', compact('siteMaster', 'supervisor'));
        }
    }

    public function paymentLedgerPdf(Request $request)
    {
        try {
            $query = PaymentMaster::select(
                'payment_masters.id',
                'payment_masters.site_id',
                'payment_masters.supervisor_id',
                'payment_masters.model_type',
                'payment_masters.model_id',
                'payment_masters.amount',
                'payment_masters.status',
                'payment_masters.remark',
                DB::raw("DATE_FORMAT(payment_masters.date, '%d-%m-%Y') as date"),
                'site_masters.site_name',
                'supervisor.name as supervisor_name',
            )
                ->leftJoin('site_masters', 'payment_masters.site_id', '=', 'site_masters.id')
                ->leftJoin('users as supervisor', 'supervisor.id', '=', 'payment_masters.supervisor_id')
                ->when(role_supervisor(), function ($q) {
                    return $q->where('user_id', Auth::id());
                })
                ->when(!empty($request->filter_site_id) && $request->filter_site_id !== 'All', function ($query) use ($request) {
                    $query->where('payment_masters.site_id', $request->filter_site_id);
                })
                ->when(!empty($request->filter_supervisor_id) && $request->filter_supervisor_id !== 'All', function ($query) use ($request) {
                    $query->where('payment_masters.supervisor_id', $request->filter_supervisor_id);
                })
                ->when(!empty($request->s_date) || !empty($request->e_date), function ($query) use ($request) {
                    $startDate = !empty($request->s_date)
                        ? date('Y-m-d 00:00:00', strtotime($request->s_date))
                        : null;

                    $endDate = !empty($request->e_date)
                        ? date('Y-m-d 23:59:59', strtotime($request->e_date))
                        : ($startDate ? date('Y-m-d 23:59:59', strtotime($request->s_date)) : null);

                    if ($startDate && $endDate) {
                        $query->whereBetween('payment_masters.date', [$startDate, $endDate]);
                    } elseif ($startDate) {
                        $query->where('payment_masters.date', '>=', $startDate);
                    } elseif ($endDate) {
                        $query->where('payment_masters.date', '<=', $endDate);
                    }
                })->orderBy('payment_masters.date', 'DESC')->get();

            if (!is_null($query)) {
                $pdf = Pdf::loadView('expensemaster::ledger-pdf', compact('query'));

                $filename = 'ledger-' . time() . '.pdf';

                $folder = 'ledger/';
                $path = public_path($folder);
                $fullPath = $path . '/' . $filename;

                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $files = glob($path . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                
                $pdf->save($fullPath);
                $fileUrl = asset($folder . $filename);
                $response = ['status_code' => 200, 'message' => 'Pdf generated successfully.', 'download_url' => $fileUrl];
            } else {
                $response = ['status_code' => 500, 'message' => 'Pdf can not generated.'];
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(array('status_code' => 500, 'message' => 'Something went wrong. Please try again.'));
        }
    }
}
