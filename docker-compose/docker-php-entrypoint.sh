#!/bin/sh

set -e

sudo service supervisor start
sudo cron

tail .gitignore
