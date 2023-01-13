FROM debian
MAINTAINER stypr (root@stypr.com)

ENV TERM xterm
ADD ./www/ /srv/
WORKDIR /srv

RUN apt-get update && apt-get install -y apt-transport-https lsb-release ca-certificates wget curl
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
RUN apt-get update && \
apt-get -y install nano php5.6-fpm php5.6 php5.6-curl php5.6-mysql php5.6-zip sudo wget nginx
RUN useradd -d /srv/ -s /bin/false sandbox && \
chown -R sandbox:www-data /srv/ && \
chmod -R 550 /srv/ && \
chmod g+s /srv/

RUN wget --no-check-certificate https://eagle-jump.org/deploy/nginx-eaglejump.conf && \
mv nginx-eaglejump.conf /etc/nginx/nginx.conf && \
echo "nginx set"
RUN wget --no-check-certificate https://eagle-jump.org/deploy/php-fpm.conf && \
sed -i 's/username/sandbox/g' php-fpm.conf && \
mv php-fpm.conf /etc/php/5.6/fpm/pool.d/www.conf && \
echo "php-fpm set"

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
