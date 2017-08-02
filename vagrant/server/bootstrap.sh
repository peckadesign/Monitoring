#!/usr/bin/env bash

apt-get install -y --force-yes \
	apt-transport-https \
	lsb-release \
	ca-certificates \
	curl

wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/sury.list

apt-get install software-properties-common
apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xcbcb082a1bb943db
add-apt-repository 'deb [arch=amd64,i386,ppc64el] http://mirror.vpsfree.cz/mariadb/repo/5.5/ubuntu trusty main'

export DEBIAN_FRONTEND=noninteractive
debconf-set-selections <<< 'mariadb-server-5.5 mysql-server/root_password password monitoring'
debconf-set-selections <<< 'mariadb-server-5.5 mysql-server/root_password_again password monitoring'

echo "deb http://www.rabbitmq.com/debian/ testing main" > /etc/apt/sources.list.d/rabbitmq.list
wget https://www.rabbitmq.com/rabbitmq-signing-key-public.asc -q -O - | apt-key add -

curl -sL https://deb.nodesource.com/setup_4.x | bash -

curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

apt-get update

apt-get upgrade -y --force-yes
apt-get install -y --force-yes \
	git \
	htop \
	vim \
	mariadb-server \
	rabbitmq-server \
	nodejs \
	yarn \
	apache2 \
	php7.1 \
	libapache2-mod-php7.1 \
	php7.1-xdebug \
	php7.1-curl \
	php7.1-xml \
	php7.1-zip \
	php7.1-mysql \
	php7.1-mbstring \
	php7.1-bcmath

a2enmod rewrite

php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

if ! [ -L "/var/www" ]; then
	rm -rf "/var/www"
	ln -fs "/vagrant" "/var/www"
fi

rm -rf /etc/apache2/sites-enabled/*
if ! [ -L "/etc/apache2/sites-available" ]; then
	if ! [ -L "/etc/apache2/sites-available/monitoring.conf" ]; then
		ln -s "/vagrant/vagrant/server/apache/sites-available/monitoring.conf" "/etc/apache2/sites-available/monitoring.conf"
	fi
	a2ensite -q monitoring.conf
fi

if ! [ -L "/etc/apache2/conf-available/monitoring.conf" ]; then
	rm -f "/etc/apache2/conf-available/monitoring.conf"
	ln -s "/vagrant/vagrant/server/apache/conf-available/monitoring.conf" "/etc/apache2/conf-available/monitoring.conf"
fi
a2enconf -q monitoring.conf

if ! [ -L "/etc/php/7.1/cli/conf.d/monitoring.ini" ]; then
	rm -f "/etc/php/7.1/cli/conf.d/monitoring.ini"
	ln -s "/vagrant/vagrant/server/php/cli.ini" "/etc/php/7.1/cli/conf.d/monitoring.ini"
fi

if ! [ -L "/etc/php/7.1/apache2/conf.d/monitoring.ini" ]; then
	rm -f "/etc/php/7.1/apache2/conf.d/monitoring.ini"
	ln -s "/vagrant/vagrant/server/php/apache2.ini" "/etc/php/7.1/apache2/conf.d/monitoring.ini"
fi

if [ -f "/etc/php/7.1/mods-available/xdebug.ini" ]; then
	rm -f "/etc/php/7.1/mods-available/xdebug.ini"
	ln -s "/vagrant/vagrant/server/php/xdebug.ini" "/etc/php/7.1/mods-available/xdebug.ini"
fi

chmod -R 0777 "/vagrant/temp" "/vagrant/log"


mysql --user=root --password="monitoring" --execute="CREATE DATABASE IF NOT EXISTS monitoring";
mysql --user=root --password="monitoring" --execute="GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'monitoring';"
sed -ie "s/^bind-address/#bind-address/g" "/etc/mysql/my.cnf"
service mysql restart


rabbitmq-plugins enable rabbitmq_management
echo "[{rabbit, [{loopback_users, []}]}]." > /etc/rabbitmq/rabbitmq.config
