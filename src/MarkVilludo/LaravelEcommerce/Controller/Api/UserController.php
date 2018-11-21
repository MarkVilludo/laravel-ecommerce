<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\User\UserStoreRequest;
use MarkVilludo\Permission\Models\Permission;
use App\Http\Resources\CustomerResource;
use MarkVilludo\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\User;

use Validator;
use Config;
use Activity;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function index()
    {
        $users = UserResource::collection($this->user->role(['Admin'])->active()->paginate(10));

        if ($users) {
            $data['message'] = 'Users list';
            $statusCode = 200;
        } else {
            $data['message'] = 'No users available';
            $statusCode = 200;
        }
        $data['users'] = $users;
        $data['roles'] = Role::get();
        return response()->json(['data' => $data], $statusCode);
    }
    //Customer list
    public function getCustomers()
    {
        
        $customers =  $this->user->role('Customer')->paginate(5);

        if ($customers) {
            $data =  CustomerResource::collection($customers);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoCustomerAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }

    public function searchCustomer(Request $request)
    {
        // return $request->all();
        // all `User`s that contain the string "John" in their name
         $customers = $this->user->role('Customer')->getByName($request->name)
                                                    ->getSortBy($request->sortBy, $request->orderBy)
                                                    ->paginate(10);
        if ($customers) {
            $data = CustomerResource::collection($customers);
            return $data;
        } else {
            $data['message'] = 'There is no customers available.';
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }

     /**
     * Display user orders
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserAccountDetails(Request $request, $user_id)
    {
       
        // return $user_id;
        $user = $this->user->where('id', $user_id)->with('orders')->with('wishlist')
                            ->with('productReviews')
                            ->first();
        if ($user) {
            $data['user'] = new UserResource($user);
            $data['message'] = config('app_messages.ShowsUserDetails');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.NotFoundUser');
            $statusCode = 400;
        }

        return response()->json($data, $statusCode);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        //clear cache customers
        Cache::forget('customers');

        // return $request->all();
        $newUser = new $this->user;
        $newUser->first_name = $request->first_name;
        $newUser->last_name = $request->last_name;
        $newUser->email = $request->email;
        $newUser->status = $request->status;
        $newUser->password = bcrypt($request->password);
        
        if ($newUser->save()) {
            $roles = $request['roles']; //Retrieving the roles field
            //Checking if a role was selected
            if (isset($roles)) {
                foreach ($roles as $role) {
                    $role_r = Role::where('id', '=', $role)->firstOrFail();
                    $newUser->assignRole($role_r); //Assigning role to user
                }
            }
          

            $data['message'] = config('app_messages.SuccessAddedUser');
            $statusCode = 200;
        } else {
            $data['message'] = 'Something went wrong, Please try again later.';
            $statusCode = 400;
        }
        return response()->json($data, $statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (auth()->user()->can('check-role-permission', 'Show user details')) {
            $user =  $this->user->getUserDetails($id);

            if ($user) {
                $user = new UserResource($user);
                $data['message'] = config('app_messages.ShowUserDetails');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.NotFoundUser');
                $statusCode = 404;
            }
            $data['data'] = $user;
        } else {
            $data['message'] = config('app_messages.NoAccess');
            $statusCode = 401;
        }
        return response()->json($data, $statusCode);
    }

    // user profile
    public function profile()
    {
        $user =  $this->user->getUserDetails(auth()->user()->id);

        if ($user) {
            $user = new UserResource($user);
            $data['message'] = config('app_messages.ShowUserDetails');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.NotFoundUser');
            $statusCode = 404;
        }
        $data['data'] = $user;
     
        return response()->json($data, $statusCode);
    }
    /**
     * Update user profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //clear cache customers
        Cache::forget('customers');
        //update user details only.
        $rules = ['first_name' => 'required|string',
                   'last_name' => 'required|string',
                   'email' => 'required|email|'.Rule::unique('users')->ignore($id, 'id'),
                   'password' => $request->password ? 'min:8|max:30|required_with:password_confirmation' : 'nullable',
                   'password_confirmation' => 'same:password|required_with:password'

        ];

        $messages = [
          'password.same'  => config('app_messages.PasswordDontMatch'),
          'password_confirmation.same'  => config('app_messages.PasswordDontMatch')
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data['errors'] = [$validator->errors()];
            $statusCode = 422;
        } else {
            $user = $this->user->findOrFail($id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = $request->password ? bcrypt($request->password) :  $user->password;

            if ($user->save()) {
                if (!$user->hasRole('Customer')) {
                    $roles = $request->roles;

                    if (isset($roles)) {
                        $user->roles()->sync($roles);
                    } else {
                        $user->roles()->detach();
                    }
                }
                
                //Activity logs
                //user helper
                $data['user_id'] = $user->id;
                $data['type'] = 'user';
                $data['action'] = 'Update Profile';
                $data['description'] = 'Update profile settings.';
                Helper::storeActivity($data);
                //End activity logs
                
                $data['message'] = config('app_messages.SuccessUpdatedUserDetails');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.SomethingWentWrong');
                $statusCode = 400;
            }
        }
        return response()->json($data, $statusCode);
    }

    //On - off notifications setting for each user
    public function notification(Request $request, $userId)
    {
        // return $request->all();
        $user = $this->user->find($userId);

        if ($user) {
            if ($request->notify) {
                $user->is_notify = 1;
            } else {
                $user->is_notify = 0;
            }
            $user->update();
            $statusCode = 200;

            $data['message'] = config('app_messages.UpdateUserNotification');
        } else {
            $data['message'] = config('app_messages.NotFoundUser');
            $statusCode = 404;
        }
        return response()->json($data, $statusCode);
    }

    /**
     * deactivate user account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deactivateAccount(Request $request, $userId)
    {
        //
        //clear cache customers
        Cache::forget('customers');

        $user = $this->user->find($userId);
        if ($user) {
            if (!$request->status) {
                $user->status = 1;
            } else {
                $user->status = 0;
            }
            $user->update();
            $statusCode = 200;

            $data['message'] = config('app_messages.UpdateUserStatus');
        } else {
            $data['message'] = config('app_messages.NotFoundUser');
            $statusCode = 404;
        }
        return response()->json($data, $statusCode);
    }
}
