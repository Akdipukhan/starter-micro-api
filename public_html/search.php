<?php
/**
 * Search Page
 */

session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

$settings = getSettings();
$categories = getCategories();
$breaking_news = getBreakingNews();

$keyword = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$results = [];

if (!empty($keyword)) {
    $results = searchPosts($keyword);
}

$page_title = 'Search';
include 'includes/header.php';
?>

<div class="container my-5">
    <h2 class="widget-title mb-4">
        <i class="fas fa-search"></i> 
        <?php if (!empty($keyword)): ?>
            অনুসন্ধান ফলাফল: "<?php echo $keyword; ?>" (<?php echo count($results); ?>)
        <?php else: ?>
            অনুসন্ধান করুন
        <?php endif; ?>
    </h2>
    
    <?php if (!empty($keyword) && empty($results)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> "<?php echo $keyword; ?>" এর জন্য কোনো ফলাফল পাওয়া যায়নি।
    </div>
    <?php endif; ?>
    
    <div class="row">
        <?php foreach ($results as $post): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card news-card h-100">
                <?php if (!empty($post['featured_image'])): ?>
                <img src="<?php echo SITE_URL; ?>/uploads/news/<?php echo $post['featured_image']; ?>" 
                     class="card-img-top" alt="<?php echo $post['title_bn']; ?>">
                <?php else: ?>
                <img src="<?php echo SITE_URL; ?>/assets/images/placeholder.jpg" 
                     class="card-img-top" alt="No Image">
                <?php endif; ?>
                <div class="card-body">
                    <span class="badge bg-danger mb-2"><?php echo $post['category_name_bn'] ?? 'News'; ?></span>
                    <h5 class="news-title">
                        <a href="<?php echo SITE_URL; ?>/news.php?slug=<?php echo $post['slug']; ?>" 
                           class="text-decoration-none text-dark">
                            <?php echo $post['title_bn']; ?>
                        </a>
                    </h5>
                    <p class="card-text text-muted small">
                        <?php echo mb_substr(strip_tags($post['content_bn']), 0, 80); ?>...
                    </p>
                    <div class="news-meta mt-2">
                        <small><i class="fas fa-clock"></i> <?php echo date('d M Y', strtotime($post['created_at'])); ?></small>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
