#!/bin/bash

# add current git commit hash as version to doxygen
# see https://christianhujer.github.io/Git-Version-in-Doxygen
export PROJECT_NUMBER="$(git rev-parse HEAD ; git diff-index --quiet HEAD || echo '(with uncommitted changes)')"

doxygen /var/www/lara/Documentation/doxyfile

echo
echo "Warnings from last run are stored in doxywarn.log"
echo
