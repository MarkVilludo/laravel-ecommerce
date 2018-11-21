<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{

   
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }
    /**
     * Display a listing of the notification
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $notifications = $this->notification->where('user_id', auth()->user()->id)->paginate(10);
        if ($notifications) {
            $data['message'] = config('app_messages.ShowOrderList');
            return $data = NotificationResource::collection($notifications);
        } else {
            $data['message'] = config('app_messages.NoOrdersAvailable');
            $statusCode = 200;
        }
        return $data;
    }
}
