<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PaymentMaster\Models\PaymentMaster;
use Modules\SiteMaster\Models\SiteMaster;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
       

        $siteMaster = PaymentMaster::select(
        'payment_masters.site_id',
        'site_masters.site_name',
        DB::raw("SUM(CASE WHEN payment_masters.status = 'Credit' THEN payment_masters.amount ELSE 0 END) as total_credit"),
        DB::raw("SUM(CASE WHEN payment_masters.status = 'Debit' THEN payment_masters.amount ELSE 0 END) as total_debit"),
        DB::raw("SUM(CASE WHEN payment_masters.status = 'Credit' THEN payment_masters.amount ELSE 0 END) - 
                 SUM(CASE WHEN payment_masters.status = 'Debit' THEN payment_masters.amount ELSE 0 END) as closing_balance")
    )
    ->leftJoin('site_masters', 'payment_masters.site_id', '=', 'site_masters.id')
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
    })
    ->whereNotNull('payment_masters.site_id')
    ->groupBy('payment_masters.site_id', 'site_masters.site_name')
    ->orderBy('site_masters.site_name', 'ASC')
    ->get();


            // dd($query);
        return view('Dashboard::index', compact('siteMaster'));
    }

    /**
     * Show the form for creating a new resource.
     */
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
