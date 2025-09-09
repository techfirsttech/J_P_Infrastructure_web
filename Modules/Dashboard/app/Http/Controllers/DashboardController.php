<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\PaymentMaster\Models\PaymentMaster;
use Modules\SiteMaster\Models\SiteMaster;
use Modules\SiteMaster\Models\SiteSupervisor;
use Modules\User\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('Dashboard::index');
    }

    public function dataFilter(Request $request)
    {
        $filterType = $request->input('filter_type', 'week');
        $applyDateFilter = function ($query) use ($filterType, $request) {
            switch ($filterType) {
                case 'today':
                    return $query->whereDate('payment_masters.date', Carbon::today());
                case 'week':
                    return $query->whereBetween('payment_masters.date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                case 'month':
                    return $query->whereMonth('payment_masters.date', Carbon::now()->month)
                        ->whereYear('payment_masters.date', Carbon::now()->year);
                case 'year':
                    return $query->whereYear('payment_masters.date', Carbon::now()->year);
                case 'custom':
                    try {
                        $startDate = $request->s_date ? Carbon::parse($request->s_date)->startOfDay() : null;
                        $endDate = $request->e_date ? Carbon::parse($request->e_date)->endOfDay() : null;

                        if ($startDate && $endDate) {
                            $query->whereBetween('payment_masters.date', [$startDate, $endDate]);
                        } elseif ($startDate) {
                            $query->where('payment_masters.date', '>=', $startDate);
                        } elseif ($endDate) {
                            $query->where('payment_masters.date', '<=', $endDate);
                        }
                    } catch (\Exception $e) {
                        return response()->json(['status_code' => 400, 'message' => 'Invalid date format']);
                    }
                    break;
            }
            return $query;
        };

        // SITE-WISE OUTSTANDING (Include all sites)
        $sitePayment = PaymentMaster::select(
            'site_id',
            DB::raw("SUM(CASE WHEN status = 'Credit' THEN amount ELSE 0 END) AS credits"),
            DB::raw("SUM(CASE WHEN status = 'Debit' THEN amount ELSE 0 END) AS debits")
        )->when(true, $applyDateFilter)
            ->groupBy('site_id');

        $siteData = SiteMaster::leftJoinSub($sitePayment, 'pm', function ($join) {
            $join->on('site_masters.id', '=', 'pm.site_id');
        })
            ->select(
                'site_masters.id AS site_id',
                'site_masters.site_name',
                DB::raw('COALESCE(pm.credits, 0) AS total_credit'),
                DB::raw('COALESCE(pm.debits, 0) AS total_debit'),
                DB::raw('(COALESCE(pm.credits, 0) - COALESCE(pm.debits, 0)) AS closing_balance')
            )
            ->get();


        // SUPERVISOR-WISE OUTSTANDING (Include all users)
        $supervisorPayment = PaymentMaster::select(
            'supervisor_id',
            DB::raw("SUM(CASE WHEN status = 'Credit' THEN amount ELSE 0 END) AS credits"),
            DB::raw("SUM(CASE WHEN status = 'Debit' THEN amount ELSE 0 END) AS debits")
        )->when(true, $applyDateFilter)
            ->groupBy('supervisor_id');

        $supervisorData = User::leftJoinSub($supervisorPayment, 'pm', function ($join) {
            $join->on('users.id', '=', 'pm.supervisor_id');
        })
            ->select(
                'users.id AS supervisor_id',
                'users.name AS supervisor_name',
                DB::raw('COALESCE(pm.credits, 0) AS total_credit'),
                DB::raw('COALESCE(pm.debits, 0) AS total_debit'),
                DB::raw('(COALESCE(pm.credits, 0) - COALESCE(pm.debits, 0)) AS closing_balance')
            )
            ->whereHas('roles', function ($q) {
                $q->where('name', 'supervisor');
            })
            ->orderBy('users.name', 'ASC')
            ->get();

        // Render to view or return JSON response
        $html = view('Dashboard::table_render', compact('siteData', 'supervisorData'))->render();

        return response()->json(['status_code' => 200, 'html' => $html, 'message' => 'Data found']);
    }


    public function dataModel(Request $request)
    {
        // Step 1: Validate and fetch supervisor IDs
        $supervisorId = SiteSupervisor::where('site_master_id', $request->id)
            ->pluck('user_id')
            ->toArray();

        if (empty($supervisorId)) {
            return response()->json(['status_code' => 404, 'message' => 'No supervisor found for the given site']);
        }

        // Step 2: Start query
        $query = PaymentMaster::select(
            'payment_masters.supervisor_id',
            'users.name as supervisor_name',
            DB::raw("SUM(CASE WHEN payment_masters.status = 'Credit' THEN payment_masters.amount ELSE 0 END) as total_credit"),
            DB::raw("SUM(CASE WHEN payment_masters.status = 'Debit' THEN payment_masters.amount ELSE 0 END) as total_debit"),
            DB::raw("
            SUM(CASE WHEN payment_masters.status = 'Credit' THEN payment_masters.amount ELSE 0 END) - 
            SUM(CASE WHEN payment_masters.status = 'Debit' THEN payment_masters.amount ELSE 0 END) as closing_balance
        ")
        )
            ->leftJoin('users', 'payment_masters.supervisor_id', '=', 'users.id')
            ->whereIn('payment_masters.supervisor_id', $supervisorId);

        if ($request->filter_type) {
            switch ($request->filter_type) {
                case 'today':
                    $query->whereDate('payment_masters.date', Carbon::today());
                    break;
                case 'week':
                    $query->whereBetween('payment_masters.date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]);
                    break;
                case 'month':
                    $query->whereMonth('payment_masters.date', Carbon::now()->month)
                        ->whereYear('payment_masters.date', Carbon::now()->year);
                    break;
                case 'year':
                    $query->whereYear('payment_masters.date', Carbon::now()->year);
                    break;
                case 'custom':
                    try {
                        $startDate = $request->s_date ? Carbon::parse($request->s_date)->startOfDay() : null;
                        $endDate = $request->e_date ? Carbon::parse($request->e_date)->endOfDay() : null;

                        if ($startDate && $endDate) {
                            $query->whereBetween('payment_masters.date', [$startDate, $endDate]);
                        } elseif ($startDate) {
                            $query->where('payment_masters.date', '>=', $startDate);
                        } elseif ($endDate) {
                            $query->where('payment_masters.date', '<=', $endDate);
                        }
                    } catch (\Exception $e) {
                        return response()->json(['status_code' => 400, 'message' => 'Invalid date format']);
                    }
                    break;
            }
        }

        $query->groupBy('payment_masters.site_id', 'payment_masters.supervisor_id');

        $supervisor = $query->get();

        if ($supervisor->count() > 0) {
            $html = view('Dashboard::model_render', compact('supervisor'))->render();
            return response()->json([
                'status_code' => 200,
                'html' => $html,
                'message' => 'Data found'
            ]);
        } else {
            return response()->json([
                'status_code' => 404,
                'message' => 'No data found'
            ]);
        }
    }


    public function create()
    {
        return view('Dashboard::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('Dashboard::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('Dashboard::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
