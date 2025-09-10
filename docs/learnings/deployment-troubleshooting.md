# Deployment Troubleshooting Guide

## Common Issues and Solutions

Based on our deployment experience with the Laravel Birthday Party website on Digital Ocean.

### 1. GitHub Authentication Failures

**Issue:**
```
remote: Support for password authentication was removed on August 13, 2021
fatal: Authentication failed for 'https://github.com/...'
```

**Solution:**
Use SSH URLs instead of HTTPS:
```bash
# Wrong
git clone https://github.com/MOKuper/liam-bday.git

# Correct
git clone git@github.com:MOKuper/liam-bday.git
```

**Prevention:**
- Always use SSH URLs in deployment scripts
- Pre-configure SSH keys before deployment
- Test SSH connection: `ssh -T git@github.com`

### 2. Docker Build Failures - Missing Dependencies

**Issue:**
```
Package 'oniguruma' not found
#0 14.49 No package 'oniguruma' available
```

**Solution:**
Add missing system dependencies to Dockerfile:
```dockerfile
RUN apt-get update && apt-get install -y \
    libonig-dev \  # Added for mbstring
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip
```

**Prevention:**
- Test Docker builds locally first
- Keep a list of PHP extension dependencies
- Use multi-stage builds for cleaner images

### 3. Supervisor Log Directory Missing

**Issue:**
```
The directory named as part of the path /var/log/supervisor/supervisord.log does not exist
```

**Solution:**
Create required directories in Dockerfile:
```dockerfile
RUN mkdir -p /var/log/supervisor \
    && mkdir -p /var/www/database \
    && chown www-data:www-data /var/www/database
```

**Prevention:**
- Always create log directories in Dockerfile
- Use volume mounts for persistent logs
- Set proper permissions during build

### 4. Nginx Configuration Errors

**Issue:**
```
nginx: [emerg] invalid value "must-revalidate" in /etc/nginx/nginx.conf:29
```

**Solution:**
Fix invalid gzip_proxied values:
```nginx
# Wrong
gzip_proxied must-revalidate;

# Correct
gzip_proxied expired no-cache no-store private auth;
```

**Prevention:**
- Validate nginx configs locally: `nginx -t`
- Use configuration templates
- Keep a reference of valid directive values

### 5. Port Conflicts

**Issue:**
```
Error response from daemon: driver failed programming external connectivity
Bind for 0.0.0.0:443 failed: port is already allocated
```

**Solution:**
Use different ports or remove conflicting containers:
```yaml
# Use non-standard ports
ports:
  - "8090:80"  # Instead of 80:80
  - "3307:3306"  # Instead of 3306:3306
```

**Prevention:**
- Check port availability: `netstat -tulpn | grep :443`
- Use reverse proxy instead of exposing ports
- Document port mappings clearly

### 6. Storage Permission Errors

**Issue:**
```
file_put_contents(/var/www/storage/framework/views/...): Failed to open stream: Permission denied
```

**Solution:**
Set correct ownership:
```bash
docker exec <container> chown -R www-data:www-data /var/www/storage
docker exec <container> chown -R www-data:www-data /var/www/bootstrap/cache
```

**Prevention:**
- Set permissions in Dockerfile
- Use proper user context
- Create storage directories with correct permissions

### 7. HTTPS/SSL Behind Proxy

**Issue:**
```
The current request is not secure, but it was initiated with a secure URL
Mixed content warnings when switching languages
```

**Solution:**
Configure Laravel to trust proxies:
```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');
})
```

**Prevention:**
- Always configure trusted proxies for production
- Use environment-specific configurations
- Test with actual SSL certificates

### 8. Database Connection Issues

**Issue:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Solution:**
- Wait for database to be ready
- Use correct connection parameters
- Check Docker network configuration

**Prevention:**
```bash
# Add health checks
healthcheck:
  test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
  timeout: 20s
  retries: 10
```

## Quick Debugging Commands

```bash
# Check container status
docker ps -a

# View container logs
docker logs <container-name>

# Execute commands in container
docker exec -it <container> bash

# Test database connection
docker exec <app-container> php artisan tinker --execute="DB::connection()->getPdo();"

# Clear Laravel caches
docker exec <app-container> php artisan config:clear
docker exec <app-container> php artisan cache:clear

# Check file permissions
docker exec <app-container> ls -la storage/

# Test nginx configuration
nginx -t

# Check port usage
netstat -tulpn | grep :<port>
```

## Deployment Order of Operations

1. **Pre-flight checks** - Verify SSH keys, check ports
2. **Pull latest code** - Use SSH URLs
3. **Build Docker images** - Validate Dockerfile first
4. **Database setup** - Wait for ready state
5. **Run migrations** - Use --force flag
6. **Set permissions** - Critical for Laravel
7. **Configure proxy** - Trust proxies for HTTPS
8. **SSL certificates** - Let's Encrypt with certbot
9. **Smoke tests** - Verify critical paths