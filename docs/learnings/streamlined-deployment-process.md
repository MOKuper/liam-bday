# Streamlined Deployment Process

## Executive Summary
Based on our deployment experience, here's a streamlined process that turns a 2-hour debugging session into a 10-minute deployment.

## The 10-Minute Deployment

### Prerequisites (One-time setup)
```bash
# 1. Run server infrastructure setup (5 minutes)
wget https://yourrepo.com/setup/infrastructure.sh
chmod +x infrastructure.sh
./infrastructure.sh

# 2. Configure DNS (varies)
# Point domain A records to server IP
```

### Project Deployment (Repeatable)
```bash
# 1. Clone and configure (2 minutes)
cd /opt
git clone git@github.com:yourorg/project.git
cd project
cp .env.production.example .env.production
vim .env.production  # Set APP_URL, DB passwords

# 2. Deploy (3 minutes)
./deploy.sh yourdomain.com production

# 3. Verify (1 minute)
curl https://yourdomain.com
```

## Complete Deployment Workflow

### Phase 1: Local Validation (2 minutes)
```bash
# Run all checks before touching the server
./scripts/validate-all.sh

✅ Docker build test............ PASS
✅ Configuration files.......... PASS
✅ Environment variables........ PASS
✅ Port requirements............ PASS
```

### Phase 2: Server Setup (5 minutes, first time only)
```bash
# Automated server preparation
ssh root@server './setup-server.sh myapp mydomain.com'

Installing Docker............... ✓
Installing Nginx................ ✓
Configuring SSL................. ✓
Creating project structure...... ✓
```

### Phase 3: Application Deployment (3 minutes)
```bash
# One command deployment
./deploy.sh mydomain.com production

🚀 Pulling latest code.......... ✓
🐳 Building containers.......... ✓
📦 Installing dependencies...... ✓
🗄️  Running migrations.......... ✓
🔧 Optimizing application....... ✓
✅ Health check passed!
```

## Key Improvements vs Original Process

| Original Issue | Time Lost | Streamlined Solution | Time Saved |
|----------------|-----------|---------------------|------------|
| GitHub auth errors | 20 min | Pre-configured SSH | 20 min |
| Missing dependencies | 15 min | Validated Dockerfile | 15 min |
| Directory permissions | 10 min | Auto-created with perms | 10 min |
| Nginx config errors | 15 min | Tested templates | 15 min |
| Port conflicts | 10 min | Pre-flight port check | 10 min |
| SSL setup confusion | 20 min | Automated certbot | 20 min |
| HTTPS proxy issues | 30 min | Pre-configured middleware | 30 min |
| **Total** | **2 hours** | **10 minutes** | **110 min saved** |

## The Magic: Automation Scripts

### 1. Master Deployment Script
```bash
#!/bin/bash
# deploy-master.sh - The only script you need

set -e

COMMAND=${1:-"help"}
DOMAIN=${2:-""}
ENV=${3:-"production"}

case $COMMAND in
  "validate")
    ./scripts/validate-all.sh
    ;;
    
  "setup")
    ./scripts/setup-infrastructure.sh $DOMAIN
    ;;
    
  "deploy")
    ./scripts/validate-all.sh || exit 1
    ./scripts/deploy-app.sh $DOMAIN $ENV
    ;;
    
  "rollback")
    ./scripts/rollback.sh
    ;;
    
  "help")
    echo "Usage: ./deploy-master.sh [command] [domain] [env]"
    echo "Commands:"
    echo "  validate - Run pre-deployment checks"
    echo "  setup    - Initial server setup"
    echo "  deploy   - Deploy application"
    echo "  rollback - Rollback to previous version"
    ;;
esac
```

### 2. Emergency Rollback
```bash
#!/bin/bash
# rollback.sh - When things go wrong

# Keep last 3 deployments
docker tag myapp:latest myapp:rollback
docker-compose down
docker tag myapp:previous myapp:latest
docker-compose up -d

echo "✅ Rolled back to previous version"
```

## Project Structure for Success

```
your-laravel-app/
├── .github/
│   └── workflows/
│       └── deploy.yml          # CI/CD pipeline
├── docker/
│   ├── nginx/
│   │   └── app.conf           # Tested nginx config
│   ├── supervisor/
│   │   └── supervisord.conf   # Process management
│   └── php/
│       └── custom.ini         # PHP optimization
├── scripts/
│   ├── validate-all.sh        # Pre-flight checks
│   ├── deploy-app.sh          # Deployment logic
│   ├── health-check.sh        # Post-deploy verification
│   └── rollback.sh            # Emergency rollback
├── docker-compose.production.yml
├── Dockerfile.production
├── .env.production.example
├── deploy.sh                  # Main entry point
└── README.md                  # Clear instructions
```

## Deployment Checklist

### Before First Deployment
- [ ] Server has Docker, Nginx, Certbot installed
- [ ] SSH keys configured for GitHub
- [ ] DNS pointing to server IP
- [ ] Firewall allows 80, 443, 22
- [ ] .env.production configured

### For Each Deployment
- [ ] Run local validation suite
- [ ] Commit and push all changes
- [ ] Tag release version
- [ ] Run deployment script
- [ ] Verify health check passes
- [ ] Check application logs
- [ ] Test critical user paths

## Common Commands Reference

```bash
# View logs
docker-compose logs -f app

# Enter container
docker exec -it myapp-app bash

# Run artisan commands
docker-compose exec app php artisan cache:clear

# Database backup
docker exec myapp-mysql mysqldump -u root -p dbname > backup.sql

# Quick health check
curl -sI https://mydomain.com | head -1

# Resource usage
docker stats

# Restart everything
docker-compose restart
```

## Monitoring and Alerts

### Simple Monitoring Script
```bash
#!/bin/bash
# monitor.sh - Run via cron every 5 minutes

URL="https://yourdomain.com"
SLACK_WEBHOOK="https://hooks.slack.com/..."

# Check site is up
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" $URL)

if [ $HTTP_CODE -ne 200 ]; then
  curl -X POST $SLACK_WEBHOOK \
    -H 'Content-type: application/json' \
    --data "{\"text\":\"🚨 Site down! HTTP $HTTP_CODE\"}"
fi

# Check disk space
DISK_USAGE=$(df -h / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
  curl -X POST $SLACK_WEBHOOK \
    -H 'Content-type: application/json' \
    --data "{\"text\":\"💾 Disk usage critical: $DISK_USAGE%\"}"
fi
```

## Lessons Learned Summary

1. **Automate Everything**: Manual steps = potential failures
2. **Validate Early**: Catch issues before they hit production
3. **Template Everything**: Reuse proven configurations
4. **Script the Happy Path**: Make success the default
5. **Plan for Failure**: Always have a rollback plan
6. **Monitor Proactively**: Know about issues before users do

## Next Project Setup

For your next Laravel project:

```bash
# 1. Clone the template
git clone https://github.com/yourorg/laravel-docker-template myproject
cd myproject

# 2. Customize
./setup.sh myproject mydomain.com

# 3. Deploy
./deploy.sh mydomain.com production

# Done in 10 minutes! 🎉
```

## Conclusion

By implementing these templates and scripts, we've transformed a complex, error-prone deployment process into a streamlined, reliable operation. The investment in automation pays off immediately on the second deployment.

Remember: **Every manual step is a future failure point. Automate it!**