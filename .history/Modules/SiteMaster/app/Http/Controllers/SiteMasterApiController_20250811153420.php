<?php

namespace Modules\SiteMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteMasterApiController extends Controller
{

    public function index()
    {
        return view('sitemaster::index');
    }

    public function create()
    {
        return view('sitemaster::create');
    }
    public function store(Request $request) {}

    public function show($id)
    {
        return view('sitemaster::show');
    }

    public function edit($id)
    {
        return view('sitemaster::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
