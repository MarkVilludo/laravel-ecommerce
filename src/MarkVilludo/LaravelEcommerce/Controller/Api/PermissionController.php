<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Permission\PermissionStoreRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MarkVilludo\Permission\Models\Role;
use MarkVilludo\Permission\Models\Permission;
use Auth;
use Response;
use Validator;
use Config;

class PermissionController extends Controller
{

   
    public function __construct(Permission $permission, Role $role)
    {
        $this->role = $role;
        $this->permission = $permission;
        $this->middleware(['auth']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $permissions = $this->permission->all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PermissionStoreRequest $request)
    {
        $name = $request->name;
        $module = $request->module;
        $permission = new Permission();
        $permission->name = $name;
        $permission->module = $module;

        $roles = $request->roles;
        
        if ($permission->save()) {
            if (!empty($request->roles)) {
                foreach ($roles as $role) {
                    $r = $this->role->where('id', '=', $role)->firstOrFail(); //Match input role to db record

                    $permission = $this->permission->where('name', '=', $name)->first();
                    $r->givePermissionTo($permission);
                }
            }
            $data['message'] = config('app_messages.SuccessCreatePermission');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.SomethingWentWrong');
            $statusCode = 400;
        }
        return Response::json(['data' => $data], $statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
  
        $rules = ['name' => 'required|unique:permissions,name,'.$id];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $data['errors'] = [$validator->errors()];
            $statusCode = 422;
        } else {
            $permission = Permission::findOrFail($id);
            $permission->name = $request->name;
            $permission->module = $request->module;
            $permission->save();

            $data['message'] = config('app_messages.SuccessUpdatePermission');
            $statusCode = 200;
        }
        return Response::json(['data' => $data], $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return config('app_messages.NotYetimplemented');
        //reason need to remove role attach in $id (permission).

        // $permission = Permission::findOrFail($id);
        // $permission->delete();
    }
}
