#!/bin/bash

free | awk 'NR==2{printf "%.2f\n", $3/$2 * 100}'
