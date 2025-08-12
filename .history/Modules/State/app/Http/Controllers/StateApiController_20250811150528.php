<?php

namespace Modules\State\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    }
}
