#!/usr/bin/env bash

wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | apt-key add -

yes | apt-get install apt-transport-https
echo "deb https://artifacts.elastic.co/packages/6.x/apt stable main" > /etc/apt/sources.list.d/elastic-6.x.list

apt-get update
yes | apt-get install openjdk-11-jre-headless
yes | apt-get install elasticsearch
yes | apt-get install kibana

