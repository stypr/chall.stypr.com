FROM debian
MAINTAINER stypr (root@stypr.com)

ENV TERM xterm
ADD ./www/ /srv/r34lsqls4ndb0x/
WORKDIR /srv


RUN apt-get update && apt-get install -y apt-transport-https lsb-release ca-certificates wget
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
RUN apt-get update && \
apt-get -y install nano php5.6-fpm php5.6-curl php5.6-mysql sudo wget nginx
RUN useradd -d /srv/ -s /bin/false sandbox && \
chown -R root:www-data /srv/ && \
chown -R root:www-data /srv/r34lsqls4ndb0x/ && \
chmod -R 551 /srv/ && \
chmod 555 /srv/r34lsqls4ndb0x/ && \
mkdir -p /srv/r34lsqls4ndb0x/tmp && \
chown -R root:sandbox /srv/r34lsqls4ndb0x/tmp && \
chmod 575 /srv/r34lsqls4ndb0x/tmp && \
chmod 444 /srv/r34lsqls4ndb0x/index.php && \
chmod g+s /srv/

RUN wget --no-check-certificate https://eagle-jump.org/deploy/nginx-sqlsandbox.conf && \
mv nginx-sqlsandbox.conf /etc/nginx/nginx.conf && \
echo "nginx set"
RUN wget --no-check-certificate https://eagle-jump.org/deploy/php-fpm-sqlsandbox.conf && \
sed -i 's/username/sandbox/g' php-fpm-sqlsandbox.conf && \
mv php-fpm-sqlsandbox.conf /etc/php/5.6/fpm/pool.d/www.conf && \
echo "php-fpm set"

ENV MYSQL_USER=sandbox \
    MYSQL_DATA_DIR=/var/lib/mysql \
    MYSQL_RUN_DIR=/run/mysqld \
    MYSQL_LOG_DIR=/var/log/mysql \
    DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    echo "mysql-server mysql-server/root_password password" | debconf-set-selections && \
    echo "mysql-server mysql-server/root_password_again password" | debconf-set-selections && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y mariadb-server nano

RUN wget --no-check-certificate https://eagle-jump.org/deploy/my-sqlsandbox.cnf && \
mv my-sqlsandbox.cnf /etc/mysql/my.cnf && \
echo "mysql set"


# php5.6-fpm && nginx
RUN echo '#!/bin/sh -e\n\
rm -rf /.dockerenv\n\
chown sandbox:sandbox /var/log/mysql\n\
service php5.6-fpm start\n\
cat /dev/null > /etc/rc.local\n\
rm -rf /var/lib/mysql\n\
mysql_install_db --user sandbox > /dev/null\n\
cat > /root/sql <<EOF\n\
USE mysql;\n\
FLUSH PRIVILEGES;\n\
CREATE DATABASE IF NOT EXISTS sqlsandbox ;\n\
GRANT ALL PRIVILEGES ON *.* TO "root"@"localhost" WITH GRANT OPTION;\n\
UPDATE user SET password=PASSWORD("$MYSQL_ROOT_PASSWORD") WHERE user="root";\n\
GRANT ALL PRIVILEGES ON sqlsandbox.* TO "sqlsandbox"@"localhost" IDENTIFIED BY "sqlsandbox";GRANT FILE ON *.* TO "sqlsandbox"@"localhost";\n\
FLUSH PRIVILEGES;\n\
USE sqlsandbox;\n\
CREATE TABLE sqlsandb0x (hint VARCHAR(50) NULL DEFAULT NULL) ENGINE=InnoDB;\n\
INSERT INTO sqlsandb0x VALUES ("you need to pwn this service!");\n\
EOF\n\
\n\
cat /root/sql\n\
echo "OK LETS START!"\n\
chown -R sandbox /var/lib/mysql/\n\
echo "CHANGE PERMISSION.."\n\
mkdir /var/run/mysqld/\n\
chmod 775 /var/run/mysqld/\n\
chown -R sandbox /var/run/mysqld/\n\
chown -R sandbox /var/log/mysq*\n\
chown root:sandbox /var/run/mysqld\n\
sed -i "s/user\t\t= mysql/user\t\t= sandbox/g" /etc/mysql/my.cnf\n\
service mariadb start\n\
sleep 5\n\
echo "AUTH.."\n\
mysql -uroot -f < /root/sql >&1 2>&1\n\
rm -rf /root/sql\n\
service cron start\n\
/usr/sbin/nginx -g "daemon off;"' > /etc/rc.local && \
chmod +x /etc/rc.local

RUN echo "ADD BACKUP SCRIPT" && \
apt-get -y install cron && \
echo '#!/bin/sh\n\
if [ ! -d "/root/mysql_backup" ]; then\n\
  yes | cp -vai /var/lib/mysql /root/mysql_backup\n\
fi\n\
yes | cp -Tiva /root/mysql_backup/ /var/lib/mysql/\n\' > /root/cron.sh && \
chmod +x /root/cron.sh && \
crontab -l | { cat; echo "* * * * * /root/cron.sh"; } | crontab - && \
echo "ALL GOOD!"

RUN rm -rf /.dockerenv

USER root
EXPOSE 80
ENTRYPOINT /etc/rc.local
