#!/usr/bin/sh
set -eau

: ${PROJECT_DIR}

cd $PROJECT_DIR

grep -rn "_t[(][\"]" * |\
	perl -pe 's#^.*_t[(]["](.*?)["][)].*$#\1#g' |\
	sort -u > test/f1.txt

grep -rn "[{][{]" * |\
	grep -v "^_ext" |\
	perl -pe 's#^.*[{][{](.*?)[}][}].*$#\1#g' |\
	grep -v "<?php" |\
	grep -v "^Binary file" |\
	sort -u >> test/f1.txt

cat test/f1.txt | sort -u > test/f2.txt
mv test/f2.txt test/f1.txt

cat test/f1.txt | while read line
do
	#echo "line=$line"
	#L=`grep -F "$line" locale/fr/messages.php | wc -l | xargs`
	if ! grep -q "${line}" locale/fr/messages.php; then
		echo "$line"
	fi
done

rm -f test/f1.txt

echo ""
echo "#################"
echo "#    Success    #"
echo "#################"