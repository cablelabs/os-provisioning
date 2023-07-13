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
@section ($title.'-chart')
    @foreach($data as $state => $infos)
        <div class="flex m-b-5 align-items-baseline">
            <i class="fa fa-circle text-{{ $state }} m-r-5"></i>
            {{ $infos['count'].' '.$infos['text'] }}
        </div>
    @endforeach
@endsection

@include ('HfcBase::troubledashboard.summarycard', [
    'title' => $title,
    'content' => $title.'-chart',
])
