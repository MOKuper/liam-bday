# Shared Hosting Deployment Guide

**Use this guide when deploying to a droplet that already hosts other projects.**

## 🚨 Important Notes

- This approach **DOES NOT** modify global server settings
- **DOES NOT** restart services automatically  
- **DOES NOT** install packages globally
- Works alongside existing projects
- Requires manual nginx configuration

## Prerequisites

Your droplet should already have:
- ✅ Nginx installed and running
- ✅ PHP 8.2-FPM installed  
- ✅ Required PHP extensions (`php8.2-gd`, `php8.2-sqlite3`, `php8.2-mbstring`, etc.)
- ✅ Composer installed
- ✅ Certbot for SSL

## Step 1: Check Current Setup

```bash
# Check nginx status
sudo systemctl status nginx

# Check PHP-FPM
sudo systemctl status php8.2-fpm

# Check installed PHP extensions
php -m | grep -E "(gd|sqlite|mbstring|xml|curl)"

# Check composer
composer --version
```

## Step 2: Deploy Application

1. **Upload the safe deployment script:**
   ```bash
   scp deploy-setup-shared.sh nginx-shared.conf root@your-server:/tmp/
   ```

2. **Run the deployment script:**
   ```bash
   ssh root@your-server
   cd /tmp
   chmod +x deploy-setup-shared.sh
   nano deploy-setup-shared.sh  # Update DOMAIN variable
   ./deploy-setup-shared.sh
   ```

## Step 3: Manual Nginx Configuration

1. **Review the generated nginx config:**
   ```bash
   nano /etc/nginx/sites-available/liam-bday
   ```

2. **Check for conflicts with existing sites:**
   ```bash
   # List existing sites
   ls -la /etc/nginx/sites-enabled/
   
   # Check if port 80/443 is already used for your domain
   grep -r "your-domain.com" /etc/nginx/sites-enabled/
   ```

3. **Enable the site:**
   ```bash
   sudo ln -s /etc/nginx/sites-available/liam-bday /etc/nginx/sites-enabled/
   ```

4. **Test nginx configuration:**
   ```bash
   sudo nginx -t
   ```

5. **Reload nginx (gentle reload, doesn't affect other sites):**
   ```bash
   sudo systemctl reload nginx
   ```

## Step 4: SSL Certificate

1. **Get SSL certificate for the new domain:**
   ```bash
   sudo certbot --nginx -d your-domain.com -d www.your-domain.com
   ```

2. **Verify SSL is working:**
   ```bash
   curl -I https://your-domain.com
   ```

## Step 5: Test the Application

1. **Visit your site:** `https://your-domain.com`
2. **Test admin access:** `https://your-domain.com/admin`
3. **Check logs if issues:**
   ```bash
   tail -f /var/log/nginx/liam-bday.error.log
   tail -f /var/www/liam-bday/storage/logs/laravel.log
   ```

## GitHub Actions for Shared Hosting

Update the GitHub Actions workflow to be less aggressive:

```yaml
# .github/workflows/deploy-shared.yml
name: Deploy to Shared Hosting

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v4
    
    - name: Deploy to server
      uses: appleboy/ssh-action@v1.0.3
      with:
        host: ${{ secrets.DO_HOST }}
        username: ${{ secrets.DO_USERNAME }}
        key: ${{ secrets.DO_SSH_KEY }}
        script: |
          cd /var/www/liam-bday
          git pull origin main
          composer install --optimize-autoloader --no-dev
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          # Note: No service restarts to avoid affecting other projects
```

## Rollback Plan

If something goes wrong:

1. **Disable the site:**
   ```bash
   sudo rm /etc/nginx/sites-enabled/liam-bday
   sudo systemctl reload nginx
   ```

2. **Remove application:**
   ```bash
   rm -rf /var/www/liam-bday
   ```

3. **Remove SSL certificate (if needed):**
   ```bash
   sudo certbot delete --cert-name your-domain.com
   ```

## Common Issues & Solutions

### Issue: PHP extensions missing
```bash
# Install missing extensions
sudo apt install php8.2-gd php8.2-sqlite3 php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip
sudo systemctl restart php8.2-fpm
```

### Issue: Permission denied
```bash
# Check web server user
ps aux | grep -E '[n]ginx|[w]ww-data' | head -1

# Fix permissions
sudo chown -R www-data:www-data /var/www/liam-bday
sudo chmod -R 755 /var/www/liam-bday
sudo chmod -R 775 /var/www/liam-bday/storage /var/www/liam-bday/bootstrap/cache
```

### Issue: Database permissions
```bash
# Fix SQLite database permissions
sudo chmod 664 /var/www/liam-bday/database/database.sqlite
sudo chmod 775 /var/www/liam-bday/database
```

## Security Considerations

- Uses separate log files: `/var/log/nginx/liam-bday.*`
- Conservative security headers to avoid conflicts
- Separate error handling
- Isolated from other projects

This approach ensures your birthday party website won't interfere with existing projects on your droplet.