<?php

namespace Modules\Labour\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Labour\Models\Labour;

class LabourController extends Controller
{

    public function index()
    {
        return view('labour::index');
    }

    public function create()
    {
        return view('labour::create');
    }

    public function store(Request $request) {}
    public function show($id)
    {
        return view('labour::show');
    }

    public function edit($id)
    {
        return view('labour::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
