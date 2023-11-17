#!/bin/bash

cd $1/$2

sudo atrm `cat task.txt`

echo "/var/www/html/$1/$2/autodelect.sh" | at now + 2 hours 2>&1 | grep -oP 'job (\d+) at' | grep -oP '\d+' > task.txt

printf "OK"