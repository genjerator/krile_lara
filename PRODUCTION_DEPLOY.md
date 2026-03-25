# Production Deployment Guide

## Prerequisites

- Docker and Docker Compose installed on production server
- Domain name configured (optional)
- SSL certificate (recommended - use Let's Encrypt)

## Initial Setup

### 1. Configure Environment Variables

Copy and edit the production environment file:
```bash
cp .env.production .env
```

**Important:** Update these values in `.env`:
- `APP_KEY` - Generate with: `php artisan key:generate`
- `APP_URL` - Your production domain
- `DB_PASSWORD` - Strong database password
- `REDIS_PASSWORD` - Strong Redis password
- Mail settings for your SMTP provider

### 2. Build and Deploy

Run the deployment script:
```bash
./deploy.sh
```

Or manually:
```bash
# Build production image
docker-compose -f docker-compose.prod.yml build --no-cache

# Start containers
docker-compose -f docker-compose.prod.yml up -d

# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Create admin user
docker-compose -f docker-compose.prod.yml exec app php artisan tinker --execute="
\$user = new App\Models\User();
\$user->name = 'Admin';
\$user->email = 'admin@yourdomain.com';
\$user->password = bcrypt('secure_password_here');
\$user->save();
"
```

## Production Features

### Optimizations
- **OPcache** - PHP bytecode caching enabled
- **Config/Route/View caching** - Laravel optimizations
- **Gzip compression** - Nginx compression for static assets
- **Asset caching** - 1-year cache for static files
- **No dev dependencies** - Smaller image size

### Security
- Security headers (X-Frame-Options, CSP, etc.)
- Rate limiting (10 requests/second)
- Hidden PHP version
- Secure session cookies
- Disabled directory listing

### Monitoring
- Health check endpoint: `/health`
- Docker health checks for all services
- Nginx access/error logs
- PHP error logging

## Useful Commands

### View Logs
```bash
# All services
docker-compose -f docker-compose.prod.yml logs -f

# Specific service
docker-compose -f docker-compose.prod.yml logs -f app
docker-compose -f docker-compose.prod.yml logs -f db
docker-compose -f docker-compose.prod.yml logs -f redis
```

### Execute Commands
```bash
# Artisan commands
docker-compose -f docker-compose.prod.yml exec app php artisan <command>

# Database backup
docker-compose -f docker-compose.prod.yml exec db pg_dump -U laravel laravel > backup.sql

# Shell access
docker-compose -f docker-compose.prod.yml exec app bash
```

### Restart Services
```bash
docker-compose -f docker-compose.prod.yml restart
docker-compose -f docker-compose.prod.yml restart app
```

### Update Application
```bash
# Pull latest code
git pull

# Rebuild and redeploy
./deploy.sh
```

## SSL/HTTPS Setup (Recommended)

### Option 1: Using Nginx Proxy with Let's Encrypt

Add a reverse proxy container:
```yaml
nginx-proxy:
  image: jwilder/nginx-proxy
  ports:
    - "80:80"
    - "443:443"
  volumes:
    - /var/run/docker.sock:/tmp/docker.sock:ro
    - ./certs:/etc/nginx/certs

letsencrypt:
  image: jrcs/letsencrypt-nginx-proxy-companion
  volumes:
    - /var/run/docker.sock:/var/run/docker.sock:ro
    - ./certs:/etc/nginx/certs
```

### Option 2: Manual SSL

1. Obtain SSL certificate (Let's Encrypt, CloudFlare, etc.)
2. Update nginx config with SSL settings
3. Mount certificates in docker-compose.prod.yml

## Backup Strategy

### Database Backup
```bash
# Manual backup
docker-compose -f docker-compose.prod.yml exec db pg_dump -U laravel laravel > backup-$(date +%Y%m%d).sql

# Automated daily backup (add to cron)
0 2 * * * cd /path/to/app && docker-compose -f docker-compose.prod.yml exec -T db pg_dump -U laravel laravel | gzip > /backups/db-$(date +\%Y\%m\%d).sql.gz
```

### Application Backup
```bash
# Backup storage and database
tar -czf backup-$(date +%Y%m%d).tar.gz storage/ backup-*.sql
```

## Performance Tuning

### Redis Configuration
Adjust in docker-compose.prod.yml:
```yaml
redis:
  command: redis-server --maxmemory 256mb --maxmemory-policy allkeys-lru
```

### PostgreSQL Tuning
Create `docker/postgres/postgresql.conf` and mount it.

### PHP-FPM Workers
Adjust in `docker/php/php-prod.ini` based on server resources.

## Monitoring

### Health Checks
- Application: `http://yourdomain.com/health`
- Redis: `docker-compose -f docker-compose.prod.yml exec redis redis-cli ping`
- Database: `docker-compose -f docker-compose.prod.yml exec db pg_isready`

### Resource Usage
```bash
docker stats
```

## Troubleshooting

### Clear Cache
```bash
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker-compose -f docker-compose.prod.yml exec app php artisan config:clear
docker-compose -f docker-compose.prod.yml exec app php artisan route:clear
docker-compose -f docker-compose.prod.yml exec app php artisan view:clear
```

### Permission Issues
```bash
docker-compose -f docker-compose.prod.yml exec app chown -R www-data:www-data /var/www/storage
docker-compose -f docker-compose.prod.yml exec app chmod -R 755 /var/www/storage
```

### Database Connection Issues
Check database logs:
```bash
docker-compose -f docker-compose.prod.yml logs db
```

Verify connection from app container:
```bash
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
>>> DB::connection()->getPdo();
```

## Security Checklist

- [ ] Strong passwords for database and Redis
- [ ] APP_DEBUG=false in production
- [ ] SSL/HTTPS enabled
- [ ] Firewall configured (only 80/443 open)
- [ ] Regular backups scheduled
- [ ] Security updates applied regularly
- [ ] Monitoring and alerting configured
- [ ] Rate limiting configured
- [ ] CSRF protection enabled (Laravel default)
- [ ] SQL injection prevention (Eloquent ORM)

## Scaling

### Horizontal Scaling
Add multiple app containers:
```yaml
app:
  scale: 3
```

Add load balancer (nginx-proxy or HAProxy).

### Vertical Scaling
Increase container resources in compose file or adjust PHP/DB settings.
