#!/bin/bash

apt-get update

apt-get upgrade

ufw disable

adduser rootshell

apt-get -y install docker.io docker-compose-v2 apache2 php-fpm libapache2-mod-php sed awk at git php-cli unzip php-mbstring

cp ./composer-setup.php /tmp/composer-setup.php

sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

sed -i "s/# Members of the admin group may gain root privileges/rootshell ALL=(ALL:ALL) NOPASSWD:ALL/g" /etc/sudoers

sed -i "s/www-data/rootshell/g" /etc/apache2/envvars

cp ./src/* ../vulhub/

cp ./daemon.json /etc/docker/daemon.json

systemctl start apache2

systemctl enable apache2

systemctl daemon-reload

systemctl restart docker

chown -R rootshell:rootshell /var/www/html/

cd ../vulhub

sudo chmod +x addtime.sh autodelect.sh checklive.sh checkport.sh checktime.sh findports.sh startdocker.sh stopdocker.sh getram.sh

composer require erusev/parsedown

echo "OK!"