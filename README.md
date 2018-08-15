# chall.stypr.com

Developed from the scratch for my personal benefits.

Brand new version 2.  Now released with [The Unlicense](LICENSE.md).

### Pull Requests

Pull requests are welcome. Please give me a request for bugfix/new feature.

The only problem is that I won't be able to merge requests as my daily spare time is very limited. (I use the internet approximately 1 hour per day because of frequent military operations)

### Requirements

The modest requirements are as follows:

* A decent web server 
  * Get nginx, apache, whatever you want. I personally recommend *nginx* or *caddy*.

* **PHP 7** or later &mdash; The project follows the latest standard (as of late 2017).
  * php-mysql, php-fpm, php-curl extensions should be enabled.
  * composer must be installed.

* Latest MySQL distribution
  *  **MySQL 5.7+** or  **MariaDB 10.2+** will be the least requirement
  * This is because of the newly-implemented ranking feature. (which is very identical to features from MSSQL)
  * Please note that MySQL 5.7 on some distros won't work. I recommend using MariaDB for this project.

* Latest versions of modern web browsers (for clients)
  * Tested in IE11, Chrome 4.x and Chrome 61

### Detailed Installation Guide

#### Basic Setup

1. Copy/Clone and put all files on your webroot directory. (`git clone`)

2. Use composer from your webroot directory to install additional dependencies (`composer install`)

3. Now, let's generate secure random salt for the service.
    * `php -r "require('lib/function.php');var_dump(generate_random_string(64));"`

4. You are now ready to fill up the `install/config.php` file.

5. Install the database with `install/config.sql`.

6. `cp install/config.php lib/exclude/config.php`

7. Remove installation directory, Cleanup useless files

#### Set user as admin

1. Register your account from the website.

2. Get into your MySQL console.

3. Run `UPDATE user SET user_permission=9 WHERE user_nickname='YOURNICK';`


**Admin functions are TBD. Coming soon.**


### Vulnerability Reports

You're allowed to send [me](https://harold.kim/) a mail ([PGP](https://harold.kim/pubkey)) on a successful development of the exploit.

The successful scope of such cases are limited to following situations:

1. Any kind of attacks that would get one's credential without one's activity. 
  * i.e. SQLi, RCE, XXE attacks
  * Attacks that require the least activity are also allowed
     * i.e CSRF on public page

2. Any kind of attacks that would compromise the system or leak important data from the system
 * i.e. RCE, LFI/RFI attacks

You're also allowed to report the vulnerablity of the [challenge network](https://eagle-jump.org/) on following cases:

1. Any kind of attacks that would escape the sandbox and get the shell of the host's system.
2. Any kind of attacks that can leak contents/packets of other sandbox(es).

On a succesful patch, your exploit will be posted with your nickname at the Hall of Fame.

Please note that,

1. Attacking challenge network should be done with purely black-box tests, without DoS or any related attacks that would consume a lot of traffic and data on the system.

2. I won't reply on mails about possible flaws/attacks outside the boundary. Please provide me full exploit or any exploit that would break the server.

3. Send me the encrypted text as a file (if mail sent by PGP)

#### TBD

1. CTF Mode

2. ChallRating

3. ChallList sort by category, score

