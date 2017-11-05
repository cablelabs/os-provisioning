#!/bin/bash

# add epel
# NOTE: IUS requires epel
yum install -y epel-release

# add IUS repo
# Note: Whitelist – only use php56u and yum-replace-plugin from IUS
rpm -ivh https://centos7.iuscommunity.org/ius-release.rpm
sed -i 's/\[ius\]/\[ius\]\nincludepkgs=php56u* yum-plugin-replace/' /etc/yum.repos.d/ius.repo

# add nmsprime repo
curl http://bit.ly/2zGarzc -Lo /etc/yum.repos.d/nmsprime.repo

# install and replace php56u
yum install -y php yum-plugin-replace
yum replace -y php --replace-with php56u

# clean & update
yum clean all && yum update -y
