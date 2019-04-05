#!/bin/bash
dir='/var/www/nmsprime'
db_dir='db_dumps'
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
	echo "Usage: $0 [> output-file.tar.gz]" >&2
	exit 1
}

while getopts "h" opt; do
	case $opt in
		h)
			display_help
			exit 0
			;;
		\?)
			echo "Invalid option: -$OPTARG" >&2
			display_help
			exit 1
			;;
	esac
done

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

mkdir -p "/root/$db_dir"
for db in cacti director icinga2 icingaweb2 nmsprime nmsprime_ccc; do
	case "$db" in
	'cacti')
		auth=$(php -r 'require_once "/etc/cacti/db.php"; echo "$database_default\n$database_password\n$database_username\n";' | xargs)
		;;
	'nmsprime')
		auth=$(grep '^DB_DATABASE\|^DB_USERNAME\|^DB_PASSWORD' /etc/nmsprime/env/global.env | sort | cut -d'=' -f2 | xargs)
		;;
	'nmsprime_ccc')
		auth=$(grep '^CCC_DB_DATABASE\|^CCC_DB_USERNAME\|^CCC_DB_PASSWORD' /etc/nmsprime/env/ccc.env | sort | cut -d'=' -f2 | xargs)
		;;
	*)
		auth=$(awk "/\[$db\]/{flag=1;next}/\[/{flag=0}flag" /etc/icingaweb2/resources.ini | grep '^dbname\|^username\|^password' | sort | cut -d'=' -f2 | xargs)
		;;
	esac

	read -r -a auths <<< "$auth"
	mysqldump -u "${auths[2]}" --password="${auths[1]}" "${auths[0]}" | gzip > "/root/$db_dir/${auths[0]}.sql.gz"
done

tar --exclude-from <(IFS=$'\n'; echo "${excludes[*]}") --transform=$(IFS=';'; echo "${transform[*]}") --hard-dereference -cz "${static[@]}" "${files[@]}" 2> /root/backup-nmsprime.txt
