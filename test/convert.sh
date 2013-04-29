#!/usr/bin/ksh
set -eau

. ./lib.sh
PROJECT_DIR=..
KIKI=`ls`
evt_convert() {
	if [[ ! -w "${1}" ]]; then
		echo "Read only: ${1}"
		return
	fi
	if file "${1}" | grep -q CRLF; then
		dos2unix "${1}"
		echo "${1}: Converted DOS=>UNIX: ${1}"
	fi
	if file "${1}" | grep -q ISO-8859; then
		iconv -f ISO-8859-1 -t UTF-8 "${1}" > "${1}.tmp"
		if diff -q "${1}" "${1}.tmp"; then
			rm "${1}.tmp"
		else
			mv "${1}.tmp" "${1}"
			echo "${1}: Converted ASCII=>UTF-8: ${1}"
		fi
	fi

	if file "${1}" | grep -q 'with BOM'; then
		tail -c +4 "${1}" > "${1}.tmp"
		mv "${1}.tmp" "${1}"
		echo "${1}: UTF-8 BOM Removed."
	fi
}

try
	LIST=`find $PROJECT_DIR -type f |\
		grep -v '^\.\./ext' |\
		grep -v '^\.\./\.git' |\
		grep -v '^\.\./test'`

	if (( $# > 0 )); then
		LIST="${@:-}"
	fi

	echo "${LIST}" | while read line
	do
		evt_convert ${line}
	done
catch
	echo ERROR
end_catch
