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

The generic ip access-list bundle blade

--}}

ip access-list extended bundle_in_acl
 remark Bundle-in-ACL
@foreach($cb->ippools()->where('type', '=', 'CM')->get() as $cm_pool)
 permit ip any host {{$cm_pool->router_ip}}
@endforeach
 deny   ip any 10.0.0.0 0.255.255.255
 deny   ip any 100.64.0.0 0.63.255.255
@foreach($cb->ippools()->where('type', '=', 'MTA')->get() as $mta_pool)
 deny   ip any {{$mta_pool->net}} {{$mta_pool->wildcard_mask()}}
@endforeach
 permit ip any any
access-list compiled
