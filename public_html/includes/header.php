<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php if (!empty($meta_title)): ?>
    <title><?php echo $meta_title; ?></title>
    <?php else: ?>
    <title><?php echo $settings['site_title']; ?> - <?php echo !empty($page_title) ? $page_title : 'Latest News & Radio'; ?></title>
    <?php endif; ?>
    
    <?php if (!empty($meta_description)): ?>
    <meta name="description" content="<?php echo $meta_description; ?>">
    <?php endif; ?>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Hind Siliguri for Bangla) -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <style>
        body {
            font-family: 'Hind Siliguri', sans-serif;
        }
        .breaking-news-ticker {
            background: #c0392b;
            color: white;
            padding: 10px 0;
            overflow: hidden;
            white-space: nowrap;
        }
        .ticker-content {
            display: inline-block;
            animation: ticker 30s linear infinite;
        }
        @keyframes ticker {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: #c0392b !important;
        }
        .category-nav .nav-link {
            color: #333;
            font-weight: 500;
            padding: 10px 15px;
        }
        .category-nav .nav-link:hover {
            color: #c0392b;
            background: #f8f9fa;
        }
        .news-card {
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        .news-card:hover {
            transform: translateY(-5px);
        }
        .news-card img {
            height: 200px;
            object-fit: cover;
        }
        .news-title {
            font-weight: 600;
            font-size: 1.1rem;
            line-height: 1.4;
        }
        .news-meta {
            font-size: 0.85rem;
            color: #666;
        }
        .radio-player {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .live-indicator {
            background: #e74c3c;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .sidebar-widget {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .widget-title {
            font-weight: 700;
            border-bottom: 3px solid #c0392b;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        footer {
            background: #2c3e50;
            color: white;
            padding: 40px 0 20px;
        }
        footer a {
            color: #ecf0f1;
            text-decoration: none;
        }
        footer a:hover {
            color: #c0392b;
        }
        .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            transition: all 0.3s;
        }
        .social-icons a:hover {
            background: #c0392b;
            transform: translateY(-3px);
        }
        /* Dark Mode */
        body.dark-mode {
            background: #1a1a1a;
            color: #e0e0e0;
        }
        body.dark-mode .news-card,
        body.dark-mode .sidebar-widget {
            background: #2d2d2d;
        }
        body.dark-mode .category-nav .nav-link {
            color: #e0e0e0;
        }
        body.dark-mode .category-nav .nav-link:hover {
            background: #3d3d3d;
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar bg-light py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small><i class="fas fa-calendar-alt"></i> <?php echo date('d F Y, l'); ?></small>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-sm btn-outline-secondary" id="darkModeToggle">
                        <i class="fas fa-moon"></i>
                    </button>
                    <a href="#" class="btn btn-sm btn-outline-primary ms-2">English</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <a href="<?php echo SITE_URL; ?>" class="navbar-brand">
                        <i class="fas fa-broadcast-tower"></i> RADIO FM CUMILLA
                    </a>
                </div>
                <div class="col-md-6">
                    <form action="<?php echo SITE_URL; ?>/search.php" method="GET" class="d-flex">
                        <input type="text" name="q" class="form-control me-2" placeholder="সংবাদ খুঁজুন...">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    <a href="<?php echo ADMIN_URL; ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-user-lock"></i> Admin
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse category-nav" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>">হোম</a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/category.php?slug=<?php echo $cat['slug']; ?>">
                            <?php echo $cat['name_bn']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/radio.php"><i class="fas fa-radio"></i> লাইভ রেডিও</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breaking News Ticker -->
    <?php if (!empty($breaking_news)): ?>
    <div class="breaking-news-ticker">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <strong><i class="fas fa-bolt"></i> ব্রেকিং নিউজ:</strong>
                    <span class="ticker-content">
                        <?php foreach ($breaking_news as $news): ?>
                            • <?php echo $news['title_bn']; ?>
                        <?php endforeach; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
