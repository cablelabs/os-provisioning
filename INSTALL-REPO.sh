#!/bin/bash

# add epel
yum install -y epel-release

# add NMS Prime repos
yum install -y https://repo.nmsprime.com/rpm/misc/nmsprime-repos-latest.noarch.rpm

# enable software collections, needed for rh-nodejs12
yum install -y centos-release-scl

# clean & update
yum clean all && yum update -y
