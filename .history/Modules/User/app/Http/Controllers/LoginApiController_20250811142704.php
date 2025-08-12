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

    public function dashboard()
    {
        dd('aa');
        $aa = User::get();
      dd($aa);

        $user = User::select('id', 'name', 'email', 'mobile', 'username',  'menu_style', 'theme', 'status', 'designation')->first();
      dd($user);
        $role = $user->roles->first();
        $user->role = $role->name;
        $permissions = Permission::select('id', 'title', 'title_tag', 'name')
            ->join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $role->id)
            ->orderBy('id', 'DESC')
            ->get();
        $permission = collect($permissions)->pluck('name');
        $user->permissions = $permission;
        unset($user->roles);
        try {
            return response([
                'status' => true,
                'message' => 'Dashboard',
                'user' => $user,
            ], 200);
        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => 'Something went wrong'
            ], 200);
        }
    }
}
