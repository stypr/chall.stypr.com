#!/bin/sh

docker run \
--detach \
--name=mysql-chall \
--publish 3000:3306 \
--volume=/root/docker/mysql/chall/:/var/lib/mysql \
mysql
