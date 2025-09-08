<?php

namespace Modules\Contractor\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Contractor\Models\Contractor;
use Modules\SiteMaster\Models\SiteMaster;
use Yajra\DataTables\Facades\DataTables;

class ContractorController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:contractor-list|contractor-create', ['only' => ['index', 'store']]);
        $this->middleware('permission:contractor-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:contractor-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:contractor-delete', ['only' => ['destroy']]);
    } 

    public function index()
    {
         $contractor =  Contractor::select('contractors.id', 'contractors.site_id','contractors.contractor_name', 'contractors.mobile','site_masters.site_name')
            ->leftJoin('site_masters', 'site_masters.id', '=', 'contractors.site_id')
            ->orderBy('contractors.id', 'DESC')
            ->get();
        if (request()->ajax()) {
            return DataTables::of($contractor)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $flag = true;
                    $show = '';
                    $edit = true ? 'contractor-edit' : '';
                    $delete = $flag ? 'contractor-delete' : '';
                    $showURL = "";
                    $editURL = "";
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
                })

                ->escapeColumns([])
                ->make(true);
        } else {
            $site = SiteMaster::get();
            return view('contractor::index',compact('site'));
        }
        // return view('contractor::index');
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
            'site_id' => 'required',
        ], [
            'site_id.required' => __('contractor::message.select_site'),
            'contractor_name.required' => __('contractor::message.enter_contractor_name'),
            'contractor_name.unique' => __('contractor::message.enter_unique_contractor_name')
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->id)) {
                $contractor = Contractor::where('id', $request->id)->first();
                $msg = ' updated ';
            } else {
                $contractor = new Contractor();
                $msg = ' added ';
            }
            $contractor->site_id = $request->site_id;
            $contractor->contractor_name = $request->contractor_name;
            $contractor->mobile = $request->mobile;
            $result = $contractor->save();
            if (!is_null($result)) {
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'Contractor' . $msg . 'successfully.']);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Contractor' . $msg . 'failed.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function show($id)
    {
        return view('contractor::show');
    }

    public function edit($id)
      {
        try {
            $contractor = Contractor::where('id', $id)->first();
            if (!is_null($contractor)) {
                return response()->json(['status_code' => 200, 'message' => 'Edit contractor ', 'result' => $contractor]);
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Contractor not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }


    public function update(Request $request, $id) {}

    public function destroy($id) {
        try {
            $contractor = Contractor::select('id')->where('id', $id)->first();
            if (!is_null($contractor)) {
                //  if (storage_delete_check($contractor->id)) {
                $contractor->delete();
                return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                // } else {
                //     return response()->json(['status_code' => 201, 'message' => 'This Contractor already use in another module.']);
                // }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Contractor not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
