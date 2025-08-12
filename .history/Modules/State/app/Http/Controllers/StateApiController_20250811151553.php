<?php

namespace Modules\State\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\Country\Models\Country;
use Modules\State\Models\State;

class StateApiController extends Controller
{

    public function index()
    {
        return view('state::index');
    }

    public function create()
    {
        return view('state::create');
    }

    public function store(Request $request) {}


    public function show($id)
    {
        return view('state::show');
    }

    public function edit($id)
    {
        return view('state::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}

    public function stateList()
    {
        try {
            $county = Country::where('name','India')->value('id')
            $state = State::select('id', 'name')
                ->orderBy('id', 'DESC')
                ->get();
            return response(['status' => true, 'message' => 'State List', 'result' => $state], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
}
