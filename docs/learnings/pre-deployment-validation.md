# Pre-Deployment Validation Checklist

## Overview
This checklist helps prevent common deployment issues by validating your environment and configuration before deployment.

## 1. Local Development Validation

### Docker Build Test
```bash
#!/bin/bash
# test-docker-build.sh

echo "🐳 Testing Docker build locally..."

# Build without cache
docker-compose -f docker-compose.production.yml build --no-cache

# Run containers
docker-compose -f docker-compose.production.yml up -d

# Wait for startup
sleep 10

# Test application
curl -f http://localhost:8090 || { echo "❌ App not responding"; exit 1; }

# Cleanup
docker-compose -f docker-compose.production.yml down

echo "✅ Docker build test passed"
```

### Configuration Validation
```bash
#!/bin/bash
# validate-configs.sh

echo "📋 Validating configuration files..."

# Check required files exist
REQUIRED_FILES=(
    "docker-compose.production.yml"
    "Dockerfile.production"
    ".env.production.example"
    "deploy-docker.sh"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ ! -f "$file" ]; then
        echo "❌ Missing required file: $file"
        exit 1
    fi
done

# Validate nginx config syntax
if [ -f "docker/nginx.conf" ]; then
    docker run --rm -v $(pwd)/docker/nginx.conf:/etc/nginx/nginx.conf:ro nginx nginx -t || {
        echo "❌ Invalid nginx configuration"
        exit 1
    }
fi

echo "✅ Configuration validation passed"
```

## 2. Server Prerequisites Check

### SSH and Access Validation
```bash
#!/bin/bash
# check-server-access.sh

echo "🔑 Checking server access..."

# Test SSH connection
ssh -o ConnectTimeout=5 root@your-server-ip "echo '✅ SSH connection successful'" || {
    echo "❌ SSH connection failed"
    exit 1
}

# Check GitHub SSH key
ssh -T git@github.com 2>&1 | grep "successfully authenticated" || {
    echo "❌ GitHub SSH key not configured"
    echo "Run: ssh-keygen -t ed25519 -C 'server@example.com'"
    echo "Then add key to GitHub"
    exit 1
}

# Check required software
REQUIRED_SOFTWARE=("docker" "docker-compose" "nginx" "certbot")
for cmd in "${REQUIRED_SOFTWARE[@]}"; do
    ssh root@your-server-ip "command -v $cmd" > /dev/null || {
        echo "❌ Missing: $cmd"
        exit 1
    }
done

echo "✅ Server access check passed"
```

### Port Availability Check
```bash
#!/bin/bash
# check-ports.sh

echo "🔌 Checking port availability..."

REQUIRED_PORTS=(80 443 8090 3307)

for port in "${REQUIRED_PORTS[@]}"; do
    ssh root@your-server-ip "netstat -tulpn | grep :$port" && {
        echo "⚠️  Port $port is already in use"
        read -p "Continue anyway? (y/n) " -n 1 -r
        echo
        [[ ! $REPLY =~ ^[Yy]$ ]] && exit 1
    }
done

echo "✅ Port check passed"
```

## 3. Environment Configuration

### Environment Variables Validation
```bash
#!/bin/bash
# validate-env.sh

echo "🔧 Validating environment variables..."

# Check .env.production exists
if [ ! -f ".env.production" ]; then
    echo "Creating .env.production from example..."
    cp .env.production.example .env.production
fi

# Required variables
REQUIRED_ENV_VARS=(
    "APP_NAME"
    "APP_URL"
    "DB_PASSWORD"
    "DB_ROOT_PASSWORD"
    "ADMIN_USERNAME"
    "ADMIN_PASSWORD"
)

# Check each variable
for var in "${REQUIRED_ENV_VARS[@]}"; do
    grep -q "^$var=" .env.production || {
        echo "❌ Missing environment variable: $var"
        exit 1
    }
done

# Validate APP_URL matches domain
APP_URL=$(grep "^APP_URL=" .env.production | cut -d= -f2)
if [[ ! "$APP_URL" =~ ^https:// ]]; then
    echo "⚠️  APP_URL should use https:// for production"
fi

echo "✅ Environment validation passed"
```

## 4. Database Readiness

### Database Connection Test
```bash
#!/bin/bash
# test-database.sh

echo "🗄️ Testing database setup..."

# Test MySQL container starts
docker run --rm \
    -e MYSQL_ROOT_PASSWORD=test123 \
    -e MYSQL_DATABASE=testdb \
    mysql:8.0 \
    mysqld --default-authentication-plugin=mysql_native_password &

MYSQL_PID=$!
sleep 10

# Test connection
docker run --rm mysql:8.0 \
    mysql -h172.17.0.2 -uroot -ptest123 -e "SELECT 1" || {
    echo "❌ MySQL test failed"
    kill $MYSQL_PID
    exit 1
}

kill $MYSQL_PID
echo "✅ Database test passed"
```

## 5. SSL/HTTPS Validation

### Domain DNS Check
```bash
#!/bin/bash
# check-dns.sh

DOMAIN="your-domain.com"

echo "🌐 Checking DNS configuration for $DOMAIN..."

# Check A record
IP=$(dig +short $DOMAIN)
if [ -z "$IP" ]; then
    echo "❌ No A record found for $DOMAIN"
    exit 1
fi

echo "✅ Domain points to: $IP"

# Check if IP matches server
SERVER_IP="your-server-ip"
if [ "$IP" != "$SERVER_IP" ]; then
    echo "⚠️  Warning: Domain points to $IP, expected $SERVER_IP"
fi
```

## 6. Complete Pre-Deployment Script

```bash
#!/bin/bash
# pre-deploy-check.sh

set -e

echo "🚀 Running pre-deployment validation..."
echo "=================================="

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
DOMAIN=${1:-"your-domain.com"}
SERVER_IP=${2:-"your-server-ip"}

# Run all checks
CHECKS=(
    "test-docker-build.sh"
    "validate-configs.sh"
    "check-server-access.sh"
    "check-ports.sh"
    "validate-env.sh"
    "check-dns.sh"
)

FAILED=0
for check in "${CHECKS[@]}"; do
    echo -e "\n${YELLOW}Running: $check${NC}"
    if ./$check; then
        echo -e "${GREEN}✅ Passed${NC}"
    else
        echo -e "${RED}❌ Failed${NC}"
        FAILED=$((FAILED + 1))
    fi
done

echo -e "\n=================================="
if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}✅ All pre-deployment checks passed!${NC}"
    echo "Ready to deploy to $DOMAIN ($SERVER_IP)"
else
    echo -e "${RED}❌ $FAILED checks failed${NC}"
    echo "Please fix the issues before deploying"
    exit 1
fi
```

## Usage

1. **Before first deployment:**
   ```bash
   chmod +x pre-deploy-check.sh
   ./pre-deploy-check.sh mydomain.com 123.456.789.0
   ```

2. **Fix any issues reported**

3. **Run deployment only after all checks pass**

## Quick Reference

| Check | Purpose | Common Fix |
|-------|---------|------------|
| Docker build | Ensures app builds correctly | Fix Dockerfile dependencies |
| Config files | Verifies all configs present | Create missing files |
| Server access | Tests SSH connectivity | Add SSH keys |
| Port availability | Checks for conflicts | Stop conflicting services |
| Environment vars | Validates .env file | Set missing variables |
| DNS | Confirms domain setup | Update DNS records |

## Integration with CI/CD

Add to your GitHub Actions workflow:
```yaml
- name: Pre-deployment validation
  run: |
    ./pre-deploy-check.sh ${{ secrets.DOMAIN }} ${{ secrets.SERVER_IP }}
```