#!/usr/bin/bash
dir='/var/www/nmsprime'
assumeyes=0

exec_cmd() {
	if [ $assumeyes -eq 1 ]; then
		return 0
	fi

	read -p "$1" ans < /dev/tty
	if [ "$ans" != 'n' ]; then
		return 0
	fi

	return 1
}

handle_module() {
	if [ "$1" == 'base' ]
	then
		path="$dir/Install"
	else
		path="$dir/modules/$1/Install"
	fi

	# per-module before install script
	if [ -f "$path/before_install.sh" ]; then
		if exec_cmd "$1: run $path/before_install.sh? [Y/n] "; then
			/usr/bin/bash "$path/before_install.sh"
		fi
	fi
	# generic-module before install script
	if [ "$1" != 'base' ]; then
		if exec_cmd "$1: run module_before_install.sh? [Y/n] "; then
			/usr/bin/bash Install/module_before_install.sh
		fi
	fi

	if [ -f "$path/config.cfg" ]; then
		# install dependencies
		IFS=';' read -r -a depends <<< $(grep '^depends[[:space:]]*=' "$path/config.cfg" | cut -d'=' -f2- | xargs)
		if [ ${#depends[@]} -ne 0 ]; then
			if exec_cmd "$1: yum install ${depends[@]}? [Y/n] "; then
				/usr/bin/yum install -y "${depends[@]}"
			fi
		fi
		# copy files
		while read -r line; do
			f_from=$(echo "$line" | cut -d'=' -f1 | xargs)
			f_to=$(echo "$line" | cut -d'=' -f2 | xargs)
			if exec_cmd "$1: copy $path/files/$f_from to $f_to ? [Y/n] "; then
				mkdir -p $(dirname "$f_to")
				cp "$path/files/$f_from" "$f_to"
			fi
		done < <(awk '/\[files\]/{flag=1;next}/\[/{flag=0}flag' "$path/config.cfg" | grep '=')
	fi

	# per-module after install script
	if [ -f "$path/after_install.sh" ]; then
		if exec_cmd "$1: run $path/after_install.sh? [Y/n] "; then
			/usr/bin/bash "$path/after_install.sh"
		fi
	fi
	# generic-module after install script
	if [ "$1" != 'base' ]; then
		if exec_cmd "$1: run module_after_install.sh? [Y/n] "; then
			/usr/bin/bash Install/module_after_install.sh
		fi
	fi
}

while getopts ":y" opt; do
	case $opt in
		y)
			assumeyes=1
			;;
		\?)
			echo "Invalid option: -$OPTARG" >&2
			exit 1
			;;
	esac
done

# composer, we can't run scripts yet, as artisan can't be executed as of now
composer install --no-scripts

# nmsprime
handle_module base
for file in $(cat <(find modules/ -name module.json | grep Ccc) <(find modules/ -name module.json | grep -v Ccc)); do
	if [ $(grep active "$file" | cut -d':' -f2 | tr -cd "[:digit:]\n") -ne 1 ]; then
		continue
	fi
	handle_module "$(echo "$file" | cut -d'/' -f2)"
done
