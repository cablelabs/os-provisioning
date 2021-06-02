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

The generic NSN DSLAM config blade

--}}

hostname {{$cb->hostname}}
!
bridge
 xdsl add line-config-profile default
 xdsl line-config-profile default use-profile-number profile17a
 xdsl 1-8 line-config default
 bridgebase taggingmode single
 bridgebase routing-mode unrestricted
 vlan create {!!$cb->mgmt_vlan!!},{!!$cb->customer_vlan!!}
 vlan add {!!$cb->mgmt_vlan!!} 9-14 tagged
 vlan add {!!$cb->customer_vlan!!} 1/1-8/1 untagged 9-14 tagged
 bridgeport 1/1-8/1 pvid {!!$cb->customer_vlan!!}
 bridgeport 9-14 pvid 1
 bridgeport host-protocol pppoe 1/1-8/1
 xdsl 1-8 enable
!
interface br{!!$cb->mgmt_vlan!!}
 no shutdown
 ip address {{$cb->ip}}/{!!$cb->prefix!!}
!
snmp community ro {!!$cb->snmp_ro!!} {!!$cb->prov_ip!!}
snmp community rw {!!$cb->snmp_rw!!} {!!$cb->prov_ip!!}
!
ntp-client server {!!$cb->prov_ip!!}
!
end
