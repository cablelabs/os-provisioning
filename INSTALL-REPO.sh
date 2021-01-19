#!/bin/bash

# add epel
yum install -y epel-release

# add Icinga repo
yum install -y https://packages.icinga.com/epel/icinga-rpm-release-7-latest.noarch.rpm

# add NMS Prime repos
yum install -y https://repo.nmsprime.com/rpm/nmsprime/nmsprime-repos-2.6.0-1.noarch.rpm

# enable software collections, needed for rh-php73 and rh-nodejs12
yum install -y centos-release-scl

# clean & update
yum clean all && yum update -y
