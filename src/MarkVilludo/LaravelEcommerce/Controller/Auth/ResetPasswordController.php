<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Http\Requests\Api\User\ForgotPasswordRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;
use Illuminate\Http\Request;
use App\User;
use Response;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->middleware('guest');
        $this->user = $user;
    }
    //Web send email
    public function sendEmail(Request $request)
    {
        // return $request->all();
        // Send mail
        //Make new auto generated password then send via email
        $user = $this->user->where('email', $request->email)->first();

        if ($user) {
            //Update new password
            $newPassword = str_random(10);

            $user->password = bcrypt($newPassword);
            $user->save();

            $data['user'] = $user;
            $data['existing'] = true;

            $user->newPassword = $newPassword;

            Mail::to($user->email)->send(new ForgotPasswordMail($user));

            $data['status_code'] = 200;
        } else {
            $data['existing'] = false;
            $data['email'] = $request->email;
            $data['status_code'] = 404;
        }

        return view('forgot_password.email_sent', $data);
    }
    //Api send email
    public function sendEmailApi(Request $request)
    {
        // return $request->all();
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $data['send'] = false;
            $data['existing'] = false;
            $data['message'] = "Invalid email format.";
            $statusCode = 400;
        } else {
            // Send mail
            //Make new auto generated password then send via email
            $user = $this->user->where('email', $request->email)->first();

            if ($user) {
                //Update new password
                $newPassword = str_random(10);

                $user->password = bcrypt($newPassword);
                $user->save();
                
                $data['existing'] = true;
                $data['send'] = true;
                $data['message'] = 'New password sent. Please check your email.';

                $user->newPassword = $newPassword;

                Mail::to($user->email)->send(new ForgotPasswordMail($user));

                $statusCode = 200;
            } else {
                $data['send'] = false;
                $data['existing'] = false;
                $data['message'] = 'User not found.';
                $statusCode = 404;
            }
        }
      
        $data['email'] = $request->email;
        return Response::json($data, $statusCode);
    }
}
