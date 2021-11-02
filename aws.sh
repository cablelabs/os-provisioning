#!/bin/bash
dir='nmsprime'

# s3 bucket
bucket=$(grep '^bucket[[:space:]]*=' ~/.aws/backup.cfg | cut -d'=' -f2 | xargs)
if [ -z "$bucket" ]; then
	echo 'no aws bucket found, terminating' >&2
	exit 1
fi

# gpg key
key_id=$(grep '^key_id[[:space:]]*=' ~/.aws/backup.cfg | cut -d'=' -f2 | xargs)
if [ -z "$key_id" ]; then
	# use default key if none was set
	key_id='0x8FDA2EA42E98F903'
fi

# if necessary import gpg backup key
gpg --list-keys "$key_id" > /dev/null 2>&1 || gpg --recv-keys "$key_id"
gpg --list-keys "$key_id" > /dev/null 2>&1 || exit 1

# check if awscli is available
which aws > /dev/null || exit 1

# expected size for aws stream upload (upper limit), due to compression the actual size should be smaller
size=$(du -sbc /home /root /var/lib/cacti/rra /var/www/nmsprime/storage /tftpboot /var/log | tail -1 | cut -f1)

# run backup script, encrypt and push into the aws s3 bucket, gpg doesn't need to compress (-z0) as we already have a gzipped tar
/var/www/nmsprime/backup.sh | gpg -z0 --encrypt --recipient "$key_id" --trust-model always | aws s3 cp - $(date "+s3://$bucket/$dir/%Y%m%dT%H%M%S.tar.gz.gpg") --expected-size "$size"
# upload stderr of tar (see backup.sh) to aws s3 as well
aws s3 cp /root/backup-nmsprime.txt "s3://$bucket/$dir/"

# this might come in handy, if /root/db_dumps/nmsprime.psql might get too large at some point in time
# psql dump via fifos is not possible, i.e. can't be read from tar in backup.sh on-the-fly
# see https://lists.gnu.org/archive/html/bug-tar/2010-01/msg00001.html
# thus create another file on S3 for the monitoring database
#size=$(echo "SELECT pg_database_size('nmsprime');" | su - postgres -c '/usr/pgsql-13/bin/psql -d nmsprime' | head -3 | tail -1 | tr -cd '[:digit:]')
#su - postgres -c '/usr/pgsql-13/bin/pg_dump -Fc nmsprime' | gpg -z0 --encrypt --recipient "$key_id" --trust-model always | aws s3 cp - $(date "+s3://$bucket/$dir/%Y%m%dT%H%M%S.psql.gpg") --expected-size "$size"

# delete backup of 2 days ago except for wednesdays, thus keeping the monday backups
# deleting older monday versions should be done using an aws s3 lifecycle rule
if [ $(date +%u) -ne 3 ]; then
	aws s3 rm "s3://$bucket/$dir" --recursive --exclude "*" --include $(date -d '2 days ago' +%Y%m%dT*)
fi
