<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\ActivityLog;
use App\User;

class Helper
{
    
    public static function storeActivity($data)
    {
        //Activity logs
        $activity = new ActivityLog;
        $activity->type = $data['type']; //Payment //Orders //Others
        $activity->user_id = $data['user_id'];
        $activity->action = $data['action']; //Edit user, add order, deliverred order, etc
        $activity->description = $data['description']; //message
        $activity->save();
    }

    //Customer with on their setting to receive notifications from FS21
    public static function makeNotificationsToCustomer($promoId, $title, $description)
    {
        //store each user notifications
        $users = User::where('is_notify', 1)->role('Customer')->get();
        //check if with users to notify
        if ($users) {
            foreach ($users as $key => $user) {
                $norify = new Notification;
                $norify->promo_id = $promoId;
                $norify->user_id = $user->id;
                $norify->title = $title;
                $norify->description = $description;
                $norify->is_read = 0;
                $norify->save();
            }
        }
    }
    //Admin users notified about new order recieved from the customer
    public static function makeNotificationsToAdmin($orderId, $title, $description)
    {
        //store each user notifications
        $users = User::role('Admin')->get();
        //check if with users to notify
        if ($users) {
            foreach ($users as $key => $user) {
                $norify = new Notification;
                $norify->order_id = $orderId;
                $norify->user_id = $user->id;
                $norify->title = $title;
                $norify->description = $description;
                $norify->is_read = 0;
                $norify->save();
            }
        }
    }

    //Dynamic store image with original path and file
    public static function storeImages($file, $origFilePath)
    {
        $filename = md5($file->getClientOriginalName());
        $filetype = $file->getClientOriginalExtension();
        $origFileName = $filename.'.'.$filetype;
        $large = $origFilePath .'/original';
        $medium = $origFilePath .'/medium';
        $small = $origFilePath .'/small';
        $xsmall = $origFilePath .'/xsmall';

        // if (!file_exists(public_path().'/'.$large)) {
        //   mkdir(public_path().$large, 0777, true);
        // }
        if (!file_exists(public_path().'/'.$medium)) {
            mkdir(public_path().$medium, 0777, true);
        }
        if (!file_exists(public_path().'/'.$small)) {
            mkdir(public_path().$small, 0777, true);
        }
        if (!file_exists(public_path().'/'.$xsmall)) {
            mkdir(public_path().$xsmall, 0777, true);
        }

        // $path = URL($filePath150 . $fileName150);

        // if (!file_exists(public_path().'/'.$large.'/'.$origFileName)) {
            $size = 1080;
            resizeAndSave($file, $size, $origFilePath, $origFileName);
        // }

        if (!file_exists(public_path().'/'.$medium.'/'.$origFileName)) {
            $size = 450;
            resizeAndSave($file, $size, $medium, $origFileName);
        }

        if (!file_exists(public_path().'/'.$small.'/'.$origFileName)) {
            $size = 300;
            resizeAndSave($file, $size, $small, $origFileName);
        }

        if (!file_exists(public_path().'/'.$xsmall.'/'.$origFileName)) {
            $size = 130;
            resizeAndSave($file, $size, $xsmall, $origFileName);
        }
    }
}
