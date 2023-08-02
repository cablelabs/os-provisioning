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

use Illuminate\Http\Request;

class SidebarController extends BaseController
{
    public function setPinnedState(Request $request)
    {
        if (! $request->has('pinned')) {
            return;
        }

        $loginName = auth()->user()->login_name;
        cache(['sidebar.pinnedState.'.$loginName => $request->get('pinned')]);

        return response()->json([
            'success' => true,
            'pinned' => $pinned = cache('sidebar.pinnedState.'.$loginName),
            'message' => $pinned ? trans('messages.sidebarPinned') : trans('messages.sidebarUnpinned'),
        ]);
    }
}
