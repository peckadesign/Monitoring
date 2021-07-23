#!/usr/bin/bash

docker compose -f docker-compose.full.yml up database rabbitmq elasticsearch -d
docker compose -f docker-compose.full.yml exec database bash -c "until mysqladmin ping -hdatabase -umonitoring -pmonitoring; do sleep 2; done"
docker compose -f docker-compose.full.yml exec elasticsearch bash -c "until curl -s http://elasticsearch:9200/; do sleep 2; done"
docker compose -f docker-compose.full.yml exec rabbitmq bash -c "until rabbitmqctl ping; do sleep 2; done"
docker compose -f docker-compose.full.yml run web bin/console migrations:continue
docker compose -f docker-compose.full.yml run web bin/console rabbitmq:setup-fabric
docker compose -f docker-compose.full.yml up -d
