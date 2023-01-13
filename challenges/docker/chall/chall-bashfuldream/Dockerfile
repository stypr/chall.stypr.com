FROM debian:latest
MAINTAINER stypr (root@stypr.com)

ENV TERM xterm
ADD ./www/ /srv/
ADD ./flag/ /dream/
WORKDIR /srv

RUN apt-get update && apt-get install -y apt-transport-https lsb-release ca-certificates wget
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
RUN apt-get update && \
apt-get -y install nano php5.6-fpm php5.6-curl php5.6-mysql sudo wget nginx
RUN useradd -d /srv/ -s /bin/false sandbox && \
useradd -d /dream/ -s /bin/false dream && \
chown -R dream:sandbox /dream/ && \
chmod -R 4550 /dream/dream && chmod -R 400 /dream/flag && \
chown -R sandbox:www-data /srv/ && \
chmod -R 550 /srv/ && \
chmod g+s /srv/ && \
chmod g+s /dream/

RUN wget --no-check-certificate https://eagle-jump.org/deploy/nginx.conf && \
mv nginx.conf /etc/nginx/nginx.conf && \
echo "nginx set"
RUN wget --no-check-certificate https://eagle-jump.org/deploy/php-fpm.conf && \
sed -i 's/username/sandbox/g' php-fpm.conf && \
mv php-fpm.conf /etc/php/5.6/fpm/pool.d/www.conf && \
echo "php-fpm set"

RUN mv /dream/flag /flag && \
mv /dream/dream /bashful && \
chown 1001:1000 /bashful && chmod 4550 /bashful && \
chown 1001:1001 /flag && chmod 440 /flag && \
chmod 000 /dream/ 2>/dev/null && \
rm -rf /dream 2>/dev/null

# php5.6-fpm && nginx
RUN echo '#!/bin/sh -e\n\
rm -rf /.dockerenv\n\
service php5.6-fpm start\n\
cat /dev/null > /etc/rc.local\n\
/usr/sbin/nginx -g "daemon off;"' > /etc/rc.local && \
chmod +x /etc/rc.local

RUN rm -rf /.dockerenv

USER root
EXPOSE 80
ENTRYPOINT /etc/rc.local
