<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Requests\Api\User\UserStoreRequest;
use MarkVilludo\Permission\Models\Permission;
use MarkVilludo\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Validator;
use App\User;
use Session;
use Auth;

class UserController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::role('Admin')->get();

        return view('users.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::get();
        return view('users.create', ['roles'=>$roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
              'first_name' => 'required|string',
              'last_name' => 'required|string',
              'email' => 'email|required|unique:users,email',
              'password' => 'required|min:8|max:30',
              'password_confirmation' => 'required|same:password'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = json_encode([$validator->errors()]);
            $statusCode = 422;
        } else {
            $user = User::create($request->only('email', 'first_name', 'last_name', 'password'));

            $roles = $request['roles'];

            if (isset($roles)) {
                foreach ($roles as $role) {
                    $role_r = Role::where('id', '=', $role)->firstOrFail();
                    $user->assignRole($role_r);
                }
            }

            $message = 'Successfully created user.';
        }
        Session::flash('message', $message);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect('users');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::get();

        return view('users.edit', compact('user', 'roles'));
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
        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'email|required|'.Rule::unique('users')->ignore($id, 'id'),
            'password' => 'required|min:8|max:30',
            'password_confirmation' => 'required|same:password'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = json_encode([$validator->errors()]);
            $statusCode = 422;
        } else {
            $roles = $request['roles'];
            $user = User::findOrFail($id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);

            $user->update();

            if (isset($roles)) {
                $user->roles()->sync($roles);
            } else {
                $user->roles()->detach();
            }
            $message = 'Successfully updated user.';
        }
        Session::flash('message', $message);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')
            ->with(
                'flash_message',
                'User successfully deleted.'
            );
    }
}
