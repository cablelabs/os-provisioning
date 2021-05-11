<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('notifications.index')->with($this->compact_prep_view([]));
    }

    /**
     * Mark all unread Notifications as read.
     *
     * @return Redirect
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back();
    }

    /**
     * Mark a selected Notification as read.
     *
     * @return Redirect
     */
    public function markRead(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return redirect()->back();
    }

    /**
     * Turbolink - Returns the Notification Navbar partial to replace this part
     * of the page when an update occurs
     *
     * @return View
     */
    public function navbar()
    {
        $user = \App\User::where('id', auth()->id())
            ->withCount('unreadNotifications')
            ->with([
                'unreadNotifications' => function ($query) {
                    $query->orderByDesc('created_at');
                },
            ])
            ->first();

        return view('bootstrap._navbar-notifications')->with(compact('user'));
    }
}
