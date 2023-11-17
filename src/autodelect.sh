#!/bin/bash

file="images"

for line in $(cat "$file"); do
    sudo docker stop `sudo docker ps -a --filter "ancestor=$line" --format "{{.ID}}"` && sudo docker rm `sudo docker ps -a --filter "ancestor=$line" --format "{{.ID}}"`
done

sudo atrm `cat task.txt`

rm -f task.txt

rm -f $0
