#!/usr/bin/env bash

apt-key adv --keyserver "hkps.pool.sks-keyservers.net" --recv-keys "0x6B73A36E6026DFCA"
echo "deb https://packages.erlang-solutions.com/debian stretch contrib" > /etc/apt/sources.list.d/erlang.list
echo "deb https://dl.bintray.com/rabbitmq/debian stretch main" > /etc/apt/sources.list.d/bintray.rabbitmq.list

apt-get update
yes | apt-get install rabbitmq-server

rabbitmq-plugins enable rabbitmq_management
echo "[{rabbit, [{loopback_users, []}]}]." > /etc/rabbitmq/rabbitmq.config
