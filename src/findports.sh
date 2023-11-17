#!/bin/bash

cd $1/$2

grep -o "[0-9]\+:[0-9tcpud/]\+" docker-compose.yml | grep -v ":/" | grep -v "^0" | sed 's/\([0-9]*\):.*/\1/'