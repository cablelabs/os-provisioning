{{--

The generic NSN DSLAM config blade

--}}

hostname {{$cb->hostname}}
!
bridge
 xdsl add line-config-profile default
 xdsl line-config-profile default use-profile-number profile17a
 xdsl 1 line-config default
 bridgebase taggingmode single
 bridgebase routing-mode unrestricted
 vlan create 100-101
 vlan add 100 9-14 tagged
 vlan add 101 1/1-8/1 untagged 9-14 tagged
 bridgeport 1/1-8/1 pvid 101
 bridgeport 9-14 pvid 1
 bridgeport host-protocol pppoe 1/1-8/1
 xdsl 1-8 enable
!
interface br100
 no shutdown
 ip address {!!$cb->ip!!}/{!!$cb->prefix!!}
!
snmp community ro {!!$cb->snmp_ro!!} {!!$cb->prov_ip!!}
snmp community rw {!!$cb->snmp_rw!!} {!!$cb->prov_ip!!}
!
ntp-client server {!!$cb->prov_ip!!}
!
end
