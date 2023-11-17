#!/bin/bash

cd $1/$2

sudo atrm `cat task.txt`

sudo sh autodelect.sh