#!/usr/bin/ksh
set -eau
KIKI=1
echo "${KIKI:-kiki}"
if (( $# > 1 )); then
	echo superieur a 1
fi