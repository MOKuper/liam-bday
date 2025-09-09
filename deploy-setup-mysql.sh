#!/bin/bash

# Digital Ocean Laravel Deployment Setup Script with MySQL
# Run this script on your Digital Ocean server to set up the initial deployment with MySQL

set -e

echo "🚀 Setting up Liam's Birthday Party Website on Digital Ocean with MySQL..."

# Variables (update these)
DOMAIN="mijnverjaardag.eu"
DB_PASSWORD="${DB_PASSWORD:-$(openssl rand -base64 32)}"  # Generate random password if not set
APP_DIR="/var/www/liam-bday"

# Allow environment variable override
if [ -z "$DB_PASSWORD" ]; then
    echo "💡 No DB_PASSWORD set, generating random password..."
    DB_PASSWORD=$(openssl rand -base64 32)
    echo "🔒 Generated password: $DB_PASSWORD"
    echo "📝 Save this password for your records!"
fi

# Update system
echo "📦 Updating system packages..."
apt update && apt upgrade -y

# Install required packages
echo "🔧 Installing required packages..."
apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-gd php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-intl php8.2-bcmath composer git certbot python3-certbot-nginx unzip

# Secure MySQL installation
echo "🔒 Securing MySQL installation..."
mysql_secure_installation

# Configure MySQL
echo "🗄️ Setting up MySQL database..."
mysql -e "CREATE DATABASE liam_bday_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER 'liam_bday_user'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';"
mysql -e "GRANT ALL PRIVILEGES ON liam_bday_production.* TO 'liam_bday_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Create application directory
echo "📁 Setting up application directory..."
mkdir -p $APP_DIR
cd $APP_DIR

# Clone repository
echo "📥 Cloning repository..."
git clone https://github.com/MOKuper/liam-bday.git .

# Install composer dependencies
echo "📦 Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

# Set up environment (copy MySQL version)
echo "⚙️ Configuring environment..."
cp .env.production.mysql .env
sed -i "s/your_secure_password_here/$DB_PASSWORD/g" .env
php artisan key:generate

# Set proper permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

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
systemctl enable mysql

# Set up automatic SSL renewal
echo "🔄 Setting up automatic SSL renewal..."
crontab -l | { cat; echo "0 12 * * * /usr/bin/certbot renew --quiet"; } | crontab -

# Cache configuration
echo "⚡ Caching Laravel configuration..."
cd $APP_DIR
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ MySQL deployment setup complete!"
echo ""
echo "🎉 Liam's Birthday Party website is now live at https://$DOMAIN"
echo ""
echo "Database: MySQL"
echo "Database Name: liam_bday_production"
echo "Database User: liam_bday_user"