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
  ip address {!!$cb->ip!!} {!!$cb->netmask!!}
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
