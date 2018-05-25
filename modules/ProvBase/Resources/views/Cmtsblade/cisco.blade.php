{{--

The generic Cisco CMTS config blade

--}}

!
hostname {{$cb->hostname}}
!
boot-start-marker
boot-end-marker
!
enable secret 5 {{$cb->enable_secret}}

!
aaa new-model
!
!
aaa session-id common
clock timezone CET 1
clock summer-time CEST recurring last Sun Mar 2:00 last Sun Oct 3:00
cable admission-control preempt priority-voice
!
!
ip subnet-zero
!
!
no ip domain lookup
ip domain name {{$cb->hostname}}.{{$cb->domain}}
ip dhcp smart-relay
ip dhcp relay information option
no ip dhcp relay information check
!
!
ip cef
ip ssh version 2
login block-for 600 attempts 3 within 60
login quiet-mode access-class 13
!
!
multilink bundle-name authenticated
call rsvp-sync
!
!
username admin privilege 15 secret 5 {{$cb->admin_psw}}
!
!
!
interface {{$cb->interface}}
 no ip address
 no ip redirects
 no ip proxy-arp
 media-type rj45
 speed auto
 duplex auto
 negotiation auto
 ip address {{$cb->ip}} {{$cb->netmask}}
!
!
interface Bundle1
@include('provbase::Cmtsblade.bundle_ips')
 ip access-group bundle_in_acl in
 ip policy route-map NAT
 cable arp filter request-send 3 2
 cable arp filter reply-accept 3 2
 cable helper-address {{$cb->prov_ip}}
!
ip classless
ip route 0.0.0.0 0.0.0.0 {{$cb->router_ip}}
!
no ip http server
no ip http secure-server
!
!
!
ip access-list standard snmp
 permit {{$cb->tf_net_1}} 0.0.0.255
!
ip access-list extended cpe-private
 permit ip 100.64.0.0 0.0.3.255 any
!
@include('provbase::Cmtsblade.acl_bundle')
!
logging cmts ipc-cable log-level errors
 access-list 13 permit {{$cb->tf_net_1}} 0.0.0.255
cpd cr-id 1
nls resp-timeout 1
!
route-map NAT permit 10
 match ip address cpe-private
 set ip next-hop {{$cb->nat_ip}}
!
snmp-server community {{$cb->snmp_ro}} RO snmp
snmp-server community {{$cb->snmp_rw}} RW snmp
!
!
control-plane
!
!
dial-peer cor custom
!
!
!
!
gatekeeper
 shutdown
!
!
line con 0
 exec-timeout 0 0
 logging synchronous
 exec prompt timestamp
 stopbits 1
line aux 0
 access-class 13 in
 transport input ssh
 stopbits 1
line vty 0 1
 access-class 13 in
 password {{$cb->vty_psw}}
 logging synchronous
 exec prompt timestamp
 transport input telnet
line vty 2 4
 access-class 13 in
 exec-timeout 120 0
 password {{$cb->vty_psw}}
 exec prompt timestamp
 transport input ssh
line vty 5 15
 access-class 13 in
 exec-timeout 120 0
 exec prompt timestamp
 transport input ssh
!
exception crashinfo buffersize 64
exception crashinfo maximum files 10
exception pxf style minimal
exception pxf flash flash:
!
ntp update-calendar
ntp server {{$cb->prov_ip}}
!
