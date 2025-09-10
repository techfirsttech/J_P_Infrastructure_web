<?php

namespace Modules\ExpenseMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\ExpenseCategory\Models\ExpenseCategory;
use Modules\ExpenseMaster\Models\ExpenseMaster;
use Modules\PaymentMaster\Models\PaymentMaster;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\User\Models\User;

class ExpenseMasterApiController extends Controller
{

    public function index(Request $request)
    {
        try {
            $query = ExpenseMaster::select(
                'expense_masters.id',
                'expense_masters.site_id',
                'expense_masters.supervisor_id',
                'expense_masters.expense_category_id',
                'expense_masters.amount',
                'expense_masters.document',
                'expense_masters.remark',
                'expense_masters.status',
                DB::raw("DATE_FORMAT(expense_masters.date, '%d-%m-%Y') as date"),
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
            // if ($role && $role->name === 'Supervisor') {
            //     $query->where(function ($q) use ($user) {
            //         $q->where('expense_masters.user_id', $user->id)
            //             ->orWhere('expense_masters.supervisor_id', $user->id);
            //     });
            // }

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

            // Total amount with format
            $totalAmount = (clone $query)->sum('expense_masters.amount');
            $totalAmount = floor($totalAmount) == $totalAmount ? (int)$totalAmount : (float)$totalAmount;

            // Pagination and formatting
            $expenseMaster = $query->orderBy('expense_masters.id', 'DESC')->simplePaginate(30);

            $formattedExpenseMaster = collect($expenseMaster->items())->map(function ($item) {
                // Format amount
                if (floor($item->amount) == $item->amount) {
                    $item->amount = (int)$item->amount;
                } else {
                    $item->amount = (float)$item->amount;
                }

                // Add full document URL
                $item->document = !empty($item->document)
                    ? url('public/expense/document/' . $item->document)
                    : null;

                return $item;
            });

            return response([
                'status' => true,
                'message' => 'Expense Master List',
                'total_amount' => $totalAmount,
                'expense_master' => $formattedExpenseMaster
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
            $expenseCategoryName = ExpenseCategory::select('expense_category_name')->where('id', $request->expense_category_id)->value('expense_category_name');
            // dd($expenseCategoryName);
            $expenseMaster = new ExpenseMaster();
            $expenseMaster->site_id = $request->site_id;
            // $expenseMaster->supervisor_id = $request->supervisor_id;
            $expenseMaster->supervisor_id = Auth::id();
            $expenseMaster->expense_category_id = $request->expense_category_id;
            $expenseMaster->amount = $request->amount;
            $expenseMaster->remark = $request->remark;
            $expenseMaster->date = (!empty($request->date)) ? date('Y-m-d', strtotime($request->date)) : null;
            $expenseMaster->year_id = $yearID;

            if ($request->hasFile('document')) {
                $upload = $this->uploadToPublicFolder(
                    $request->file('document'),
                    $expenseCategoryName,
                    'expense/document',
                    'expense/document/thumbnail'
                );

                $expenseMaster->document = $upload['original'];
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
            $paymentMaster->date = (!empty($expenseMaster->date)) ? date('Y-m-d', strtotime($expenseMaster->date)) : null;;
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
            dd($e);
            DB::rollBack();
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
            $expenseMaster->date = (!empty($request->date)) ? date('Y-m-d', strtotime($request->date)) : null;

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
            $paymentMaster->date = (!empty($expenseMaster->date)) ? date('Y-m-d', strtotime($expenseMaster->date)) : null;;
            $paymentMaster->year_id = $yearID;
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
            $expenseCategoryDropdown = ExpenseCategory::select('id', 'expense_category_name')->orderBy('expense_category_name', 'asc')->get();
            return response(['status' => true, 'message' => 'Expense Category Dropdown', 'expense_category_dropdown' => $expenseCategoryDropdown], 200);
        } catch (Exception $e) {
            dd($e);
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function paymentLedger(Request $request)
    {
        try {
            $query = PaymentMaster::select(
                'payment_masters.id',
                'payment_masters.site_id',
                'payment_masters.supervisor_id',
                'payment_masters.to_supervisor_id',
                'payment_masters.model_type',
                'payment_masters.model_id',
                'payment_masters.amount',
                'payment_masters.remark',
                DB::raw("DATE_FORMAT(payment_masters.date, '%d-%m-%Y') as date"),
                'payment_masters.status',
                // 'site_masters.site_name',
                'supervisor.name as supervisor_name',
                // DB::raw("CONCAT_WS('-', site_masters.site_name, supervisor.name) as site_name"),
                'to_supervisor.name as to_supervisor_name'
            )
                ->leftJoin('site_masters', 'payment_masters.site_id', '=', 'site_masters.id')
                ->leftJoin('users as supervisor', 'supervisor.id', '=', 'payment_masters.supervisor_id')
                ->leftJoin('users as to_supervisor', 'to_supervisor.id', '=', 'payment_masters.to_supervisor_id')
                ->when(role_supervisor(), function ($q) {
                    return $q->where('payment_masters.supervisor_id', Auth::id());
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

            if (role_supervisor()) {
                $query->addSelect(DB::raw("CONCAT_WS('-', site_masters.site_name, to_supervisor.name) as site_name"));
            } else {
                $query->addSelect(DB::raw("CONCAT_WS('-', site_masters.site_name, supervisor.name) as site_name"));
            }
        
            $totalExpense = (clone $query)->where('model_type', 'Expense')->sum('payment_masters.amount');
            $totalIncome = (clone $query)->where('model_type', 'Income')->sum('payment_masters.amount');

            // Format totalExpense and totalIncome to remove trailing .000 if any
            if (floor($totalExpense) == $totalExpense) {
                $totalExpense = (int) $totalExpense;
            } else {
                $totalExpense = (float) $totalExpense;
            }

            if (floor($totalIncome) == $totalIncome) {
                $totalIncome = (int) $totalIncome;
            } else {
                $totalIncome = (float) $totalIncome;
            }


            $payment = $query->orderBy('payment_masters.id', 'DESC')->simplePaginate(30);

            // Format amount to remove trailing zeros (like 100.000 → 100)
            $formattedPayments = collect($payment->items())->map(function ($item) {
                if (floor($item->amount) == $item->amount) {
                    $item->amount = (int) $item->amount;  // Convert to int to remove decimal .000
                } else {
                    $item->amount = (float) $item->amount; // Keep decimals if present
                }
                return $item;
            });

            return response([
                'status' => true,
                'message' => 'Expense Master List',
                'total_expense' => $totalExpense,
                'total_income' => $totalIncome,
                'closing_balance' => $totalIncome -  $totalExpense,
                'expense_master' => $formattedPayments
            ], 200);
        } catch (\Exception $e) {
            // For debugging, better to log error instead of dd in production
            // dd($e);
            return response([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function statusChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:expense_masters,id',
            'status' => 'required',
        ], [
            'id.required' => 'ID is required.',
            'id.integer' => 'ID must be an integer.',
            'id.exists' => 'The enter expense master ID does not exist.',
            'status.required' => 'Enter expense status',
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        try {
            ExpenseMaster::where('id', $request->id)->update(['status' => $request->status]);

            return response()->json(['status_code' => 200, 'message' => 'Status change successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    // private function uploadToPublicFolder($file, $imageName, $folder, $thumbFolder)
    // {
    //     $originalExtension = $file->getClientOriginalExtension();
    //     $filename = Str::slug($imageName) . '-' . time() . '.' . $originalExtension;

    //     $originalPath = public_path($folder);
    //     $thumbPath = public_path($thumbFolder);

    //     if (!File::exists($originalPath)) {
    //         File::makeDirectory($originalPath, 0755, true);
    //     }
    //     if (!File::exists($thumbPath)) {
    //         File::makeDirectory($thumbPath, 0755, true);
    //     }

    //     // Save original without modification
    //     $file->move($originalPath, $filename);

    //     // Generate thumbnail (always webp to save space)
    //     $tempPath = $originalPath . '/' . $filename;
    //     $src = imagecreatefromstring(file_get_contents($tempPath));

    //     if (!$src) return $filename;

    //     $trueColor = imagecreatetruecolor(200, 200);
    //     list($width, $height) = getimagesize($tempPath);
    //     imagecopyresampled($trueColor, $src, 0, 0, 0, 0, 200, 200, $width, $height);

    //     // Save thumbnail as webp
    //     $thumbName = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
    //     imagewebp($trueColor, $thumbPath . '/' . $thumbName, 90);

    //     imagedestroy($src);
    //     imagedestroy($trueColor);

    //     return $filename;
    // }

    private function uploadToPublicFolder($file, $fileName, $folder, $thumbFolder)
    {
        $originalExtension = strtolower($file->getClientOriginalExtension());
        $baseName = Str::slug($fileName) . '-' . time();
        $filename = $baseName . '.' . $originalExtension;

        $originalPath = public_path($folder);
        $thumbPath = public_path($thumbFolder);

        if (!File::exists($originalPath)) {
            File::makeDirectory($originalPath, 0755, true);
        }
        if (!File::exists($thumbPath)) {
            File::makeDirectory($thumbPath, 0755, true);
        }

        // Move file to original path
        $file->move($originalPath, $filename);
        $tempPath = $originalPath . '/' . $filename;

        // If PDF → only save original (no thumbnail)
        if ($originalExtension === 'pdf') {
            return [
                'original' => $filename,
                'thumbnail' => null
            ];
        }

        // If Image → generate thumbnail
        $src = null;
        switch ($originalExtension) {
            case 'jpg':
            case 'jpeg':
                $src = imagecreatefromjpeg($tempPath);
                break;
            case 'png':
                $src = imagecreatefrompng($tempPath);
                break;
            case 'gif':
                $src = imagecreatefromgif($tempPath);
                break;
            default:
                $src = imagecreatefromstring(file_get_contents($tempPath));
        }

        if (!$src) {
            return [
                'original' => $filename,
                'thumbnail' => null
            ];
        }

        // Resize to 200x200
        list($width, $height) = getimagesize($tempPath);
        $thumbWidth = 200;
        $thumbHeight = 200;

        $trueColor = imagecreatetruecolor($thumbWidth, $thumbHeight);

        // Transparency support
        imagealphablending($trueColor, false);
        imagesavealpha($trueColor, true);

        imagecopyresampled($trueColor, $src, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        // Save thumbnail as webp
        $thumbName = $baseName . '.webp';
        imagewebp($trueColor, $thumbPath . '/' . $thumbName, 90);

        imagedestroy($src);
        imagedestroy($trueColor);

        return [
            'original' => $filename,
            'thumbnail' => $thumbName
        ];
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
                'payment_masters.remark',
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

            return response()->json([
                'status' => true,
                'message' => 'Ledger PDF generated successfully.',
                'url' => $fileUrl
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
