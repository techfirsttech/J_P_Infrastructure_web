<?php

namespace Modules\User\Http\Controllers;

use Spatie\Permission\Models\Role;
use Modules\User\Models\User;
use Modules\User\Models\UserHierarchy;
use Modules\User\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:users-list|users-create|users-edit|users-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:users-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:users-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:users-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(UserProfile::with('user')->where('user_id', '!=', '1'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $show = '';
                    $edit = 'users-edit';
                    $delete = 'users-delete';
                    $assign = ''; //'assign-user-list';
                    $showURL = "";
                    $editURL = route('users.edit', $row->id);
                    return view('layouts.action', compact('row', 'show', 'edit', 'delete', 'showURL', 'editURL', 'assign'));
                })

                ->addColumn('is_blocked', function ($row) {
                    //                    return $row->user->is_blocked;
                    return loginStatus($row);
                })
                ->addColumn('role', function ($row) {
                    $roles = '';
                    foreach ($row->user->getRoleNames() as $v) {
                        $roles .= '<span class="label bg-label-success p-50 px-2">' . ucwords($v) . '</span> ';
                    }
                    return $roles;
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('user::index');
        }
    }

    public function create()
    {
        $array_role = ['Super Admin'];
        $roleMaster = Role::whereNotIn('name', $array_role)->pluck('name', 'name')->all();
        return view('user::create', compact('roleMaster'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('deleted_at', '=', null);
                }),
                'numeric',
                'digits:10'
            ],
            'username' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('deleted_at', '=', null);
                }),
            ],
            'password' => 'required_with:confirm_password|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'confirm_password' => 'required|same:password',
            'designation' => 'required',
            'roles' => 'required'
        ], [
            'mobile.required' => __('user::message.enter_mobile'),
            'mobile.numeric' => __('user::message.enter_mobile'),
            'mobile.digits' => __('user::message.enter_digits'),
            'username.required' => __('user::message.enter_username'),
            'password.min' => __('user::message.enter_password_min'),
            'password.regex' => __('user::message.enter_password_regex'),
            'designation' => __('user::message.select_designation'),
            'roles' => __('user::message.select_role')
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = ucwords($request->firstname) . ' ' . ucwords($request->lastname);
            $user->email = strtolower($request->email);
            $user->mobile = $request->mobile;
            $user->username = $request->username;
            $user->status = $request->status;
            $user->designation = $request->designation;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
            $user->syncRoles([]);
            $user->assignRole($request->input('roles'));

            $userProfile = new UserProfile();
            $userProfile->user_id = $user->id;
            $userProfile->firstname = ucwords($request->firstname);
            $userProfile->lastname = ucwords($request->lastname);
            $userProfile->date_of_birth = (!empty($request->dateofbirth)) ? date('Y-m-d', strtotime($request->dateofbirth)) : null;
            $result = $userProfile->save();
            DB::commit();

            if ($result) {
                DB::commit();
                return redirect()->route('users.index')->with('success', 'User added successfully');
            } else {
                DB::rollback();
                return redirect()->back()->with('warning', 'User added failed');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try again');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $userProfile = UserProfile::with('user')->where('id', $id)->first();
        $user = User::find($userProfile->user_id);
        $userRole = $user->roles->pluck('name')->toArray();
        $array_role = ['Super Admin'];
        $roleMaster = Role::whereNotIn('name', $array_role)->pluck('name', 'name')->all();
        // $locations = Location::get();
        return view('user::edit', compact('roleMaster', 'userProfile', 'user', 'userRole'));
    }

    public function changeLayout(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::user()->id);
            $user->menu_style = $request->menu_style;
            $user->theme = $request->theme;
            $user->save();
            if ($user) {
                DB::commit();
                return redirect()->route('users.index')->with('success', 'Layout changed successfully');
            } else {
                DB::rollback();
                return redirect()->back()->with('error', 'Temporary not available');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try again');
        }
    }

    public function update($id, Request $request)
    {
        $userProfile = UserProfile::where('id', $request->user_profile_id)->first();
        $user = User::where('id', $userProfile->user_id)->first();
        $validator = Validator::make($request->all(), [
            'mobile' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('deleted_at', '=', null);
                })->ignore($user->id),
            ],
            'username' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('deleted_at', '=', null);
                })->ignore($user->id),
            ],
            'email' => [
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('deleted_at', '=', null);
                })->ignore($user->id),
            ],
            'password' => 'nullable|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'confirm_password' => 'nullable|same:password',
            'roles' => 'required'
        ], [
            'mobile.required' => __('user::message.enter_mobile'),
            'mobile.numeric' => __('user::message.enter_mobile'),
            'mobile.digits' => __('user::message.enter_digits'),
            'username.required' => __('user::message.enter_username'),
            'password.min' => __('user::message.enter_password_min'),
            'password.regex' => __('user::message.enter_password_regex'),
            'roles' => __('user::message.select_designation')
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $user = User::where('id', $userProfile->user_id)->first();
            $user->name = ucwords($request->firstname) . ' ' . ucwords($request->lastname);
            $user->email = strtolower($request->email);
            $user->mobile = $request->mobile;
            $user->username = $request->username;
            // $user->status = 'Active';
            $user->status = $request->status;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
            $user->syncRoles([]);
            $user->assignRole($request->input('roles'));

            $userProfile->firstname = ucwords($request->firstname);
            $userProfile->lastname = ucwords($request->lastname);
            $userProfile->date_of_birth = (!empty($request->dateofbirth)) ? date('Y-m-d', strtotime($request->dateofbirth)) : null;
            $result = $userProfile->save();
            if ($result) {
                DB::commit();
                return redirect()->route('users.index')->with('success', 'User updated successfully');
            } else {
                DB::rollback();
                return redirect()->back()->with('warning', 'User updated failed');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try again');
        }
    }

    public function destroy($id)
    {
        try {
            $userProfile = UserProfile::where('id', $id)->first();
            $user = User::findOrFail($userProfile->user_id);
            $user->delete();
            $userProfile->delete();
            return response()->json(['status_code' => 200, 'message' => 'Deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function assignUserWise(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ], [
            'id.required' => 'Parent user not found.',
        ]);

        if ($validator->fails()) {
            $response = ['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        try {
            $id = $request->id;
            $parentUser = User::select('name', 'id')->where('id', '=', $id)->first();
            $getUserChildIds = UserHierarchy::where('parent_id', $id)->pluck('user_id')->toArray();
            $userProfileData = User::select('id', 'name', 'mobile')->where('id', '!=', $id)->whereNotIn('id', $getUserChildIds)->get();
            $array_role = ['Super Admin', 'Admin'];
            $roleMaster = Role::whereNotIn('name', $array_role)->select('name', 'id')->get();
            $userProfile = array();
            foreach ($roleMaster as $ro) {
                $userArray = array();
                foreach ($userProfileData as $us) {
                    if ($ro->name == $us->getRoleNames()->first()) {
                        $userArray[] = array(
                            'id' => $us->id,
                            'name' => $us->name,
                            'user_id' => $us->id,
                            'mobile' => $us->mobile,
                            'role_name' => $us->getRoleNames()->first(),
                        );
                    }
                }
                $userProfile[$ro->name] = $userArray;
            }

            $getUserTreesData = UserHierarchy::with('user:id,name')->select('id', 'user_id', 'parent_id')->where('parent_id', '=', $id)->get();
            $getUserTree = $this->buildUserTree($getUserTreesData);
            if (!is_null($parentUser) && !is_null($userProfile)) {
                $return = view('user::assign-user', compact('userProfile', 'parentUser', 'getUserTree'))->render();
                return response()->json(array('status_code' => 200, 'message' => 'User found.', 'result' => $return));
            } else {
                return response()->json(array('status_code' => 500, 'message' => 'User not found.'));
            }
        } catch (\Exception $e) {
            return response()->json(array('status_code' => 500, 'message' => 'Something went wrong. Please try again.' . $e));
        }
    }

    private function buildUserTree($users)
    {
        $tree = [];
        foreach ($users as $user) {
            $children = UserHierarchy::with('user:id,name')
                ->select('id', 'user_id', 'parent_id')
                ->where('parent_id', '=', $user->user_id)->get();
            $user->children = $this->buildUserTree($children);
            $tree[] = $user;
        }
        return $tree;
    }

    public function assignUserStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'child_id.*' => 'required|integer',
            'parent_id' => 'required|integer',
        ], [
            'child_id.*.required' => 'Select child user.',
            'parent_id.required' => 'Select parent user',
            'child_id.*.integer' => 'Child user required is integer',
            'parent_id.integer' => 'Parent user required is integer',
        ]);

        if ($validator->fails()) {
            $response = ['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()];
            return response()->json($response);
        }

        DB::beginTransaction();
        try {
            if (isset($request->parent_id) && ($request->child_id)) {
                $childIds = $request->child_id;
                $result = '';
                foreach ($childIds as $childId) {
                    // $userProfile = UserHierarchy::where([]'parent_id', $childId)->first();
                    // if ($userProfile) {
                    //     $userProfile->parent_id = $request->parent_id;
                    //     $userProfile->user_id = $childId;
                    //     $result = $userProfile->save();
                    // }else{
                    $userProfile = new UserHierarchy();
                    $userProfile->parent_id = $request->parent_id;
                    $userProfile->user_id = $childId;
                    $result = $userProfile->save();
                    // }
                }

                if ($result) {
                    DB::commit();
                    return response()->json(array('status_code' => 200, 'message' => 'User updated successfully.'));
                } else {
                    return response()->json(array('status_code' => 500,  'message' => 'User updated filed.'));
                }
            } else {
                DB::rollback();
                return response()->json(array('status_code' => 500, 'message' => 'Something went wrong. Please try again.'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('status_code' => 500, 'message' => 'Something went wrong. Please try again.'));
        }
    }

    public function assignUserRemove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:users,id',
            'parentId' => 'required|integer|exists:users,id',
        ], [
            'id.required' => 'User is required',
            'id.integer' => 'User required is integer',
            'id.exists' => 'Selected user does not exist',
            'parentId.required' => 'Parent user is required',
            'parentId.integer' => 'Parent user is required integer',
            'parentId.exists' => 'Selected user does not exist',
        ]);

        if ($validator->fails()) {
            $response = ['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()];
            return response()->json($response);
        }

        DB::beginTransaction();
        try {
            $userUpdate = UserHierarchy::where([['user_id', '=', $request->id], ['parent_id', '=', $request->parentId]])->first();
            if ($userUpdate) {
                $result = $userUpdate->delete();
                if ($result) {
                    DB::commit();
                    return response()->json(array('status_code' => 200, 'message' => 'User remove successfully.'));
                } else {
                    return response()->json(array('status_code' => 500,  'message' => 'User remove filed.'));
                }
            } else {
                return response()->json(array('status_code' => 500,  'message' => 'User not found.'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('status_code' => 500, 'message' => 'Something went wrong. Please try again.'));
        }
    }

    public function language(Request $request)
    {
        App::setLocale($request->lang);
        session()->put('locale', $request->lang);
        return redirect()->back();
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required_with:confirm_password|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'confirm_password' => 'same:password'
        ], [
            'password.min' => __('user::message.enter_password_min'),
            'password.regex' => __('user::message.enter_password_regex'),
        ]);
        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $user = User::where('id', Auth::id())->first();
            if (!is_null($user) && Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->password);
                $user->save();
                DB::commit();
                return response()->json(['status_code' => 200, 'message' => 'Your password has been updated.']);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Current password does not match.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function yearChange(Request $request)
    {
        session()->put('year', $request->year);
        return redirect()->back();
    }

    public function statusChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:users,id',
            'status' => 'required',
        ], [
            'id.required' => 'ID is required.',
            'id.integer' => 'ID must be an integer.',
            'id.exists' => 'The enter user id does not exist.',
            'status.required' => 'Enter user status',
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 201, 'message' => 'Please input proper data.', 'errors' => $validator->errors()]);
        }

        try {

            User::where('id', $request->id)->update(['is_blocked' => $request->status, 'login_attempts' => 0]);
            return response()->json(['status_code' => 200, 'message' => 'Status change successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function dashboard()
    {
dd(Auth::id());
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
