#!/bin/bash

# this is a simple starter script for test development
# it allows to run single classes or even single tests so you don't need to run the complete testing suite
# for real testing use nmsprime_testsuite.php

LOGFILE="phpunit/phpunit_log.htm"
OUTFILE="phpunit/phpunit_output.htm"

rm -f phpunit/*.htm

# debug e.g. shows the tests to be run
DEBUG=" --debug"
DEBUG=""

PHPUNIT="source scl_source enable rh-php71; vendor/bin/phpunit"

OPTS=""
OPTS=" --testdox-html $LOGFILE --colors --stop-on-failure --bootstrap /var/www/nmsprime/bootstrap/autoload.php"

# if empty: run all tests
# can be used on developing tests (you don't want to run the complete test suite in this case)
TESTS=""
TESTS=" --filter testDeleteFromIndexView modules/ProvBase/Tests/ModemLifecycleTest.php"
TESTS=" --filter testDatatableDataReturned modules/ProvBase/Tests/ModemLifecycleTest.php"
TESTS=" modules/ProvBase/Tests/DomainLifecycleTest.php"
TESTS=" modules/ProvBase/Tests/CmtsLifecycleTest.php"
TESTS=" modules/ProvBase/Tests/IpPoolLifecycleTest.php"
TESTS=" modules/ProvBase/Tests/EndpointLifecycleTest.php"
TESTS=" modules/ProvBase/Tests/IpPoolLifecycleTest.php"
TESTS=" --filter testRoutesAuthMiddleware tests/RoutesAuthTest"
TESTS=" modules/ProvVoip/Tests/MtaLifecycleTest.php"
TESTS=" modules/ProvVoip/Tests/PhonenumberLifecycleTest.php"
TESTS=" modules/ProvBase/Tests/ModemLifecycleTest.php"
TESTS=" modules/ProvBase/Tests/ContractLifecycleTest.php"
TESTS=" modules/ProvVoip/Tests/PhoneTariffLifecycleTest.php"
TESTS=" modules/BillingBase/Tests/ItemLifecycleTest.php"
TESTS=" modules/BillingBase/Tests/ProductLifecycleTest.php"


touch $LOGFILE $OUTFILE
chmod 666 $LOGFILE $OUTFILE

# CMD="sudo -u apache $PHPUNIT$OPTS$DEBUG$TESTS | tee $OUTFILE"
# CMD="$PHPUNIT --version"
CMD="$PHPUNIT$OPTS$DEBUG$TESTS | tee $OUTFILE"

clear
echo
echo $CMD
echo
eval $CMD

echo
echo
echo "–––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––"
echo
echo "Log data can be found in $LOGFILE"
echo "PHPUnit output can be found in $OUTFILE"
echo
