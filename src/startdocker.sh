#!/bin/bash

cd $1/$2

echo "/var/www/html/$1/$2/autodelect.sh" | at now + 2 hours 2>&1 | grep -oP "job (\d+) at" | grep -oP "\d+" > task.txt

sudo docker compose up -d

sudo cp ../../autodelect.sh autodelect.sh

sudo chmod +x autodelect.sh

