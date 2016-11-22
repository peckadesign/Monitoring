#!/usr/bin/env bash

touch ~/.ssh/known_hosts && ssh-keyscan -H "github.com" > ~/.ssh/known_hosts && chmod 600 ~/.ssh/known_hosts
grep -q "cd /vagrant" ~/.profile 2> "/dev/null" || echo "cd /vagrant" >> ~/.profile
cd "/vagrant"
#composer install --no-interaction
