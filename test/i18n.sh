#!/usr/bin/sh

PROJECT_DIR=..

cd $PROJECT_DIR

grep -rn "_t[(][\"]" * |\
	perl -pe 's#^.*_t[(]["](.*?)["][)].*$#\1#g' |\
	sort -u > test/f1.txt

cat test/f1.txt | while read line
do
	#echo "line=$line"
	line=`echo $line | perl -pe 's#"#\"#g'`
	L=`grep "$line" locale/fr/messages.php | wc -l | xargs`
	if [ $L -eq 0 ]; then
		echo $line
	fi
done

#rm -f test/f1.txt