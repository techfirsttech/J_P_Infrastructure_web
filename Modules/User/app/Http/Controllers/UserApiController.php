<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ExpenseMaster\Models\ExpenseMaster;
use Modules\IncomeMaster\Models\IncomeMaster;
use Modules\Labour\Models\Labour;
use Modules\SiteMaster\Models\SiteSupervisor;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;

class UserApiController extends Controller
{

    public function index()
    {
        return view('user::index');
    }

    public function create()
    {
        return view('user::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('user::show');
    }

    public function edit($id)
    {
        return view('user::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}

    public function supervisorList(Request $request)
    {
        try {
            if (!is_null($request->site_id)) {
                $supervisor = SiteSupervisor::select(
                    // 'site_supervisors.id',
                    // 'site_supervisors.site_master_id',
                    // 'site_supervisors.user_id',
                    // 'users.name'
                    'site_supervisors.id as site_supervisor_id',
                    'site_supervisors.site_master_id',
                    'site_supervisors.user_id as id',
                    'users.name'
                )
                    ->where('site_supervisors.site_master_id', $request->site_id)
                    ->leftJoin('users', 'site_supervisors.user_id', '=', 'users.id')
                    ->orderBy('site_supervisors.id', 'DESC')
                    ->get();
            } else {
                $supervisor = User::select('id', 'name')
                    ->whereHas('roles', function ($q) {
                        $q->where('name', 'supervisor');
                    })
                    ->orderBy('users.name', 'asc')
                    ->get();
            }
            return response(['status' => true, 'message' => 'Supervisor List', 'result' => $supervisor], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
    public function siteSupervisorList(Request $request)
    {
        try {
            $supervisor = SiteSupervisor::select(
                'site_supervisors.id as site_supervisor_id',
                'site_supervisors.site_master_id',
                'site_supervisors.user_id as id',
                'users.name'
            )
                ->where('site_supervisors.site_master_id', $request->site_id)
                ->leftJoin('users', 'site_supervisors.user_id', '=', 'users.id')
                ->orderBy('users.name', 'asc')
                ->get();
            // $supervisor = User::select('id', 'name')->orderBy('id', 'DESC')->get();
            return response(['status' => true, 'message' => 'Supervisor List', 'result' => $supervisor], 200);
        } catch (Exception $e) {
            dd($e);
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
    public function userDropdown(Request $request)
    {
        try {
            $user = User::select('id','name')
                ->orderBy('name', 'asc')
                ->get();
            // $supervisor = User::select('id', 'name')->orderBy('id', 'DESC')->get();
            return response(['status' => true, 'message' => 'User Dropdown', 'all_user' => $user], 200);
        } catch (Exception $e) {
            dd($e);
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function dashboard()
    {
        $user = User::select('id', 'name', 'email', 'mobile', 'username',  'menu_style', 'theme', 'status', 'designation')->where('id', Auth::id())->first();
        $role = $user->roles->first();
        $user->role = $role->name;

        // $totalIncome = IncomeMaster::where('supervisor_id', Auth::id())->sum('amount');
        // $totalExpense = ExpenseMaster::where('supervisor_id', Auth::id())->sum('amount');
        // $totalBalance = $totalIncome - $totalExpense;
        // $monthIncome = IncomeMaster::where('supervisor_id', Auth::id())
        //     ->whereMonth('created_at', Carbon::now()->month)
        //     ->whereYear('created_at', Carbon::now()->year)
        //     ->sum('amount');
        // $monthExpense = ExpenseMaster::where('supervisor_id', Auth::id())
        //     ->whereMonth('created_at', Carbon::now()->month)
        //     ->whereYear('created_at', Carbon::now()->year)
        //     ->sum('amount');

        // $todayIncome = IncomeMaster::where('supervisor_id', Auth::id())
        //     ->whereDate('created_at', Carbon::today())
        //     ->sum('amount');
        // $todayExpense = ExpenseMaster::where('supervisor_id', Auth::id())
        //     ->whereDate('created_at', Carbon::today())
        //     ->sum('amount');
        // $totalLabour = Labour::where('supervisor_id', Auth::id())->count();

        $isSupervisor = $role->name === 'Supervisor';

        // Filter logic based on role
        if ($isSupervisor) {
            $incomeQuery = IncomeMaster::where('supervisor_id', Auth::id());
            $expenseQuery = ExpenseMaster::where('supervisor_id', Auth::id());
            $labourQuery = Labour::where('supervisor_id', Auth::id());
        } else {
            $incomeQuery = IncomeMaster::query();
            $expenseQuery = ExpenseMaster::query();
            $labourQuery = Labour::query();
        }

        // Totals
        $totalIncome = $incomeQuery->clone()->sum('amount');
        $totalExpense = $expenseQuery->clone()->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        // Monthly
        $monthIncome = $incomeQuery->clone()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $monthExpense = $expenseQuery->clone()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        // Today
        $todayIncome = $incomeQuery->clone()
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        $todayExpense = $expenseQuery->clone()
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        // Labour count
        $totalLabour = $labourQuery->clone()->count();
        $permissions = Permission::select('id', 'title', 'title_tag', 'name')
            ->join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $role->id)
            ->orderBy('id', 'DESC')
            ->get();
        $permission = collect($permissions)->pluck('name');
        $user->permissions = $permission;
        unset($user->roles);
        try {
            return response([
                'status' => true,
                'message' => 'Dashboard',
                'totalBalance' => $totalBalance,
                // 'totalIncome' => $totalIncome,
                // 'todayIncome' => $todayIncome,
                // 'monthIncome' => $monthIncome,
                // 'monthExpense' => $monthExpense,
                // 'todayIncome' => $todayIncome,
                // 'totalExpense' => $totalExpense,
                // 'todayExpense' => $todayExpense,
                 'totalIncome' => floor($totalIncome) == $totalIncome ? (int) $totalIncome : (float) $totalIncome,
                'todayIncome' => floor($todayIncome) == $todayIncome ? (int) $todayIncome : (float) $todayIncome,
                'monthIncome' => floor($monthIncome) == $monthIncome ? (int) $monthIncome : (float) $monthIncome,
                'monthExpense' =>floor($monthExpense) == $monthExpense ? (int) $monthExpense : (float) $monthExpense,
                // 'todayIncome' => floor($totalIncome) == $totalIncome ? (int) $totalIncome : (float) $totalIncome,
                'totalExpense' =>floor($totalExpense) == $totalExpense ? (int) $totalExpense : (float) $totalExpense,
                'todayExpense' =>floor($todayExpense) == $todayExpense ? (int) $todayExpense : (float) $todayExpense,
                'totalLabour' => $totalLabour,
                'user' => $user,
            ], 200);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => 'Something went wrong'
            ], 200);
        }
    }
}
