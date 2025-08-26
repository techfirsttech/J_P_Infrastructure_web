<?php

namespace Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Attendance\Models\Attendance;
use Modules\Labour\Models\Labour;

class AttendanceController extends Controller
{

    // public function index(Request $request)
    // {
    //     try {
    //         $user = Auth::user();

    //         // Base Query
    //         $baseQuery = Attendance::query()
    //             ->leftJoin('site_masters', 'site_masters.id', '=', 'attendances.site_id')
    //             ->leftJoin('users', 'users.id', '=', 'attendances.supervisor_id')
    //             ->leftJoin('labours', 'labours.id', '=', 'attendances.labour_id')
    //             ->leftJoin('contractors', 'contractors.id', '=', 'attendances.contractor_id');

    //         // Supervisor restriction
    //         if ($user->role === 'supervisor') {
    //             $baseQuery->where('attendances.supervisor_id', $user->id);
    //         }

    //         // Filters
    //         if ($request->filled('site_id')) {
    //             $baseQuery->where('attendances.site_id', $request->site_id);
    //         }
    //         if ($request->filled('supervisor_id')) {
    //             $baseQuery->where('attendances.supervisor_id', $request->supervisor_id);
    //         }
    //         if ($request->filled('labour_id')) {
    //             $baseQuery->where('attendances.labour_id', $request->labour_id);
    //         }
    //         if ($request->filled('type')) {
    //             $baseQuery->where('attendances.type', $request->type);
    //         }

    //         // Date range
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

    //         // Labour wise count
    //         $labourSummary = (clone $baseQuery)
    //             ->select(
    //                 'attendances.labour_id',
    //                 'labours.labour_name',
    //                 'site_masters.site_name',
    //                 'users.name as supervisor_name',
    //                 DB::raw("SUM(CASE WHEN attendances.type = 'Full' THEN 1 ELSE 0 END) as full_days"),
    //                 DB::raw("SUM(CASE WHEN attendances.type = 'Half' THEN 1 ELSE 0 END) as half_days"),
    //                 DB::raw("SUM(CASE WHEN attendances.type = 'Absent' THEN 1 ELSE 0 END) as absent_days")
    //             )
    //             ->groupBy('attendances.labour_id', 'labours.labour_name', 'site_masters.site_name', 'users.name')
    //             ->orderBy('labours.labour_name')
    //             ->get();

    //         // Totals
    //         $totalFull = $labourSummary->sum('full_days');
    //         $totalHalf = $labourSummary->sum('half_days');
    //         $totalAbsent = $labourSummary->sum('absent_days');

    //         return response([ 'status' => true, 'message' => 'Labour-wise Attendance Summary','result' => $labourSummary, 'total_full' => $totalFull,'total_half' => $totalHalf,'total_absent' => $totalAbsent,], 200);
    //     } catch (\Exception $e) {
    //         return response([ 'status' => false,'message' => 'Something went wrong.','error' => $e->getMessage()], 500);
    //     }
    // }

    // clear code=========================
    // public function index(Request $request)
    // {
    //     try {
    //         $user = Auth::user();

    //         // Base Query
    //         $baseQuery = Attendance::query()
    //             ->leftJoin('site_masters', 'site_masters.id', '=', 'attendances.site_id')
    //             ->leftJoin('users', 'users.id', '=', 'attendances.supervisor_id')
    //             ->leftJoin('labours', 'labours.id', '=', 'attendances.labour_id')
    //             ->leftJoin('contractors', 'contractors.id', '=', 'attendances.contractor_id'); // 👈 Contractor join

    //         // Supervisor restriction
    //         if ($user->role === 'supervisor') {
    //             $baseQuery->where('attendances.supervisor_id', $user->id);
    //         }

    //         // Filters
    //         if ($request->filled('site_id')) {
    //             $baseQuery->where('attendances.site_id', $request->site_id);
    //         }
    //         if ($request->filled('supervisor_id')) {
    //             $baseQuery->where('attendances.supervisor_id', $request->supervisor_id);
    //         }
    //         if ($request->filled('labour_id')) {
    //             $baseQuery->where('attendances.labour_id', $request->labour_id);
    //         }
    //         if ($request->filled('contractor_id')) {
    //             $baseQuery->where('attendances.contractor_id', $request->contractor_id); // 👈 Contractor filter
    //         }
    //         if ($request->filled('type')) {
    //             $baseQuery->where('attendances.type', $request->type);
    //         }

    //         // Date range
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

    //         // Labour wise count
    //         $labourSummary = (clone $baseQuery)
    //             ->select(
    //                 'attendances.labour_id',
    //                 'labours.labour_name',
    //                 'site_masters.site_name',
    //                 'users.name as supervisor_name',
    //                 'contractors.contractor_name', // 👈 Contractor name
    //                 DB::raw("SUM(CASE WHEN attendances.type = 'Full' THEN 1 ELSE 0 END) as full_days"),
    //                 DB::raw("SUM(CASE WHEN attendances.type = 'Half' THEN 1 ELSE 0 END) as half_days"),
    //                 DB::raw("SUM(CASE WHEN attendances.type = 'Absent' THEN 1 ELSE 0 END) as absent_days")
    //             )
    //             ->groupBy(
    //                 'attendances.labour_id',
    //                 'labours.labour_name',
    //                 'site_masters.site_name',
    //                 'users.name',
    //                 'contractors.contractor_name' // 👈 Group by contractor name
    //             )
    //             ->orderBy('labours.labour_name')
    //             ->get();

    //         // Totals
    //         $totalFull = $labourSummary->sum('full_days');
    //         $totalHalf = $labourSummary->sum('half_days');
    //         $totalAbsent = $labourSummary->sum('absent_days');

    //         return response([
    //             'status' => true,
    //             'message' => 'Labour-wise Attendance Summary',
    //             'result' => $labourSummary,
    //             'total_full' => $totalFull,
    //             'total_half' => $totalHalf,
    //             'total_absent' => $totalAbsent,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response([
    //             'status' => false,
    //             'message' => 'Something went wrong.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }


    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $baseQuery = Attendance::query()
                ->leftJoin('site_masters', 'site_masters.id', '=', 'attendances.site_id')
                ->leftJoin('users', 'users.id', '=', 'attendances.supervisor_id')
                ->leftJoin('labours', 'labours.id', '=', 'attendances.labour_id')
                ->leftJoin('contractors', 'contractors.id', '=', 'attendances.contractor_id');

            if ($user->role === 'supervisor') {
                $baseQuery->where('attendances.supervisor_id', $user->id);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                $baseQuery->whereBetween('attendances.created_at', [$start, $end]);
            } elseif ($request->filled('start_date')) {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $baseQuery->where('attendances.created_at', '>=', $start);
            } elseif ($request->filled('end_date')) {
                $end = Carbon::parse($request->end_date)->endOfDay();
                $baseQuery->where('attendances.created_at', '<=', $end);
            } else {
                $baseQuery->whereDate('attendances.created_at', Carbon::today());
            }

            // Get data
            $attendanceData = $baseQuery
                ->select(
                    'attendances.site_id',
                    'site_masters.site_name',
                    'attendances.contractor_id',
                    'contractors.contractor_name',
                    'attendances.labour_id',
                    'attendances.type',
                    'labours.labour_name',
                    DB::raw("SUM(CASE WHEN attendances.type = 'Full' THEN 1 ELSE 0 END) as full_days"),
                    DB::raw("SUM(CASE WHEN attendances.type = 'Half' THEN 1 ELSE 0 END) as half_days"),
                    DB::raw("SUM(CASE WHEN attendances.type = 'Absent' THEN 1 ELSE 0 END) as absent_days")
                )
                ->groupBy(
                    'attendances.site_id',
                    'site_masters.site_name',
                    'attendances.contractor_id',
                    'contractors.contractor_name',
                    'attendances.labour_id',
                    'attendances.type',
                    'labours.labour_name'
                )
                ->orderBy('site_masters.site_name')
                ->get();

            // Nest data: Site > Contractor > Labour
            $grouped = $attendanceData
                ->groupBy('site_id')
                ->map(function ($siteGroup) {
                    $site = $siteGroup->first();

                    return [
                        'site_id' => $site->site_id,
                        'site_name' => $site->site_name,
                        'contractors' => $siteGroup->groupBy('contractor_id')->map(function ($contractorGroup) {
                            $contractor = $contractorGroup->first();

                            return [
                                'contractor_id' => $contractor->contractor_id,
                                'contractor_name' => $contractor->contractor_name,
                                'labours' => $contractorGroup->map(function ($row) {
                                    return [
                                        'labour_id' => $row->labour_id,
                                        'labour_name' => $row->labour_name,
                                        'type' => $row->type,
                                        'full_days' => $row->full_days,
                                        'half_days' => $row->half_days,
                                        'absent_days' => $row->absent_days,
                                    ];
                                })->values()
                            ];
                        })->values()
                    ];
                })
                ->values();

            return response([
                'status' => true,
                'message' => 'Attendance List',
                'data' => $grouped
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // public function index(Request $request)
    // {
    //     try {
    //          $user = Auth::user();

    //         $attendances = Attendance::select(
    //             'attendances.id',
    //             'attendances.site_id',
    //             'attendances.supervisor_id',
    //             'attendances.labour_id',
    //             'attendances.type',
    //             'attendances.amount',

    //             'site_masters.site_name',
    //             'users.name as supervisor_name',
    //             'labours.labour_name',
    //         )
    //             ->leftJoin('site_masters', 'site_masters.id', '=', 'attendances.site_id')
    //             ->leftJoin('users', 'users.id', '=', 'attendances.supervisor_id')
    //             ->leftJoin('labours', 'labours.id', '=', 'attendances.labour_id')
    //             ->when($user->role === 'supervisor', function ($query) use ($user) {
    //                 return $query->where('attendances.supervisor_id', $user->id);
    //             })

    //             ->when($request->site_id, function ($query) use ($request) {
    //                 return $query->where('attendances.site_id', $request->site_id);
    //             })
    //             ->when($request->supervisor_id, function ($query) use ($request) {
    //                 return $query->where('attendances.supervisor_id', $request->supervisor_id);
    //             })
    //             ->when($request->labour_id, function ($query) use ($request) {
    //                 return $query->where('attendances.labour_id', $request->labour_id);
    //             })
    //             ->when($request->type, function ($query) use ($request) {
    //                 return $query->where('attendances.type', $request->type);
    //             })

    //             ->orderBy('attendances.id', 'DESC')
    //             ->simplePaginate(12);


    //         return response(['status' => true, 'message' => 'Attendances List', 'half'=> '','full'=> '', 'absent'=> '', 'result' => $attendances->items()], 200);
    //     } catch (Exception $e) {
    //         return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
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


    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'site_id' => 'required|integer|exists:site_masters,id',

    //     ], [
    //         'site_id.required' => 'Site ID is required.',
    //         'site_id.integer' => 'Site ID is must be integer.',
    //         'site_id.exists' => 'The selected site id does not exist',
    //     ]);

    //     if ($validator->fails()) {
    //         $error = $validator->errors();
    //         $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
    //         return response()->json($response, 422);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $yearID = getSelectedYear();

    //         foreach ($request->labour as $labour) {
    //             $dailyWage = Labour::where('id', $labour['labour_id'])->value('daily_wage');
    //             $attendance = new Attendance();
    //             $attendance->supervisor_id = Auth::id();
    //             $attendance->site_id = $request->site_id;
    //             $attendance->labour_id = $labour['labour_id'];
    //             $attendance->type = $labour['type'];
    //             if ($labour['type'] == 'Half') {
    //                 $amount = $dailyWage / 2;
    //             } elseif ($labour['type'] == 'Full') {
    //                 $amount = $dailyWage;
    //             } else {
    //                 $amount = 00.00;
    //             }
    //             $attendance->amount = $amount;
    //             $attendance->year_id = $yearID;
    //             $attendance->save();
    //         }

    //         DB::commit();
    //         return response()->json(['status' => true, 'message' => 'Labour attendance successfully.'], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 500);
    //     }
    // }

    public function show($id)
    {
        return view('attendance::show');
    }


    public function edit($id)
    {
        return view('attendance::edit');
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


    // public function labourDropdown(Request $request)
    // {
    //     try {

    //         $labourList = Labour::select('id', 'supervisor_id','site_id','labour_name','mobile')->where('status','Active')->get();
    //         return response(['status' => true,'message' => 'Labour List','labour_list' => $labourList], 200);
    //     } catch (Exception $e) {
    //         return response([ 'status' => false,'message' => 'Something went wrong. Please try again.'], 200);
    //     }
    // }
}
