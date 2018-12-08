#!/usr/bin/env bash

wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | apt-key add -

apt-get install apt-transport-https
echo "deb https://artifacts.elastic.co/packages/6.x/apt stable main" > /etc/apt/sources.list.d/elastic-6.x.list

apt-get update
apt-get install openjdk-11-jre-headless
apt-get install elasticsearch
apt-get install kibana

