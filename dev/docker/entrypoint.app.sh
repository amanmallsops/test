#!/bin/bash

set -e

env

if [[ -n "$1" ]]; then
    exec "$@"
else
    composer install
    docker-php-ext-install pdo pdo_mysql
    # Use DB_HOST if set, otherwise use "db"
    db_host=${DB_HOST:-"db"}
    wait-for-it $db_host:3306 -t 100
    yes | php artisan migrate
    chown -R www-data:www-data storage
    exec apache2-foreground
fi