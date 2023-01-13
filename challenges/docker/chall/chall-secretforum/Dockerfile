FROM debian
MAINTAINER stypr (root@stypr.com)

ENV TERM xterm
ADD ./www/ /srv/s3cr3tf0rum/
WORKDIR /srv

RUN apt-get update && apt-get install -y apt-transport-https lsb-release ca-certificates wget
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
RUN apt-get update && \
apt-get -y install nano php5.6-fpm php5.6-curl php5.6-mysql sudo wget nginx
RUN useradd -d /srv/ -s /bin/false sandbox && \
chown -R sandbox:www-data /srv/ && \
chmod -R 550 /srv/ && \
chmod g+s /srv/

RUN wget --no-check-certificate https://eagle-jump.org/deploy/nginx-secretforum.conf && \
mv nginx-secretforum.conf /etc/nginx/nginx.conf && \
echo "nginx set"
RUN wget --no-check-certificate https://eagle-jump.org/deploy/php-fpm.conf && \
sed -i 's/username/sandbox/g' php-fpm.conf && \
mv php-fpm.conf /etc/php/5.6/fpm/pool.d/www.conf && \
echo "php-fpm set"

ENV MYSQL_USER=mysql \
    MYSQL_DATA_DIR=/var/lib/mysql \
    MYSQL_RUN_DIR=/run/mysqld \
    MYSQL_LOG_DIR=/var/log/mysql \
    DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    echo "mysql-server mysql-server/root_password password" | debconf-set-selections && \
    echo "mysql-server mysql-server/root_password_again password" | debconf-set-selections && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server nano

# php5.6-fpm && nginx
RUN echo '#!/bin/sh -e\n\
rm -rf /.dockerenv\n\
service php5.6-fpm start\n\
cat /dev/null > /etc/rc.local\n\
mysql_install_db --user mysql > /dev/null\n\
cat > /tmp/sql <<EOF\n\
USE mysql;\n\
FLUSH PRIVILEGES;\n\
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;\n\
UPDATE user SET password=PASSWORD("$MYSQL_ROOT_PASSWORD") WHERE user='root';\n\
EOF\n\
\n\
rm -rf /etc/rc.local\n\
cat /tmp/sql\n\
mysqld --bootstrap --verbose=1 < /tmp/sql\n\
cat /tmp/sql\n\
rm -rf /tmp/sql\n\
mysqld\n\
/usr/sbin/nginx -g "daemon off;"' > /etc/rc.local && \
chmod +x /etc/rc.local

RUN rm -rf /.dockerenv

USER root
EXPOSE 80
ENTRYPOINT /etc/rc.local
