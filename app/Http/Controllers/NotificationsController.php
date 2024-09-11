<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\User;
use App\Models\Users;
use App\Notifications\CustomApiRequestStatusUpdate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Utils\PaginateResourceCollection;

class NotificationsController extends Controller
{
    public function getUnreadNotifications()
    {
        return new JsonResponse([
            'success' => false,
            'message' => 'Fetched unread notifications.',
            'data' => NotificationResource::collection(Auth::user()->unreadNotifications),
        ], JsonResponse::HTTP_OK);
    }

    public function getNotifications()
    {
        $unreadNotifications = Auth::user()->unreadNotifications ?? collect([]);
        $readNotifications = Auth::user()->readNotifications ?? collect([]);
        $notifications = $unreadNotifications->merge($readNotifications);
        $collection = NotificationResource::collection($notifications)->collect();
        return new JsonResponse([
            'success' => false,
            'message' => 'Fetched all notifications.',
            'data' => PaginateResourceCollection::paginate($collection, 15)
        ], JsonResponse::HTTP_OK);
    }

    public function getUnreadNotificationsStream()
    {
        ini_set('max_execution_time', '999999');
        return response()->stream(function () {
            $lastLength = 0;
            $lastRequestSent = null;
            $broadcastCount = 0;
            while (true) {
                // Users:find to get UPDATED user data
                $notifs = NotificationResource::collection(Users::find(Auth::user()->id)->unreadNotifications);
                $newLength = sizeof($notifs);
                if ($newLength != $lastLength) { // Notif Changes Submit directly new updates to Notifs
                    $lastLength = $newLength;
                    echo "id: " . (++$broadcastCount) . "\ndata: " . json_encode($notifs) . "\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    $lastRequestSent = Carbon::now();
                } else { // no changes submit every 13 seconds
                    if ($lastRequestSent && $lastRequestSent->diffInSeconds(Carbon::now()) <= 13) {
                        continue;
                    }
                    echo "id: " . (++$broadcastCount) . "\ndata: " . json_encode($notifs) . "\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    $lastRequestSent = Carbon::now();
                }
                // usleep(500000); // usleep 1 sec = 1000000
                if(connection_aborted()) {
                    break;
                }
                sleep(1); // Will check for updates every second
            }
        }, 200, [
            "Content-Type" => "text/event-stream",
            "Cache-Control" => "no-cache",
            "Connection" => "keep-alive",
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function readAllNotifications()
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function readNotification($notif)
    {
        Auth::user()->notifications->find($notif)->markAsRead();
    }

    public function unreadNotification($notif)
    {
        Auth::user()->notifications->find($notif)->markAsUnread();
    }

    public function addNotification(ApiNotificationRequest $request, Users $user)
    {
        $validData = $request->validated();
        $user->notify(new CustomApiRequestStatusUpdate($validData["module"], $validData["action"], $validData["message"], $validData["request_id"], $validData["request_type"] ));
        return new JsonResponse([
            'success' => false,
            'message' => 'Successfully notified user.',
        ], JsonResponse::HTTP_OK);
    }
}
