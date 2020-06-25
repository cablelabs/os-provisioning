{{--
The generic Zyxel OLT config blade
--}}

no service-control telnet
no service-control ftp
no remote-management 1 service telnet ftp
vlan {!!$cb->mgmt_vlan!!}
  ip address {{$cb->ip}} {!!$cb->netmask!!}
  ip address default-gateway {!!$cb->router_ip!!}
  name MGMT
  ingress-counter ""
  fixed 9
  forbidden 1-8,10-28
  untagged 1-8,10-28
exit
vlan {!!$cb->customer_vlan!!}
  name Customer
  ingress-counter ""
  fixed 1-9
  forbidden 10-28
  untagged 10-28
exit
qos bwprof MAX ustype 4 pir 1200000
qos ingprof DEFVAL dot1p0tc 1 dot1p1tc 1 dot1p2tc 1 dot1p3tc 1 dot1p4tc 1 dot1p5tc 1 dot1p6tc 1 dot1p7tc 1
interface route-domain {{$cb->ip}}/{!!$cb->prefix!!}
exit
interface port-channel pon-1
  speed-duplex 2500-full
  bpdu-control discard
exit
interface port-channel pon-2
  speed-duplex 2500-full
  bpdu-control discard
exit
interface port-channel pon-3
  speed-duplex 2500-full
  bpdu-control discard
exit
interface port-channel pon-4
  speed-duplex 2500-full
  bpdu-control discard
exit
interface port-channel pon-5
  speed-duplex 2500-full
  bpdu-control discard
exit
interface port-channel pon-6
  speed-duplex 2500-full
  bpdu-control discard
exit
interface port-channel pon-7
  speed-duplex 2500-full
  bpdu-control discard
exit
interface port-channel pon-8
  speed-duplex 2500-full
  bpdu-control discard
exit
interface port-channel eth-1
  name uplink
  pppoe intermediate-agent trust
exit
interface olt pon-1
  register-method D
  register-method template-option 128
  rogue-onu-detection
  no inactive
exit
interface olt pon-2
  register-method D
  register-method template-option 128
  rogue-onu-detection
  no inactive
exit
interface olt pon-3
  register-method D
  register-method template-option 128
  rogue-onu-detection
  no inactive
exit
interface olt pon-4
  register-method D
  register-method template-option 128
  rogue-onu-detection
  no inactive
exit
interface olt pon-5
  register-method D
  register-method template-option 128
  rogue-onu-detection
  no inactive
exit
interface olt pon-6
  register-method D
  register-method template-option 128
  rogue-onu-detection
  no inactive
exit
interface olt pon-7
  register-method D
  register-method template-option 128
  rogue-onu-detection
  no inactive
exit
interface olt pon-8
  register-method D
  register-method template-option 128
  rogue-onu-detection
  no inactive
exit
ip name-server {!!$cb->prov_ip!!}
hostname {{$cb->hostname}}
time timezone 100
time daylight-saving-time
time daylight-saving-time start-date last sunday march 2
time daylight-saving-time end-date last sunday october 3
timesync server {!!$cb->prov_ip!!}
timesync ntp
ont-alarm-profile DEFVAL
exit
ont-acl-profile DEFVAL
exit
snmp-server get-community {!!$cb->snmp_ro!!}
snmp-server set-community {!!$cb->snmp_rw!!}
remote-management 1 start-addr {!!$cb->prov_ip!!} end-addr {!!$cb->prov_ip!!} service http icmp snmp ssh https
port-security pon-1 address-limit 256
port-security pon-2 address-limit 256
port-security pon-3 address-limit 256
port-security pon-4 address-limit 256
port-security pon-5 address-limit 256
port-security pon-6 address-limit 256
port-security pon-7 address-limit 256
port-security pon-8 address-limit 256
pppoe intermediate-agent
pppoe intermediate-agent vlan {!!$cb->customer_vlan!!}
pppoe intermediate-agent vlan {!!$cb->customer_vlan!!} circuit-id
pppoe intermediate-agent vlan {!!$cb->customer_vlan!!} remote-id
pppoe intermediate-agent option vlan {!!$cb->customer_vlan!!}
pppoe intermediate-agent option circuit-id vlan {!!$cb->customer_vlan!!} option-info %hname%spaceont-%pid-%oid
pppoe intermediate-agent option remote-id vlan {!!$cb->customer_vlan!!} option-info %ontname%space%sn
remote ont ont-1-121
  inactive
exit
remote ont ont-1-122
  inactive
exit
remote ont ont-1-123
  inactive
exit
remote ont ont-1-124
  inactive
exit
remote ont ont-1-125
  inactive
exit
remote ont ont-1-126
  inactive
exit
remote ont ont-1-127
  inactive
exit
remote ont ont-1-128
  no inactive
  bwgroup 1 usbwprofname MAX dsbwprofname MAX
  alarm-profile DEFVAL
exit
remote ont ont-2-121
  inactive
exit
remote ont ont-2-122
  inactive
exit
remote ont ont-2-123
  inactive
exit
remote ont ont-2-124
  inactive
exit
remote ont ont-2-125
  inactive
exit
remote ont ont-2-126
  inactive
exit
remote ont ont-2-127
  inactive
exit
remote ont ont-2-128
  no inactive
  bwgroup 1 usbwprofname MAX dsbwprofname MAX
  alarm-profile DEFVAL
exit
remote ont ont-3-121
  inactive
exit
remote ont ont-3-122
  inactive
exit
remote ont ont-3-123
  inactive
exit
remote ont ont-3-124
  inactive
exit
remote ont ont-3-125
  inactive
exit
remote ont ont-3-126
  inactive
exit
remote ont ont-3-127
  inactive
exit
remote ont ont-3-128
  no inactive
  bwgroup 1 usbwprofname MAX dsbwprofname MAX
  alarm-profile DEFVAL
exit
remote ont ont-4-121
  inactive
exit
remote ont ont-4-122
  inactive
exit
remote ont ont-4-123
  inactive
exit
remote ont ont-4-124
  inactive
exit
remote ont ont-4-125
  inactive
exit
remote ont ont-4-126
  inactive
exit
remote ont ont-4-127
  inactive
exit
remote ont ont-4-128
  no inactive
  bwgroup 1 usbwprofname MAX dsbwprofname MAX
  alarm-profile DEFVAL
exit
remote ont ont-5-121
  inactive
exit
remote ont ont-5-122
  inactive
exit
remote ont ont-5-123
  inactive
exit
remote ont ont-5-124
  inactive
exit
remote ont ont-5-125
  inactive
exit
remote ont ont-5-126
  inactive
exit
remote ont ont-5-127
  inactive
exit
remote ont ont-5-128
  no inactive
  bwgroup 1 usbwprofname MAX dsbwprofname MAX
  alarm-profile DEFVAL
exit
remote ont ont-6-121
  inactive
exit
remote ont ont-6-122
  inactive
exit
remote ont ont-6-123
  inactive
exit
remote ont ont-6-124
  inactive
exit
remote ont ont-6-125
  inactive
exit
remote ont ont-6-126
  inactive
exit
remote ont ont-6-127
  inactive
exit
remote ont ont-6-128
  no inactive
  bwgroup 1 usbwprofname MAX dsbwprofname MAX
  alarm-profile DEFVAL
exit
remote ont ont-7-121
  inactive
exit
remote ont ont-7-122
  inactive
exit
remote ont ont-7-123
  inactive
exit
remote ont ont-7-124
  inactive
exit
remote ont ont-7-125
  inactive
exit
remote ont ont-7-126
  inactive
exit
remote ont ont-7-127
  inactive
exit
remote ont ont-7-128
  no inactive
  bwgroup 1 usbwprofname MAX dsbwprofname MAX
  alarm-profile DEFVAL
exit
remote ont ont-8-121
  inactive
exit
remote ont ont-8-122
  inactive
exit
remote ont ont-8-123
  inactive
exit
remote ont ont-8-124
  inactive
exit
remote ont ont-8-125
  inactive
exit
remote ont ont-8-126
  inactive
exit
remote ont ont-8-127
  inactive
exit
remote ont ont-8-128
  no inactive
  bwgroup 1 usbwprofname MAX dsbwprofname MAX
  alarm-profile DEFVAL
exit
remote uniport uniport-1-128-1-1
  no inactive
  port-speed auto
  queue tc 1 priority 2 weight 2 usbwprofname MAX dsbwprofname MAX dsoption olt bwsharegroupid 1
  vlan {!!$cb->customer_vlan!!} network {!!$cb->customer_vlan!!} txtag untag ingprof DEFVAL aesencrypt disable
  pvid {!!$cb->customer_vlan!!}
exit
remote uniport uniport-2-128-1-1
  no inactive
  port-speed auto
  queue tc 1 priority 2 weight 2 usbwprofname MAX dsbwprofname MAX dsoption olt bwsharegroupid 1
  vlan {!!$cb->customer_vlan!!} network {!!$cb->customer_vlan!!} txtag untag ingprof DEFVAL aesencrypt disable
  pvid {!!$cb->customer_vlan!!}
exit
remote uniport uniport-3-128-1-1
  no inactive
  port-speed auto
  queue tc 1 priority 2 weight 2 usbwprofname MAX dsbwprofname MAX dsoption olt bwsharegroupid 1
  vlan {!!$cb->customer_vlan!!} network {!!$cb->customer_vlan!!} txtag untag ingprof DEFVAL aesencrypt disable
  pvid {!!$cb->customer_vlan!!}
exit
remote uniport uniport-4-128-1-1
  no inactive
  port-speed auto
  queue tc 1 priority 2 weight 2 usbwprofname MAX dsbwprofname MAX dsoption olt bwsharegroupid 1
  vlan {!!$cb->customer_vlan!!} network {!!$cb->customer_vlan!!} txtag untag ingprof DEFVAL aesencrypt disable
  pvid {!!$cb->customer_vlan!!}
exit
remote uniport uniport-5-128-1-1
  no inactive
  port-speed auto
  queue tc 1 priority 2 weight 2 usbwprofname MAX dsbwprofname MAX dsoption olt bwsharegroupid 1
  vlan {!!$cb->customer_vlan!!} network {!!$cb->customer_vlan!!} txtag untag ingprof DEFVAL aesencrypt disable
  pvid {!!$cb->customer_vlan!!}
exit
remote uniport uniport-6-128-1-1
  no inactive
  port-speed auto
  queue tc 1 priority 2 weight 2 usbwprofname MAX dsbwprofname MAX dsoption olt bwsharegroupid 1
  vlan {!!$cb->customer_vlan!!} network {!!$cb->customer_vlan!!} txtag untag ingprof DEFVAL aesencrypt disable
  pvid {!!$cb->customer_vlan!!}
exit
remote uniport uniport-7-128-1-1
  no inactive
  port-speed auto
  queue tc 1 priority 2 weight 2 usbwprofname MAX dsbwprofname MAX dsoption olt bwsharegroupid 1
  vlan {!!$cb->customer_vlan!!} network {!!$cb->customer_vlan!!} txtag untag ingprof DEFVAL aesencrypt disable
  pvid {!!$cb->customer_vlan!!}
exit
remote uniport uniport-8-128-1-1
  no inactive
  port-speed auto
  queue tc 1 priority 2 weight 2 usbwprofname MAX dsbwprofname MAX dsoption olt bwsharegroupid 1
  vlan {!!$cb->customer_vlan!!} network {!!$cb->customer_vlan!!} txtag untag ingprof DEFVAL aesencrypt disable
  pvid {!!$cb->customer_vlan!!}
exit
