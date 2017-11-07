#!/usr/bin/bash
dir='/var/www/nmsprime'

handle_module() {
	if [ "$1" == 'base' ]
	then
		path="$dir/Install"
	else
		path="$dir/modules/$1/Install"
	fi

	# per-module before install script
	if [ -f "$path/before_install.sh" ]; then
		read -p "$1: run $path/before_install.sh? [Y/n] " ans
		if [ "$ans" != 'n' ]; then
			/usr/bin/bash "$path/before_install.sh"
		fi
	fi
	# generic-module before install script
	if [ "$1" != 'base' ]; then
		read -p "$1: run module_before_install.sh? [Y/n] " ans
		if [ "$ans" != 'n' ]; then
			/usr/bin/bash Install/module_before_install.sh
		fi
	fi

	if [ -f "$path/config.cfg" ]; then
		# install dependencies
		depends=$(grep '^depends[[:space:]]*=' "$path/config.cfg" | cut -d'=' -f2 | xargs | tr -d '"')
		if [ -n "$depends" ]; then
			read -p "$1: yum install $depends? [Y/n] " ans
			if [ "$ans" != 'n' ]; then
				/usr/bin/yum install -y $depends
			fi
		fi
		# copy files
		while read -r line; do
			f_from=$(echo "$line" | cut -d'=' -f1 | xargs)
			f_to=$(echo "$line" | cut -d'=' -f2 | xargs)
			read -p "$1: copy $path/files/$f_from to $f_to ? [Y/n] " ans </dev/tty
			if [ "$ans" != 'n' ]; then
				mkdir -p $(dirname "$f_to")
				cp "$path/files/$f_from" "$f_to"
			fi
		done < <(awk '/\[files\]/{flag=1;next}/\[/{flag=0}flag' "$path/config.cfg" | grep '=')
	fi

	# per-module after install script
	if [ -f "$path/after_install.sh" ]; then
		read -p "$1: run $path/after_install.sh? [Y/n] " ans
		if [ "$ans" != 'n' ]; then
			/usr/bin/bash "$path/after_install.sh"
		fi
	fi
	# generic-module after install script
	if [ "$1" != 'base' ]; then
		read -p "$1: run module_after_install.sh? [Y/n] " ans
		if [ "$ans" != 'n' ]; then
			/usr/bin/bash Install/module_after_install.sh
		fi
	fi
}

handle_module base
for file in $(cat <(find modules/ -name module.json | grep Ccc) <(find modules/ -name module.json | grep -v Ccc)); do
	if [ $(grep active "$file" | cut -d':' -f2 | tr -cd "[:digit:]\n") -ne 1 ]; then
		continue
	fi
	handle_module "$(echo "$file" | cut -d'/' -f2)"
done
