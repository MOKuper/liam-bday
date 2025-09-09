# Deployment Guide - Liam's Birthday Party Website

This guide will help you deploy the birthday party website to Digital Ocean using GitHub Actions.

## Prerequisites

1. **Digital Ocean Droplet** - Ubuntu 22.04 LTS (recommended)
2. **Domain name** pointed to your droplet's IP
3. **GitHub repository** for the code
4. **SSH access** to your server

## Step 1: Choose Database & Initial Server Setup

### Option A: SQLite (Recommended - Simpler)

**Perfect for small websites like this birthday party site.**

1. **Connect to your Digital Ocean server:**
   ```bash
   ssh root@your-server-ip
   ```

2. **Download and run the SQLite setup script:**
   ```bash
   wget https://raw.githubusercontent.com/YOUR_USERNAME/liam-bday/main/deploy-setup.sh
   chmod +x deploy-setup.sh
   nano deploy-setup.sh  # Update DOMAIN variable
   ./deploy-setup.sh
   ```

### Option B: MySQL (More Robust)

**Better for larger sites or if you need advanced database features.**

1. **Connect to your Digital Ocean server:**
   ```bash
   ssh root@your-server-ip
   ```

2. **Download and run the MySQL setup script:**
   ```bash
   wget https://raw.githubusercontent.com/YOUR_USERNAME/liam-bday/main/deploy-setup-mysql.sh
   chmod +x deploy-setup-mysql.sh
   nano deploy-setup-mysql.sh  # Update DOMAIN and DB_PASSWORD variables
   ./deploy-setup-mysql.sh
   ```

## Step 2: Configure GitHub Repository

1. **Push your code to GitHub:**
   ```bash
   git init
   git add .
   git commit -m "Initial commit - Liam's birthday website"
   git branch -M main
   git remote add origin https://github.com/YOUR_USERNAME/liam-bday.git
   git push -u origin main
   ```

2. **Add GitHub Secrets** (Repository Settings > Secrets and Variables > Actions):
   - `DO_HOST`: Your server IP address
   - `DO_USERNAME`: Your server username (usually `root`)
   - `DO_SSH_KEY`: Your private SSH key content

3. **Generate SSH key** (if you don't have one):
   ```bash
   ssh-keygen -t rsa -b 4096 -C "your-email@example.com"
   cat ~/.ssh/id_rsa.pub  # Copy this to your server's ~/.ssh/authorized_keys
   cat ~/.ssh/id_rsa      # Copy this to GitHub secret DO_SSH_KEY
   ```

## Step 3: Update Configuration Files

1. **Update domain in files:**
   - `.env.production`: Update `APP_URL` and database credentials
   - `nginx-production.conf`: Replace `your-domain.com` with your actual domain
   - `deploy-setup.sh`: Update `DOMAIN` variable

2. **Update GitHub repository URL** in `deploy-setup.sh`:
   ```bash
   git clone https://github.com/YOUR_USERNAME/liam-bday.git .
   ```

## Step 4: Deploy

1. **Push changes to trigger deployment:**
   ```bash
   git add .
   git commit -m "Update production configuration"
   git push origin main
   ```

2. **Check GitHub Actions** - Go to your repository > Actions tab to monitor deployment

## Step 5: Post-Deployment

1. **Verify the website** is working at `https://your-domain.com`

2. **Test admin access:**
   - Go to `https://your-domain.com/admin`
   - Username: `admin_myrthe`, Password: `19888888`
   - Username: `admin_matthew`, Password: `helloworld`

3. **Upload party details:**
   - Add party information in Admin > Party Details
   - Import guests via CSV or add manually
   - Upload friendship photos

## Important Files

- **`.github/workflows/deploy.yml`** - GitHub Actions deployment workflow
- **`nginx-production.conf`** - Nginx configuration for production
- **`.env.production`** - Production environment template
- **`deploy-setup.sh`** - Initial server setup script

## Security Notes

- The admin passwords are hardcoded for simplicity
- SSL certificate is automatically configured via Let's Encrypt
- File uploads are limited to 10MB
- Basic security headers are configured in Nginx

## Troubleshooting

1. **Check logs:**
   ```bash
   tail -f /var/log/nginx/error.log
   tail -f /var/www/liam-bday/storage/logs/laravel.log
   ```

2. **Restart services:**
   ```bash
   sudo systemctl restart nginx
   sudo systemctl restart php8.2-fpm
   ```

3. **Clear Laravel cache:**
   ```bash
   cd /var/www/liam-bday
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

## Manual Deployment (Alternative)

If you prefer manual deployment via SCP:

```bash
# Build locally
composer install --optimize-autoloader --no-dev
npm run build  # if you have frontend assets

# Upload files
rsync -avz --exclude '.git' --exclude 'node_modules' . root@your-server:/var/www/liam-bday/

# Run on server
ssh root@your-server "cd /var/www/liam-bday && php artisan migrate --force && php artisan config:cache"
```