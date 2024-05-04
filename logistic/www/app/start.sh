# chown -R www-data:www-data ./
# chmod -R 755 ./storage

composer install
composer dump-autoload
composer run-script post-root-package-install
chmod -R 777 ./bootstrap ./storage
php artisan key:generate
php artisan optimize
php artisan migrate
php artisan create:view-orders
php artisan db:seed
pm2 start run-scheduler.sh