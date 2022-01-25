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
{{-- The generic ip bundle --}}

@php
    $cb->missing_routes = false;
@endphp

@foreach ($view_var->ippools->where('version', '4') as $pool)
@if ($view_var->isOnline && $pool->ip_route_online())
ip address {{$pool->router_ip}} {{$pool->netmask}} {{$pool->is_secondary($view_var)}}
@else
<div class="label label-danger m-l-5">
ip address {{$pool->router_ip}} {{$pool->netmask}} {{$pool->is_secondary($view_var)}}
</div>
<?php $cb->missing_routes=true; ?>
@endif
@endforeach
@foreach ($view_var->ippools->where('version', '6') as $pool)
@if ($view_var->isOnline && $pool->ip_route_online())
ipv6 address {{$pool->router_ip.$pool->netmask}}
@else
<div class="label label-danger m-l-5">
ipv6 address {{$pool->router_ip.$pool->netmask}}
</div>
<?php $cb->missing_routes=true; ?>
@endif
@endforeach
