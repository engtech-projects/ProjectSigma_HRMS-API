<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\User;
use App\Notifications\CustomApiRequestStatusUpdate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $notifications = Auth::user()->notifications()->paginate();
        return NotificationResource::collection($notifications)
        ->additional([
            'success' => true,
            'message' => 'Fetched all notifications.',
        ]);
    }

    public function getUnreadNotificationsStream()
    {
        // COMMENTED because i'm not sure it will change back to the default value if removed will remove after testing
        ini_set('max_execution_time', '65');
        return response()->stream(function () {
            $lastLength = 0;
            $lastRequestSent = null;
            $broadcastCount = 0;
            $start = Carbon::now();
            while (true) {
                // Users:find to get UPDATED user data
                $unreadNotifications = User::find(Auth::user()->id)->unreadNotifications;
                $newLength = sizeof($unreadNotifications);
                $notification = NotificationResource::collection($unreadNotifications->take(10)); // Show 10 at a time
                $notification->additional(['unread_notifications_count' => $unreadNotifications->count()]);
                $notificationsJsonData = json_encode($notification, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
                $notifications = json_encode([
                    "notifications" => $notificationsJsonData,
                    "total_notifications" => $newLength,
                ]);
                if ($newLength != $lastLength) { // Notif Changes Submit directly new updates to Notifs
                    $lastLength = $newLength;
                    echo "id: " . (++$broadcastCount) . "\ndata: " . $notifications . "\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    $lastRequestSent = Carbon::now();
                } else { // no changes submit every 13 seconds
                    if ($lastRequestSent && $lastRequestSent->diffInSeconds(Carbon::now()) <= 13) {
                        continue;
                    }
                    echo "id: " . (++$broadcastCount) . "\ndata: " . $notifications . "\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    $lastRequestSent = Carbon::now();
                }
                // Check if the client has closed the connection or if the request has been running for more than 50 seconds(to avoid execution time limit) and stop the loop
                if (connection_aborted() || $start->diffInSeconds(Carbon::now()) >= 60) {
                    echo "\n\n\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
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

    public function getUnreadNotificationsStreamV2() // Unused most likely will have same problem
    {
        $broadcastCount = 0;
        return new StreamedResponse(
            function () use (&$broadcastCount) {
                // Send notifications to the client using SSE
                $unreadNotifications = User::find(Auth::user()->id)->unreadNotifications;
                $notification = NotificationResource::collection($unreadNotifications->take(10)); // Show 10 at a time
                $notification->additional(['unread_notifications_count' => $unreadNotifications->count()]);
                $notificationsJsonData = json_encode($notification, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
                echo "id: " . (++$broadcastCount) . "\ndata: " . $notificationsJsonData . "\n\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            },
            200,
            [
                "Content-Type" => "text/event-stream",
                "Cache-Control" => "no-cache",
                "Connection" => "keep-alive",
                'X-Accel-Buffering' => 'no',
            ]
        );
    }

    public function readAllNotifications()
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function readNotification($notif)
    {
        Auth::user()->notifications->find($notif)?->markAsRead();
    }

    public function unreadNotification($notif)
    {
        Auth::user()->notifications->find($notif)?->markAsUnread();
    }

    public function addNotification(ApiNotificationRequest $request, User $user)
    {
        $validData = $request->validated();
        $user->notify(new CustomApiRequestStatusUpdate($validData["module"], $validData["action"], $validData["message"], $validData["request_id"], $validData["request_type"]));
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully notified user.',
        ], JsonResponse::HTTP_OK);
    }
}
