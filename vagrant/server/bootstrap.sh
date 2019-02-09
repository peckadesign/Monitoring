#!/usr/bin/env bash

echo "deb http://ftp.debian.org/debian stretch-backports main" >> /etc/apt/sources.list

yes | apt-get install \
	apt-transport-https

apt-get update

yes | apt-get install \
	lsb-release \
	ca-certificates \
	curl

wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/sury.list

curl -sL https://deb.nodesource.com/setup_8.x | bash -

apt-get update

yes | apt-get upgrade
yes | apt-get install \
	git \
	htop \
	vim \
	nodejs \
	apache2 \
	php7.3 \
	libapache2-mod-php7.3 \
	php7.3-xdebug \
	php7.3-curl \
	php7.3-xml \
	php7.3-zip \
	php7.3-mysql \
	php7.3-mbstring \
	php7.3-bcmath

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

if ! [ -L "/etc/php/7.3/cli/conf.d/monitoring.ini" ]; then
	rm -f "/etc/php/7.3/cli/conf.d/monitoring.ini"
	ln -s "/vagrant/vagrant/server/php/cli.ini" "/etc/php/7.3/cli/conf.d/monitoring.ini"
fi

if ! [ -L "/etc/php/7.3/apache2/conf.d/monitoring.ini" ]; then
	rm -f "/etc/php/7.3/apache2/conf.d/monitoring.ini"
	ln -s "/vagrant/vagrant/server/php/apache2.ini" "/etc/php/7.3/apache2/conf.d/monitoring.ini"
fi

if [ -f "/etc/php/7.3/mods-available/xdebug.ini" ]; then
	rm -f "/etc/php/7.3/mods-available/xdebug.ini"
	ln -s "/vagrant/vagrant/server/php/xdebug.ini" "/etc/php/7.3/mods-available/xdebug.ini"
fi

chmod -R 0777 "/vagrant/temp" "/vagrant/log"
