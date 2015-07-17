#!/bin/bash

#
# parameter
#
name=$1
hostname=$2
community=$3

cacti="/var/lib/cacti/cli"


#
# Add Host
#
host_id=`php -q $cacti/add_device.php --description=$name --ip=$hostname --template=9 --community=$community | grep Success | tr '()' '\n\n' | head -n 2 | tail -n1`


echo -e "added Host with ID $host_id\n";

#
# Add Graphs
#
php -q $cacti/add_graphs.php --host-id=$host_id --graph-type=cg --graph-template-id=35
php -q $cacti/add_graphs.php --host-id=$host_id --graph-type=cg --graph-template-id=36
php -q $cacti/add_graphs.php --host-id=$host_id --graph-type=cg --graph-template-id=37
php -q $cacti/add_graphs.php --host-id=$host_id --graph-type=cg --graph-template-id=38


#
# Add Host to Tree Page
#
php -q $cacti/add_tree.php --type=node --node-type=host --tree-id=2 --host-id=$host_id

#
# example:
#
# php -q add_device.php --description="cm-100051" --ip="cm-100051.test.erznet.tv" --template=9 --community="public"
# php -q add_graphs.php --host-id=3 --graph-type=cg --graph-template-id=35
# php -q add_tree.php --type=node --node-type=host --tree-id=2 --host-id=5
#
# Note: template see: http://docs.cacti.net/usertemplate:data:docs_if_mib:modem_stats
#