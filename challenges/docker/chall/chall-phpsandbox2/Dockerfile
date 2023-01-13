FROM debian
MAINTAINER stypr (root@stypr.com)

ENV TERM xterm
ADD ./www/ /srv/
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

RUN wget --no-check-certificate https://eagle-jump.org/deploy/nginx.conf && \
mv nginx.conf /etc/nginx/nginx.conf && \
echo "nginx set"
RUN wget --no-check-certificate https://eagle-jump.org/deploy/php-fpm.conf && \
sed -i 's/username/sandbox/g' php-fpm.conf && \
mv php-fpm.conf /etc/php/5.6/fpm/pool.d/www.conf && \
echo "php-fpm set"
# monkey patch deployment for php.ini -- disable all
RUN cat /etc/php/5.6/fpm/php.ini | sed -e "s/$(cat /etc/php/5.6/fpm/php.ini | grep disable_functions)/disable_functions=phpinfo,glob,root,ftok,safe_dir,sh_name,highlight_file,show_source,_getppid,apache_child_terminate,apache_setenv,define_syslog_variables,diskfreespace,dl,error_reporting,escapeshellarg,escapeshellcmd,exec,fpassthru,fputs,ftp_connect,ftp_exec,ftp_get,ftp_login,ftp_nb_fput,ftp_puts,ftp_raw,ftp_rawlist,get_current_user,getmygid,getmypid,getmyuid,ignore_user_abord,imagegrabscreen,ini_alter,ini_get_all,ini_restore,ini_set,inject_code,leak,link,listen,mysql_pconnect,openlog,passthru,pcntl_exec,pcntl_fork,pcntl_get_last_error,pcntl_getpriority,pcntl_setpriority,pcntl_signal,pcntl_signal_dispatch,pcntl_sigprocmask,pcntl_sigtimedwait,pcntl_sigwaitinfo,pcntl_strerror,pcntl_wait,pcntl_waitpid,pcntl_wexitstatus,pcntl_wifexited,pcntl_wifsignaled,pcntl_wifstopped,pcntl_wstopsig,pcntl_wtermsig,phpAds_XmlRpc,phpAds_remoteInfo,phpAds_xmlrpcDecode,phpAds_xmlrpcEncode,php_uname,popen,posix,posix_ctermid,posix_getcwd,posix_getegid,posix_geteuid,posix_getgid,posix_getgrgid,posix_getgrnam,posix_getgroups,posix_getlogin,posix_getpgid,posix_getpgrp,posix_getpid,posix_getpwnam,posix_getpwuid,posix_getrlimit,posix_getsid,posix_getuid,posix_isatty,posix_kill,posix_mkfifo,posix_setegid,posix_seteuid,posix_setgid,posix_setpgid,posix_setsid,posix_setuid,posix_times,posix_ttyname,posix_uname,proc_close,proc_get_status,proc_nice,proc_open,proc_terminate,shell_exec,source,syslog,system,tmpfile,virtual,xmlrpc_entity_decode,pcntl_alarmopenlog,syslog,readlink,symlink,link,php_uname,wscriptpcntl_alarm,pcntl_fork,pcntl_waitpid,pcntl_wait,pcntl_wifexited,pcntl_wifstopped,pcntl_wifsignaled,pcntl_wexitstatus,pcntl_wtermsig,pcntl_wstopsig,pcntl_signal,pcntl_signal_dispatch,pcntl_get_last_error,pcntl_strerror,pcntl_sigprocmask,pcntl_sigwaitinfo,pcntl_sigtimedwait,pcntl_exec,pcntl_getpriority,pcntl_setpriority,/" /etc/php/5.6/fpm/php.ini > /etc/php/5.6/fpm/php2.ini && \
mv /etc/php/5.6/fpm/php2.ini /etc/php/5.6/fpm/php.ini && \
echo "php.ini set"

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
