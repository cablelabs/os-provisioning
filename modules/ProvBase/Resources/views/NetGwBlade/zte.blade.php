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

The generic ZTE OLT config blade

--}}

vlan database
  vlan 1,{!!$cb->mgmt_vlan!!},{!!$cb->customer_vlan!!}
!
gpon
  profile tcont MAX-UP type 4 maximum 1244160
!
pon
  onu-type universal gpon
  onu-type-if universal eth_0/1
!
onu-profile gpon line universal
  fec upstream
  tcont 1 profile MAX-UP
  gemport 1 unicast tcont 1 dir both
!
onu-profile gpon remote universal
  service INET gemport 1 untag
  vlan port eth_0/1 mode transparent
!
interface vlan {!!$cb->mgmt_vlan!!}
  ip address {{$cb->ip}} {!!$cb->netmask!!}
!
interface gpon-olt_x/y/z
  auto-learning enable
!
interface gei_x/y/z
  switchport mode hybrid
  switchport vlan {!!$cb->mgmt_vlan!!},{!!$cb->customer_vlan!!} tag
!
ip route 0.0.0.0 0.0.0.0 {!!$cb->prov_ip!!}
!
hostname {{$cb->hostname}}
!
username {{$cb->username}} password 0 {{$cb->password}} privilege 15
username {{$cb->username}} password 0 {{$cb->password}} max-sessions 16
!
login-authentication-type local
login-authorization-type local
!
snmp-server community {!!$cb->snmp_rw!!} view DefaultView rw
snmp-server community {!!$cb->snmp_ro!!} view DefaultView ro
!
ntp server {!!$cb->prov_ip!!} priority 1
ntp enable
!
ssh server enable
ssh server authentication ispgroup 1
ssh server authentication mode local
ssh server authentication type chap
no ssh server only
ssh server version 2
!
authentication local enable
!
end
