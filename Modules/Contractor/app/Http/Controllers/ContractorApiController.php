<?php

namespace Modules\Contractor\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Contractor\Models\Contractor;
use Modules\Labour\Models\Labour;

class ContractorApiController extends Controller
{

    public function index(Request $request)
    {
        try {
            $contractor = Contractor::select('id', 'site_id', 'contractor_name', 'mobile')->orderBy('id', 'DESC')->simplePaginate(12);
            return response(['status' => true, 'message' => 'Contractor List', 'contractor_list' => $contractor->items()], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function create()
    {
        return view('contractor::create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contractor_name' => ['required', Rule::unique('contractors')->where(function ($query) use ($request) {
                if (!is_null($request->id)) {
                    return $query->where([['deleted_at', null], ['id', '!=', $request->id]]);
                } else {
                    return $query->where([['deleted_at', null]]);
                }
            })],
        ], [
            'contractor_name.required' => __('contractor::message.enter_contractor_name'),
            'contractor_name.unique' => __('contractor::message.enter_unique_contractor_name')
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {

            $yearID = getSelectedYear();
            $contractor = new Contractor();
            $contractor->site_id = $request->site_id;
            $contractor->contractor_name = $request->contractor_name;
            $contractor->mobile = $request->mobile;
            $contractor->year_id = $yearID;
            $result = $contractor->save();
            DB::commit();
            if ($result) {
                DB::commit();
                return response()->json(['status' => true, 'message' => 'Contractor created successfully.'], 200);
            } else {
                DB::rollBack();
                return response(['status' => false, 'message' => 'Contractor can not create.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function show($id)
    {
        return view('contractor::show');
    }

    public function edit($id)
    {
        return view('contractor::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:contractors,id',
        ], [
            'id.required' => 'ID is required.',
            'id.integer' => 'ID must be an integer.',
            'id.exists' => 'The selected Contractors ID does not exist.',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response, 400);
        }
        try {
            $contractor = Contractor::where('id', $request->id)->first();
            if (!is_null($contractor)) {
                $contractor->delete();
                $response = ['status' => true, 'message' => 'Contractor successfully.'];
            } else {
                $response = ['status' => false, 'message' => 'Contractor not found.'];
            }
            return response($response, 200);
        } catch (\Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

    public function ContractorDropdown(Request $request)
    {
        try {
            $contractorQuery = Contractor::select('id', 'contractor_name', 'mobile')
                ->orderBy('id', 'DESC');

            if (!empty($request->site_id)) {
                $contractorQuery->where('site_id', $request->site_id);
            }

            $contractor = $contractorQuery->get();
            return response(['status' => true, 'message' => 'Contractor Dropdown', 'contractor_list' => $contractor], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
    public function contractorLabourDropdown(Request $request)
    {
        try {
            $labour = Labour::select('id', 'labour_name')->where('id', $request->contractor_id)->orderBy('id', 'DESC')->get();
            return response(['status' => true, 'message' => 'Contractor wise Labour Dropdown', 'contractor_labour_list' => $labour], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
}
