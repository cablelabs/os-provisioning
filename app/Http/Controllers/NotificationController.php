<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
