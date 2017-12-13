#!/bin/bash 

LOGFILE="tmp_phpunit_log.htm"
OUTFILE="tmp_phpunit_out.htm"

# debug e.g. shows the tests to be run
DEBUG=""
DEBUG=" --debug"

PHPUNIT="/usr/bin/phpunit"

OPTS=""
OPTS=" --testdox-html $LOGFILE --colors"

# if empty: run all tests
# can be used on developing tests (you don't want to run the complete test suite in this case)
TESTS=" modules/ProvBase/Tests/ModemLifecycleTest.php"
TESTS=""
TESTS=" modules/ProvVoip/Tests/PhonenumberLifecycleTest.php"


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
