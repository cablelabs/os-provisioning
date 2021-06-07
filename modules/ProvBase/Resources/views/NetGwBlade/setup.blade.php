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
{{--

NETGW Setup Blade:
HOWTO: setup a new NETGW

--}}

<b>Setup command:</b><br>
@if (! $cb->provBase->prov_ip_online())
	<div class="label label-danger">!!! Provisioning Server Management IP {!!$cb->prov_ip!!} Offline !!!</div><br><br>
@else
	<i>connect <b>{!!$cb->prov_if!!}</b> Interface with <b>{!!$cb->interface!!}</b> Interface</i><br><br>
@endif


{{-- NETGW setup code --}}
<pre>
interface {!!$cb->interface!!}.{!!$cb->mgmt_vlan!!}
 ip address {{$cb->ip}} {!!$cb->netmask!!}
 no shutdown
@if ($cb->ipv6)
 ipv6 address {{$cb->ipv6.$cb->netmask6}}
@endif

copy tftp://{!!$cb->prov_ip!!}/netgw/{!!$cb->hostname!!}.cfg startup-config

reload
</pre>
