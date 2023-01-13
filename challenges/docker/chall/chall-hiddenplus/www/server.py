#!/usr/bin/python -u
#-*- coding: utf-8 -*-

import SocketServer
import os
import sys
import re
import random
import re
import time
import hashlib

class Handler(SocketServer.BaseRequestHandler):
	safe_output = re.compile('[\W_]+', re.UNICODE)

	def is_valid_url(self, url):
		# https://code.djangoproject.com/browser/django/trunk/django/core/validators.py#L47
		regex = re.compile(
			r'^https?://'  # http:// or https://
			r'(?:(?:[A-Z0-9](?:[A-Z0-9-]{0,61}[A-Z0-9])?\.)+[A-Z]{2,6}\.?|stypr.com)'  # domain...
			r'(?::\d+)?'  # optional port
			r'(?:/?|[/?]\S+)$', re.IGNORECASE)
		try:
			return url is not None and regex.search(url)
		except:
			return False

	def generate_random_filename(self):
		return hashlib.md5(str(time.time()) + str(random.randint(100000, 999999))).hexdigest()

	def spacer(self):
		self.send('=' * 40 + '\n')

	def recv(self):
		try:
			return self.request.recv(1024).strip()
		except:
			return False

	def send(self, d):
		try:
			self.request.sendall(d)
			return True
		except:
			return False

	def prettify(self, d):
		""" str -> list of str """
		SPACING = 40
		s = []
		for i in range(0, len(d), SPACING):
			s.append(d[i:i+SPACING] + "\n")
		return s

	def choice(self, type=0):
		try:
			if not type:
				while True:
					self.send('> ')
					_selected = self.recv()
					if _selected == False:
						self.send('Timeout/Socket Error!\n')
						return False
					try:
						_selected = str(int(_selected))
					except:
						self.send('Please enter one of following numbers.\n')
						continue
					break
			else:
				while True:
					self.send('> ')
					_selected = self.recv()
					if _selected == False:
						self.send('Timeout/Socket Error!\n')
						return False
					try:
						_selected = str(_selected)
					except:
						self.send('Please enter one of following numbers.\n')
						continue
					break
			return _selected
		except:
			return False

	def add_note(self):
		self.spacer()
		self.send('Maximum char: 1024 bytes\n')
		self.send('1. Upload text\n')
		self.send('2. Upload url\n')
		self.spacer()
		k = self.choice()
		if not k:
			return
		if k:
			if k == '1':
				self.spacer()
				self.send('Enter your text followed by \\x0a.\n')
				d = ''
				while True:
					t = self.recv()
					if not t or t == "\x0a" or t == "\x0d\x0a":
						break
					d += t
				d = self.safe_output.sub('', d.strip().lower())[:1024]
				rand = self.generate_random_filename()
				note = os.path.join(UPLOAD, rand)
				k = open(note, 'wb')
				k.write(d)
				k.close()
				self.send("Upload Complete.\n")
				self.send("ID: %s\n" % (rand))

			elif k == '2':
				self.spacer()
				self.send('Enter your URL.\n')
				k = self.choice(1)
				if self.is_valid_url(k):
					r = os.popen('curl --header "X-Leak: flag{a6cf2edb458587d4c3598e96195a16c1}" -m 10 --connect-timeout 10 -s "%s" 2>&1' % (k.replace('"', '').replace('(', '').replace(')', '').replace('$', '').replace("\x27", ''))).read()
					d = self.safe_output.sub('', r)[:1024]
					rand = self.generate_random_filename()
					note = os.path.join(UPLOAD, rand)
					k = open(note, 'wb')
					k.write(d)
					k.close()
					self.send("Upload Complete.\n")
					self.send("ID: %s\n" % (rand))
				else:
					try:
						self.send("Invalid URL.\n")
					except:
						return
			else: # wtf?
				try:
					self.add_note()
				except:
					return

	def view_note(self):
		self.spacer()
		self.send('Please enter your ID.\n')
		self.spacer()
		k = self.choice(1)
		if k:
			_valid = re.findall(r"([a-fA-F\d]{32})", k)
			if len(_valid) == 1 and _valid[0] == k:
				fn = os.path.join(UPLOAD, _valid[0])
				if os.path.exists(fn):
					raw = self.prettify(file(fn).read())
					self.spacer()
					for i in raw:
						self.send(i)
					self.spacer()
					return
			self.send("ID Not Found.\n")

	def remove_note(self):
		self.spacer()
		self.send('Please enter your ID.\n')
		self.spacer()
		k = self.choice(1)
		if k:
			_valid = re.findall(r"([a-fA-F\d]{32})", k)
			if len(_valid) == 1 and _valid[0] == k:
				fn = os.path.join(UPLOAD, _valid[0])
				if os.path.exists(fn):
					os.remove(fn)
					self.send("ID delete complete.")
					return
			self.send("ID Not Found\n")
		pass

	def init(self):
		self.request.settimeout(60)
		self.spacer()
		self.send('Stereotyped Deep Note Service.\n')
		self.send('You can now send secret notes to stypr!\n')
		self.send('(Received notes are deleted every hour)\n')
		self.spacer()
		self.send('1. Add\n')
		self.send('2. View\n')
		self.send('3. Remove\n')
		self.send('4. Exit\n')
		self.spacer()
		m = {'1': self.add_note, '2': self.view_note,
			'3': self.remove_note, '4': self.exit, '5': self.hint }
		k = self.choice()

		if not k:
			return
		if k:
			m.get(k)()

	def hint(self):
		self.send("just think about it..\nyou're supposed to pwn the hidden network!\n")
		return

	def exit(self):
		self.send("Bye!\n")
		return

	def handle(self):
		self.init()

if __name__ == "__main__":
	HOST, PORT = "localhost", 1337
	UPLOAD = "/tmp/upload"

	# upload directory creation
	if not os.path.isdir(UPLOAD):
		os.mkdir(UPLOAD)
	else:
		# flush directory
		os.popen("cd " + UPLOAD + "; rm -rf *").read()

	# socket file creation
	#SOCK = "/tmp/upload.sock"
	#if os.path.exists(SOCK):
	#	os.remove(SOCK)

	# initiate service
	server = SocketServer.ThreadingTCPServer((HOST, PORT), Handler)
	server.serve_forever()


