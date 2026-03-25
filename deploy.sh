#!/bin/bash

set -e

echo "Starting production deployment..."

# Load environment variables
if [ -f .env.production ]; then
    export $(cat .env.production | grep -v '^#' | xargs)
fi

# Build the production image
echo "Building production Docker image..."
docker-compose -f docker-compose.prod.yml build --no-cache

# Stop existing containers
echo "Stopping existing containers..."
docker-compose -f docker-compose.prod.yml down

# Start new containers
echo "Starting containers..."
docker-compose -f docker-compose.prod.yml up -d

# Wait for database to be ready
echo "Waiting for database..."
sleep 10

# Run migrations
echo "Running database migrations..."
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

# Clear and cache config
echo "Optimizing application..."
docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

# Set proper permissions
echo "Setting permissions..."
docker-compose -f docker-compose.prod.yml exec -T app chown -R www-data:www-data /var/www/storage
docker-compose -f docker-compose.prod.yml exec -T app chmod -R 755 /var/www/storage

echo "Deployment completed successfully!"
echo "Application is running on port ${APP_PORT:-80}"
