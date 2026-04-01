# Radio FM Cumilla - Complete News & Radio Portal

## 🎯 Project Overview

**Radio FM Cumilla** is a professional, production-ready Bangladeshi news and radio portal built with PHP 8.x and MySQL 8.x. It features a modern responsive design, multi-language support (Bangla + English), live radio streaming, and a comprehensive admin panel.

## ✨ Features

### Frontend (Public Website)
- ✅ Fully responsive design (Desktop, Tablet, Mobile)
- ✅ Breaking news ticker with animation
- ✅ Multi-language support (Bangla + English)
- ✅ Categories: National, International, Politics, Sports, Entertainment, Technology, Cumilla
- ✅ Live radio player with stream integration
- ✅ Program schedule display
- ✅ Comment system with moderation
- ✅ Social media sharing (Facebook, Twitter, WhatsApp)
- ✅ Dark mode toggle
- ✅ SEO-friendly URLs and meta tags
- ✅ Search functionality
- ✅ Newsletter subscription ready

### Admin Panel (Separate Backend)
- ✅ Secure login with role-based access (Admin, Editor, RJ)
- ✅ Comprehensive dashboard with statistics
- ✅ News management (Add/Edit/Delete, Draft/Publish/Schedule)
- ✅ Category management
- ✅ User management with permissions
- ✅ Live radio program scheduling
- ✅ Comment moderation
- ✅ Site settings configuration
- ✅ Brute force protection
- ✅ CSRF & XSS protection

### Security Features
- ✅ Password hashing (bcrypt)
- ✅ Prepared statements (SQL injection prevention)
- ✅ Input validation & sanitization
- ✅ CSRF token protection
- ✅ Session security
- ✅ File upload security (MIME type checking)
- ✅ Brute force login protection
- ✅ Role-based access control

## 📁 Project Structure

```
/workspace/
├── database.sql                 # MySQL database schema
├── INSTALLATION_GUIDE.md        # Detailed installation instructions
├── README.md                    # This file
│
├── public_html/                 # Frontend Website
│   ├── index.php               # Homepage
│   ├── news.php                # Single news article
│   ├── category.php            # Category listing
│   ├── radio.php               # Live radio page
│   ├── search.php              # Search results
│   ├── includes/
│   │   ├── config.php          # Database & site configuration
│   │   ├── functions.php       # Helper functions
│   │   ├── header.php          # Header template
│   │   └── footer.php          # Footer template
│   ├── assets/
│   │   ├── css/style.css       # Custom styles
│   │   ├── js/main.js          # JavaScript functionality
│   │   └── images/             # Static images
│   └── uploads/                # User uploads directory
│       ├── news/              # News images
│       ├── audio/             # Audio files
│       └── avatars/           # User avatars
│
└── admin/                       # Admin Panel
    ├── index.php               # Dashboard
    ├── login.php               # Admin login
    ├── logout.php              # Logout handler
    ├── includes/
    │   ├── config.php          # Admin configuration
    │   ├── header.php          # Admin header/sidebar
    │   └── footer.php          # Admin footer
    └── uploads/                # Admin uploads
```

## 🚀 Quick Start

### Prerequisites
- PHP 8.0+
- MySQL 8.0+ or MariaDB 10.3+
- Apache/Nginx web server

### Installation

1. **Import Database:**
```bash
mysql -u username -p radio_fm_cumilla < database.sql
```

2. **Configure Settings:**
Edit `public_html/includes/config.php` and `admin/includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'radio_fm_cumilla');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('SITE_URL', 'https://yourdomain.com');
```

3. **Set Permissions:**
```bash
chmod 755 public_html/uploads -R
chmod 755 admin/uploads -R
```

4. **Login to Admin:**
- URL: `yoursite.com/admin/login.php`
- Email: `admin@radiofmcumilla.com`
- Password: `admin123`
- ⚠️ **Change password immediately!**

## 🔒 Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@radiofmcumilla.com | admin123 |

## 📊 Database Tables

- `users` - Admin users (Admin, Editor, RJ)
- `categories` - News categories
- `posts` - News articles
- `comments` - User comments
- `radio_programs` - Radio show schedules
- `listeners` - Listener tracking
- `settings` - Site configuration

## 🛡️ Security Best Practices

1. Change default admin password immediately
2. Enable HTTPS/SSL certificate
3. Regular backups of database and files
4. Keep PHP and MySQL updated
5. Use strong database passwords
6. Enable firewall protection
7. Monitor error logs regularly

## 📱 Responsive Design

The portal is fully responsive using Bootstrap 5:
- Mobile-first approach
- Touch-friendly navigation
- Optimized images
- Fast loading times

## 🎨 Technologies Used

- **Backend:** PHP 8.x with PDO
- **Database:** MySQL 8.x
- **Frontend:** HTML5, CSS3, JavaScript ES6+
- **CSS Framework:** Bootstrap 5.3.2
- **Icons:** Font Awesome 6.4.0
- **Fonts:** Google Fonts (Hind Siliguri for Bangla)
- **Audio:** HTML5 Audio API

## 📄 License

Proprietary - All rights reserved to Radio FM Cumilla

## 🤝 Support

For technical support or custom development, please refer to the INSTALLATION_GUIDE.md file.

---

**Version:** 1.0.0  
**Build Date:** 2024  
**Status:** Production Ready
