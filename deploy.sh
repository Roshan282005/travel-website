#!/bin/bash

# TravelGo Deployment Script for VPS/Cloud
# Usage: bash deploy.sh

echo " TravelGo Deployment Script"
echo "================================"
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo " Please run as root: sudo bash deploy.sh"
    exit 1
fi

# Update system
echo " Updating system packages..."
apt update && apt upgrade -y

# Install LAMP stack
echo "🔧 Installing LAMP stack..."
apt install -y apache2 mysql-server php php-mysql php-curl php-json php-mbstring php-xml libapache2-mod-php

# Enable Apache modules
echo " Enabling Apache modules..."
a2enmod rewrite
a2enmod headers
a2enmod ssl

# Start services
echo "▶  Starting services..."
systemctl start apache2
systemctl start mysql
systemctl enable apache2
systemctl enable mysql

# Configure MySQL
echo "🗄️  Configuring MySQL..."
read -p "Enter MySQL root password: " MYSQL_ROOT_PASS
read -p "Enter database name (default: travel_db): " DB_NAME
DB_NAME=${DB_NAME:-travel_db}
read -p "Enter database user (default: travel_user): " DB_USER
DB_USER=${DB_USER:-travel_user}
read -p "Enter database password: " DB_PASS

mysql -u root -p"$MYSQL_ROOT_PASS" <<EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF

echo " Database created successfully!"

# Import database
if [ -f "database_enhanced.sql" ]; then
    echo " Importing database..."
    mysql -u root -p"$MYSQL_ROOT_PASS" $DB_NAME < database_enhanced.sql
    echo " Database imported!"
else
    echo "  database_enhanced.sql not found. Please import manually."
fi

# Update db.php
echo " Updating db.php..."
cat > /var/www/html/db.php <<EOF
<?php
\$host = "localhost";
\$user = "$DB_USER";
\$pass = "$DB_PASS";
\$dbname = "$DB_NAME";

\$conn = new mysqli(\$host, \$user, \$pass, \$dbname);

if (\$conn->connect_error) {
    die("Database Connection Failed: " . \$conn->connect_error);
}
?>
EOF

# Set permissions
echo " Setting file permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod 644 /var/www/html/db.php

# Install Composer
echo " Installing Composer..."
cd /tmp
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Install PHP dependencies
if [ -f "/var/www/html/composer.json" ]; then
    echo " Installing PHP dependencies..."
    cd /var/www/html
    composer install --no-dev
fi

# Configure firewall
echo " Configuring firewall..."
apt install -y ufw
ufw allow 22
ufw allow 80
ufw allow 443
ufw --force enable

# SSL Certificate (Let's Encrypt)
read -p "Do you want to install SSL certificate? (y/n): " INSTALL_SSL
if [ "$INSTALL_SSL" = "y" ]; then
    read -p "Enter your domain name (e.g., example.com): " DOMAIN
    
    apt install -y certbot python3-certbot-apache
    certbot --apache -d $DOMAIN -d www.$DOMAIN
    
    echo " SSL certificate installed!"
    echo "  Remember to add $DOMAIN to Firebase authorized domains"
fi

# Create backup script
echo "💾 Creating backup script..."
cat > /root/backup_travelgo.sh <<'EOF'
#!/bin/bash
BACKUP_DIR="/root/backups"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u travel_user -p travel_db > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/html

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
EOF

chmod +x /root/backup_travelgo.sh

# Add to crontab for daily backups at 2 AM
(crontab -l 2>/dev/null; echo "0 2 * * * /root/backup_travelgo.sh") | crontab -

echo ""
echo " Deployment Complete!"
echo "================================"
echo "📍 Your site is accessible at:"
echo "   http://$(hostname -I | awk '{print $1}')"
if [ ! -z "$DOMAIN" ]; then
    echo "   https://$DOMAIN"
fi
echo ""
echo "  Database Info:"
echo "   Name: $DB_NAME"
echo "   User: $DB_USER"
echo "   Password: $DB_PASS"
echo ""
echo " Next Steps:"
echo "   1. Update Firebase authorized domains"
echo "   2. Test website functionality"
echo "   3. Configure email settings"
echo "   4. Setup Google Analytics"
echo ""
echo " Backups: Configured to run daily at 2 AM"
echo "   Location: /root/backups/"
echo ""
echo " Security: Firewall enabled (ports 22, 80, 443)"
echo ""
echo " All done! Your TravelGo website is now live!"