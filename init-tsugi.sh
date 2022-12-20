#!/bin/bash

shopt -s nocaseglob

chmod 600 /etc/msmtprc
chown www-data.www-data /etc/msmtprc

touch /var/pipe/hostpipe
chown www-data.www-data /var/pipe/hostpipe
chmod 666 /var/pipe/hostpipe

echo "Waiting for DB"
while ! nc -z tsugi-mysql-db 3306; do   
  sleep 5 # wait 5 seconds before check again
  echo "Database unavailable, rechecking in 5 seconds"
done

# Get the database setup
cd /var/www/html/tsugi/admin && php upgrade.php

# This is the entry line
apache2-foreground
