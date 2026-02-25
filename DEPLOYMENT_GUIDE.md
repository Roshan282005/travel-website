# TravelGo - Complete Deployment Guide

## 🚀 Deployment Instructions

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer (for PHP dependencies)
- Node.js 18+ (optional, for React components)
- Firebase account (for authentication)
- Web hosting with PHP and MySQL support

---

## 📋 Step-by-Step Deployment

### 1. **Prepare Your Environment**

#### A. Local Development Setup (XAMPP/WAMP)
```bash
# 1. Install XAMPP/WAMP/MAMP
# 2. Clone or copy the project to htdocs folder
cp -r travel-website /path/to/xampp/htdocs/

# 3. Start Apache and MySQL services
```

#### B. Production Server (Linux/Ubuntu)
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install LAMP stack
sudo apt install apache2 mysql-server php php-mysql php-curl php-json php-mbstring -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Enable Apache modules
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

### 2. **Database Setup**

```bash
# 1. Access MySQL
mysql -u root -p

# 2. Create database and user
CREATE DATABASE travel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'travel_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON travel_db.* TO 'travel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# 3. Import database schema
mysql -u root -p travel_db < database_enhanced.sql
```

**Update db.php with your credentials:**
```php
<?php
$host = "localhost";
$user = "travel_user";
$pass = "your_secure_password";
$dbname = "travel_db";
?>
```

---

### 3. **Install Dependencies**

```bash
# Install PHP dependencies
cd /path/to/travel-website
composer install

# Install Node.js dependencies (if using React components)
npm install
```

---

### 4. **Firebase Configuration**

#### A. Create Firebase Project
1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Create a new project or use existing one
3. Enable **Authentication** → Email/Password and Google providers
4. Get your Firebase config from Project Settings

#### B. Update Firebase Config
Replace Firebase credentials in `login.php` and `signup.php`:

```javascript
const firebaseConfig = {
  apiKey: "YOUR_API_KEY",
  authDomain: "YOUR_PROJECT.firebaseapp.com",
  projectId: "YOUR_PROJECT_ID",
  storageBucket: "YOUR_PROJECT.appspot.com",
  messagingSenderId: "YOUR_SENDER_ID",
  appId: "YOUR_APP_ID"
};
```

#### C. Configure Google OAuth
1. In Firebase Console → Authentication → Sign-in method
2. Enable Google provider
3. Add authorized domains (your production domain)
4. Download OAuth credentials

---

### 5. **File Permissions (Linux)**

```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/html/travel-website
sudo chmod -R 755 /var/www/html/travel-website

# Make upload directories writable
sudo chmod -R 775 /var/www/html/travel-website/assets/images
sudo chmod -R 775 /var/www/html/travel-website/uploads
```

---

### 6. **Apache Configuration**

Create virtual host configuration:

```bash
sudo nano /etc/apache2/sites-available/travelgo.conf
```

Add this configuration:
```apache
<VirtualHost *:80>
    ServerName travelgo.com
    ServerAlias www.travelgo.com
    DocumentRoot /var/www/html/travel-website
    
    <Directory /var/www/html/travel-website>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/travelgo_error.log
    CustomLog ${APACHE_LOG_DIR}/travelgo_access.log combined
</VirtualHost>
```

Enable the site:
```bash
sudo a2ensite travelgo.conf
sudo systemctl reload apache2
```

---

### 7. **SSL Certificate (HTTPS)**

Using Let's Encrypt (Free):
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y

# Get SSL certificate
sudo certbot --apache -d travelgo.com -d www.travelgo.com

# Auto-renewal (cron job)
sudo certbot renew --dry-run
```

---

### 8. **Environment Variables**

Create `.env` file (add to .gitignore):
```env
DB_HOST=localhost
DB_USER=travel_user
DB_PASS=your_secure_password
DB_NAME=travel_db

FIREBASE_API_KEY=your_api_key
FIREBASE_AUTH_DOMAIN=your_domain
FIREBASE_PROJECT_ID=your_project_id

OPENAI_API_KEY=your_openai_key
WEATHER_API_KEY=your_weather_key

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your_email@gmail.com
SMTP_PASS=your_app_password
```

---

## 🌐 Hosting Platforms

### Option 1: Shared Hosting (cPanel)

**Recommended Providers:** Hostinger, Bluehost, SiteGround

1. **Upload Files**
   - Use FTP/SFTP client (FileZilla)
   - Upload all files to public_html folder
   
2. **Create Database**
   - Use cPanel MySQL Database Wizard
   - Import `database_enhanced.sql` via phpMyAdmin
   
3. **Update Credentials**
   - Edit `db.php` with cPanel database details
   
4. **Configure Domain**
   - Point domain to hosting nameservers
   - Wait for DNS propagation (24-48 hours)

---

### Option 2: VPS/Cloud (DigitalOcean, AWS, Linode)

**DigitalOcean Deployment:**

```bash
# 1. Create Droplet (Ubuntu 22.04 LTS)
# 2. SSH into server
ssh root@your_server_ip

# 3. Install LAMP stack
sudo apt update
sudo apt install lamp-server^

# 4. Clone repository
cd /var/www/html
git clone https://github.com/yourusername/travelgo.git

# 5. Configure as shown in previous steps
# 6. Set up firewall
sudo ufw allow 'Apache Full'
sudo ufw enable
```

**AWS EC2 Deployment:**
1. Launch EC2 instance (Ubuntu)
2. Configure security groups (ports 80, 443, 22)
3. Install LAMP stack
4. Clone repository
5. Configure domain with Route 53
6. Add SSL with ACM

---

### Option 3: Free Hosting (Testing Only)

**InfinityFree / 000webhost:**
- Upload via FTP
- Limited PHP/MySQL resources
- Free subdomain included
- Not recommended for production

---

## 🔒 Security Checklist

- [ ] Change all default passwords
- [ ] Enable HTTPS/SSL
- [ ] Set secure file permissions
- [ ] Add `.htaccess` security rules
- [ ] Enable PHP error logging (disable display_errors)
- [ ] Use prepared statements (already implemented)
- [ ] Implement CSRF protection
- [ ] Add rate limiting for API endpoints
- [ ] Regular backups (database + files)
- [ ] Update dependencies regularly
- [ ] Hide sensitive files (.env, config files)

**Sample .htaccess:**
```apache
# Security Headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"

# Disable directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "^\.(.env|htaccess|htpasswd)">
    Require all denied
</FilesMatch>

# Enable GZIP compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

---

## 🧪 Testing Checklist

Before going live:
- [ ] Test user registration and login
- [ ] Test Google OAuth authentication
- [ ] Test booking functionality
- [ ] Test wishlist features
- [ ] Test review submission
- [ ] Test admin dashboard
- [ ] Test map functionality
- [ ] Test responsive design (mobile/tablet)
- [ ] Test email notifications
- [ ] Check all links and images
- [ ] Test payment integration
- [ ] Performance testing (GTmetrix, PageSpeed)

---

## 📊 Performance Optimization

```bash
# Enable OPcache (php.ini)
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=4000

# MySQL optimization (my.cnf)
innodb_buffer_pool_size = 256M
query_cache_size = 64M

# Enable browser caching (.htaccess)
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## 🔄 Maintenance & Updates

### Database Backups
```bash
# Manual backup
mysqldump -u travel_user -p travel_db > backup_$(date +%Y%m%d).sql

# Automated daily backup (crontab)
0 2 * * * mysqldump -u travel_user -p'password' travel_db > /backups/db_$(date +\%Y\%m\%d).sql
```

### Application Updates
```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer update
npm update

# Run database migrations if any
mysql -u travel_user -p travel_db < migrations/latest.sql

# Clear cache
php artisan cache:clear  # if using Laravel
```

---

## 🐛 Troubleshooting

### Common Issues

**1. Database Connection Failed**
```bash
# Check MySQL status
sudo systemctl status mysql

# Test connection
mysql -u travel_user -p travel_db
```

**2. Firebase Authentication Not Working**
- Verify Firebase config credentials
- Check authorized domains in Firebase Console
- Ensure HTTPS is enabled for production

**3. File Upload Errors**
```bash
# Check PHP upload limits (php.ini)
upload_max_filesize = 10M
post_max_size = 10M

# Check directory permissions
sudo chmod 775 /var/www/html/travel-website/uploads
```

**4. 500 Internal Server Error**
```bash
# Check Apache error logs
sudo tail -f /var/log/apache2/error.log

# Check PHP errors
sudo tail -f /var/log/php_errors.log
```

---

## 📱 Mobile App (Optional)

Convert to Progressive Web App (PWA):

1. Create `manifest.json`:
```json
{
  "name": "TravelGo",
  "short_name": "TravelGo",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#0d6efd",
  "icons": [
    {
      "src": "/icon-192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/icon-512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}
```

2. Add service worker for offline support
3. Test with Lighthouse PWA audit

---

## 📞 Support & Documentation

- **GitHub Issues:** Report bugs and request features
- **Documentation:** Available in `/docs` folder
- **API Documentation:** Available at `/api/docs`
- **Admin Guide:** See `ADMIN_GUIDE.md`

---

## 🎉 Go Live Checklist

- [ ] Domain configured and DNS propagated
- [ ] SSL certificate installed and working
- [ ] Database populated with sample data
- [ ] All API keys configured
- [ ] Email functionality tested
- [ ] Backups configured
- [ ] Monitoring set up (Google Analytics, etc.)
- [ ] SEO optimized (meta tags, sitemap)
- [ ] Privacy policy and terms added
- [ ] Contact forms working
- [ ] Payment gateway tested (if applicable)

---

**Congratulations! Your TravelGo website is now deployed! 🎊**

For support: support@travelgo.com
Version: 2.0.0
Last Updated: December 2025