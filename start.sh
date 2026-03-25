#!/bin/bash

echo "Starting Laravel with Filament..."

# Check if .env exists, if not copy from .env.example
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
    echo "Please edit .env file with your database credentials"
    exit 1
fi

# Start Docker containers
echo "Starting Docker containers..."
docker-compose up -d

# Wait for database to be ready
echo "Waiting for database to be ready..."
sleep 5

# Check if Laravel is installed
if [ ! -f "composer.json" ]; then
    echo "Laravel not installed. Running setup..."
    docker-compose exec app bash setup.sh
else
    echo "Laravel already installed."
    echo "Running migrations..."
    docker-compose exec app php artisan migrate
fi

echo ""
echo "====================================="
echo "Laravel + Filament is ready!"
echo "====================================="
echo "Application: http://localhost:99"
echo "Admin Panel: http://localhost:99/admin"
echo "====================================="
