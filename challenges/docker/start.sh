#!/bin/sh

# Security stuff..
export DOCKER_CONTENT_TRUST=0

# Delete instances and networks
docker rm -f mysql-chall
docker rm -f chall-windowspro
docker rm -f chall-smartie
docker rm -f chall-googlemaster
docker rm -f chall-phpreverse2
docker rm -f chall-phpreverse
docker rm -f chall-yetanothersql
docker rm -f chall-guesser
docker rm -f chall-phpsandbox
docker rm -f chall-tofukimchi
docker rm -f chall-patchinject
docker rm -f chall-phptrick
docker rm -f chall-slashslash
docker rm -f chall-pysandbox
docker rm -f chall-hiddenplus
docker rm -f chall-bashfuldream
docker rm -f chall-phpsandbox2
docker rm -f chall-interview
docker rm -f chall-lameass
docker rm -f chall-sqlsandbox
docker rm -f chall-eaglejump
docker rm -f chall-rmt
docker rm -f chall-tofukimchi2
docker network rm chall


# Create network (10.1.0.x, 10.2.0.x)
docker network create --subnet=10.1.0.0/16 --ip-range=10.1.0.0/24 chall

# Pull latest debian image (which updates to the latest services)
# docker pull debian/latest

# docker volume create --name volume-mysql-*
# Run MySQL instances (10.1.0.137, 10.2.0.137)
docker run \
	--ip=10.1.0.137 \
	--env="MYSQL_ROOT_PASSWORD=jtiXABJlHILbCppu24jXFFlTNuDypnORsuBRMMOqs4fcf0pUL0" \
	 -itd --net=chall --detach --name=mysql-chall \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 512M -c 256 \
	--volume=volume-mysql-chall:/var/lib/mysql mysql:5

# Run LOS instances (10.2.0.138)
# docker build -t "chall-los" /srv/docker/chall/chall-los/
# docker run \
#	--ip=10.2.0.138 \
#	-h eaglejump-los \
#	 -itd --net=los --detach --name=chall-los \
#	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
#	--volume=/srv/docker/chall/chall-los/www:/srv:ro chall-los

# tofukimchi2 10.1.0.223
docker build -t "chall-tofukimchi2" /srv/docker/chall/chall-tofukimchi2/
docker run \
        --ip=10.1.0.223 \
        -h eaglejump-tofukimchi2 \
        -itd --net=chall --detach --name=chall-tofukimchi2 \
        --security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
        --volume=/srv/docker/chall/chall-tofukimchi2/www:/srv:ro chall-tofukimchi2

# rmt 10.1.0.222
docker build -t "chall-rmt" /srv/docker/chall/chall-rmt/
docker run \
        --ip=10.1.0.222 \
        -h eaglejump-rmt \
        -itd --net=chall --detach --name=chall-rmt \
        --security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
        --volume=/srv/docker/chall/chall-rmt/www:/srv:ro chall-rmt

# secretforum 10.1.0.221
#docker build -t "chall-secretforum" /srv/docker/chall/chall-secretforum/
#docker run \
#	--ip=10.1.0.221 \
#	--env="MYSQL_ROOT_PASSWORD=1D8A6GQ8VOGnTtUxN45p" \
#	-h eaglejump-secretforum \
#	 -itd --net=chall --detach --name=chall-secretforum \
#	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 256M -c 64 \
#	--volume=/srv/docker/chall/chall-secretforum/www:/srv/s3cr3tf0rum:ro chall-secretforum

# eaglejump 10.1.0.219
docker build -t "chall-eaglejump" /srv/docker/chall/chall-eaglejump/
docker run \
        --ip=10.1.0.219 \
        -h eaglejump-eaglejump \
        -itd --net=chall --detach --name=chall-eaglejump \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-eaglejump/www:/srv:ro chall-eaglejump

# phpsandbox2 10.1.0.218
docker build -t "chall-phpsandbox2" /srv/docker/chall/chall-phpsandbox2/
docker run \
        --ip=10.1.0.218 \
        -h eaglejump-phpsandbox2 \
        -itd --net=chall --detach --name=chall-phpsandbox2 \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-phpsandbox2/www:/srv:ro chall-phpsandbox2

# googlemaster 10.1.0.217
docker build -t "chall-googlemaster" /srv/docker/chall/chall-googlemaster/
docker run \
	--ip=10.1.0.217 \
	-h eaglejump-googlemaster \
	 -itd --net=chall --detach --name=chall-googlemaster \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-googlemaster/www:/srv:ro chall-googlemaster

# smartie 10.1.0.216
docker build -t "chall-smartie" /srv/docker/chall/chall-smartie/
docker run \
	--ip=10.1.0.216 \
	-h eaglejump-smartie \
	 -itd --net=chall --detach --name=chall-smartie \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-smartie/www:/srv:ro chall-smartie

# windowspro 10.1.0.215
docker build -t "chall-windowspro" /srv/docker/chall/chall-windowspro/
docker run \
	--ip=10.1.0.215 \
	-h eaglejump-windowspro \
	 -itd --net=chall --detach --name=chall-windowspro \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-windowspro/www:/srv:ro chall-windowspro

# sqlsandbox 10.1.0.214
docker build -t "chall-sqlsandbox" /srv/docker/chall/chall-sqlsandbox/
docker run \
       --ip=10.1.0.214 \
       --env="MYSQL_ROOT_PASSWORD=XnpIxdrmW4Wu7yo5G3z1" \
       -h eaglejump-sqlsandbox \
        -itd --net=chall --detach --name=chall-sqlsandbox \
       --security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 256M -c 64 \
       chall-sqlsandbox
#--volume=/srv/docker/chall/chall-sqlsandbox/www:/srv/s3cr3tf0rum:ro


# bashfuldream 10.1.0.213
docker build -t "chall-bashfuldream" /srv/docker/chall/chall-bashfuldream/
docker run \
	--ip=10.1.0.213 \
	-h eaglejump-bashfuldream \
	 -itd --net=chall --detach --name=chall-bashfuldream \
	--security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-bashfuldream/www:/srv:ro chall-bashfuldream

# interview 10.1.0.212
docker build -t "chall-interview" /srv/docker/chall/chall-interview/
docker run \
	--ip=10.1.0.212 \
	-h eaglejump-interview \
	 -itd --net=chall --detach --name=chall-interview \
	--security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-interview/www:/srv:ro chall-interview


# tofukimchi 10.1.0.211
docker build -t "chall-tofukimchi" /srv/docker/chall/chall-tofukimchi/
docker run \
	--ip=10.1.0.211 \
	-h eaglejump-tofukimchi \
	 -itd --net=chall --detach --name=chall-tofukimchi \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-tofukimchi/www:/srv:ro chall-tofukimchi

# phpreverse2 10.1.0.210
docker build -t "chall-phpreverse2" /srv/docker/chall/chall-phpreverse2/
docker run \
	--ip=10.1.0.210 \
	-h eaglejump-phpreverse2 \
	 -itd --net=chall --detach --name=chall-phpreverse2 \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-phpreverse2/www:/srv:ro chall-phpreverse2

# slashslash 10.1.0.209
docker build -t "chall-slashslash" /srv/docker/chall/chall-slashslash/
docker run \
	--ip=10.1.0.209 \
	-h eaglejump-slashslash \
	 -itd --net=chall --detach --name=chall-slashslash \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-slashslash/www:/srv:ro chall-slashslash

# patchinject 10.1.0.208
docker build -t "chall-patchinject" /srv/docker/chall/chall-patchinject/
docker run \
	--ip=10.1.0.208 \
	-h eaglejump-patchinject \
	 -itd --net=chall --detach --name=chall-patchinject \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-patchinject/www:/srv:ro chall-patchinject


# yetanothersql 10.1.0.207
docker build -t "chall-yetanothersql" /srv/docker/chall/chall-yetanothersql/
docker run \
	--ip=10.1.0.207 \
	-h eaglejump-yetanothersql \
	 -itd --net=chall --detach --name=chall-yetanothersql \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-yetanothersql/www:/srv:ro chall-yetanothersql


# phpreverse 10.1.0.206
docker build -t "chall-phpreverse" /srv/docker/chall/chall-phpreverse/
docker run \
	--ip=10.1.0.206 \
	-h eaglejump-phpreverse \
	 -itd --net=chall --detach --name=chall-phpreverse \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-phpreverse/www:/srv:ro chall-phpreverse

# phptrick 10.1.0.205
docker build -t "chall-phptrick" /srv/docker/chall/chall-phptrick/
docker run \
	--ip=10.1.0.205 \
	-h eaglejump-phptrick \
	 -itd --net=chall --detach --name=chall-phptrick \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-phptrick/www:/srv:ro chall-phptrick


# lameass 10.1.0.204
docker build -t "chall-lameass" /srv/docker/chall/chall-lameass/
docker run \
	--ip=10.1.0.204 \
	-h eaglejump-lameass \
	 -itd --net=chall --detach --name=chall-lameass \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-lameass/www:/srv:ro chall-lameass


# guesser 10.1.0.203
docker build -t "chall-guesser" /srv/docker/chall/chall-guesser/
docker run \
	--ip=10.1.0.203 \
	-h eaglejump-guesser \
	 -itd --net=chall --detach --name=chall-guesser \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-guesser/www:/srv:ro chall-guesser

# pysandbox 10.1.0.202
docker build -t "chall-pysandbox" /srv/docker/chall/chall-pysandbox/
docker run \
	--ip=10.1.0.202 \
	-h eaglejump-pysandbox \
	 -itd --net=chall --detach --name=chall-pysandbox \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-pysandbox/www:/srv:ro chall-pysandbox

# phpsandbox 10.1.0.201
docker build -t "chall-phpsandbox" /srv/docker/chall/chall-phpsandbox/
docker run \
	--ip=10.1.0.201 \
	-h eaglejump-phpsandbox \
	 -itd --net=chall --detach --name=chall-phpsandbox \
	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart on-failure:5 -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-phpsandbox/www:/srv:ro chall-phpsandbox

# hiddenplus 10.1.0.200
docker build -t "chall-hiddenplus" /srv/docker/chall/chall-hiddenplus/
docker run \
	--ip=10.1.0.200 \
	-h eaglejump-hiddenplus \
	 -itd --net=chall --detach --name=chall-hiddenplus \
	--restart always -m 128M -c 64 \
	--volume=/srv/docker/chall/chall-hiddenplus/www:/srv:ro \
	--volume=/srv/docker/chall/chall-hiddenplus/key:/etc/tor/hidden_service chall-hiddenplus
#	--security-opt="no-new-privileges" --security-opt="apparmor=docker-default" --restart always -m 128M -c 64 \
