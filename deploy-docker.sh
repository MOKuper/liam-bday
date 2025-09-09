#!/bin/bash

# Docker deployment script for existing droplet
# This is completely isolated and won't affect other projects

set -e

echo "🐳 Setting up Liam's Birthday Party Website with Docker..."

# Variables (update these)
DOMAIN="mijnverjaardag.eu"
PROJECT_DIR="/opt/liam-bday"

echo "📋 This script will:"
echo "   - Clone the repository to $PROJECT_DIR"
echo "   - Build and run Docker containers (Laravel app on port 8090)"
echo "   - Set up global nginx configuration"
echo "   - Configure SSL certificates"
echo ""
read -p "Continue? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Cancelled."
    exit 1
fi

# Create project directory
echo "📁 Setting up project directory..."
mkdir -p $PROJECT_DIR
cd $PROJECT_DIR

# Clone repository using SSH
echo "📥 Cloning repository..."
if [ ! -d ".git" ]; then
    git clone git@github.com:MOKuper/liam-bday.git .
else
    git pull origin main
fi

# Update domain in configs
echo "⚙️ Configuring domain..."
sed -i "s/your-domain.com/$DOMAIN/g" docker/proxy.conf
sed -i "s/your-domain.com/$DOMAIN/g" .env.docker

# Create required directories
echo "📂 Creating storage directories..."
mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
mkdir -p bootstrap/cache
mkdir -p database

# Set permissions
chmod -R 755 storage bootstrap/cache database

# Build and start containers
echo "🐳 Building Docker containers..."
docker-compose -f docker-compose.production.yml down || true
docker-compose -f docker-compose.production.yml build --no-cache

echo "🚀 Starting containers..."
docker-compose -f docker-compose.production.yml up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 15

# Test MySQL connection
echo "🗄️ Testing database connection..."
docker exec liam-bday-app php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully!';"

# Run Laravel setup inside container
echo "⚙️ Setting up Laravel application..."
docker exec liam-bday-app php artisan migrate --force
docker exec liam-bday-app php artisan storage:link
docker exec liam-bday-app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Set up global nginx configuration
echo "🌐 Setting up global nginx configuration..."
cp nginx-site.conf /etc/nginx/sites-available/liam-bday
ln -sf /etc/nginx/sites-available/liam-bday /etc/nginx/sites-enabled/

# Test nginx configuration
nginx -t

# Get SSL certificate if it doesn't exist
if [ ! -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]; then
    echo "🔒 Getting SSL certificate..."
    
    # Stop nginx temporarily for certbot
    systemctl stop nginx
    
    # Run certbot
    certbot certonly \
        --standalone \
        --email admin@$DOMAIN \
        --agree-tos \
        --no-eff-email \
        -d $DOMAIN -d www.$DOMAIN
    
    # Start nginx
    systemctl start nginx
    
    # Set up auto-renewal
    echo "🔄 Setting up SSL auto-renewal..."
    (crontab -l 2>/dev/null; echo "0 12 * * * /usr/bin/certbot renew --quiet && systemctl reload nginx") | crontab -
else
    echo "🔒 SSL certificate already exists, reloading nginx..."
    systemctl reload nginx
fi

# Show status
echo ""
echo "✅ Docker deployment complete!"
echo ""
echo "🐳 Containers status:"
docker-compose -f docker-compose.production.yml ps

echo ""
echo "🌐 Your website is available at:"
echo "   https://$DOMAIN"
echo "   Admin: https://$DOMAIN/admin"

echo ""
echo "🔧 Admin credentials:"
echo "   Username: admin_myrthe, Password: 19888888"
echo "   Username: admin_matthew, Password: helloworld"

echo ""
echo "📊 Useful commands:"
echo "   View logs: cd $PROJECT_DIR && docker-compose -f docker-compose.production.yml logs -f"
echo "   Restart: cd $PROJECT_DIR && docker-compose -f docker-compose.production.yml restart"
echo "   Stop: cd $PROJECT_DIR && docker-compose -f docker-compose.production.yml down"
echo "   Update: cd $PROJECT_DIR && git pull && docker-compose -f docker-compose.production.yml up -d --build"
