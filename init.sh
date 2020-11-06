#!/usr/bin/env bash

composer install

php artisan migrate

php artisan cache:clear
php artisan config:clear
php artisan clockwork:clean

sudo service supervisor restart

php artisan remove:draft-tasks
php artisan computing:timers
php artisan computing:timer_billings

chown -R www-data:www-data $PWD
chmod -R 775 $PWD/storage
