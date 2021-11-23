#!/bin/bash

shopt -s nocaseglob

echo "Waiting for DB"
while ! nc -z tsugi-mysql-db 3306; do   
  sleep 5 # wait 5 seconds before check again
  echo "Database unavailable, rechecking in 5 seconds"
done

# Get the database setup
cd /var/www/html/tsugi/admin && php upgrade.php

# This is the entry line
apache2-foreground
