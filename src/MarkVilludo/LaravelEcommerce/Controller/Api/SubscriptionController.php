<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscribeMail;
use App\Models\SubscribeEmail;
use App\User;

class SubscriptionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(SubscribeEmail $subscribeEmail, User $user)
    {
        $this->subscribeEmail = $subscribeEmail;
        $this->user = $user;
    }

    //subscribe account send email link for changing status
    public function sendVerification(Request $request)
    {
        // return $request->all();
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $data['send'] = false;
            $data['existing'] = false;
            $data['message'] = config('app_messages.InvalidEmailFormat');
            $statusCode = 400;
        } else {
            //Check if exist email
            $subscribe = $this->subscribeEmail->where('email', $request->email)->first();

            $user = $this->user->where('email', $request->email)->first();
            $userId = null;

            if ($user) {
                $userId = $user->id;
            }
            
            $newSubscribe = '';
            $sendMail = false;
            $subscriptionKey = date('Ymd').''. str_pad($userId, 4, rand(9922399, 992), STR_PAD_LEFT);

            if ($request->email) {
                if (!$subscribe) {
                    $newSubscribe = new $this->subscribeEmail;
                    $newSubscribe->user_id = $userId ? $userId : null;
                    $newSubscribe->email = $request->email;
                    $newSubscribe->subscription_key = $subscriptionKey;
                    $newSubscribe->is_verify = 0;
                    $newSubscribe->save();

                    // Send mail
                    $data['message'] = config('app_messages.SuccessSentSubscriptionRequest');
                    Mail::to($request->email)->send(new SubscribeMail($newSubscribe ? $newSubscribe : $subscribe));
                    $data['existing'] = false;
                } else {
                    if (!$subscribe->is_verify) {
                        $data['message'] = config('app_messages.EmailAlreadyExistPleaseVerify');
                    }
                    $data['existing'] = true;
                    $data['message'] = config('app_messages.EmailAdddressHasBeenSubsCribed');
                }
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.EmailCannotBeNull');
                $statusCode = 400;
            }
            $data['email'] = $request->email;
        }

        return response()->json($data, $statusCode);
    }

    public function verifySubscription($subscriptionKey)
    {
        // return $subscriptionKey;
        $subscriber = $this->subscribeEmail->where('subscription_key', $subscriptionKey)->first();
        
        $data['subscribe'] = $subscriber;
        if (!$subscriber->is_verify) {
            $subscriber->is_verify = 1;
            $subscriber->update();

            $data['message'] = config('app_messages.ThanksForSubscribing');
            $statusCode = 200;
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.AlreadySubscribed');
        }
        
        //Generate subscription key and pass to views
        return view('emails.subscribe.confirm_subscription', $data);
    }
}
