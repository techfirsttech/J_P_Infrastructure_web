<?php

namespace Modules\Party\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\IncomeMaster\Models\IncomeMaster;
use Modules\Party\Models\Party;
use Yajra\DataTables\Facades\DataTables;

class PartyController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:party-list|party-create', ['only' => ['index', 'store']]);
        $this->middleware('permission:party-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:party-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:party-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $party =  Party::select('id',  'party_name')->orderBy('id', 'DESC')->get();
        if (request()->ajax()) {
            return DataTables::of($party)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $flag = true;
                    $show = '';
                    $edit = true ? 'party-edit' : '';
                    $delete = $flag ? 'party-delete' : '';
                    $showURL = "";
                    $editURL = "";
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
                })

                ->escapeColumns([])
                ->make(true);
        } else {

            return view('party::index');
        }
        // return view('contractor::index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('party::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'party_name' => ['required', Rule::unique('parties')->where(function ($query) use ($request) {
                if (!is_null($request->id)) {
                    return $query->where([['deleted_at', null], ['id', '!=', $request->id]]);
                } else {
                    return $query->where([['deleted_at', null]]);
                }
            })],
        ], [
            'party_name.required' => __('party::message.enter_party_name'),
            'party_name.unique' => __('party::message.enter_unique_party_name')
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->id)) {
                $party = Party::where('id', $request->id)->first();
                $msg = ' updated ';
            } else {
                $party = new Party();
                $msg = ' added ';
            }
            $party->party_name = $request->party_name;
            $result = $party->save();
            if (!is_null($result)) {
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'Party' . $msg . 'successfully.']);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Party' . $msg . 'failed.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('party::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $party = Party::where('id', $id)->first();
            if (!is_null($party)) {
                return response()->json(['status_code' => 200, 'message' => 'Edit party ', 'result' => $party]);
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Party not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $party = Party::select('id')->where('id', $id)->first();
            if (!is_null($party)) {
                $income = IncomeMaster::where('party_id', $party->id)->count();
                if ($income == 0) {
                    $party->delete();
                    return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                } else {
                    return response()->json(['status_code' => 201, 'message' => 'This Party already use in another module.']);
                }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Party not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
