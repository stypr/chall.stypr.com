## Challenges

* `docker/`: Challenge Infrastructure
    * `chall/`: challenge containers
    * `mysql`: MySQL containers
    * `optimize.py`: Cronjob for removing useless contents
    * `start.sh`: Start script on bootup
* `nginx/`: Files from :`/etc/nginx`
* `www/`: Files from `/var/www`
    * `www/html/deploy`: Configuration scripts for deploying challenge instances

## Ratelimits

```sh
#!/bin/sh

./wondershaper -a enp1s0 -d 12000 -u 12000
```

