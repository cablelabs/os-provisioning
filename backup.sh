#!/bin/bash
dir='/var/www/nmsprime'
ref_dir='ref'

handle_module() {
	if [[ "$1" == 'base' ]]
	then
		cfg="$dir/Install/config.cfg"
	else
		cfg="$(dirname $1)/Install/config.cfg"
	fi

	if [[ -f "$cfg" ]]; then
		while read -r line; do
			files+=($(echo "$line" | cut -d'=' -f2 | xargs))
		done < <(awk '/\[files\]/{flag=1;next}/\[/{flag=0}flag' "$cfg" | grep '=')

		for file in $(grep '^configfiles' "$cfg" | cut -d'=' -f2 | grep -o '"[^"]\+"' | tr -d '"'); do
			files+=("$(dirname $1)/$file")
		done
	fi
}

display_help() {
	echo "Usage: $0 -p mysql_root_password [> output-file.tar.gz]" >&2
	exit 1
}

while getopts ":p:h" opt; do
	case $opt in
		h)
			display_help
			exit 0
			;;
		p)
			pw="$OPTARG"
			;;
		\?)
			echo "Invalid option: -$OPTARG" >&2
			display_help
			exit 1
			;;
	esac
done

if [[ -z "$pw" ]]; then
	display_help
	exit 1
fi

excludes=(
	"$dir/storage/app/tmp"
	"$dir/storage/framework"
)

static=(
	'/etc/cron.d'
	'/etc/dhcp-nmsprime'
	'/etc/hostname'
	'/etc/firewalld'
	'/etc/group'
	'/etc/named'*
	'/etc/nmsprime'
	'/etc/passwd'
	'/etc/pki/tls/private'
	'/etc/shadow'
	'/etc/sysconfig/network-scripts/ifcfg-'*
	'/etc/sysconfig/network-scripts/route-'*
	'/home'
	'/root'
	'/tftpboot'
	'/var/lib/acme'
	'/var/lib/cacti/rra'
	'/var/lib/dhcpd'
	'/var/log'
	'/var/named/dynamic'
	"$dir/storage"
)

# transformed files won't overwrite their system counterparts while untarring
# instead they will be put into /$ref_dir for reference/diffing
transform=(
	"s|etc/group|$ref_dir/etc/group|"
	"s|etc/nmsprime/env|$ref_dir/etc/nmsprime/env|"
	"s|etc/passwd|$ref_dir/etc/passwd|"
	"s|etc/shadow|$ref_dir/etc/shadow|"
	"s|etc/sysconfig|$ref_dir/etc/sysconfig|"
	"s|^|$(date +%Y%m%dT%H%M%S)/|"
)

files=()
rpm_files=$(rpm -qa 'nmsprime*' -c)
if [[ -n "$rpm_files" ]]
then
	# rpm
	readarray -t files <<< "$rpm_files"
else
	# git
	handle_module base
	for module in "$dir"/modules/*/module.json; do
		handle_module "$module"
	done
fi

mysqldump -u root --password="$pw" --databases cacti director icinga2 icingaweb2 nmsprime nmsprime_ccc | gzip > /root/databases.sql.gz
tar --exclude-from <(IFS=$'\n'; echo "${excludes[*]}") --transform=$(IFS=';'; echo "${transform[*]}") --hard-dereference -cz "${static[@]}" "${files[@]}"
