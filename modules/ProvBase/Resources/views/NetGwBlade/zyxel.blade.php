vlan {!!$cb->mgmt_vlan!!}
  ip address {{$cb->ip}} {!!$cb->netmask!!}
  ip address default-gateway {!!$cb->router_ip!!}
  ingress-counter ""
  fixed 25
  forbidden 1-24,26-28
  untagged 1-24,26-28
exit
vlan {!!$cb->customer_vlan!!}
  ingress-counter ""
  fixed 1-8,25
  forbidden 9-24,26-28
  untagged 9-24,26-28
exit
qos bwprof 1G ustype 5 sir 102400 air 204800 pir 1126400
qos bwprof DEFVAL ustype 5 sir 1024 air 1024 pir 2048
qos ingprof DEFVAL dot1p0tc 1 dot1p1tc 1 dot1p2tc 1 dot1p3tc 1 dot1p4tc 1 dot1p5tc 1 dot1p6tc 1 dot1p7tc 1
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
  inactive
exit
interface port-channel eth-2
  inactive
exit
interface port-channel eth-3
  inactive
exit
interface port-channel eth-4
  inactive
exit
interface port-channel eth-5
  inactive
exit
interface port-channel eth-6
  inactive
exit
interface port-channel eth-7
  inactive
exit
interface port-channel eth-8
  inactive
exit
interface port-channel eth-9
  inactive
exit
interface port-channel eth-10
  inactive
exit
interface port-channel eth-11
  inactive
exit
interface port-channel eth-12
  inactive
exit
interface port-channel eth-13
  inactive
exit
interface port-channel eth-14
  inactive
exit
interface port-channel eth-15
  inactive
exit
interface port-channel eth-16
  inactive
exit
interface port-channel eth-17
  name uplink
  pppoe intermediate-agent trust
exit
interface port-channel eth-18
  inactive
exit
interface port-channel eth-19
  inactive
exit
interface port-channel eth-20
  inactive
exit
interface olt pon-1
  register-method D
  register-method template-option 128
  rogue-onu-detection
  no inactive
exit
hostname {{$cb->hostname}}
timesync server {!!$cb->prov_ip!!}
timesync ntp
ont-alarm-profile DEFVAL
exit
ont-acl-profile DEFVAL
exit
snmp-server get-community {!!$cb->snmp_ro!!}
snmp-server set-community {!!$cb->snmp_rw!!}
remote-management 2 start-addr {!!$cb->prov_ip!!} end-addr {!!$cb->prov_ip!!} service icmp snmp ssh https
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
remote ont ont-1-128
  template-description Template-1-128
  no inactive
  plan-version 2.8.0-R
  bwgroup 1 usbwprofname 1G dsbwprofname 1G
  alarm-profile DEFVAL
  anti-mac-spoofing inactive
exit
remote uniport uniport-1-128-1-1
  no inactive
  port-speed auto
  queue tc 1 priority 2 weight 2 usbwprofname 1G dsbwprofname 1G dsoption olt bwsharegroupid 1
  vlan {!!$cb->customer_vlan!!} network {!!$cb->customer_vlan!!} txtag untag ingprof DEFVAL aesencrypt disable
  pvid {!!$cb->customer_vlan!!}
exit
