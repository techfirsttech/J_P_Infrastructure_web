<?php

namespace Modules\Currency\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Currency\Models\Currency;
use Yajra\DataTables\DataTables;

class CurrencyController extends Controller
{
     function __construct()
    {
        $this->middleware('permission:currency-list|currency-create', ['only' => ['index', 'store']]);
        $this->middleware('permission:currency-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:currency-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:currency-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(Currency::select('id', 'currency_name', 'currency_symbol'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $flag = true;
                    $show = '';
                    $edit = true ? 'currency-edit' : '';
                    $delete = $flag ? 'currency-delete' : '';
                    $showURL = "";
                    $editURL = "";
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL'));
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('currency::index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('currency::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency_name' => ['required', Rule::unique('currencies')->where(function ($query) use ($request) {
                if (!is_null($request->id)) {
                    return $query->where([['deleted_at', null], ['id', '!=', $request->id]]);
                } else {
                    return $query->where([['deleted_at', null]]);
                }
            })],
            'currency_symbol' => ['required']
        ], [
            'currency_symbol.required' => __('storagecondition::message.enter_priority'),
            'currency_name.required' => __('storagecondition::message.enter_name'),
            'currency_name.unique' => __('storagecondition::message.enter_unique_name')
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->id)) {
                $currency = Currency::where('id', $request->id)->first();
                $msg = ' updated ';
            } else {
                $currency = new Currency();
                $msg = ' added ';
            }
            $currency->currency_name = $request->currency_name;
            $currency->currency_symbol = $request->currency_symbol;
            $result = $currency->save();
            if (!is_null($result)) {
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'Currency' . $msg . 'successfully.']);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Currency' . $msg . 'failed.']);
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
        return view('currency::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $currency = Currency::where('id', $id)->first();
            if (!is_null($currency)) {
                    return response()->json(['status_code' => 200, 'message' => 'Edit currency ', 'result' => $currency]);

            } else {
                return response()->json(['status_code' => 404, 'message' => 'Currency not found.']);
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
            $currency = Currency::select('id')->where('id', $id)->first();
            if (!is_null($currency)) {
              //  if (storage_delete_check($currency->id)) {
                    $currency->delete();
                    return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
                // } else {
                //     return response()->json(['status_code' => 201, 'message' => 'This Currency already use in another module.']);
                // }
            } else {
                return response()->json(['status_code' => 404, 'message' => 'Currency not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
