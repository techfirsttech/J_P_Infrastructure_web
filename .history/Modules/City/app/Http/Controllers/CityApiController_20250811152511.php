<?php

namespace Modules\City\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CityApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('city::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('city::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('city::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('city::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}

    public function stateList()
    {
        try {
            $county = Country::where('name','India')->value('id');
            $state = State::select('id', 'name')->where('country_id',$county)
                ->orderBy('id', 'DESC')
                ->get();
            return response(['status' => true, 'message' => 'State List', 'result' => $state], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
}
