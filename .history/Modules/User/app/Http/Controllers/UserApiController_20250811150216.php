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
             $supervisor = User::select('id', 'name')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'supervisor');
            })
            ->orderBy('id', 'DESC')
            ->get();
            // $supervisor = User::select('id', 'name')->orderBy('id', 'DESC')->get();
            return response(['status' => true, 'message' => 'Product List', 'result' => $supervisor], 200);
        } catch (Exception $e) {
            return response(['status' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
    }

     public function dashboard()
    {
        $user = User::select('id', 'name', 'email', 'mobile', 'username',  'menu_style', 'theme', 'status', 'designation')->where('id',Auth::id())->first();
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
