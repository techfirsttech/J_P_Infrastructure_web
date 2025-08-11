=> Setup Project
    `composer install`

==> setup environment file

copy .env.example to .env
cp .env.example .env

==> setup database in .env

    DB_CONNECTION="mysql"
    DB_DRIVER="mariadb"
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_name
    DB_USERNAME=user_name
    DB_PASSWORD=password
    DB_CHARSET="utf8mb4"
    DB_COLLATION="utf8mb4_unicode_ci"
    DB_STRICT=true

    DB_CONNECTION_LOCAL="mysql"
    DB_DRIVER_LOCAL="mariadb"
    DB_HOST_LOCAL=127.0.0.1
    DB_PORT_LOCAL=3306
    DB_DATABASE_LOCAL=db_name
    DB_USERNAME_LOCAL=user_name
    DB_PASSWORD_LOCAL="password"
    DB_CHARSET_LOCAL="utf8mb4"
    DB_COLLATION_LOCAL="utf8mb4_unicode_ci"
    DB_STRICT_LOCAL=true

==> clear composer cache
    `composer dump-autoload -o`

Command for creating migration file in module:
=====>   php artisan module:make-migration create_units_table Unit

Command for seed user permission :
====> php artisan db:seed --class=Modules\User\Database\Seeders\PermissionTableSeeder

Command for all seed of module:
====> php artisan module:seed Module_name_here 

command for run migration in module:

php artisan module:migrate Unit

command for disable or enable module:
php artisan module:enable unit 

command for Main module inside meta module create :
php artisan module:make-model PurchaseMeta --migration Purchase

to generate the module please use following command:

`php artisan module:make-with-model Customers`
