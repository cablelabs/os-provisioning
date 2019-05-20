#!/bin/bash
# do not run ddns for CPEs with a private IP address, those aren't publicly reachable anyway
if grep -q -E '^(10\.|192\.168)' <<< "$2"; then
	exit 0
fi
if grep -q -E '^(172\.|100\.)' <<< "$2"; then
	IFS='.' read -r -a ip <<< "$2"
	if [ "${ip[0]}" -eq 172 -a "${ip[1]}" -ge 16 -a "${ip[1]}" -le 31 ]; then
		exit 0
	fi
	if [ "${ip[0]}" -eq 100 -a "${ip[1]}" -ge 64 -a "${ip[1]}" -le 127 ]; then
		exit 0
	fi
fi

# we use a secret to salt the generation of hostnames (base32 encoded and truncated to 6 characters)
# the python code should be replaced by coreutuils base32, which will be available with version 8.25
mangle=$(echo "$1" | tr -cd "[:xdigit:]" | xxd -r -p | openssl dgst -sha256 -macopt hexkey:$(cat /etc/named-ddns-cpe.key) -binary | python -c 'import base64; import sys; print(base64.b32encode(sys.stdin.read())[:6].lower())')
rev=$(awk -F. '{OFS="."; print $4,$3,$2,$1}' <<< "$2")

if [ "$3" -ne 0 ]
then
	cmd="
	update delete ${mangle}.cpe.nmsprime.test.
	send
	update delete ${rev}.in-addr.arpa.
	send
	"
else
	cmd="
	update add ${mangle}.cpe.nmsprime.test. 3600 A $2
	send
	update add ${rev}.in-addr.arpa. 3600 PTR ${mangle}.cpe.nmsprime.test.
	send
	"
fi

echo "$cmd" | nsupdate -v -l -y dhcpupdate:<DNS-PASSWORD>
