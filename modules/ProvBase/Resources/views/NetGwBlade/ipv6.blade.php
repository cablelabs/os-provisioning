@if ($cb->ipv6)
 ipv6 enable
 ipv6 nd prefix default no-advertise
 ipv6 nd managed-config-flag
 ipv6 nd other-config-flag
 ipv6 nd ra interval 5
 ipv6 nd ra lifetime 120
 ipv6 dhcp relay destination FD00::1
@endif
