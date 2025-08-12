<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginApiController extends Controller
{

    public function index()
    {
        return view('user::index');
    }


    public function create()
    {
        return view('user::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('user::show');
    }

    public function edit($id)
    {
        return view('user::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
