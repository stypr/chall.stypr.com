#!/usr/bin/python -u
#-*- coding: utf-8 -*-

import os
import sys

def process_list():
	""" Return [(id, name), ...] """
	ps = os.popen("docker ps").read().split("\n")[1:-1]
	ps = [(i.split(" ")[0], i.split(" ")[-1]) for i in ps]
	return ps


if __name__ == "__main__":
	os.popen("cat /dev/null > /etc/nginx/log/sandbox.log").read()
	for i in process_list():
		if i[1].startswith("chall"):
			# kill all users starting with sandbox
			if i[1].endswith("sqlsandbox"):
				res = os.popen("docker exec %s /bin/sh -c 'rm -rf /srv/r34lsqls4ndb0x/tmp/*'" % (i[1])).read()
				res = os.popen("docker exec %s /bin/sh -c 'rm -rf /var/lib/mysql/*; yes | cp -variT /root/mysql_backup /var/lib/mysql;service mariadb restart'" % (i[1])).read()
				print(res)
			if i[1].endswith("eaglejump"):
				print("eaglejump")
				res = os.popen("docker exec %s /bin/sh -c 'rm -rf /tmp/* /var/tmp/*'" % (i[1])).read()
			if i[1].endswith("hiddenplus"):
				# restart tor
				print("hiddenplus")
				res = os.popen("docker exec %s /bin/sh -c 'service tor restart'" % (i[1])).read()
			res = os.popen("docker exec %s /bin/sh -c 'killall -9 -u sandbox 2>/dev/null'" % (i[1])).read()
			# remove temporary files
			res = os.popen("docker exec %s /bin/sh -c 'rm -rf /tmp/* /var/tmp/*'" % (i[1])).read()
		if i[1].startswith("mysql"):
			pass
