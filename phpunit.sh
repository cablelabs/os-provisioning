#!/bin/bash

LOGFILE="phpunit_log.htm"
OUTFILE="phpunit_output.htm"

# debug e.g. shows the tests to be run
DEBUG=""
DEBUG=" --debug"

PHPUNIT="/usr/bin/phpunit"

OPTS=""
OPTS=" --testdox-html $LOGFILE --colors"

# if empty: run all tests
# can be used on developing tests (you don't want to run the complete test suite in this case)
TESTS=""
TESTS=" modules/ProvVoip/Tests/PhonenumberLifecycleTest.php"
TESTS=" --filter testIndexViewDatatablesDataAvailable modules/ProvBase/Tests/ModemLifecycleTest.php"
TESTS=" --filter testDeleteFromIndexView modules/ProvBase/Tests/ModemLifecycleTest.php"
TESTS=" --filter testRoutesAuthMiddleware tests/RoutesAuthTest"


touch $LOGFILE $OUTFILE
chmod 666 $LOGFILE $OUTFILE

CMD="sudo -u apache $PHPUNIT$OPTS$DEBUG$TESTS | tee $OUTFILE"

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
