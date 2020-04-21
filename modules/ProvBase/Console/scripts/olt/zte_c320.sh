if [ "$#" -ne 4 ]; then
    echo "Usage: $0 hostname username password vlan" >&2
    exit 1
fi

hostname="$1"
username="$2"
password="$3"
vlan="$4"

interfaces=$(expect <<- EOF |
set timeout 5

spawn ssh "$username@$hostname"
expect "*assword:"
send "$password\n"
expect "*#"
send "terminal length 0\nshow running-config\n"
expect eof
send "exit\n"
EOF
grep '^interface gpon-onu_' | cut -d' ' -f2)

cmd=''
while read interface; do
    cmd+='conf t\n'
    cmd+="interface $interface\n"
    cmd+='type universal\n'
    cmd+='tcont 1 profile MAX-UP\n'
    cmd+='gemport 1 unicast tcont 1 dir both\n'
    cmd+='switchport mode hybrid vport 1\n'
    cmd+="service-port 1 vport 1 user-vlan untag vlan $vlan\n"
    cmd+='end\n\conf t\n'

    cmd+="pon-onu-mng $interface\n"
    cmd+='service INET gemport 1 untag\n'
    cmd+='vlan port eth_0/1 mode transparent\n'
    cmd+='end\n'
done <<< "$interfaces"

expect <<- EOF
set timeout 5

spawn ssh "$username@$hostname"
expect "*assword:"
send "$password\n"
expect "*#"
send "$cmd"
expect eof
send "exit\n"
EOF
