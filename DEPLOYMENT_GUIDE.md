# Deployment Guide - Barangay Submission and Report System

## 🚀 Production Deployment Guide

This guide provides step-by-step instructions for deploying the Barangay Submission and Report System to a production environment.

---

## 📋 Prerequisites

### **System Requirements**
- **Operating System**: Ubuntu 20.04+ / CentOS 8+ / Windows Server 2019+
- **PHP**: 8.2 or higher
- **MySQL**: 5.7+ or MariaDB 10.3+
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Memory**: Minimum 2GB RAM (4GB+ recommended)
- **Storage**: Minimum 20GB free space
- **SSL Certificate**: For HTTPS (Let's Encrypt recommended)

### **Required Software**
- **Composer**: PHP dependency manager
- **Node.js**: 16+ (for asset compilation)
- **Git**: Version control
- **Supervisor**: Process management (for queues)

---

## 🛠️ Server Setup

### **1. Update System Packages**
```bash
# Ubuntu/Debian
sudo apt update && sudo apt upgrade -y

# CentOS/RHEL
sudo yum update -y
```

### **2. Install PHP 8.2+**
```bash
# Ubuntu/Debian
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl -y

# CentOS/RHEL
sudo yum install epel-release -y
sudo yum install https://rpms.remirepo.net/enterprise/remi-release-8.rpm -y
sudo yum module enable php:remi-8.2 -y
sudo yum install php php-cli php-fpm php-mysqlnd php-xml php-mbstring php-curl php-zip php-gd php-bcmath php-intl -y
```

### **3. Install MySQL/MariaDB**
```bash
# Ubuntu/Debian
sudo apt install mysql-server -y

# CentOS/RHEL
sudo yum install mysql-server -y

# Start and enable MySQL
sudo systemctl start mysql
sudo systemctl enable mysql
```

### **4. Install Nginx**
```bash
# Ubuntu/Debian
sudo apt install nginx -y

# CentOS/RHEL
sudo yum install nginx -y

# Start and enable Nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### **5. Install Composer**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### **6. Install Node.js**
```bash
# Using NodeSource repository
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Or using snap
sudo snap install node --classic
```

---

## 🗄️ Database Setup

### **1. Create Database and User**
```sql
-- Login to MySQL
mysql -u root -p

-- Create database
CREATE DATABASE dilg_barangay_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'dilg_user'@'localhost' IDENTIFIED BY 'secure_password_here';

-- Grant privileges
GRANT ALL PRIVILEGES ON dilg_barangay_system.* TO 'dilg_user'@'localhost';
FLUSH PRIVILEGES;

-- Exit MySQL
EXIT;
```

### **2. Test Database Connection**
```bash
mysql -u dilg_user -p dilg_barangay_system
```

---

## 📁 Application Deployment

### **1. Clone Repository**
```bash
# Create application directory
sudo mkdir -p /var/www/dilg-system
sudo chown -R www-data:www-data /var/www/dilg-system

# Clone repository
cd /var/www/dilg-system
sudo -u www-data git clone https://github.com/alexandernicolebravo/barangay-submission-report-system.git .

# Or if using SSH
sudo -u www-data git clone git@github.com:alexandernicolebravo/barangay-submission-report-system.git .
```

### **2. Install Dependencies**
```bash
cd /var/www/dilg-system

# Install PHP dependencies
sudo -u www-data composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
sudo -u www-data npm install

# Build assets
sudo -u www-data npm run production
```

### **3. Environment Configuration**
```bash
# Copy environment file
sudo -u www-data cp .env.example .env

# Generate application key
sudo -u www-data php artisan key:generate

# Edit environment file
sudo nano .env
```

### **4. Environment Variables (.env)**
```env
APP_NAME="DILG Barangay System"
APP_ENV=production
APP_KEY=base64:your_generated_key_here
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dilg_barangay_system
DB_USERNAME=dilg_user
DB_PASSWORD=secure_password_here

BROADCAST_DRIVER=pusher
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

PUSHER_APP_ID=your-pusher-app-id
PUSHER_APP_KEY=your-pusher-app-key
PUSHER_APP_SECRET=your-pusher-app-secret
PUSHER_HOST=your-pusher-host
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### **5. Database Migration and Seeding**
```bash
# Run migrations
sudo -u www-data php artisan migrate --force

# Seed initial data
sudo -u www-data php artisan db:seed --force

# Create storage link
sudo -u www-data php artisan storage:link
```

### **6. Set Permissions**
```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/dilg-system
sudo chmod -R 755 /var/www/dilg-system
sudo chmod -R 775 /var/www/dilg-system/storage
sudo chmod -R 775 /var/www/dilg-system/bootstrap/cache
```

---

## 🌐 Web Server Configuration

### **Nginx Configuration**

Create `/etc/nginx/sites-available/dilg-system`:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/dilg-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Handle Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Security headers
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # File upload size
    client_max_body_size 25M;
}
```

### **Enable Site**
```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/dilg-system /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### **PHP-FPM Configuration**

Edit `/etc/php/8.2/fpm/pool.d/www.conf`:
```ini
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

php_admin_value[upload_max_filesize] = 25M
php_admin_value[post_max_size] = 25M
php_admin_value[max_execution_time] = 300
php_admin_value[memory_limit] = 256M
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

---

## 🔒 SSL Certificate Setup

### **Using Let's Encrypt (Certbot)**
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtain certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Test renewal
sudo certbot renew --dry-run
```

### **Automatic Renewal**
```bash
# Add to crontab
sudo crontab -e

# Add this line
0 12 * * * /usr/bin/certbot renew --quiet
```

---

## ⚙️ Queue Configuration

### **1. Install Supervisor**
```bash
sudo apt install supervisor -y
```

### **2. Create Queue Worker Configuration**

Create `/etc/supervisor/conf.d/dilg-queue.conf`:
```ini
[program:dilg-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/dilg-system/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/dilg-system/storage/logs/queue-worker.log
stopwaitsecs=3600
```

### **3. Start Queue Worker**
```bash
# Reread configuration
sudo supervisorctl reread

# Update configuration
sudo supervisorctl update

# Start queue worker
sudo supervisorctl start dilg-queue-worker:*
```

---

## 🔄 Application Optimization

### **1. Laravel Optimizations**
```bash
cd /var/www/dilg-system

# Cache configuration
sudo -u www-data php artisan config:cache

# Cache routes
sudo -u www-data php artisan route:cache

# Cache views
sudo -u www-data php artisan view:cache

# Optimize autoloader
sudo -u www-data composer dump-autoload --optimize
```

### **2. Set Up Cron Jobs**
```bash
# Edit crontab
sudo crontab -e

# Add Laravel scheduler
* * * * * cd /var/www/dilg-system && php artisan schedule:run >> /dev/null 2>&1
```

### **3. Log Rotation**
Create `/etc/logrotate.d/dilg-system`:
```
/var/www/dilg-system/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
    postrotate
        sudo systemctl reload php8.2-fpm
    endscript
}
```

---

## 📊 Monitoring Setup

### **1. Install Monitoring Tools**
```bash
# Install htop for system monitoring
sudo apt install htop -y

# Install fail2ban for security
sudo apt install fail2ban -y
```

### **2. Configure Fail2ban**

Create `/etc/fail2ban/jail.local`:
```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[nginx-http-auth]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log

[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log
maxretry = 10
```

### **3. Set Up Log Monitoring**
```bash
# Install logwatch
sudo apt install logwatch -y

# Configure logwatch
sudo nano /etc/logwatch/conf/logwatch.conf
```

---

## 🚀 Deployment Script

Create `deploy.sh`:
```bash
#!/bin/bash

# Deployment script for DILG Barangay System
set -e

echo "Starting deployment..."

# Navigate to application directory
cd /var/www/dilg-system

# Pull latest changes
echo "Pulling latest changes..."
sudo -u www-data git pull origin main

# Install/update dependencies
echo "Installing dependencies..."
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data npm install
sudo -u www-data npm run production

# Run migrations
echo "Running migrations..."
sudo -u www-data php artisan migrate --force

# Clear and cache configurations
echo "Optimizing application..."
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Restart services
echo "Restarting services..."
sudo systemctl reload nginx
sudo systemctl restart php8.2-fpm
sudo supervisorctl restart dilg-queue-worker:*

echo "Deployment completed successfully!"
```

Make it executable:
```bash
sudo chmod +x deploy.sh
```

---

## 🔧 Backup Strategy

### **1. Database Backup Script**

Create `backup-db.sh`:
```bash
#!/bin/bash

# Database backup script
BACKUP_DIR="/var/backups/dilg-system"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="dilg_barangay_system"
DB_USER="dilg_user"
DB_PASS="secure_password_here"

# Create backup directory
mkdir -p $BACKUP_DIR

# Create database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Remove backups older than 30 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete

echo "Database backup completed: db_backup_$DATE.sql.gz"
```

### **2. File Backup Script**

Create `backup-files.sh`:
```bash
#!/bin/bash

# File backup script
BACKUP_DIR="/var/backups/dilg-system"
DATE=$(date +%Y%m%d_%H%M%S)
APP_DIR="/var/www/dilg-system"

# Create backup directory
mkdir -p $BACKUP_DIR

# Create file backup (excluding node_modules and vendor)
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='.git' \
    -C $APP_DIR .

# Remove backups older than 30 days
find $BACKUP_DIR -name "files_backup_*.tar.gz" -mtime +30 -delete

echo "File backup completed: files_backup_$DATE.tar.gz"
```

### **3. Automated Backup**

Add to crontab:
```bash
# Daily database backup at 2 AM
0 2 * * * /var/www/dilg-system/backup-db.sh

# Weekly file backup on Sundays at 3 AM
0 3 * * 0 /var/www/dilg-system/backup-files.sh
```

---

## 🚨 Troubleshooting

### **Common Issues**

#### **1. Permission Errors**
```bash
# Fix storage permissions
sudo chown -R www-data:www-data /var/www/dilg-system/storage
sudo chmod -R 775 /var/www/dilg-system/storage
```

#### **2. Database Connection Issues**
```bash
# Check MySQL status
sudo systemctl status mysql

# Test database connection
mysql -u dilg_user -p dilg_barangay_system
```

#### **3. Queue Worker Issues**
```bash
# Check supervisor status
sudo supervisorctl status

# Restart queue workers
sudo supervisorctl restart dilg-queue-worker:*
```

#### **4. Nginx Configuration Issues**
```bash
# Test Nginx configuration
sudo nginx -t

# Check Nginx error logs
sudo tail -f /var/log/nginx/error.log
```

### **Log Files**
- **Application logs**: `/var/www/dilg-system/storage/logs/laravel.log`
- **Nginx logs**: `/var/log/nginx/error.log`, `/var/log/nginx/access.log`
- **PHP-FPM logs**: `/var/log/php8.2-fpm.log`
- **Queue worker logs**: `/var/www/dilg-system/storage/logs/queue-worker.log`

---

## 📈 Performance Tuning

### **1. PHP Optimization**
Edit `/etc/php/8.2/fpm/php.ini`:
```ini
# Memory and execution limits
memory_limit = 256M
max_execution_time = 300
max_input_time = 300

# File upload settings
upload_max_filesize = 25M
post_max_size = 25M
max_file_uploads = 20

# OPcache settings
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### **2. MySQL Optimization**
Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:
```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
query_cache_type = 1
query_cache_size = 64M
max_connections = 200
```

### **3. Nginx Optimization**
Add to server block:
```nginx
# Gzip compression
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

# Browser caching
location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

---

## 🔐 Security Checklist

- [ ] SSL certificate installed and configured
- [ ] Firewall configured (UFW/iptables)
- [ ] Fail2ban installed and configured
- [ ] Database user has minimal privileges
- [ ] Application files have correct permissions
- [ ] Regular security updates enabled
- [ ] Backup strategy implemented
- [ ] Monitoring and logging configured
- [ ] Strong passwords used
- [ ] Debug mode disabled in production

---

## 📞 Support

For deployment issues or questions:
- **Documentation**: Check this guide and technical documentation
- **Logs**: Review application and server logs
- **GitHub Issues**: Report bugs and feature requests
- **Email Support**: Available for critical issues

---

*This deployment guide is maintained and updated with each release.*
