#!/usr/bin/env bash

PASSWORD="root"

echo "mariadb-server-10.1 mysql-server/root_password password $PASSWORD" | debconf-set-selections
echo "mariadb-server-10.1 mysql-server/root_password_again password $PASSWORD" | debconf-set-selections

yes | apt-get install mariadb-server

mysql --user=root --password="$PASSWORD" --execute="CREATE DATABASE vagrant";
mysql --user=root --password="$PASSWORD" --execute="GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '$PASSWORD';"
sed -ie "s/^bind-address/#bind-address/g" "/etc/mysql/mariadb.conf.d/50-server.cnf"

service mysql restart
