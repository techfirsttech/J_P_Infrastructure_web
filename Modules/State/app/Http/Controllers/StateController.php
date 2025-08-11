<?php

namespace Modules\State\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Country\Models\Country;
use Modules\State\Models\State;
use Yajra\DataTables\Facades\DataTables;

class StateController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:state-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:state-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:state-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:state-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(State::with('country:id,name,code'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $flag = state_delete_check($row->id);
                    $show = '';
                    $edit = true ? 'state-edit' : '';
                    $delete = $flag ? 'state-delete' : '';
                    $showURL = "";
                    $editURL = "";
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            $country = Country::select('id', 'name', 'code')->get();
            return view('state::index', compact('country'));
        }
    }

    public function create()
    {
        return view('state::create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', Rule::unique('states')->where(function ($query) use ($request) {
                if (!is_null($request->id)) {
                    return $query->where([['deleted_at', null], ['id', '!=', $request->id]]);
                } else {
                    return $query->where('deleted_at', null);
                }
            })],
        ], [
            'name.required' => __('state::message.enter_name'),
            'name.unique' => __('state::message.enter_unique_name')
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->id)) {
                $state = State::where('id', $request->id)->first();
                $response = ['status_code' => 200, 'data' => route('state.index'), 'message' => 'State updated successfully.'];
            } else {
                $state = new State();
                $response = ['status_code' => 200, 'data' => route('state.index'), 'message' => 'State added successfully.'];
            }

            $state->country_id = $request->country_id;
            $state->name = $request->name;
            $state->code = $request->code;
            $result = $state->save();

            if (!is_null($result)) {
                DB::commit();
                return response()->json($response);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'State added failed']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function show(Request $request)
    {
        try {
            $stateData = State::where('country_id', $request->id)->select('id', 'country_id', 'name', 'code')->get();
            if ($stateData->count() > 0) {
                return response()->json(array('status_code' => 200, 'result' => $stateData));
            } else {
                return response()->json(array('status_code' => 403, 'message' => 'State List Not Found'));
            }
        } catch (\Exception $e) {
            return response()->json(array('status_code' => 500, 'message' => 'Something went wrong. Please try again.'));
        }
    }

    public function edit($id)
    {
        try {
            $state = State::where('id', $id)->first();
            if (!is_null($state)) {
                if (state_delete_check($state->id)) {
                    return response()->json(['status_code' => 200,  'result' => $state]);
                } else {
                    return response()->json(['status_code' => 201, 'message' => 'This state already use in another module.']);
                }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'State not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function update(Request $request, $id) {}

    public function destroy($id)
    {
        try {
            $state = State::select('id')->where('id', $id)->first();
            if (!is_null($state)) {
                if (state_delete_check($state->id)) {
                    $state->delete();
                    return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                } else {
                    return response()->json(['status_code' => 201, 'message' => 'This state already use in another module.']);
                }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'State not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
