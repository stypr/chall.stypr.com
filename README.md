## chall.stypr.com

### WARNING: This repo is on a development stage and features are incomplete. Do not make issues/bugs until the development stage is over.

Brand new version 2, is now on development stage. Now released with [The Unlicense](LICENSE.md).

I'm trying to optimize on demand, since the whole new version is developed from the scratch and I am developing this on my daily spare time which is approx ~1h per day.

### Requirements

The server's modest requirements are as follows:
* a decent web server &mdash; *nginx* would be the most appropriate.
* PHP7 or later &mdash; the project follows the latest standard; PHP5 won't work.
* Latest MySQL distro &mdash; the project utilizes features from the latest editions.

Scripts are tested on the x64 linux/x64 windows with the following softwares:
* nginx 1.0+
* php 7.0.22 (php_mysqli, php_fpm, php_curl, composer)
* mariadb 10.0.31_x64
* Latest edition of chrome was used for client-side tests.

### Installation

1. Please put all files on your webroot directory.

2. Run composer on the webroot directory by the shell. (i.e. `composer install`)

3. Run `install.php` from the web browser for further installation.

### Customization

* The default flag format on the database is `flag{blah}`. You can change te prefix and suffix by changing `get_by_flag` function in `model.php`.

* You may customize files to whatever you want, unless the service does not crash down so bad! :)

### For pwners who seek to report vuln..

You're allowed to send [me](https://harold.kim/) a mail ([PGP](https://harold.kim/pubkey)) on a successful development of the exploit. The scope of such cases are limited to following situations:

* Any kind of attacks that would get one's credential without one's activity. 
	* SQLi, RCE, XXE attacks would be one of such attacks.
	* attacks that require the least activity are also allowed (i.e. cross-site scripting on the page that everyone can view)
* Any kind of attacks that would compromise the system or leak important data from the system (RCE, LFI/RFI)

You're also allowed to report the vulnerablity of the [challenge network](https://eagle-jump.org/) on following cases:
* Any kind of vulnerability that would escape the sandbox and get the shell of the host's system.
* Any kind of vulnerability that would leak contents/packets of other machines.

On a succesful patch, your exploit will be posted with your nickname on the Hall of Fame.

Please note that
1. Attacking the challenge network should be done with black-box tests, without DoS or attacks that would consume a lot of traffic and data on the system.
2. I won't reply on mails about possible attacks outside the boundary.

### Pull Requests

You are allowed to send pull requests for new features/improvements.  however, merging commits would take a bit of time as I don't frequently utilize github. (actually I've got personal git server running for personal projects :wink:)

