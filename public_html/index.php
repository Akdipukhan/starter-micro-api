<?php
/**
 * Homepage - Radio FM Cumilla
 */

session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get data for homepage
$settings = getSettings();
$categories = getCategories();
$breaking_news = getBreakingNews();
$latest_news = getPosts(['limit' => 9]);
$trending_news = getTrendingPosts(5);
$live_program = getCurrentLiveProgram();

$page_title = 'Home';
include 'includes/header.php';
?>

<!-- Radio Player Section -->
<div class="container mt-4">
    <div class="radio-player">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3><i class="fas fa-broadcast-tower"></i> লাইভ রেডিও স্ট্রিম</h3>
                <?php if ($live_program): ?>
                <p class="mb-2"><span class="live-indicator">LIVE NOW</span></p>
                <h4><?php echo $live_program['title']; ?></h4>
                <p class="mb-0"><i class="fas fa-user-microphone"></i> <?php echo $live_program['rj_name']; ?></p>
                <?php else: ?>
                <p>Currently offline - Check program schedule</p>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-center">
                <audio id="radioPlayer" controls preload="none" style="width: 100%;">
                    <source src="<?php echo $settings['stream_url']; ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div class="container my-5">
    <div class="row">
        <!-- Left Column - Latest News -->
        <div class="col-lg-8">
            <h2 class="widget-title">সর্বশেষ সংবাদ</h2>
            <div class="row">
                <?php foreach ($latest_news as $news): ?>
                <div class="col-md-6">
                    <div class="card news-card h-100">
                        <?php if (!empty($news['featured_image'])): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/news/<?php echo $news['featured_image']; ?>" 
                             class="card-img-top" alt="<?php echo $news['title_bn']; ?>">
                        <?php else: ?>
                        <img src="<?php echo SITE_URL; ?>/assets/images/placeholder.jpg" 
                             class="card-img-top" alt="No Image">
                        <?php endif; ?>
                        <div class="card-body">
                            <span class="badge bg-danger mb-2"><?php echo $news['category_name_bn']; ?></span>
                            <h5 class="news-title">
                                <a href="<?php echo SITE_URL; ?>/news.php?slug=<?php echo $news['slug']; ?>" 
                                   class="text-decoration-none text-dark">
                                    <?php echo $news['title_bn']; ?>
                                </a>
                            </h5>
                            <div class="news-meta mt-2">
                                <small><i class="fas fa-clock"></i> <?php echo date('d M Y, h:i A', strtotime($news['created_at'])); ?></small>
                                <span class="ms-2"><i class="fas fa-eye"></i> <?php echo $news['views']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="col-lg-4">
            <!-- Trending News Widget -->
            <div class="sidebar-widget">
                <h4 class="widget-title">জনপ্রিয় সংবাদ</h4>
                <?php foreach ($trending_news as $news): ?>
                <div class="mb-3 pb-3 border-bottom">
                    <a href="<?php echo SITE_URL; ?>/news.php?slug=<?php echo $news['slug']; ?>" 
                       class="text-decoration-none text-dark">
                        <h6 class="mb-1"><?php echo $news['title_bn']; ?></h6>
                    </a>
                    <small class="text-muted"><i class="fas fa-eye"></i> <?php echo $news['views']; ?> views</small>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Ad Space -->
            <div class="sidebar-widget text-center">
                <h4 class="widget-title">বিজ্ঞাপন</h4>
                <div style="background: #e9ecef; height: 250px; display: flex; align-items: center; justify-content: center;">
                    <p class="text-muted">Advertisement Space (300x250)</p>
                </div>
            </div>

            <!-- Categories Widget -->
            <div class="sidebar-widget">
                <h4 class="widget-title">ক্যাটাগরি</h4>
                <ul class="list-group list-group-flush">
                    <?php foreach ($categories as $cat): ?>
                    <li class="list-group-item bg-transparent">
                        <a href="<?php echo SITE_URL; ?>/category.php?slug=<?php echo $cat['slug']; ?>" 
                           class="text-decoration-none">
                            <i class="fas fa-angle-right text-danger"></i> <?php echo $cat['name_bn']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Program Schedule Section -->
<div class="bg-light py-5">
    <div class="container">
        <h2 class="widget-title">আজকের অনুষ্ঠানসূচি</h2>
        <div class="table-responsive">
            <table class="table table-hover bg-white rounded shadow-sm">
                <thead class="bg-danger text-white">
                    <tr>
                        <th>সময়</th>
                        <th>অনুষ্ঠান</th>
                        <th>উপস্থাপনায়</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $programs = getRadioPrograms(7);
                    foreach ($programs as $prog): 
                    ?>
                    <tr>
                        <td><?php echo date('g:i A', strtotime($prog['start_time'])); ?> - <?php echo date('g:i A', strtotime($prog['end_time'])); ?></td>
                        <td><?php echo $prog['title']; ?></td>
                        <td><i class="fas fa-user-microphone"></i> <?php echo $prog['rj_name']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
