#!/bin/bash

echo "Installing Laravel..."
composer create-project laravel/laravel temp
shopt -s dotglob
mv temp/* .
rm -rf temp

echo "Setting permissions..."
chown -R www-data:www-data /var/www
chmod -R 755 storage bootstrap/cache

echo "Installing Filament..."
composer require filament/filament

echo "Running Filament install..."
php artisan filament:install --panels

echo "Setting up environment..."
cp .env.example .env
php artisan key:generate

echo "Configuring database in .env..."
# Read database credentials from Docker .env file
DB_DATABASE=${DB_DATABASE:-laravel}
DB_USERNAME=${DB_USERNAME:-laravel}
DB_PASSWORD=${DB_PASSWORD:-password}
DB_PORT=${DB_PORT:-5432}

sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=pgsql/' .env
sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=db/' .env
sed -i "s/# DB_PORT=3306/DB_PORT=${DB_PORT}/" .env
sed -i "s/# DB_DATABASE=laravel/DB_DATABASE=${DB_DATABASE}/" .env
sed -i "s/# DB_USERNAME=root/DB_USERNAME=${DB_USERNAME}/" .env
sed -i "s/# DB_PASSWORD=/DB_PASSWORD=${DB_PASSWORD}/" .env

echo "Running migrations..."
php artisan migrate

echo "Creating Filament admin user..."
php artisan make:filament-user

echo "Setup complete! Your Laravel + Filament application is ready."
echo "Access it at: http://localhost:99"
echo "Admin panel at: http://localhost:99/admin"
