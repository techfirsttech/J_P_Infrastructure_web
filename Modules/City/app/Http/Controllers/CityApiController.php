<?php

namespace Modules\City\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\City\Models\City;
use Modules\Country\Models\Country;

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

    public function cityList(Request $request )
    {
        try {
            $county = Country::where('name','India')->value('id');
            $city = City::select('id', 'name')->where('state_id',$request->id)
                ->orderBy('name', 'ASC')
                ->get();
            return response(['status' => true, 'message' => 'City List', 'result' => $city], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
}
