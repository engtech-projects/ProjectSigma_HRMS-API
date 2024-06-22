<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\EmployeeLeaves;
use App\Notifications\LeaveRequestForApproval;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    //
    public function getUnreadNotifications()
    {
        return new JsonResponse
        ([
            'success' => false,
            'message' => 'Fetched Notifications',
            'data' => NotificationResource::collection(Auth::user()->unreadNotifications),
        ], JsonResponse::HTTP_OK);
    }

    public function getUnreadNotificationsStream()
    {
        return response()->stream(function() {
            $lastLength = 0;
            while (true) {
                $notifs = Auth::user()->unreadNotifications;
                $newLength = sizeof($notifs);
                if($newLength != $lastLength){
                    echo "data: ".json_encode($notifs). "\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    sleep(2);
                }else{
                    echo "data: none\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    usleep(500000); // usleep 1 sec = 1000000
                }
                if(connection_aborted()){
                    break;
                }
            }
        }, 200, [
            "Content-Type" => "text/event-stream",
            "Cache-Control" => "no-cache",
            "Connection" => "keep-alive",
            // 'X-Accel-Buffering' => 'no',
        ]);
    }

    public function readAllNotifications()
    {
        Auth::user()->unreadNotifications->markAsRead();
    }
    public function readNotification($notif)
    {
        Auth::user()->unreadNotifications->find($notif)->markAsRead();
    }

    public function testCreateNotif()
    {
        $theLeave = EmployeeLeaves::find(1);
        Auth::user()->notify(new LeaveRequestForApproval($theLeave));
    }
}
