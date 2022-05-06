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
        {{--Help Section--}}
    <div class="align-self-end m-r-5 dropdown btn-group">
        <button id="dropdownMenuButton" type="button" class="btn btn-outline float-right dropdown-toggle"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            data-delay='{"show":"250"}' data-placement="top"
            title="{{ trans('messages.support') }}" form="IndexForm" name="support">
            <i class="fa fa-question fa-2x" aria-hidden="true"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
            @php
                $help = isset($documentation) ? config('documentation.'.strtolower($documentation)) : $view_help;
            @endphp

            @if ($help && $help['doc'])
            <a class="dropdown-item" href="{{$help['doc']}}" target="_blank">Documentation</a>
            @endif
            @if ($help && $help['url'])
            <a class="dropdown-item" href="{{$help['url']}}" target="_blank">URL</a>
            @endif
            @if ($help && $help['youtube'])
            <a class="dropdown-item" href="{{$help['youtube']}}" target="_blank">Youtube</a>
            @endif
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{route('SupportRequest.index')}}">Request Professional Help</a>
        </div>
    </div>
