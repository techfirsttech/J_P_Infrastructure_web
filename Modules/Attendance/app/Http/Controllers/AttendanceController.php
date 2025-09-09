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
use Modules\Contractor\Models\Contractor;
use Modules\Labour\Models\Labour;
use Modules\SiteMaster\Models\SiteMaster;
use Modules\SiteMaster\Models\SiteSupervisor;
use Modules\User\Models\User;
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


    public function create()
    {
        // $supervisor = User::select('id', 'name')->get();

        $supervisor = User::select('id', 'name')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'supervisor');
            })
            ->orderBy('users.name', 'asc')
            ->get();
        $site = SiteMaster::select('id', 'site_name')->get();
        $contractor = Contractor::select('id', 'contractor_name')->get();
        return view('attendance::create', compact('supervisor', 'site', 'contractor'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer',
            'contractor_id' => 'required|integer',
            // 'labour' => 'required|array|min:1',
            'labour.*.id' => 'required|integer|exists:labours,id',
            'labour.*.type' => 'required|in:Full,Half,Absent',
        ], [
            'site_id.required' => 'Site ID is required.',
            'site_id.integer' => 'Site ID must be an integer.',
            // 'site_id.exists' => 'The selected site ID does not exist.',
            'contractor_id.required' => 'Contractor ID is required.',
            'contractor_id.integer' => 'Contractor ID must be an integer.',
            // 'contractor_id.exists' => 'The selected contractor ID does not exist.',
            // 'labour.required' => 'At least one labour entry is required.',
            'labour.array' => 'Labour must be an array.',
            'labour.*.id.required' => 'Labour ID is required.',
            'labour.*.id.integer' => 'Labour ID must be an integer.',
            'labour.*.id.exists' => 'The selected labour ID does not exist.',
            'labour.*.type.required' => 'Attendance type is required.',
            'labour.*.type.in' => 'Attendance type must be either Full or Half.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }

        DB::beginTransaction();

        try {
            $yearID = getSelectedYear();
            // $date = now()->toDateString();
            $date =  date('Y-m-d', strtotime($request->date));

            foreach ($request->labours as $labour) {
                $dailyWage = Labour::where('id', $labour['id'])->value('daily_wage');


                $amount = 0.00;
                if ($labour['type'] == 'Half') {
                    $amount = $dailyWage / 2;
                } elseif ($labour['type'] == 'Full') {
                    $amount = $dailyWage;
                }

                // Check if attendance already exists for same day
                $existing = Attendance::where('labour_id', $labour['id'])
                    ->where('site_id', $request->site_id)
                    ->where('contractor_id', $request->contractor_id)
                    ->whereDate('date', $date)
                    ->first();

                if ($existing) {
                    // Update existing record
                    $existing->type = $labour['type'];
                    $existing->amount = $amount;
                    $existing->supervisor_id = $request->supervisor_id;
                    $existing->year_id = $yearID;
                    $result = $existing->save();
                } else {

                    // Create new attendance
                    $attendance = new Attendance();
                    $attendance->supervisor_id = $request->supervisor_id;
                    $attendance->site_id = $request->site_id;
                    $attendance->contractor_id = $request->contractor_id;
                    $attendance->labour_id = $labour['id'];
                    $attendance->type = $labour['type'];
                    $attendance->amount = $amount;
                    $attendance->year_id = $yearID;
                    $attendance->date = (!empty($request->date)) ? date('Y-m-d', strtotime($request->date)) : null;;
                    $attendance->created_at = $date;
                    $result = $attendance->save();
                }
            }

            DB::commit();
            if ($result) {
                DB::commit();
                return redirect()->route('attendance.index')->with('success', 'Attendance add successfully');
            } else {
                DB::rollback();
                return redirect()->back()->with('warning', 'Attendance added failed');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try again');
        }
    }

    public function show($id, Request $request)
    {
        //
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

    public function getSitesBySupervisor(Request $request)
    {
        // $sites = SiteMaster::select('id', 'site_name')
        //     ->where('supervisor_id', $request->id)
        //     ->get();
        // $sites = SiteSupervisor::select('id', 'site_name')
        //     ->where('supervisor_id', $request->id)
        //     ->get();
        $sites = SiteMaster::select('site_masters.id', 'site_masters.site_name')
            ->join('site_supervisors', 'site_supervisors.site_master_id', '=', 'site_masters.id')
            ->where('site_supervisors.user_id', $request->id)
            ->get();

        if ($sites->count() > 0) {
            return response()->json([
                'status_code' => 200,
                'result' => $sites
            ]);
        } else {
            return response()->json([
                'status_code' => 404,
                'message' => 'No sites found'
            ]);
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

    public function getContractorLabour(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'   => 'required|integer',
        ], [
            'id.required' => 'Contractor ID is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 201,
                'message'     => 'Please input proper data.',
                'errors'      => $validator->errors()
            ]);
        }

        try {
            // form ma select karel date value
            $date = $request->date ?
                Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d')
                : now()->format('Y-m-d');

            $labours = Labour::select('id', 'labour_name')
                ->where('contractor_id', $request->id)
                ->get();

            foreach ($labours as $labour) {
                $attendance = Attendance::where('labour_id', $labour->id)
                    ->whereDate('date', $date)
                    ->first();

                $labour->attendance_type = $attendance ? $attendance->type : 'Absent';
            }

            return response([
                'status_code' => 200,
                'message'     => 'Labour List',
                'result'      => $labours
            ]);
        } catch (Exception $e) {
            return response([
                'status_code' => 500,
                'message'     => 'Something went wrong. Please try again.',
                'error'       => $e->getMessage()
            ]);
        }
    }

   
}
