#/bin/bash
# do not run ddns for CPEs with a private IP address, those aren't publicly reachable anyway
if grep -q -E '^(10\.|172\.1[6789]\.|172\.2[0-9]\.|172\.3[01]\.|100\.64|192\.168)' <<< "$3"; then
	exit 0
fi

# we use a secret to salt the generation of hostnames (base32 encoded and truncated to 6 characters)
# the python code should be replaced by coreutuils base32, which will be available with version 8.25
name=$(echo -n "$1:$2" | tr -cd "[:xdigit:]" | xxd -r -p | openssl dgst -sha256 -macopt hexkey:$(cat /etc/named-ddns-cpe.key) -binary | python -c 'import base64; import sys; print(base64.b32encode(sys.stdin.read())[:6].lower())')
nsupdate -v -l -y dhcpupdate:<DNS-PASSWORD> << EOF
update delete ${name}.cpe.nmsprime.test.
update add ${name}.cpe.nmsprime.test. 3600 A $3

update add $(awk -F. '{OFS="."; print $4,$3,$2,$1}' <<< "$3").in-addr.arpa. 3600 PTR ${name}.cpe.nmsprime.test.
send
EOF
