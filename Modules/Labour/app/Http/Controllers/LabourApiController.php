<?php

namespace Modules\Labour\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Labour\Models\Labour;

class LabourApiController extends Controller
{

    // public function index()
    // {
    //     try {
    //         $labour = Labour::select(
    //             'labours.id',
    //             'labours.supervisor_id',
    //             'labours.site_id',
    //             'labours.labour_name',
    //             'labours.daily_wage',
    //             'labours.mobile',
    //             'labours.address',
    //             'labours.status',
    //             'labours.user_id',
    //             'site_masters.site_name',
    //             'users.name as supervisor_name',
    //         )
    //             ->leftJoin('site_masters', 'site_masters.id', '=', 'labours.site_id')
    //             ->leftJoin('users', 'users.id', '=', 'labours.supervisor_id')
    //             ->orderBy('labours.id', 'DESC')
    //             ->simplePaginate(12);

    //         return response(['status' => true, 'message' => 'labour List', 'labour_list' => $labour->items()], 200);
    //     } catch (Exception $e) {
    //         return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
    //     }
    // }

    public function index(Request $request)
    {
        try {
            $query = Labour::select(
                'labours.id',
                'labours.supervisor_id',
                'labours.site_id',
                'labours.contractor_id',
                'labours.labour_name',
                'labours.daily_wage',
                'labours.mobile',
                'labours.address',
                'labours.status',
                'labours.user_id',
                'site_masters.site_name',
                'users.name as supervisor_name',
                'contractors.contractor_name',
            )
                ->leftJoin('site_masters', 'site_masters.id', '=', 'labours.site_id')
                ->leftJoin('contractors', 'contractors.id', '=', 'labours.contractor_id')
                ->leftJoin('users', 'users.id', '=', 'labours.supervisor_id');

            $user = Auth::user();
            $role = $user->roles->first();
            if ($role && $role->name === 'Supervisor') {
                $query->where('labours.user_id', $user->id);
            }

            if ($request->filled('site_id')) {
                $query->where('labours.site_id', $request->site_id);
            }



            if ($request->filled('site_id')) {
                $query->where('labours.site_id', $request->site_id);
            }

            // if ($request->filled('supervisor_id')) {
            //     $query->where('labours.supervisor_id', $request->supervisor_id);
            // }
            if ($request->filled('contractor_id')) {
                $query->where('labours.contractor_id', $request->contractor_id);
            }

            $labour = $query->orderBy('labours.id', 'DESC')->simplePaginate(12);

            return response([
                'status' => true,
                'message' => 'Labour List',
                'labour_list' => $labour->items()
            ], 200);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
                // 'error' => $e->getMessage()
            ], 200);
        }
    }


    public function create()
    {
        return view('labour::create');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'labour_name' => ['required'],
        ], [
            'labour_name.required' => __('labour::message.enter_labour_name'),

        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {

            $yearID = getSelectedYear();
            $labour = new Labour();
            $labour->labour_name = ucwords($request->labour_name);
            $labour->supervisor_id = Auth::id();
            $labour->site_id = $request->site_id;
            $labour->contractor_id = $request->contractor_id;
            $labour->daily_wage = $request->daily_wage;
            $labour->mobile = $request->mobile;
            $labour->address = $request->address;
            $labour->status = "Active";
            $labour->user_id = Auth::id();
            $labour->year_id = $yearID;
            $result = $labour->save();
            DB::commit();
            if ($result) {
                DB::commit();
                return response()->json(['status' => true, 'message' => 'Labour created successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Labour can not create.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }


    public function show($id)
    {
        return view('labour::show');
    }

    public function edit($id)
    {
        return view('labour::edit');
    }


    public function update(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'labour_name' => ['required'],
        ], [
            'labour_name.required' => __('labour::message.enter_labour_name'),

        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {

            $yearID = getSelectedYear();
            $labour = Labour::where('id',$request->id)->first();
            $labour->labour_name = ucwords($request->labour_name);
            $labour->supervisor_id = Auth::id();
            $labour->site_id = $request->site_id;
            $labour->contractor_id = $request->contractor_id;
            $labour->daily_wage = $request->daily_wage;
            $labour->mobile = $request->mobile;
            $labour->address = $request->address;
            $labour->status = "Active";
            $labour->user_id = Auth::id();
            $labour->year_id = $yearID;
            $result = $labour->save();
            DB::commit();
            if ($result) {
                DB::commit();
                return response()->json(['status' => true, 'message' => 'Labour Update successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Labour can not create.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:labours,id',
        ], [
            'id.required' => 'ID is required.',
            'id.integer' => 'ID must be an integer.',
            'id.exists' => 'The selected labour ID does not exist.',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }
        try {
            $labour = Labour::where('id', $request->id)->first();
            if (!is_null($labour)) {
                // SiteSupervisor::where('site_master_id', $labour->id)->delete();
                $labour->delete();
                $response = ['status' => true, 'message' => 'Labour deleted successfully.'];
            } else {
                $response = ['status' => false, 'message' => 'Labour not found.'];
            }
            return response($response, 200);
        } catch (\Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function statusChange(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:labours,id',
            'status' => 'required',
        ], [
            'id.required' => 'ID is required.',
            'id.integer' => 'ID must be an integer.',
            'id.exists' => 'The enter labour ID does not exist.',
            'status.required' => 'Enter labours status',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 400);
        }

        try {
            $data = Labour::select('id', 'status')->where('id', $request->id)->first();
            $data->status = $request->status;
            $data->save();
            if (!empty($data)) {
                return response()->json(['status' => true, 'message' => 'Labour status change successfully'], 200);
            } else {
                return response()->json(['status' => true, 'message' => 'Labour not found'], 200);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
}
