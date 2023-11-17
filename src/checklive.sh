#!/bin/bash

cd $1/$2

grep "image:" docker-compose.yml | awk '{print $2}' > images

file="images"

for line in $(cat "$file"); do
    sudo docker ps --filter "ancestor=$line" --format "{{.Image}}"
done