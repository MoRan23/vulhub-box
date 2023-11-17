#/bin/bash

sudo docker ps -a --format '{{.Image}}\t{{.Ports}}'|grep :$1|awk -F '\t' -v ORS=' ' '{print $1}'
