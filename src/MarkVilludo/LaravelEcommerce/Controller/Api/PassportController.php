<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\User\UserStoreRequest;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use App\Helpers\Helper;
use App\User;

use Validator;

class PassportController extends Controller
{

    public $successStatus = 200;

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
    */

    public function login(Request $request)
    {
        // return $request->all();
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            //Check if account is active
            if (auth()->user()->status) {
                $user = auth()->user();
            
                $data['token'] =  $user->createToken('FS21')->accessToken;
                $data['user'] =  new UserResource($user);

                return response()->json(['success' => $data], $this->successStatus);
            } else {
                //when deactivated account
                return response()->json([
                        'success' => false,
                        'message' => config('app_messages.AccoundDeactivated')
                    ], $this->successStatus);
            }
        } else {
            return response()->json(['error'=> config('app_messages.InvalidEmailPassword')], 401);
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(UserStoreRequest $request)
    {
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user->assignRole('Customer');
        $data['token'] =  $user->createToken('FS21')->accessToken;
        $data['user'] =  $user;

        //Activity logs
        //user helper
        $logs['user_id'] = $user->id;
        $logs['type'] = 'user';
        $logs['action'] = 'User Registration.';
        $logs['description'] = config('app_messages.RegisteredNewUser');
        Helper::storeActivity($logs);
        //End activity logs

        //clear cache customers
        Cache::forget('customers');
        //end clear cache

        return response()->json(['success'=>$data], $this->successStatus);
    }
    public function logout()
    {
        Passport::tokensExpireIn(now());
        return response()->json(['message' => config('app_messages.SuccessLogoutApplication'), 'success' => true], 200);
    }
}
