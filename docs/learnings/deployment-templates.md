# Deployment Templates

## Overview
Ready-to-use templates for streamlined Laravel Docker deployments.

## 1. Docker Compose Production Template

### docker-compose.production.yml
```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile.production
    container_name: ${APP_NAME:-myapp}-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./storage/app:/var/www/storage/app
      - ./storage/logs:/var/www/storage/logs
    networks:
      - app-network
    depends_on:
      mysql:
        condition: service_healthy
    environment:
      - DB_HOST=mysql
      - REDIS_HOST=redis
    ports:
      - "${APP_PORT:-8090}:80"

  mysql:
    image: mysql:8.0
    container_name: ${APP_NAME:-myapp}-mysql
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql-data:/var/lib/mysql
      - ./docker/mysql/custom.cnf:/etc/mysql/conf.d/custom.cnf
    networks:
      - app-network
    ports:
      - "${DB_PORT:-3307}:3306"
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost", "-u", "root", "-p${DB_ROOT_PASSWORD}"]
      interval: 10s
      timeout: 5s
      retries: 5

  redis:
    image: redis:7-alpine
    container_name: ${APP_NAME:-myapp}-redis
    restart: unless-stopped
    networks:
      - app-network
    volumes:
      - redis-data:/data
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
      timeout: 5s
      retries: 5

networks:
  app-network:
    driver: bridge

volumes:
  mysql-data:
  redis-data:
```

## 2. Optimized Dockerfile Template

### Dockerfile.production
```dockerfile
# Multi-stage build for smaller image
FROM composer:2 AS composer
FROM node:18-alpine AS node

# Main application stage
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Copy composer from composer stage
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copy node from node stage (optional)
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/bin/npm /usr/local/bin/npm

# Create application directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Build assets (if needed)
RUN if [ -f "package.json" ]; then \
    npm ci --production && \
    npm run build && \
    rm -rf node_modules; \
    fi

# Create required directories
RUN mkdir -p \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    /var/log/supervisor

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 storage bootstrap/cache

# Copy configuration files
COPY docker/nginx/app.conf /etc/nginx/sites-available/default
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Expose port
EXPOSE 80

# Start services
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

## 3. Configuration Templates

### docker/nginx/app.conf
```nginx
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    
    root /var/www/public;
    index index.php;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
```

### docker/supervisor/supervisord.conf
```ini
[supervisord]
nodaemon=true
user=root
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=/usr/local/sbin/php-fpm
autostart=true
autorestart=true
priority=5
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=/usr/sbin/nginx -g "daemon off;"
autostart=true
autorestart=true
priority=10
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/storage/logs/worker.log
stopwaitsecs=3600
```

### docker/php/custom.ini
```ini
[PHP]
; Maximum upload size
upload_max_filesize = 20M
post_max_size = 20M

; Memory and execution limits
memory_limit = 256M
max_execution_time = 300

; Error handling
display_errors = Off
log_errors = On
error_log = /var/www/storage/logs/php-error.log

; Opcache settings
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 0
opcache.validate_timestamps = 0

; Session settings
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = "Lax"
```

### docker/mysql/custom.cnf
```ini
[mysqld]
# Performance settings
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
innodb_flush_method = O_DIRECT
innodb_flush_log_at_trx_commit = 2

# Connection settings
max_connections = 100
connect_timeout = 10

# Character set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

# Slow query log
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2
```

## 4. Environment Template

### .env.production.example
```env
# Application
APP_NAME="My Laravel App"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel_user
DB_PASSWORD=secure_password_here
DB_ROOT_PASSWORD=secure_root_password_here

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# AWS (Optional)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

# Pusher (Optional)
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

# Docker specific
APP_PORT=8090
DB_PORT=3307
```

## 5. One-Command Deployment Script

### deploy.sh
```bash
#!/bin/bash
# One-command deployment script

set -e

# Configuration
DOMAIN=${1:-"example.com"}
PROJECT_NAME=$(echo $DOMAIN | sed 's/\./-/g')
DEPLOY_ENV=${2:-"production"}

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}🚀 Deploying $PROJECT_NAME to $DEPLOY_ENV${NC}"

# Pre-flight checks
echo -e "${YELLOW}Running pre-flight checks...${NC}"
./scripts/pre-deploy-check.sh || exit 1

# Pull latest code
echo -e "${YELLOW}Pulling latest code...${NC}"
git pull origin main

# Build and deploy
echo -e "${YELLOW}Building Docker images...${NC}"
docker-compose -f docker-compose.$DEPLOY_ENV.yml build --no-cache

echo -e "${YELLOW}Starting containers...${NC}"
docker-compose -f docker-compose.$DEPLOY_ENV.yml up -d

# Wait for database
echo -e "${YELLOW}Waiting for database...${NC}"
sleep 10

# Run migrations
echo -e "${YELLOW}Running migrations...${NC}"
docker-compose -f docker-compose.$DEPLOY_ENV.yml exec -T app php artisan migrate --force

# Clear caches
echo -e "${YELLOW}Optimizing application...${NC}"
docker-compose -f docker-compose.$DEPLOY_ENV.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.$DEPLOY_ENV.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.$DEPLOY_ENV.yml exec -T app php artisan view:cache

# Health check
echo -e "${YELLOW}Running health check...${NC}"
curl -f http://localhost:8090 > /dev/null || {
    echo -e "${RED}❌ Health check failed!${NC}"
    docker-compose -f docker-compose.$DEPLOY_ENV.yml logs --tail=50
    exit 1
}

echo -e "${GREEN}✅ Deployment complete!${NC}"
echo -e "Visit: https://$DOMAIN"
```

## 6. GitHub Actions Deployment

### .github/workflows/deploy.yml
```yaml
name: Deploy to Production

on:
  push:
    branches: [main]
  workflow_dispatch:

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /opt/${{ secrets.PROJECT_NAME }}
          git pull origin main
          ./deploy.sh ${{ secrets.DOMAIN }} production
```

## 7. Quick Start Guide

### For New Projects

1. **Copy all templates to your project**
   ```bash
   curl -L https://github.com/yourusername/deployment-templates/archive/main.zip -o templates.zip
   unzip templates.zip
   cp -r deployment-templates-main/* .
   ```

2. **Configure environment**
   ```bash
   cp .env.production.example .env.production
   # Edit .env.production with your values
   ```

3. **Run deployment**
   ```bash
   chmod +x deploy.sh
   ./deploy.sh yourdomain.com production
   ```

### For Existing Projects

1. **Add Docker files**
   ```bash
   mkdir -p docker/{nginx,php,mysql,supervisor}
   # Copy template files
   ```

2. **Update .gitignore**
   ```
   .env.production
   /storage/*.key
   /vendor
   /node_modules
   ```

3. **Test locally**
   ```bash
   docker-compose -f docker-compose.production.yml up --build
   ```

## 8. Troubleshooting Quick Reference

| Issue | Check | Fix |
|-------|-------|-----|
| Container won't start | `docker logs <container>` | Check Dockerfile syntax |
| Database connection refused | `docker-compose ps` | Wait for mysql healthy state |
| 502 Bad Gateway | `docker logs app` | Check PHP-FPM is running |
| Permission denied | `ls -la storage/` | Run permission fix script |
| SSL not working | `nginx -t` | Check certificate paths |

## Summary

These templates provide:
- ✅ Production-ready Docker setup
- ✅ Optimized for Laravel
- ✅ Security best practices
- ✅ Health checks included
- ✅ One-command deployment
- ✅ CI/CD ready

Customize as needed for your specific requirements!