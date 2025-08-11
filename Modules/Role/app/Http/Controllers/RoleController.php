<?php

namespace Modules\Role\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $roles = Role::with('users')->where('id', '!=', '1')->orderBy('name', 'DESC')->get();
        // $permissions = Permission::all();
        // $permission = [];
        // foreach ($permissions as $key => $value):
        //     $permission[Str::slug($value->title_tag)]['name'] = $value->title_tag;
        //     $permission[Str::slug($value->title_tag)]['child'][] = $value;
        // endforeach;
        // return view('role::index', compact('roles', 'permission'));
        return view('role::index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(): View
    {


        $permissions =  Permission::where('title_tag', '!=', 'Menu')->get();

        $permission = [];
        foreach ($permissions as $key => $value):
            $permission[Str::slug($value->title_tag)]['name'] = $value->title_tag;
            $permission[Str::slug($value->title_tag)]['child'][] = $value;
        endforeach;


        return view('role::create', compact('permission'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $this->validate($request, [
                'name' => 'required|unique:roles,name',
                'permission' => 'required',
            ], [
                'name.required' => __('role::message.enter_name'),
                'name.unique' => 'The role name must be unique.',
                'permission.required' => 'At least one permission must be selected.',
            ]);
            $role = Role::create(['name' => $request->input('name'), 'title' => '']);
            $permissions = Permission::whereIn('id', $request->input('permission'))->get();
            $role->syncPermissions($permissions);

            return redirect()->route('roles.index')
                ->with('success', 'Role created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Validation failed: ' . implode(', ', $e->validator->errors()->all()));
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get();
        return view('role::show', compact('role', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $role = Role::find($id);
        if ($role->name == "Super Admin") {
            $permissions = Permission::all();
        } else {
            $permissions = Permission::where('title_tag', '!=', 'Menu')->get();
        }
        $permission = [];
        foreach ($permissions as $key => $value):
            $permission[Str::slug($value->title_tag)]['name'] = $value->title_tag;
            $permission[Str::slug($value->title_tag)]['child'][] = $value;
        endforeach;
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        return view('role::edit', compact('role', 'permission', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        if ($id != 1 && $id != 2 && $id != 3) {

            $role->name = $request->input('name');
            $role->save();
        }
        $permissions = Permission::whereIn('id', $request->input('permission'))->get();
        $role->syncPermissions($permissions);
        return redirect()->route('roles.index')->with('success', 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     */
    //     public function destroy(string $id)
    //     {
    //         $role = Role::findOrFail($id);
    //         $role->delete();
    //         return response()->json(['status_code' => 200, 'message' => 'Role deleted successfully']);
    //     }

    public function destroy(string $id)
    {
        $role = Role::findById($id);

        //check is not assign to any user
        if ($role->users()->count() > 0) {
            return response()->json([
                'status_code' => 403,
                'message' => 'This role cannot be deleted because it is assigned to users.',
            ]);
        }
        // Optional: Check if role is protected (like 'admin')
        if (in_array($role->name, ['admin'])) {
            return response()->json([
                'status_code' => 403,
                'message' => 'This role cannot be deleted.',
            ]);
        }
        $role->delete();
        return response()->json([
            'status_code' => 200,
            'message' => 'Role deleted successfully',
        ]);
    }
}
