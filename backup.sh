#!/usr/bin/bash
dir='/var/www/nmsprime'
out='/tmp'

handle_module() {
	if [ "$1" == 'base' ]
	then
		path="$dir/Install"
	else
		path="$dir/modules/$1/Install"
	fi

	# backup config files
	if [ -f "$path/config.cfg" ]; then
		while read -r line; do
			f_to=$(echo "$line" | cut -d'=' -f2 | xargs)
			mkdir -p $(dirname "$out$f_to")
			cp -a "$f_to" "$out$f_to"
		done < <(awk '/\[files\]/{flag=1;next}/\[/{flag=0}flag' "$path/config.cfg" | grep '=')
	fi
}

while getopts ":o:p:" opt; do
	case $opt in
		o)
			out="$OPTARG"
			;;
		p)
			pw="$OPTARG"
			;;
		\?)
			echo "Invalid option: -$OPTARG" >&2
			exit 1
			;;
	esac
done

date=$(date +%Y%m%dT%H%M%S)
out="$out/$date"
mkdir -p "$out"

# dump all needed databases
mysqldump -u root --password="$pw" --databases cacti director icinga2 icingaweb2 nmsprime nmsprime_ccc > "$out/dump.sql"

# backup all rrd files
mkdir -p "$out/var/lib/cacti"
rsync -a /var/lib/cacti/rra "$out/var/lib/cacti/"

# backup dhcpd lease file
systemctl restart dhcpd
mkdir -p "$out/var/lib/dhcpd"
cp -a /var/lib/dhcpd/dhcpd.leases "$out/var/lib/dhcpd/"

# backup named zone files
mkdir -p "$out/var/named/dynamic"
cp -a /var/named/dynamic/*.zone "$out/var/named/dynamic/"

# backup firewalld zones
mkdir -p "$out/etc/firewalld/zones"
rsync -a /etc/firewalld/zones "$out/etc/firewalld"

# backup tftpboot folder
mkdir -p "$out/tftpboot"
rsync -a /tftpboot "$out"

# backup nmsprime storage folder excluding temporary data
mkdir -p "$out$dir"
rsync -a --exclude framework "$dir/storage" "$out$dir"

# backup config files of nmsprime and all its modules
handle_module base
for file in $(find modules/ -name module.json); do
	handle_module "$(echo "$file" | cut -d'/' -f2)"
	# backup module.json, stating if modules is enabled or not
	mkdir -p $(dirname "$out$dir/$file")
	cp -a "$dir/$file" "$out$dir/$file"
done

cd "$out/.."
tar czf "$date.tar.gz" "$date"
