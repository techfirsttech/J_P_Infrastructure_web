<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\User\Models\User;

class UserApiController extends Controller
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

    public function supervisorList()
    {
        try {
            $supervisor = User::select('id', 'name')->orderBy('id', 'DESC')->get();
            return response(['status' => true, 'message' => 'Product List', 'result' => $supervisor], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }
}
