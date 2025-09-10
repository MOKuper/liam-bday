# Infrastructure Setup Guide

## Overview
This guide provides a reproducible infrastructure setup for Laravel applications on Digital Ocean (or any Ubuntu server).

## 1. Initial Server Setup

### Base Server Configuration
```bash
#!/bin/bash
# setup-server.sh

set -e

echo "🏗️ Setting up base server infrastructure..."

# Update system
apt update && apt upgrade -y

# Install essential packages
apt install -y \
    curl \
    wget \
    git \
    vim \
    htop \
    ufw \
    software-properties-common \
    apt-transport-https \
    ca-certificates \
    gnupg \
    lsb-release

# Setup timezone
timedatectl set-timezone UTC

# Configure firewall
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable

echo "✅ Base server setup complete"
```

### Docker Installation
```bash
#!/bin/bash
# install-docker.sh

echo "🐳 Installing Docker..."

# Remove old versions
apt remove docker docker-engine docker.io containerd runc || true

# Add Docker's official GPG key
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Add Docker repository
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker
apt update
apt install -y docker-ce docker-ce-cli containerd.io

# Install Docker Compose
curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Start and enable Docker
systemctl start docker
systemctl enable docker

# Add current user to docker group (optional)
# usermod -aG docker $USER

echo "✅ Docker installation complete"
docker --version
docker-compose --version
```

### Nginx Installation
```bash
#!/bin/bash
# install-nginx.sh

echo "🌐 Installing Nginx..."

# Install Nginx
apt install -y nginx

# Create sites-available/enabled structure
mkdir -p /etc/nginx/sites-available
mkdir -p /etc/nginx/sites-enabled

# Backup default config
cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.backup

# Optimize Nginx configuration
cat > /etc/nginx/nginx.conf << 'EOF'
user www-data;
worker_processes auto;
pid /run/nginx.pid;
include /etc/nginx/modules-enabled/*.conf;

events {
    worker_connections 768;
    multi_accept on;
}

http {
    # Basic Settings
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    server_tokens off;

    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # SSL Settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers off;

    # Logging
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    # Gzip Settings
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss application/rss+xml application/atom+xml image/svg+xml;

    # Virtual Host Configs
    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;
}
EOF

# Remove default site
rm -f /etc/nginx/sites-enabled/default

# Test configuration
nginx -t

# Start and enable Nginx
systemctl restart nginx
systemctl enable nginx

echo "✅ Nginx installation complete"
```

### SSL/Let's Encrypt Setup
```bash
#!/bin/bash
# install-certbot.sh

echo "🔒 Installing Certbot for SSL..."

# Install snapd
apt install -y snapd

# Install certbot
snap install core; snap refresh core
snap install --classic certbot
ln -s /snap/bin/certbot /usr/bin/certbot

# Install certbot nginx plugin
snap set certbot trust-plugin-with-root=ok
snap install certbot-dns-cloudflare  # Optional: for wildcard certs

echo "✅ Certbot installation complete"
```

## 2. Project-Specific Setup

### Create Project Structure
```bash
#!/bin/bash
# setup-project-structure.sh

PROJECT_NAME=${1:-"myapp"}
DOMAIN=${2:-"example.com"}

echo "📁 Setting up project structure for $PROJECT_NAME..."

# Create directory structure
mkdir -p /opt/$PROJECT_NAME/{backups,logs,data}
mkdir -p /var/log/$PROJECT_NAME

# Create deployment user (optional)
# useradd -m -s /bin/bash deploy
# usermod -aG docker deploy

# Set permissions
chown -R www-data:www-data /opt/$PROJECT_NAME
chmod -R 755 /opt/$PROJECT_NAME

echo "✅ Project structure created"
```

### Nginx Site Configuration Template
```bash
#!/bin/bash
# create-nginx-site.sh

PROJECT_NAME=${1:-"myapp"}
DOMAIN=${2:-"example.com"}
PORT=${3:-"8090"}

cat > /etc/nginx/sites-available/$PROJECT_NAME << EOF
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN www.$DOMAIN;

    # Redirect to HTTPS
    return 301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name $DOMAIN www.$DOMAIN;

    # SSL configuration (managed by Certbot)
    ssl_certificate /etc/letsencrypt/live/$DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$DOMAIN/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Proxy settings
    location / {
        proxy_pass http://localhost:$PORT;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_cache_bypass \$http_upgrade;
        
        # Timeouts
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
    }

    # Static files (optional)
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to sensitive files
    location ~ /\\.(?!well-known) {
        deny all;
    }
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/$PROJECT_NAME /etc/nginx/sites-enabled/

# Test configuration
nginx -t

echo "✅ Nginx site configuration created"
```

## 3. Complete Infrastructure Setup Script

```bash
#!/bin/bash
# complete-infrastructure-setup.sh

set -e

# Configuration
PROJECT_NAME=${1:-"myapp"}
DOMAIN=${2:-"example.com"}
ADMIN_EMAIL=${3:-"admin@example.com"}

echo "🚀 Complete Infrastructure Setup"
echo "==============================="
echo "Project: $PROJECT_NAME"
echo "Domain: $DOMAIN"
echo "Email: $ADMIN_EMAIL"
echo "==============================="

# Run setup scripts in order
./setup-server.sh
./install-docker.sh
./install-nginx.sh
./install-certbot.sh
./setup-project-structure.sh $PROJECT_NAME $DOMAIN
./create-nginx-site.sh $PROJECT_NAME $DOMAIN

# Get initial SSL certificate
echo "🔒 Obtaining SSL certificate..."
certbot --nginx -d $DOMAIN -d www.$DOMAIN --non-interactive --agree-tos -m $ADMIN_EMAIL

# Setup auto-renewal
echo "0 12 * * * /usr/bin/certbot renew --quiet && systemctl reload nginx" | crontab -

# Create deployment script
cat > /opt/$PROJECT_NAME/deploy.sh << 'EOF'
#!/bin/bash
cd /opt/PROJECT_NAME
git pull origin main
docker-compose -f docker-compose.production.yml up -d --build
docker exec app php artisan migrate --force
docker exec app php artisan config:cache
docker exec app php artisan route:cache
docker exec app php artisan view:cache
EOF

sed -i "s/PROJECT_NAME/$PROJECT_NAME/g" /opt/$PROJECT_NAME/deploy.sh
chmod +x /opt/$PROJECT_NAME/deploy.sh

# System info
echo ""
echo "✅ Infrastructure setup complete!"
echo ""
echo "📊 System Information:"
echo "   OS: $(lsb_release -d | cut -f2)"
echo "   Docker: $(docker --version)"
echo "   Nginx: $(nginx -v 2>&1)"
echo "   PHP: Will be in Docker container"
echo ""
echo "🔧 Next Steps:"
echo "   1. Clone your repository to /opt/$PROJECT_NAME"
echo "   2. Configure .env.production"
echo "   3. Run: cd /opt/$PROJECT_NAME && ./deploy.sh"
echo ""
echo "📝 Useful Commands:"
echo "   View logs: docker-compose logs -f"
echo "   Enter container: docker exec -it ${PROJECT_NAME}_app bash"
echo "   Restart services: docker-compose restart"
```

## 4. Monitoring and Maintenance

### Basic Monitoring Setup
```bash
#!/bin/bash
# setup-monitoring.sh

echo "📊 Setting up basic monitoring..."

# Install monitoring tools
apt install -y \
    htop \
    iotop \
    nethogs \
    ncdu

# Setup log rotation
cat > /etc/logrotate.d/docker-containers << EOF
/var/lib/docker/containers/*/*.log {
    daily
    rotate 7
    compress
    delaycompress
    missingok
    notifempty
    create 0640 root root
}
EOF

# Basic health check script
cat > /opt/health-check.sh << 'EOF'
#!/bin/bash
# Simple health monitoring

echo "=== System Health Check ==="
echo "Date: $(date)"
echo ""

# Disk usage
echo "📁 Disk Usage:"
df -h | grep -E '^/dev/'
echo ""

# Memory usage
echo "💾 Memory Usage:"
free -h
echo ""

# Docker status
echo "🐳 Docker Containers:"
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
echo ""

# Nginx status
echo "🌐 Nginx Status:"
systemctl is-active nginx
echo ""

# Check web response
echo "🔍 Web Response:"
curl -sI http://localhost | head -n 1
EOF

chmod +x /opt/health-check.sh

# Add to crontab for daily reports
echo "0 9 * * * /opt/health-check.sh > /var/log/health-check.log 2>&1" | crontab -

echo "✅ Monitoring setup complete"
```

## 5. Security Hardening

### Basic Security Configuration
```bash
#!/bin/bash
# security-hardening.sh

echo "🔐 Applying security hardening..."

# SSH hardening
sed -i 's/#PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
sed -i 's/#PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config
systemctl restart sshd

# Install fail2ban
apt install -y fail2ban

# Configure fail2ban for SSH and Nginx
cat > /etc/fail2ban/jail.local << EOF
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true

[nginx-http-auth]
enabled = true

[nginx-limit-req]
enabled = true
EOF

systemctl restart fail2ban

# Automatic security updates
apt install -y unattended-upgrades
dpkg-reconfigure -plow unattended-upgrades

echo "✅ Security hardening complete"
```

## Usage

1. **Fresh server setup:**
   ```bash
   wget https://raw.githubusercontent.com/yourrepo/setup/main/complete-infrastructure-setup.sh
   chmod +x complete-infrastructure-setup.sh
   ./complete-infrastructure-setup.sh myapp mydomain.com admin@mydomain.com
   ```

2. **Individual components:**
   ```bash
   ./setup-server.sh          # Base setup only
   ./install-docker.sh        # Docker only
   ./install-nginx.sh         # Nginx only
   ```

3. **Verify setup:**
   ```bash
   /opt/health-check.sh       # Run health check
   ```

## Quick Reference

| Component | Version | Config Location | Logs |
|-----------|---------|-----------------|------|
| Docker | Latest | /etc/docker/daemon.json | `docker logs` |
| Nginx | Latest | /etc/nginx/sites-available/ | /var/log/nginx/ |
| Certbot | Snap | /etc/letsencrypt/ | /var/log/letsencrypt/ |
| Fail2ban | Latest | /etc/fail2ban/jail.local | /var/log/fail2ban.log |