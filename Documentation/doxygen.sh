#!/bin/bash

DST_DIR="/var/www/html/lara/doxygen"


SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

cd $SCRIPT_DIR

# add current git commit hash as version to doxygen
# see https://christianhujer.github.io/Git-Version-in-Doxygen
export PROJECT_NUMBER="$(git rev-parse HEAD ; git diff-index --quiet HEAD || echo '(with uncommitted changes)')"

doxygen $SCRIPT_DIR/doxyfile

echo
echo "Changing group of doxygen dir to apache"
sudo chgrp -R apache $DST_DIR

echo
echo "Warnings from last run are stored in $SCRIPT_DIR/doxywarn.log"
echo
