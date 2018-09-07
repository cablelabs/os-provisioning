{{--

The generic Arris CMTS config blade

--}}

configure
hostname "{{$cb->hostname}}"
autorecovery on
no cable shared-secret
cable load-interval 30
cable load-balance utilization-modems-to-check 25
cable load-balance failed-list timeout 350
cable load-balance general-group-defaults init-technique broadcast-ranging
cable load-balance general-group-defaults policy 1
cable load-balance rule 1 enable
cable load-balance rule 1 method utilization
cable load-balance policy 1 rule 1
snmp-server
no ip domain-lookup
logging host {!!$cb->prov_ip!!} facility local0
ntp server {!!$cb->prov_ip!!} burst prefer minpoll 6 maxpoll 10 version 4 key 0
clock timezone Europe/Berlin
clock network ntp
enable secret 5 {!!$cb->enable_secret!!}
username "admin" privilege 15 secret 5 {!!$cb->admin_psw!!}
cable submgmt default active
vrf default
 auto-import enable
exit
fan monitor 0 no shutdown
fan monitor 1 no shutdown
fan monitor 2 no shutdown
power-monitor A no shutdown
power-monitor B no shutdown
snmp-server community "{!!$cb->snmp_rw!!}" rw
snmp-server community "{!!$cb->snmp_ro!!}" ro
ipv6 hop-limit 64
interface cable-mac 1
 description "cable-mac 1"
 cable annex A
 cable freq-ds-min 112
 cable freq-ds-max 858
 cable freq-us-max 65
 cable us-freq-range Extended
 cable cm-ip-prov-mode ipv4only
 cable verbose-cm-rcp
 cable dynamic-rcc
 cable downstream-bonding-group dynamic enable
 cable upstream-bonding-group dynamic enable
 cable mult-tx-chl-mode
 cable cm-status event-type all max-event-holdoff 32000
exit
interface cable-mac 1.0
@include('provbase::Cmtsblade.bundle_ips')
 cable helper-address {!!$cb->prov_ip!!}
exit
interface cable-mac 1 cable bundle master
interface cable-mac 1 no shutdown
interface {!!$cb->interface!!}
 ip address {{$cb->ip}} {!!$cb->netmask!!}
 no shutdown
exit
ip route vrf management 0.0.0.0 0.0.0.0 {!!$cb->router_ip!!}
ip route 0.0.0.0 0.0.0.0 {!!$cb->router_ip!!}
packetcable dqos no shutdown
packetcable pcmm no shutdown
authentication users local
authentication default local none
ip ssh max-clients 12
ip ssh max-auth-fail 7
ip ssh no shutdown
line console 0 idle-timeout 60000
line console 0 length 24
line console 0 1 speed 9600
line console 1 idle-timeout 60000
line console 1 length 24
line vty 0 authentication "users" login-authentication
line vty 0 authentication "users" enable-authentication
line vty 1 authentication "users" login-authentication
line vty 1 authentication "users" enable-authentication
line vty 2 authentication "users" login-authentication
line vty 2 authentication "users" enable-authentication
line vty 3 authentication "users" login-authentication
line vty 3 authentication "users" enable-authentication
line vty 4 authentication "users" login-authentication
line vty 4 authentication "users" enable-authentication
line vty 5 authentication "users" login-authentication
line vty 5 authentication "users" enable-authentication
line vty 6 authentication "users" login-authentication
line vty 6 authentication "users" enable-authentication
line vty 7 authentication "users" login-authentication
line vty 7 authentication "users" enable-authentication
line vty 8 authentication "users" login-authentication
line vty 8 authentication "users" enable-authentication
line vty 9 authentication "users" login-authentication
line vty 9 authentication "users" enable-authentication
line vty 10 authentication "users" login-authentication
line vty 10 authentication "users" enable-authentication
line vty 11 authentication "users" login-authentication
line vty 11 authentication "users" enable-authentication
line vty 12 authentication "users" login-authentication
line vty 12 authentication "users" enable-authentication
line vty 13 authentication "users" login-authentication
line vty 13 authentication "users" enable-authentication
line vty 14 authentication "users" login-authentication
line vty 14 authentication "users" enable-authentication
line vty 15 authentication "users" login-authentication
line vty 15 authentication "users" enable-authentication
end
