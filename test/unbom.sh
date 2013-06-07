#!/usr/bin/bash
#tree -if | grep -v '^./_ext' | xargs -i test/unbom.sh {}
for F in $1
do
	echo "Processing $F"
  if [[ -f $F && `head -c 3 $F` == $'\xef\xbb\xbf' ]]; then
      # file exists and has UTF-8 BOM
      mv $F $F.bak
      tail -c +4 $F.bak > $F
      rm $F.bak
      echo "removed BOM from $F"
  fi
done