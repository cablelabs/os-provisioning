#!/bin/bash

# add epel
yum install -y epel-release

# add Icinga repo
rpm -ivh https://packages.icinga.com/epel/icinga-rpm-release-7-latest.noarch.rpm

# add nmsprime repo
curl https://raw.githubusercontent.com/nmsprime/nmsprime/master/nmsprime.repo -Lo /etc/yum.repos.d/nmsprime.repo

# enable software collections, since icingaweb2 depends on php7.1 from there
yum install -y centos-release-scl

# workaround until next release
yum install -y rh-php71-php-fpm

# clean & update
yum clean all && yum update -y
