<?php
/**
 * Category Page
 */

session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['slug'])) {
    header('Location: ' . SITE_URL);
    exit;
}

$settings = getSettings();
$categories = getCategories();
$breaking_news = getBreakingNews();
$category = getCategoryBySlug($_GET['slug']);

if (!$category) {
    header('Location: ' . SITE_URL);
    exit;
}

$posts = getPosts(['category_id' => $category['id']]);

$page_title = $category['name_bn'];
include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h2 class="widget-title mb-4">
                <i class="fas fa-folder-open"></i> <?php echo $category['name_bn']; ?>
            </h2>
            
            <?php if (empty($posts)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> এই ক্যাটাগরিতে কোনো সংবাদ নেই।
            </div>
            <?php else: ?>
            <div class="row">
                <?php foreach ($posts as $post): ?>
                <div class="col-md-6">
                    <div class="card news-card h-100">
                        <?php if (!empty($post['featured_image'])): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/news/<?php echo $post['featured_image']; ?>" 
                             class="card-img-top" alt="<?php echo $post['title_bn']; ?>">
                        <?php else: ?>
                        <img src="<?php echo SITE_URL; ?>/assets/images/placeholder.jpg" 
                             class="card-img-top" alt="No Image">
                        <?php endif; ?>
                        <div class="card-body">
                            <span class="badge bg-danger mb-2"><?php echo $post['category_name_bn']; ?></span>
                            <h5 class="news-title">
                                <a href="<?php echo SITE_URL; ?>/news.php?slug=<?php echo $post['slug']; ?>" 
                                   class="text-decoration-none text-dark">
                                    <?php echo $post['title_bn']; ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted small">
                                <?php echo mb_substr(strip_tags($post['content_bn']), 0, 100); ?>...
                            </p>
                            <div class="news-meta mt-2">
                                <small><i class="fas fa-clock"></i> <?php echo date('d M Y, h:i A', strtotime($post['created_at'])); ?></small>
                                <span class="ms-2"><i class="fas fa-eye"></i> <?php echo $post['views']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Trending News -->
            <div class="sidebar-widget">
                <h4 class="widget-title">জনপ্রিয় সংবাদ</h4>
                <?php 
                $trending = getTrendingPosts(5);
                foreach ($trending as $news): 
                ?>
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
                <div style="background: #e9ecef; height: 250px; display: flex; align-items: center; justify-content: center;">
                    <p class="text-muted">Advertisement Space</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
