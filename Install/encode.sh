#!/bin/bash
#
# call like ./encode.sh /var/www/lara /tmp/lara "1 2 3 4 5 6 7 8 9 10"

sour=$1
dest=$2
psws=($3)	# array

# Ioncube Settings
opt="--replace-target --license-check auto --ignore-interface-aliases"
cmd="ioncube_encoder.sh"


# Base Package Encode Directories 
directorys=( app database resources )

# List all Modules
modules=(`ls $sour/modules`)

# Prepare Code
rm -rf $dest
cp -R $sour $dest

rm -rf $dest/.git
rm -rf $dest/.git*
rm -rf $dest/.deprecated
rm -rf $dest/.env
rm -rf $dest/.env*

mkdir $dest/license


# Encode Base Package
psw_count=0
for dir in "${directorys[@]}"; do
        echo $cmd -56 $opt --with-license /etc/nms/license/Base.txt --passphrase ${psws[$psw_count]} $sour/$dir/ -o $dest/$dir
done


# Encode all Modules
psw_count=1
for dir in "${modules[@]}"; do
        echo $cmd -56 $opt --with-license /etc/nms/license/$dir.txt --passphrase ${psws[$psw_count]} $sour/modules/$dir/ -o $dest/modules/$dir
        psw_count=$[$psw_count+1]
done

