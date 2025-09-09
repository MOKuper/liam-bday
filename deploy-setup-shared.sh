#!/bin/bash

# Safe deployment for EXISTING droplet with other projects
# This script DOES NOT touch global nginx, PHP, or system configs

set -e

echo "🚀 Setting up Liam's Birthday Party Website on SHARED droplet..."

# Variables (update these)
DOMAIN="your-domain.com"
APP_DIR="/var/www/liam-bday"

echo "⚠️  WARNING: This script assumes you already have:"
echo "   - Nginx installed and running"
echo "   - PHP 8.2-FPM installed"
echo "   - Required PHP extensions"
echo "   - Composer installed"
echo "   - Certbot installed"
echo ""
read -p "Continue? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Cancelled."
    exit 1
fi

# Create application directory
echo "📁 Setting up application directory..."
mkdir -p $APP_DIR
cd $APP_DIR

# Clone repository (you'll need to update this with your actual repo)
echo "📥 Cloning repository..."
git clone https://github.com/MOKuper/liam-bday.git .

# Install composer dependencies
echo "📦 Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

# Set up environment
echo "⚙️ Configuring environment..."
cp .env.production .env
php artisan key:generate

# Create SQLite database
echo "🗄️ Setting up SQLite database..."
touch $APP_DIR/database/database.sqlite
chmod 664 $APP_DIR/database/database.sqlite

# Set proper permissions (using existing web user)
WEB_USER=$(ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
echo "🔒 Setting permissions for web user: $WEB_USER"
chown -R $WEB_USER:$WEB_USER $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache $APP_DIR/database

# Create storage symlink
php artisan storage:link

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Create nginx site config (does not enable it automatically)
echo "🌐 Creating Nginx configuration..."
cp nginx-production.conf /etc/nginx/sites-available/liam-bday
sed -i "s/your-domain.com/$DOMAIN/g" /etc/nginx/sites-available/liam-bday

echo "📝 Nginx configuration created at: /etc/nginx/sites-available/liam-bday"
echo ""
echo "⚠️  MANUAL STEPS REQUIRED:"
echo "1. Review the nginx config: nano /etc/nginx/sites-available/liam-bday"
echo "2. Enable the site: ln -s /etc/nginx/sites-available/liam-bday /etc/nginx/sites-enabled/"
echo "3. Test nginx config: nginx -t"
echo "4. Reload nginx: systemctl reload nginx"
echo "5. Get SSL certificate: certbot --nginx -d $DOMAIN"
echo ""

# Cache configuration
echo "⚡ Caching Laravel configuration..."
cd $APP_DIR
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Application setup complete!"
echo ""
echo "🎉 Liam's Birthday Party website files are ready at: $APP_DIR"
echo ""
echo "📋 NEXT STEPS (run manually):"
echo "1. sudo ln -s /etc/nginx/sites-available/liam-bday /etc/nginx/sites-enabled/"
echo "2. sudo nginx -t"
echo "3. sudo systemctl reload nginx"
echo "4. sudo certbot --nginx -d $DOMAIN"
echo ""
echo "🔧 Admin access will be available at:"
echo "   https://$DOMAIN/admin"
echo "   Username: admin_myrthe, Password: 19888888"
echo "   Username: admin_matthew, Password: helloworld"
