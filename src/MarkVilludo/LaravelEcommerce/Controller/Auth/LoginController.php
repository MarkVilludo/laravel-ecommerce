<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function login()
    {

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();

            if (auth()->user()->status) {
                $data['message'] = 'Login successful';

                if (Auth::user()->hasRole('Admin')) {
                    return redirect()->route('products.index');
                } elseif (Auth::user()->hasRole('Super Admin')) {
                    return view('superadmin.dashboard', $data);
                } else {
                    return view('customer.dashboard', $data);
                }
            } else {
                $message = 'Account deactivated, Please contact system administrator.';
                Session::flash('message', $message);
                return redirect()->back();
            }
        } else {
            $message = 'Invalid credentials';
            Session::flash('message', $message);
            return redirect()->back();
        }
    }
}
