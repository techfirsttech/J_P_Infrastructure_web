<?php

namespace Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PaymentMaster\Models\PaymentMaster;
use Modules\SiteMaster\Models\SiteMaster;
use Modules\User\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Modules\Attendance\Models\Attendance;
use Modules\SiteMaster\Models\SiteSupervisor;

class ReportController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:report-ledger-list', ['only' => ['ledgerReport', 'ledgerPdf']]);
        $this->middleware('permission:report-attendance-list', ['only' => ['attendanceReport', 'attendancePdf']]);
    }

    public function index(Request $request)
    {
        //
    }

    public function ledgerReport(Request $request)
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

        if (request()->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
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
            $url = route('report-ledger');
            $urlPdf = route('report-ledger-pdf');
            return view('report::ledger', compact('siteMaster', 'supervisor', 'url', 'urlPdf'));
        }
    }

    public function ledgerPdf(Request $request)
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
                    return $q->where('payment_masters.supervisor_id', Auth::id());
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
                $pdf = Pdf::loadView('report::ledger-pdf', compact('query'));

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

    public function attendanceReport(Request $request)
    {
        $query = Attendance::select(
            'attendances.site_id',
            'attendances.contractor_id',
            'attendances.labour_id',
            DB::raw("SUM(amount) as salary"),
            DB::raw("SUM(CASE WHEN attendances.type = 'Full' THEN 1 ELSE 0 END) as full_count"),
            DB::raw("SUM(CASE WHEN attendances.type = 'Half' THEN 1 ELSE 0 END) as half_count"),
            DB::raw("SUM(CASE WHEN attendances.type = 'Absent' THEN 1 ELSE 0 END) as absent_count"),
            DB::raw("COUNT(*) as total_days")
        )
            ->with('labour', 'contractor', 'site', 'user')
            ->when(!role_super_admin(), function ($q) {
                return $q->where('attendances.supervisor_id', Auth::id());
            })
            ->when(!empty($request->leave_type) && $request->leave_type !== 'All', function ($query) use ($request) {
                $query->where('attendances.type', $request->leave_type);
            })
            ->when(!empty($request->site_id) && $request->site_id !== 'All', function ($query) use ($request) {
                $query->where('attendances.site_id', $request->site_id);
            })
            ->when(!empty($request->contractor_id) && $request->contractor_id !== 'All', function ($query) use ($request) {
                $query->where('attendances.contractor_id', $request->contractor_id);
            })
            ->when(!empty($request->labour_id) && $request->labour_id !== 'All', function ($query) use ($request) {
                $query->where('attendances.labour_id', $request->labour_id);
            })
            ->when(!empty($request->s_date) || !empty($request->e_date), function ($query) use ($request) {
                $startDate = !empty($request->s_date)
                    ? date('Y-m-d 00:00:00', strtotime($request->s_date))
                    : null;

                $endDate = !empty($request->e_date)
                    ? date('Y-m-d 23:59:59', strtotime($request->e_date))
                    : ($startDate ? date('Y-m-d 23:59:59', strtotime($request->s_date)) : null);

                if ($startDate && $endDate) {
                    $query->whereBetween('attendances.date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->where('attendances.date', '>=', $startDate);
                } elseif ($endDate) {
                    $query->where('attendances.date', '<=', $endDate);
                }
            })
            ->groupBy('attendances.labour_id', 'attendances.site_id', 'attendances.contractor_id');

        if (request()->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('labour_name', function ($row) {
                    return $row->labour?->labour_name ?? 'N/A';
                })
                ->addColumn('contractor_name', function ($row) {
                    return $row->contractor?->contractor_name ?? 'N/A';
                })
                ->addColumn('site_name', function ($row) {
                    return $row->site?->site_name ?? 'N/A';
                })
                ->addColumn('user_name', function ($row) {
                    return $row->user?->name ?? 'N/A';
                })
                ->addColumn('full_count', function ($row) {
                    return $row->full_count;
                })
                ->addColumn('half_count', function ($row) {
                    return $row->half_count;
                })
                ->addColumn('absent_count', function ($row) {
                    return $row->absent_count;
                })
                ->addColumn('total_days', function ($row) {
                    return $row->total_days;
                })
                ->addColumn('salary', function ($row) {
                    return number_format($row->salary, 2);
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            $site = SiteMaster::select('id', 'site_name')->get();
            $url = route('report-attendance');
            $urlPdf = route('report-attendance-pdf');
            return view('report::attendance-report', compact('site', 'url', 'urlPdf'));
        }
    }

    public function attendancePdf(Request $request)
    {
        try {
            $query = Attendance::select(
                'attendances.site_id',
                'attendances.contractor_id',
                'attendances.labour_id',
                DB::raw("SUM(amount) as salary"),
                DB::raw("SUM(CASE WHEN attendances.type = 'Full' THEN 1 ELSE 0 END) as full_count"),
                DB::raw("SUM(CASE WHEN attendances.type = 'Half' THEN 1 ELSE 0 END) as half_count"),
                DB::raw("SUM(CASE WHEN attendances.type = 'Absent' THEN 1 ELSE 0 END) as absent_count"),
                DB::raw("COUNT(*) as total_days")
            )
                ->with('labour', 'contractor', 'site', 'user')
                ->when(!role_super_admin(), function ($q) {
                    return $q->where('attendances.supervisor_id', Auth::id());
                })
                ->when(!empty($request->site_id) && $request->site_id !== 'All', function ($query) use ($request) {
                    $query->where('attendances.site_id', $request->site_id);
                })
                ->when(!empty($request->contractor_id) && $request->contractor_id !== 'All', function ($query) use ($request) {
                    $query->where('attendances.contractor_id', $request->contractor_id);
                })
                ->when(!empty($request->labour_id) && $request->labour_id !== 'All', function ($query) use ($request) {
                    $query->where('attendances.labour_id', $request->labour_id);
                })
                ->when(!empty($request->s_date) || !empty($request->e_date), function ($query) use ($request) {
                    $startDate = !empty($request->s_date)
                        ? date('Y-m-d 00:00:00', strtotime($request->s_date))
                        : null;

                    $endDate = !empty($request->e_date)
                        ? date('Y-m-d 23:59:59', strtotime($request->e_date))
                        : ($startDate ? date('Y-m-d 23:59:59', strtotime($request->s_date)) : null);

                    if ($startDate && $endDate) {
                        $query->whereBetween('attendances.date', [$startDate, $endDate]);
                    } elseif ($startDate) {
                        $query->where('attendances.date', '>=', $startDate);
                    } elseif ($endDate) {
                        $query->where('attendances.date', '<=', $endDate);
                    }
                })
                ->groupBy('attendances.labour_id', 'attendances.site_id', 'attendances.contractor_id')
                ->get();


            if (count($query) > 0) {
                $pdf = Pdf::loadView('report::attendance-pdf', compact('query'));
                $filename = 'attendance-' . time() . '.pdf';
                $folder = 'attendance/';
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

    public function getSupervisor(Request $request)
    {
        $siteSuperwiser = SiteSupervisor::where('site_master_id', $request->id)->pluck('user_id');
        $supervisor = User::select('id', 'name')->whereIn('id', $siteSuperwiser)->get();
        return response()->json(['status_code' => 200, 'result' => $supervisor]);
    }
}
