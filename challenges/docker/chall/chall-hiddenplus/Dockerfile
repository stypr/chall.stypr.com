FROM debian:bullseye
MAINTAINER stypr (root@stypr.com)

ENV TERM xterm
ADD ./www/ /srv/
WORKDIR /srv

RUN apt-get update && \
apt-get -y install tor python sudo curl
RUN useradd -d /srv/ -s /bin/false sandbox && \
chown -R sandbox:www-data /srv/ && \
chmod -R 550 /srv/ && \
chmod g+s /srv/

ADD ./key/ /etc/tor/hidden_service/
RUN chown -R debian-tor:debian-tor /etc/tor/hidden_service && \
chmod 700 /etc/tor/hidden_service && \
chmod 700 /etc/tor/hidden_service/*

# rc.local
RUN echo 'HiddenServiceDir /etc/tor/hidden_service\n\
HiddenServicePort 80 127.0.0.1:1337' > /etc/tor/torrc

RUN echo '#!/bin/sh -e\n\
chown debian-tor:debian-tor /etc/tor/hidden_service\n\
chmod 700 /etc/tor/hidden_service\n\
service tor start\n\
sudo -u sandbox python server.py 2>&1' > /etc/rc.local && \
chmod +x /etc/rc.local

RUN rm -rf /.dockerenv

USER root
EXPOSE 80
ENTRYPOINT /etc/rc.local
