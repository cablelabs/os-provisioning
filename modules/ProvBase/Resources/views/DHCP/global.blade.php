ddns-domainname "{{$domainName}}.";
option domain-name "{{$domainName}}";
option domain-name-servers {{$ipList}};
default-lease-time {{$defLeaseTime}};
max-lease-time {{$maxLeaseTime}};
next-server {{$ownIp}};

@if ($vivso)
# option vivso.2 (CL_V4OPTION_TFTPSERVERS, see https://www.excentis.com/blog/how-provision-cable-modem-using-isc-dhcp-server)
option vivso {{$vivso}};
@endif

option log-servers {{$ipList}};
option time-servers {{$ipList}};
option time-offset {{date('Z')}};

# zone
zone {{$domainName}} {
    primary 127.0.0.1;
    key dhcpupdate;
}

# reverse zone
zone in-addr.arpa {
    primary 127.0.0.1;
    key dhcpupdate;
}

@if ($mtaDomain)
# zone for voip devices
zone {{$mtaDomain}} {
    primary {{$ownIp}};
    key dhcpupdate;
}
@endif

class "CM" {
    match if (substring(option vendor-class-identifier,0,6) = "docsis");
    option ccc.dhcp-server-1 0.0.0.0;
    ddns-updates on;
}

class "MTA" {
    match if (substring(option vendor-class-identifier,0,4) = "pktc");
    option ccc.provision-server 0 "{{$dhcpFqdn}}"; # number of letters before every through dot seperated word
    option ccc.realm "\005BASIC\0011\000";
    ddns-updates on;
}

@if ($stbMatch)
class "STB" {
    match if (({!!$stbMatch!!}));
    spawn with binary-to-ascii(16, 8, ":", substring(option agent.remote-id, 0, 6)); # create a sub-class automatically
    lease limit {{$leaseLimit}}; # max number of stbs per cm
}
@endif

class "Client" {
    match if (({!!$cpeMatch!!}));
    spawn with binary-to-ascii(16, 8, ":", substring(option agent.remote-id, 0, 6)); # create a sub-class automatically
    lease limit {{$leaseLimit}}; # max number of private cpes per cm
}

class "Client-Public" {
    match if (({!!$cpeMatch!!}));
    match pick-first-value (option agent.remote-id);
    lease limit {{$leaseLimit}}; # max number of public cpes per cm
}

# All CPEs of modems without internet access - will be defined as subclass
class "blocked" {
    match if (({!!$cpeMatch!!}));
    match pick-first-value (option agent.remote-id);
    deny booting;
}
