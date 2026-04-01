# Radio FM Cumilla - Installation & Deployment Guide

## 📋 System Requirements

- **PHP**: 8.0 or higher
- **MySQL**: 8.0 or higher (or MariaDB 10.3+)
- **Web Server**: Apache with mod_rewrite or Nginx
- **Extensions**: PDO, PDO_MySQL, GD, Fileinfo, mbstring
- **SSL Certificate**: Required for production (HTTPS)

---

## 🚀 Installation Steps

### Step 1: Database Setup

1. Create a new MySQL database:
```sql
CREATE DATABASE radio_fm_cumilla CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import the database schema:
```bash
mysql -u username -p radio_fm_cumilla < database.sql
```

Or use phpMyAdmin to import `database.sql` file.

### Step 2: File Configuration

1. Upload all files to your web server:
   - Frontend files: `/public_html/` → Your website root
   - Admin files: `/admin/` → admin.radiofmcumilla.com or /admin subdirectory

2. Update configuration files:

**Frontend Config** (`public_html/includes/config.php`):
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'radio_fm_cumilla');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('SITE_URL', 'https://radiofmcumilla.com');
define('ADMIN_URL', 'https://admin.radiofmcumilla.com');
```

**Admin Config** (`admin/includes/config.php`):
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'radio_fm_cumilla');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('ADMIN_URL', 'https://admin.radiofmcumilla.com');
define('SITE_URL', 'https://radiofmcumilla.com');
```

### Step 3: Set Permissions

```bash
chmod 755 public_html/uploads
chmod 755 public_html/uploads/news
chmod 755 public_html/uploads/audio
chmod 755 public_html/uploads/avatars
chmod 755 admin/uploads
chmod 644 public_html/includes/config.php
chmod 644 admin/includes/config.php
```

### Step 4: First Login

1. Visit: `https://admin.radiofmcumilla.com/login.php`
2. Default credentials:
   - **Email**: `admin@radiofmcumilla.com`
   - **Password**: `admin123`
3. ⚠️ **IMPORTANT**: Change password immediately after first login!

### Step 5: Configure Radio Stream

1. Login to admin panel
2. Go to Settings
3. Add your radio stream URL (e.g., from Zeno.fm, Shoutcast, Icecast)
4. Example: `https://stream.zeno.fm/your_stream_key`

---

## 🔒 Security Hardening

### Essential Security Measures

1. **Change Default Admin Password Immediately**
```sql
UPDATE users SET password = '$2y$10$YOUR_NEW_HASH' WHERE email = 'admin@radiofmcumilla.com';
```

2. **Enable HTTPS**
   - Get SSL certificate (Let's Encrypt is free)
   - Force HTTPS redirect in .htaccess

3. **Update .htaccess** (Apache):
```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Protect config files
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>

# Prevent directory listing
Options -Indexes
```

4. **Secure Database**:
   - Use strong database password
   - Restrict database user privileges
   - Change database prefix from default

5. **File Upload Security**:
   - Already implemented MIME type checking
   - File size limits enforced
   - Unique filenames generated

6. **Session Security**:
   - Secure cookies enabled
   - Session regeneration on login
   - Brute force protection implemented

### Additional Recommendations

- Enable Web Application Firewall (WAF)
- Set up regular backups
- Implement CDN for static assets
- Enable DDoS protection (Cloudflare)
- Regular security updates
- Monitor error logs
- Disable PHP error display in production

---

## 📁 Directory Structure

```
radiofmcumilla/
├── database.sql                 # Database schema
├── public_html/                 # Frontend Website
│   ├── includes/
│   │   ├── config.php          # Configuration
│   │   ├── functions.php       # Helper functions
│   │   ├── header.php          # Header template
│   │   └── footer.php          # Footer template
│   ├── assets/
│   │   ├── css/style.css       # Custom styles
│   │   ├── js/main.js          # JavaScript
│   │   └── images/             # Static images
│   ├── uploads/                # User uploads
│   │   ├── news/              # News images
│   │   ├── audio/             # Audio files
│   │   └── avatars/           # User avatars
│   ├── index.php              # Homepage
│   ├── news.php               # Single news page
│   ├── category.php           # Category page
│   ├── radio.php              # Live radio page
│   └── search.php             # Search page
│
└── admin/                      # Admin Panel
    ├── includes/
    │   ├── config.php         # Admin configuration
    │   ├── header.php         # Admin header
    │   └── footer.php         # Admin footer
    ├── login.php              # Login page
    ├── logout.php             # Logout handler
    ├── index.php              # Dashboard
    ├── posts.php              # News management
    ├── post_add.php           # Add news
    ├── post_edit.php          # Edit news
    ├── categories.php         # Category management
    ├── comments.php           # Comment moderation
    ├── radio.php              # Radio management
    ├── users.php              # User management
    └── settings.php           # Site settings
```

---

## ⚙️ Configuration Options

### Radio Streaming Services

Recommended free/paid streaming services:
- **Zeno.fm** (Free)
- **Shoutcast** (Paid)
- **Icecast** (Free/Self-hosted)
- **Radio.co** (Paid)
- **Live365** (Paid)

### Email Configuration (for future features)

Add to config.php:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your@email.com');
define('SMTP_PASS', 'your_password');
```

---

## 🛠️ Troubleshooting

### Common Issues

**1. Database Connection Error**
- Check database credentials in config.php
- Verify database exists
- Check MySQL service is running

**2. Permission Denied**
```bash
chmod -R 755 uploads/
chown -R www-data:www-data uploads/
```

**3. Images Not Uploading**
- Check upload_max_filesize in php.ini
- Verify uploads folder permissions
- Check GD extension is enabled

**4. Session Issues**
- Ensure session_start() is called
- Check session.save_path permissions
- Verify cookies are enabled

**5. 404 Errors**
- Enable mod_rewrite in Apache
- Check .htaccess file exists
- Verify SITE_URL constant

---

## 📊 Performance Optimization

### Recommended Settings

**PHP (php.ini)**:
```ini
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
opcache.enable = 1
```

**MySQL**:
```sql
SET GLOBAL query_cache_size = 67108864;
SET GLOBAL query_cache_type = 1;
```

### Caching

1. Enable browser caching (.htaccess):
```apache
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

2. Use CDN for static assets
3. Enable Gzip compression

---

## 📱 Mobile App Integration (Future)

API endpoints can be added for mobile apps:
- `/api/news` - Get latest news
- `/api/radio` - Get stream URL
- `/api/categories` - Get categories

---

## 🔄 Backup Strategy

### Automated Backup Script

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -p password radio_fm_cumilla > backup_db_$DATE.sql
tar -czf backup_files_$DATE.tar.gz public_html/ admin/
```

### Schedule with Cron:
```cron
0 2 * * * /path/to/backup.sh
```

---

## 📞 Support & Maintenance

### Regular Maintenance Tasks

1. **Daily**: Monitor error logs
2. **Weekly**: Review pending comments, check backups
3. **Monthly**: Update software, review analytics
4. **Quarterly**: Security audit, performance review

### Useful Commands

```bash
# Check PHP errors
tail -f /var/log/apache2/error.log

# Monitor database
mysqlcheck -u root -p --auto-repair --check radio_fm_cumilla

# Clear cache
find . -name "*.cache" -delete
```

---

## ✅ Pre-Launch Checklist

- [ ] Changed default admin password
- [ ] Enabled HTTPS/SSL
- [ ] Configured radio stream URL
- [ ] Tested file uploads
- [ ] Verified comment system
- [ ] Set up automated backups
- [ ] Configured error logging
- [ ] Tested mobile responsiveness
- [ ] Added at least 5 news articles
- [ ] Created user accounts for staff
- [ ] Tested radio player
- [ ] Configured SEO meta tags
- [ ] Set up Google Analytics
- [ ] Tested contact forms
- [ ] Verified social media links

---

## 🎉 You're Ready!

Your Radio FM Cumilla portal is now ready to broadcast!

**Frontend**: https://radiofmcumilla.com  
**Admin**: https://admin.radiofmcumilla.com

For technical support or custom development, contact your system administrator.

---

**Version**: 1.0.0  
**Last Updated**: 2024  
**License**: Proprietary
