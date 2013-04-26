#!/usr/bin/ksh

alias try='
set +e
(
set -e
'

alias catch='
)
if [ $? -ne 0 ]; then
'

alias end_catch='
fi
'