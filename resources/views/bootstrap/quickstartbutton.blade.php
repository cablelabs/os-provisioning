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
?>
<a href="{{ route($route) }}" style="text-decoration: none;">
    <span class="btn btn-dark p-5 m-1 text-center min-w-[100px]">
        <i style="font-size: 25px;" class="img-center fa fa-{{ $icon }} p-10"></i><br />
        <span class="username text-ellipsis text-center">{{ $title }}</span>
    </span>
</a>
