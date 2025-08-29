<?php

namespace Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Attendance\Models\Attendance;
use Modules\Contractor\Models\Contractor;
use Modules\Labour\Models\Labour;
use Modules\SiteMaster\Models\SiteMaster;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('site', 'contractor', 'user', 'labour')
            ->when(!role_super_admin(), function ($q) {
                return $q->where('user_id', Auth::id());
            })
            ->when(!empty($request->leave_type) && $request->leave_type !== 'All', function ($query) use ($request) {
                $query->where('type', $request->leave_type);
            })
            ->when(!empty($request->site_id) && $request->site_id !== 'All', function ($query) use ($request) {
                $query->where('site_id', $request->site_id);
            })
            ->when(!empty($request->contractor_id) && $request->contractor_id !== 'All', function ($query) use ($request) {
                $query->where('contractor_id', $request->contractor_id);
            })
            ->when(!empty($request->s_date) || !empty($request->e_date), function ($query) use ($request) {
                $startDate = !empty($request->s_date)
                    ? date('Y-m-d 00:00:00', strtotime($request->s_date))
                    : null;

                $endDate = !empty($request->e_date)
                    ? date('Y-m-d 23:59:59', strtotime($request->e_date))
                    : ($startDate ? date('Y-m-d 23:59:59', strtotime($request->s_date)) : null);

                if ($startDate && $endDate) {
                    $query->whereBetween('.date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->where('date', '>=', $startDate);
                } elseif ($endDate) {
                    $query->where('date', '<=', $endDate);
                }
            });

        if (request()->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $show = 'attendance-lists';
                    $edit = '';
                    $delete = '';
                    $assign = '';
                    $showURL = route('attendance.show',  $row->id);
                    $editURL = route('attendance.edit', $row->id);
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL', 'assign'));
                })
                ->editColumn('site_name', function ($row) {
                    return $row?->site?->site_name;
                })
                ->editColumn('contractor_name', function ($row) {
                    return $row?->contractor?->contractor_name;
                })
                ->editColumn('user_name', function ($row) {
                    return $row?->user?->name;
                })
                ->editColumn('labour_name', function ($row) {
                    return $row?->labour?->labour_name;
                })
                ->editColumn('date', function ($row) {
                    if (!is_null($row->date)) {
                        return date("d-m-Y", strtotime($row->date));
                    } else {
                        return '';
                    }
                })
                ->editColumn('type', function ($row) {
                    if ($row->type == 'Absent') {
                        $color = 'danger';
                    } else if ($row->type == 'Half') {
                        $color = 'warning';
                    } else {
                        $color = 'success';
                    }
                    return '<span class="badge bg-label-' . $color . '">' . $row->type . '</span>';
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            $site = SiteMaster::select('id', 'site_name')->get();
            return view('attendance::index', compact('site'));
        }
    }

    //  public function index(Request $request)
    // {
    //     $query = Attendance::select(
    //         DB::raw('MIN(attendances.id) as id'),
    //         'attendances.site_id',
    //         'site_masters.site_name',
    //         'attendances.contractor_id',
    //         'contractors.contractor_name',
    //         DB::raw('GROUP_CONCAT(DISTINCT users.name SEPARATOR ", ") as supervisors'),
    //         DB::raw('COUNT(attendances.id) as total_records')
    //     )
    //         ->leftJoin('site_masters', 'site_masters.id', '=', 'attendances.site_id')
    //         ->leftJoin('users', 'users.id', '=', 'attendances.supervisor_id')
    //         ->leftJoin('contractors', 'contractors.id', '=', 'attendances.contractor_id')
    //         ->groupBy('attendances.site_id', 'site_masters.site_name', 'attendances.contractor_id', 'contractors.contractor_name');

    //     $user = Auth::user();
    //     $role = $user->roles->first();

    //     if ($role && $role->name === 'Supervisor') {
    //         $query->where('attendances.supervisor_id', $user->id);
    //     }

    //     if ($request->filled('supervisor_id')) {
    //         $query->where('attendances.supervisor_id', $request->supervisor_id);
    //     }
    //     if ($request->filled('site_id')) {
    //         $query->where('attendances.site_id', $request->site_id);
    //     }
    //     if ($request->filled('contractor_id')) {
    //         $query->where('attendances.contractor_id', $request->contractor_id);
    //     }

    //     if (request()->ajax()) {
    //         return DataTables::of($query)
    //             ->addIndexColumn()
    //             ->addColumn('action', function ($row) {
    //                 $show = 'attendance-list';
    //                 $edit = '';
    //                 $delete = '';
    //                 $assign = '';
    //                 $showURL = route('attendance.show',  $row->id);
    //                 // $showURL = route('attendance.show', $row->contractor_id);
    //                 $editURL = route('attendance.edit', $row->id);
    //                 return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL', 'assign'));
    //             })
    //             ->escapeColumns([])
    //             ->make(true);
    //     } else {
    //         return view('attendance::index');
    //     }
    // }

    public function getLaboursByDateContractor(Request $request)
    {
        $date = $request->date;
        $contractor_id = $request->contractor_id;

        if (!$date || !$contractor_id) {
            return response()->json(['status' => false, 'message' => 'Date and Contractor required']);
        }

        // Attendance માં already present labor IDs fetch
        $attendedLabourIds = Attendance::where('date', $date)
            ->where('contractor_id', $contractor_id)
            ->pluck('labour_id')
            ->toArray();

        // Contractor ના labor fetch
        $labours = Labour::where('contractor_id', $contractor_id)
            ->get();

        return response()->json([
            'status' => true,
            'labours' => $labours,
            'attendedLabourIds' => $attendedLabourIds
        ]);
    }




    // =====================

    // public function index(Request $request)
    // {
    //     try {
    //         $user = Auth::user();
    //         $baseQuery = Attendance::query()
    //             ->leftJoin('site_masters', 'site_masters.id', '=', 'attendances.site_id')
    //             ->leftJoin('users', 'users.id', '=', 'attendances.supervisor_id')
    //             ->leftJoin('labours', 'labours.id', '=', 'attendances.labour_id')
    //             ->leftJoin('contractors', 'contractors.id', '=', 'attendances.contractor_id');

    //         if ($user->role === 'supervisor') {
    //             $baseQuery->where('attendances.supervisor_id', $user->id);
    //         }

    //         if ($request->filled('start_date') && $request->filled('end_date')) {
    //             $start = Carbon::parse($request->start_date)->startOfDay();
    //             $end = Carbon::parse($request->end_date)->endOfDay();
    //             $baseQuery->whereBetween('attendances.created_at', [$start, $end]);
    //         } elseif ($request->filled('start_date')) {
    //             $start = Carbon::parse($request->start_date)->startOfDay();
    //             $baseQuery->where('attendances.created_at', '>=', $start);
    //         } elseif ($request->filled('end_date')) {
    //             $end = Carbon::parse($request->end_date)->endOfDay();
    //             $baseQuery->where('attendances.created_at', '<=', $end);
    //         } else {
    //             $baseQuery->whereDate('attendances.created_at', Carbon::today());
    //         }

    //         // Get data
    //         $attendanceData = $baseQuery
    //             ->select(
    //                 'attendances.site_id',
    //                 'site_masters.site_name',
    //                 'attendances.contractor_id',
    //                 'contractors.contractor_name',
    //                 'attendances.labour_id',
    //                 'attendances.type',
    //                 'labours.labour_name',
    //                 DB::raw("SUM(CASE WHEN attendances.type = 'Full' THEN 1 ELSE 0 END) as full_days"),
    //                 DB::raw("SUM(CASE WHEN attendances.type = 'Half' THEN 1 ELSE 0 END) as half_days"),
    //                 DB::raw("SUM(CASE WHEN attendances.type = 'Absent' THEN 1 ELSE 0 END) as absent_days")
    //             )
    //             ->groupBy(
    //                 'attendances.site_id',
    //                 'site_masters.site_name',
    //                 'attendances.contractor_id',
    //                 'contractors.contractor_name',
    //                 'attendances.labour_id',
    //                 'attendances.type',
    //                 'labours.labour_name'
    //             )
    //             ->orderBy('site_masters.site_name')
    //             ->get();

    //         // Nest data: Site > Contractor > Labour
    //         $grouped = $attendanceData
    //             ->groupBy('site_id')
    //             ->map(function ($siteGroup) {
    //                 $site = $siteGroup->first();

    //                 return [
    //                     'site_id' => $site->site_id,
    //                     'site_name' => $site->site_name,
    //                     'contractors' => $siteGroup->groupBy('contractor_id')->map(function ($contractorGroup) {
    //                         $contractor = $contractorGroup->first();

    //                         return [
    //                             'contractor_id' => $contractor->contractor_id,
    //                             'contractor_name' => $contractor->contractor_name,
    //                             'labours' => $contractorGroup->map(function ($row) {
    //                                 return [
    //                                     'labour_id' => $row->labour_id,
    //                                     'labour_name' => $row->labour_name,
    //                                     'type' => $row->type,
    //                                     'full_days' => $row->full_days,
    //                                     'half_days' => $row->half_days,
    //                                     'absent_days' => $row->absent_days,
    //                                 ];
    //                             })->values()
    //                         ];
    //                     })->values()
    //                 ];
    //             })
    //             ->values();

    //         return response([
    //             'status' => true,
    //             'message' => 'Attendance List',
    //             'data' => $grouped
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response([
    //             'status' => false,
    //             'message' => 'Something went wrong.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function create()
    {
        return view('attendance::create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer',
            'contractor_id' => 'required|integer',
            'labour' => 'required|array|min:1',
            'labour.*.labour_id' => 'required|integer|exists:labours,id',
            'labour.*.type' => 'required|in:Full,Half,Absent',
        ], [
            'site_id.required' => 'Site ID is required.',
            'site_id.integer' => 'Site ID must be an integer.',
            // 'site_id.exists' => 'The selected site ID does not exist.',
            'contractor_id.required' => 'Contractor ID is required.',
            'contractor_id.integer' => 'Contractor ID must be an integer.',
            // 'contractor_id.exists' => 'The selected contractor ID does not exist.',
            'labour.required' => 'At least one labour entry is required.',
            'labour.array' => 'Labour must be an array.',
            'labour.*.labour_id.required' => 'Labour ID is required.',
            'labour.*.labour_id.integer' => 'Labour ID must be an integer.',
            'labour.*.labour_id.exists' => 'The selected labour ID does not exist.',
            'labour.*.type.required' => 'Attendance type is required.',
            'labour.*.type.in' => 'Attendance type must be either Full or Half.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please input proper data.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $yearID = getSelectedYear();
            $date = now()->toDateString();

            foreach ($request->labour as $labour) {
                $dailyWage = Labour::where('id', $labour['labour_id'])->value('daily_wage');


                $amount = 0.00;
                if ($labour['type'] == 'Half') {
                    $amount = $dailyWage / 2;
                } elseif ($labour['type'] == 'Full') {
                    $amount = $dailyWage;
                }

                // Check if attendance already exists for same day
                $existing = Attendance::where('labour_id', $labour['labour_id'])
                    ->where('site_id', $request->site_id)
                    ->where('contractor_id', $request->contractor_id)
                    ->whereDate('created_at', $date)
                    ->first();

                if ($existing) {
                    // Update existing record
                    $existing->type = $labour['type'];
                    $existing->amount = $amount;
                    $existing->supervisor_id = Auth::id();
                    $existing->year_id = $yearID;
                    $existing->save();
                } else {
                    // Create new attendance
                    $attendance = new Attendance();
                    $attendance->supervisor_id = Auth::id();
                    $attendance->site_id = $request->site_id;
                    $attendance->contractor_id = $request->contractor_id;
                    $attendance->labour_id = $labour['labour_id'];
                    $attendance->type = $labour['type'];
                    $attendance->amount = $amount;
                    $attendance->year_id = $yearID;
                    $attendance->date = (!empty($request->date)) ? date('Y-m-d', strtotime($request->date)) : null;;
                    $attendance->created_at = $date;
                    $attendance->save();
                }
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Labour attendance successfully recorded.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function show($id, Request $request)
    {
        // $contractorId = Attendance::where('attendances.id',$id)->pluck('contractor_id');
        $contractorId = Attendance::where('attendances.id', $id)->value('contractor_id');

        $query = Attendance::select(
            // DB::raw('MIN(attendances.id) as id'),

            'attendances.id',
            'attendances.labour_id',
            'attendances.site_id',
            'site_masters.site_name',
            'attendances.contractor_id',
            'contractors.contractor_name',
            'users.name as supervisors',
            'labours.labour_name',
            'attendances.type',
            // 'attendances.date',
            DB::raw("DATE_FORMAT(attendances.date, '%d-%m-%Y') as date"),

            // DB::raw('GROUP_CONCAT(DISTINCT users.name SEPARATOR ", ") as supervisors'),
        )
            ->leftJoin('site_masters', 'site_masters.id', '=', 'attendances.site_id')
            ->leftJoin('users', 'users.id', '=', 'attendances.supervisor_id')
            ->leftJoin('contractors', 'contractors.id', '=', 'attendances.contractor_id')
            ->leftJoin('labours', 'labours.id', '=', 'attendances.labour_id')
            // ->groupBy('attendances.site_id', 'attendances.contractor_id', 'site_masters.site_name', 'contractors.contractor_name', 'attendances.id')
            ->where('attendances.contractor_id', $contractorId);
        $user = Auth::user();
        $role = $user->roles->first();

        // if ($role && $role->name === 'Supervisor') {
        //     $query->where('attendances.supervisor_id', $user->id);
        // }

        // if ($request->filled('supervisor_id')) {
        //     $query->where('attendances.supervisor_id', $request->supervisor_id);
        // }
        // if ($request->filled('site_id')) {
        //     $query->where('attendances.site_id', $request->site_id);
        // }
        // if ($request->filled('contractor_id')) {
        //     $query->where('attendances.contractor_id', $request->contractor_id);
        // }

        if (request()->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $show = 'attendance-list';
                    $edit = '';
                    // $edit = 'attendance-edit';
                    $delete = '';
                    $assign = '';
                    $showURL = '';
                    // $showURL = route('attendance.edit', $row->id);
                    $editURL = '';
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL', 'assign'));
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            $showURL = route('attendance.show', $id);
            return view('attendance::show', compact('showURL'));
        }
        // return view('attendance::show');
    }

    public function edit($id)
    {
        $attendance = Attendance::where('attendances.id', $id)
            ->select(

                'attendances.id',
                'attendances.site_id',
                'attendances.supervisor_id',
                'attendances.labour_id',
                'attendances.type',
                'attendances.amount',
                'site_masters.site_name',
                'contractors.contractor_name',

            )
            ->leftJoin('site_masters', 'site_masters.id', '=', 'attendances.site_id')
            ->leftJoin('users', 'users.id', '=', 'attendances.supervisor_id')
            ->leftJoin('contractors', 'contractors.id', '=', 'attendances.contractor_id')
            ->first();
        return view('attendance::edit', compact('attendance'));
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}

    public function labourDropdown(Request $request)
    {
        try {
            // $siteId = $request->input('site_id');
            // $contractorId = $request->input('contractor_id');
            // $query = Labour::select(
            //     'labours.id',
            //     'labours.supervisor_id',
            //     'labours.site_id',
            //     'labours.contractor_id',
            //     'labours.labour_name',
            //     'labours.mobile',
            //     'labours.daily_wage',
            //     'attendances.type'
            // )
            //     ->where('labours.status', 'Active')
            //     ->leftJoin('attendances', 'labours.id', '=', 'attendances.labour_id')
            //     ->where('attendances.date', date('Y-m-d'));


            // if ($contractorId) {
            //     $query->where('labours.contractor_id', $contractorId);
            // }
            // $labourList = $query->get();
            $contractorId = $request->input('contractor_id');

            $query = Labour::select(
                'labours.id',
                'labours.supervisor_id',
                'labours.site_id',
                'labours.contractor_id',
                'labours.labour_name',
                'labours.mobile',
                'labours.daily_wage',
                'attendances.type'
            )
                ->leftJoin('attendances', function ($join) {
                    $join->on('labours.id', '=', 'attendances.labour_id')
                        ->whereDate('attendances.date', '=', date('Y-m-d'));
                })
                ->where('labours.status', 'Active');

            if ($contractorId) {
                $query->where('labours.contractor_id', $contractorId);
            }

            $labourList = $query->get();
            return response(['status' => true, 'message' => 'Labour List', 'labour_list' => $labourList], 200);
        } catch (Exception $e) {
            dd($e);
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
    public function getContractor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',

        ], [
            'site_id.required' => 'Site ID is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        try {
            $query = Contractor::select('id', 'contractor_name')->where('site_id', $request->id)->get();
            return response(['status_code' => 200, 'message' => 'Contractor List', 'result' => $query]);
        } catch (Exception $e) {
            return response(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
