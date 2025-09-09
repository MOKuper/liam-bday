#!/bin/bash

# Digital Ocean Laravel Deployment Setup Script
# Run this script on your Digital Ocean server to set up the initial deployment

set -e

echo "🚀 Setting up Liam's Birthday Party Website on Digital Ocean..."

# Variables (update these)
DOMAIN="mijnverjaardag.eu"
APP_DIR="/var/www/liam-bday"

# Update system
echo "📦 Updating system packages..."
apt update && apt upgrade -y

# Install required packages (using SQLite instead of MySQL)
echo "🔧 Installing required packages..."
apt install -y nginx php8.2-fpm php8.2-sqlite3 php8.2-gd php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-intl php8.2-bcmath composer git certbot python3-certbot-nginx unzip sqlite3

# Note: No MySQL setup needed with SQLite

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

# Set proper permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache $APP_DIR/database

# Create storage symlink
php artisan storage:link

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Configure Nginx
echo "🌐 Configuring Nginx..."
cp nginx-production.conf /etc/nginx/sites-available/liam-bday
sed -i "s/your-domain.com/$DOMAIN/g" /etc/nginx/sites-available/liam-bday
ln -sf /etc/nginx/sites-available/liam-bday /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
nginx -t

# Get SSL certificate
echo "🔒 Setting up SSL certificate..."
certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos --email admin@$DOMAIN

# Configure PHP-FPM
echo "⚡ Optimizing PHP-FPM..."
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 10M/' /etc/php/8.2/fpm/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 10M/' /etc/php/8.2/fpm/php.ini
sed -i 's/max_execution_time = 30/max_execution_time = 300/' /etc/php/8.2/fpm/php.ini

# Restart services
echo "🔄 Restarting services..."
systemctl restart php8.2-fpm
systemctl restart nginx
systemctl enable nginx
systemctl enable php8.2-fpm

# Set up automatic SSL renewal
echo "🔄 Setting up automatic SSL renewal..."
crontab -l | { cat; echo "0 12 * * * /usr/bin/certbot renew --quiet"; } | crontab -

# Cache configuration
echo "⚡ Caching Laravel configuration..."
cd $APP_DIR
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment setup complete!"
echo ""
echo "🎉 Liam's Birthday Party website is now live at https://$DOMAIN"
echo ""
echo "Next steps:"
echo "1. Update your GitHub repository URL in this script"
echo "2. Set up GitHub secrets for deployment:"
echo "   - DO_HOST: Your server IP"
echo "   - DO_USERNAME: Your server username (usually 'root')"
echo "   - DO_SSH_KEY: Your private SSH key"
echo "3. Update the domain in .env.production and nginx-production.conf"
echo "4. Push your code to GitHub to trigger automatic deployment"
echo ""
echo "🔧 Admin access:"
echo "   Username: admin_myrthe, Password: 19888888"
echo "   Username: admin_matthew, Password: helloworld"
