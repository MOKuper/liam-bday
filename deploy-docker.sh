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
echo "   - Build and run Docker containers"
echo "   - Set up SSL certificates"
echo "   - Run on ports 80/443 (or 8090 if you prefer)"
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

# Clone repository (update this with your actual repo)
echo "📥 Cloning repository..."
if [ ! -d ".git" ]; then
    git clone https://github.com/YOUR_USERNAME/liam-bday.git .
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

# Get SSL certificate (if using port 80/443)
if docker-compose -f docker-compose.production.yml ps | grep -q "nginx-proxy.*Up"; then
    echo "🔒 Setting up SSL certificate..."
    
    # Stop nginx-proxy temporarily for certbot
    docker-compose -f docker-compose.production.yml stop nginx-proxy
    
    # Run certbot
    docker run --rm -it \
        -v /etc/letsencrypt:/etc/letsencrypt \
        -v /var/www/certbot:/var/www/certbot \
        -p 80:80 \
        certbot/certbot certonly \
        --standalone \
        --email admin@$DOMAIN \
        --agree-tos \
        --no-eff-email \
        -d $DOMAIN -d www.$DOMAIN
    
    # Restart nginx-proxy
    docker-compose -f docker-compose.production.yml start nginx-proxy
    
    # Set up auto-renewal
    echo "🔄 Setting up SSL auto-renewal..."
    (crontab -l 2>/dev/null; echo "0 12 * * * cd $PROJECT_DIR && docker run --rm -v /etc/letsencrypt:/etc/letsencrypt -v /var/www/certbot:/var/www/certbot certbot/certbot renew --quiet && docker-compose -f docker-compose.production.yml restart nginx-proxy") | crontab -
fi

# Show status
echo ""
echo "✅ Docker deployment complete!"
echo ""
echo "🐳 Containers status:"
docker-compose -f docker-compose.production.yml ps

echo ""
echo "🌐 Your website is available at:"
if docker-compose -f docker-compose.production.yml ps | grep -q "nginx-proxy.*Up"; then
    echo "   https://$DOMAIN"
    echo "   Admin: https://$DOMAIN/admin"
else
    echo "   http://your-server-ip:8090"
    echo "   Admin: http://your-server-ip:8090/admin"
fi

echo ""
echo "🔧 Admin credentials:"
echo "   Username: admin_myrthe, Password: 19888888"
echo "   Username: admin_matthew, Password: helloworld"

echo ""
echo "📊 Useful commands:"
echo "   View logs: docker-compose -f docker-compose.production.yml logs -f"
echo "   Restart: docker-compose -f docker-compose.production.yml restart"
echo "   Stop: docker-compose -f docker-compose.production.yml down"
echo "   Update: cd $PROJECT_DIR && git pull && docker-compose -f docker-compose.production.yml up -d --build"