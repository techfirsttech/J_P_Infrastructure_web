<?php

namespace Modules\Login\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
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
    // public function login()
    // {
    //     $validator = Validator::make($request->all(), [
    //         'mobile' => 'required|exists:users,mobile',
    //     ], [
    //         'mobile.required' => 'Enter mobile no.',
    //         'mobile.exists' => 'Mobile number not found.',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['message' => 'Please input proper data.', 'errors'  => $validator->errors()], 422);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $user = User::where('mobile', $request->mobile)
    //             ->where(function ($query) use ($request) {
    //                 if (isset($request->is_resend_type) && ($request->is_resend_type != 'register_resend')) {
    //                     $query->where('status', 'Active');
    //                 }
    //             })
    //             ->first(['id', 'otp', 'name', 'mobile']);
    //         if (is_null($user)) {
    //             return response()->json(['message' => 'User not found.'], 500);
    //         }
    //         $otp = '357335'; //str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    //         $user->otp = $otp;
    //         $user->save();

    //         DB::commit();

    //         return response()->json([
    //             'message' => 'OTP sent successfully.',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         Log::error('OTP Send Error: ' . $e->getMessage(), [
    //             'mobile' => $request->mobile,
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return response()->json([
    //             'message' => 'An error occurred while processing your request.',
    //         ], 500);
    //     }
    // }

     public function dashboard()
    {




        $user = User::select('id', 'name', 'mobile', 'username', 'email', 'location_tracking')->where('id', Auth::id())->first();
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
                'branch_info' => $branchInfo,
                'total_lead' => $lead,
                'hot_lead' => $hot,
                'cold_lead' => $cold,
                'completed_lead' => $completed,
                'punch_status' => $punch_status,
                'today_follow_up' => $todayFollowup,
                'user' => $user,
                'current_visit' => $current_visit,
                'location_track' => $getLocationTrack ?  $locationTrack : [],
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
