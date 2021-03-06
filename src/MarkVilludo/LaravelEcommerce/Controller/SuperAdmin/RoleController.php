<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use MarkVilludo\Permission\Models\Role;
use MarkVilludo\Permission\Models\Permission;
use Session;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['roles'] = Role::all();

        return view('superadmin.roles.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $permissionsArray = Permission::all();

        // begin the iteration for grouping module name
        $permissions = [];
        $modulefunctionArray = [];
        $result = [];

        foreach ($permissionsArray as $key => $module) {
            //Group module by guard nmae
            $modulefunctionArray[$module->module] = [
                'module' => $module->module,
                'guard_name' => $module->guard_name,
                'id' => $module->id
            ];
        }
        foreach ($modulefunctionArray as $keyModule => $value) {
            $moduleFunction = [];
            $moduleName = $value['module'];
            foreach ($permissionsArray as $key => $module) {
                if ($module->module == $moduleName) {
                    $moduleFunction[] = ['id' => $module->id,'module' => $module->module,'name' => $module->name];
                }
            }
            $permissions[] = ['module' => $value['module'],'id' => $value['id'], 'module_functions' => $moduleFunction];
        }


        return view('superadmin.roles.create', ['permissions'=> $permissions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $this->validate($request, [
            'name'=>'required|unique:roles|min:4|max:25',
            'permissions' =>'required',
            ]);

        $name = $request['name'];
        $role = new Role();
        $role->name = $name;

        $permissions = $request['permissions'];

        $role->save();

        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail();
            $role = Role::where('name', '=', $name)->first();
            $role->givePermissionTo($p);
        }

        return redirect()->route('roles.index')
            ->with(
                'flash_message',
                'Role'. $role->name.' added!'
            );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect('roles');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $role = Role::findOrFail($id);
        $permissionsArray = Permission::all();

        // begin the iteration for grouping module name
        $permissions = [];
        $modulefunctionArray = [];
        $result = [];
        foreach ($permissionsArray as $key => $module) {
            $modulefunctionArray[$module->module] = [
                'module' => $module->module,
                'guard_name' => $module->guard_name,
                'id' => $module->id
            ];
        }
        foreach ($modulefunctionArray as $keyModule => $value) {
            $moduleFunction = [];
            $moduleName = $value['module'];
            foreach ($permissionsArray as $key => $module) {
                if ($module->module == $moduleName) {
                    $moduleFunction[] = ['id' => $module->id,'module' => $module->module,'name' => $module->name];
                }
            }
            $permissions[] = ['module' => $value['module'],'id' => $value['id'], 'module_functions' => $moduleFunction];
        }

        return view('superadmin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        // return $request->all();
        $role = Role::findOrFail($id);
        $this->validate($request, [
            'name'=>'required|'.Rule::unique('roles')->ignore($id, 'id'),
            'permissions' =>'required',
        ]);

        $input = $request->except(['permissions']);
        $permissions = $request['permissions'];
        $role->fill($input)->save();
        $p_all = Permission::all();

        foreach ($p_all as $p) {
            $role->revokePermissionTo($p);
        }

        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail(); //Get corresponding form permission in db
            $role->givePermissionTo($p);
        }

        return redirect()->route('roles.index')
            ->with(
                'flash_message',
                'Role'. $role->name.' updated!'
            );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')
            ->with(
                'flash_message',
                'Role deleted!'
            );
    }
}
