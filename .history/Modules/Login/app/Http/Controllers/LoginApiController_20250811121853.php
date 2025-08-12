<?php

namespace Modules\Login\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginApiController extends Controller
{

    public function index()
    {
        return view('login::index');
    }

    public function create()
    {
        return view('login::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('login::show');
    }

    public function edit($id)
    {
        return view('login::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
