<?php

namespace Modules\ExpenseMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
                    $edit = 'expense-master-edit';
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
                        $url = url('public/expense/document/' . $row->document);
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
            $expenseCategory = ExpenseCategory::select('id', 'expense_category_name')->get();
            $siteMaster = SiteMaster::get();
            $supervisor = User::select('id', 'name')
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'supervisor');
                })
                ->orderBy('id', 'DESC')
                ->get();
            return view('expensemaster::index', compact('expenseCategory', 'siteMaster', 'supervisor'));

            // return view('expensemaster::index');
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer|exists:site_masters,id',
            'supervisor_id' => 'required|integer|exists:users,id',
            'amount' => 'required',
        ], [
            'site_id.required' => __('expensemaster::message.site_id_is_required.'),
            'site_id.integer' => __('expensemaster::message.site_id_must_be_an_integer.'),
            'site_id.exists' => __('expensemaster::message.the_selected_site_master_id_does_not_exist.'),

            'supervisor_id.required' => __('expensemaster::message.supervisor_id_is_required.'),
            'supervisor_id.integer' => __('expensemaster::message.supervisor_id_must_be_an_integer.'),
            'supervisor_id.exists' => __('expensemaster::message.the_selected_supervisor_id_does_not_exist.'),

            'amount.required' => __('expensemaster::message.amount_is_required.'),
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
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
            // if ($request->hasFile('document')) {
            //     $file = $request->file('document');
            //     $base64 = 'data:image/' . $file->extension() . ';base64,' . base64_encode(file_get_contents($file));
            //     $uploadResponse = imageUploadFromBase64([
            //         'base64' => $base64,
            //         'fileName' => 'expense-document',
            //         'folder' => 'upload/expense/documents',
            //         'thumfolder' => 'upload/expense/documents/thumbs',
            //     ]);
            //     if ($uploadResponse) {
            //         $expenseMaster->document = $uploadResponse['original'];
            //     }
            // }
            $result = $expenseMaster->save();

            if ($request->hasFile('document')) {
                if ($expenseMaster->document) {
                    @unlink(public_path('expense/document/' . $expenseMaster->document));
                    @unlink(public_path('expense/document/thumbnail/' . $expenseMaster->document));
                }

                $expenseMaster->document = $this->uploadToPublicFolder(
                    $request->file('document'),
                    $request->company_name,
                    'expense/document',
                    'expense/document/thumbnail'
                );
            }
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
            dd($e);
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
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
        // return view('expensemaster::edit');
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
            $sites = SiteMaster::orderBy('site_name','ASC')->get();
            $supervisors = User::whereHas('roles', fn($q) => $q->where('name', 'Supervisor'))->orderBy('name','ASC')->get();

            return view('expensemaster::ledger', compact('sites', 'supervisors', 'totalExpense', 'totalIncome', 'closingBalance'));
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
                DB::raw("DATE_FORMAT(payment_masters.date, '%d-%m-%Y') as date"),
                'payment_masters.status',
                'site_masters.site_name',
                'users.name as supervisor_name'
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

            // Fetch data without pagination for PDF
            $payments = $query->orderBy('payment_masters.id', 'DESC')->get();

            // Format amount
            $payments->transform(function ($item) {
                $item->amount = floor($item->amount) == $item->amount ? (int)$item->amount : (float)$item->amount;
                return $item;
            });

            // Totals
            $totalExpense = $query->clone()->where('model_type', 'Expense')->sum('payment_masters.amount');
            $totalIncome = $query->clone()->where('model_type', 'Income')->sum('payment_masters.amount');
            $totalExpense = floor($totalExpense) == $totalExpense ? (int)$totalExpense : (float)$totalExpense;
            $totalIncome = floor($totalIncome) == $totalIncome ? (int)$totalIncome : (float)$totalIncome;

            // PDF data
            $data = [
                'title' => 'Ledger Report',
                'payments' => $payments,
                'total_expense' => $totalExpense,
                'total_income' => $totalIncome,
                'closing_balance' => $totalIncome - $totalExpense,
                'filters' => $request->all()
            ];

            $pdf = Pdf::loadView('expensemaster::ledger-pdf', $data);
            $pdf->setPaper('A4', 'portrait');

            $fileName = 'ledger-' . Str::random(10) . '.pdf';
            $folder = 'ledger';
            $fileRelativePath = $folder . '/' . $fileName;

            Storage::disk('public')->put($fileRelativePath, $pdf->output());
            $fileUrl = url('public/storage/' . $fileRelativePath);

            if (!is_null($query)) {
                $response = ['status_code' => 200, 'message' => 'Pdf generated successfully.', 'file_url' => $fileUrl, 'file_name' => $fileName];
            } else {
                $response = ['status_code' => 500, 'message' => 'Pdf can not generated.'];
            }
            return response()->json($response);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(array('status_code' => 500, 'message' => 'Something went wrong. Please try again.'));
        }
    }

     private function uploadToPublicFolder($file, $imageName, $folder, $thumbFolder)
    {
        $originalExtension = $file->getClientOriginalExtension();
        $filename = Str::slug($imageName) . '-' . time() . '.' . $originalExtension;

        $originalPath = public_path($folder);
        $thumbPath = public_path($thumbFolder);

        if (!File::exists($originalPath)) {
            File::makeDirectory($originalPath, 0755, true);
        }
        if (!File::exists($thumbPath)) {
            File::makeDirectory($thumbPath, 0755, true);
        }

        // Save original without modification
        $file->move($originalPath, $filename);

        // Generate thumbnail (always webp to save space)
        $tempPath = $originalPath . '/' . $filename;
        $src = imagecreatefromstring(file_get_contents($tempPath));

        if (!$src) return $filename;

        $trueColor = imagecreatetruecolor(200, 200);
        list($width, $height) = getimagesize($tempPath);
        imagecopyresampled($trueColor, $src, 0, 0, 0, 0, 200, 200, $width, $height);

        // Save thumbnail as webp
        $thumbName = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        imagewebp($trueColor, $thumbPath . '/' . $thumbName, 90);

        imagedestroy($src);
        imagedestroy($trueColor);

        return $filename;
    }
}
