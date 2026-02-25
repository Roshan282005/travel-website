#!/bin/bash
set -e

echo "Deploying TravelGo application to Azure App Service..."

# Copy application files to deployment directory
echo "Setting up application files..."
cp -r . /home/site/wwwroot/ || true

# Create necessary directories
mkdir -p /home/site/wwwroot/logs
mkdir -p /home/site/wwwroot/uploads
chmod -R 755 /home/site/wwwroot/logs
chmod -R 755 /home/site/wwwroot/uploads

# Update permissions
chmod -R 644 /home/site/wwwroot/*.php
chmod -R 644 /home/site/wwwroot/*.html

echo "Installing PHP dependencies..."
if [ -f composer.json ]; then
    composer install --no-dev --optimize-autoloader
fi

echo "Initializing database..."
if [ -f database.sql ]; then
    # Database will be initialized during first deployment
    echo "Database schema ready for migration"
fi

echo "Application deployment completed successfully!"
echo "Application URL: https://$(hostname)"
