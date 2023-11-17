#!/bin/bash

cd $1/$2

current_time=$(sudo date +"%s")

at_time=$(sudo date -d "`sudo atq | sudo grep \`cat task.txt\` | sudo awk '{print $3,$4,$5,$6}'`" +"%s")

remaining_seconds=$(( $at_time - $current_time ))

hours=$(( $remaining_seconds / 3600 ))
minutes=$(( ($remaining_seconds % 3600) / 60 ))
seconds=$(( ($remaining_seconds % 3600) % 60 ))

printf "%02d:%02d:%02d" $hours $minutes $seconds