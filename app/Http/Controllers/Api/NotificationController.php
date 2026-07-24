<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class NotificationController extends Controller
{
    /**
     * Display a listing of the authenticated user's notifications.
     */
    public function index(Request $request)
    {
        $notifications = QueryBuilder::for(Notification::class)
            ->where('user_id', $request->user()->id)
            ->allowedFilters(
                'task_id',
                'maintenance_report_id',
                AllowedFilter::callback('unread_only', function (Builder $query, $value) {
                    if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
                        $query->whereNull('read_at');
                    }
                })
            )
            ->allowedIncludes('task', 'maintenanceReport', 'user')
            ->latest()
            ->paginate(15);

        return NotificationResource::collection($notifications);
    }

    /**
     * Store a newly created notification.
     * Can target a single user or dispatch to all users with a specific role.
     */
    public function store(StoreNotificationRequest $request)
    {
        $validated = $request->validated();

        if (isset($validated['role_name'])) {
            // Find all users with the specified role
            $recipients = User::role($validated['role_name'])->get();
            
            if ($recipients->isEmpty()) {
                return response()->json([
                    'message' => 'No users found with the specified role.',
                ], 404);
            }

            foreach ($recipients as $recipient) {
                Notification::create(array_merge($validated, [
                    'user_id' => $recipient->id,
                ]));
            }

            return response()->json([
                'message' => 'Notifications sent successfully to all users in role: ' . $validated['role_name'],
                'recipients_count' => $recipients->count(),
            ], 201);
        }

        // Direct user notification
        $notification = Notification::create($validated);

        return response()->json([
            'message' => 'Notification sent successfully',
            'notification' => new NotificationResource($notification),
        ], 201);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        // Authorize that the notification belongs to the current user
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => new NotificationResource($notification),
        ]);
    }

    /**
     * Mark all notifications of the authenticated user as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully',
        ]);
    }
}
