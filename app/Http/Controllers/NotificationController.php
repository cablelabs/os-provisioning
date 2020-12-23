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

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back();
    }

    public function markRead(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return redirect()->back();
    }

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
