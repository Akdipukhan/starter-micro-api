<?php
/**
 * Single News Page
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
$post = getPostBySlug($_GET['slug']);

if (!$post) {
    header('Location: ' . SITE_URL);
    exit;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (verifyCSRFToken($_POST['csrf_token'])) {
        $user_name = sanitize($_POST['user_name']);
        $user_email = sanitize($_POST['user_email']);
        $comment = sanitize($_POST['comment']);
        
        if (!empty($user_name) && !empty($comment)) {
            addComment($post['id'], $user_name, $user_email, $comment);
            setMessage('success', 'Comment submitted for approval!');
        }
    }
}

$comments = getComments($post['id']);
$related_posts = getPosts(['category_id' => $post['category_id'], 'limit' => 4]);

$page_title = $post['title_bn'];
$meta_title = $post['meta_title'] ?? $post['title_bn'];
$meta_description = $post['meta_description'] ?? mb_substr(strip_tags($post['content_bn']), 0, 160);

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Main Article -->
        <div class="col-lg-8">
            <article>
                <?php if (!empty($post['featured_image'])): ?>
                <img src="<?php echo SITE_URL; ?>/uploads/news/<?php echo $post['featured_image']; ?>" 
                     class="img-fluid rounded w-100 mb-4" 
                     alt="<?php echo $post['title_bn']; ?>" 
                     style="max-height: 500px; object-fit: cover;">
                <?php endif; ?>
                
                <div class="mb-3">
                    <a href="<?php echo SITE_URL; ?>/category.php?slug=<?php echo $post['category_slug']; ?>" 
                       class="badge bg-danger text-decoration-none">
                        <?php echo $post['category_name_bn']; ?>
                    </a>
                    <?php if ($post['is_breaking']): ?>
                    <span class="badge bg-warning text-dark">Breaking</span>
                    <?php endif; ?>
                </div>
                
                <h1 class="mb-3"><?php echo $post['title_bn']; ?></h1>
                
                <div class="news-meta mb-4 pb-3 border-bottom">
                    <span><i class="fas fa-user"></i> <?php echo $post['author_name']; ?></span>
                    <span class="ms-3"><i class="fas fa-calendar"></i> <?php echo date('d F Y, h:i A', strtotime($post['created_at'])); ?></span>
                    <span class="ms-3"><i class="fas fa-eye"></i> <?php echo $post['views']; ?> views</span>
                </div>
                
                <div class="content mb-4">
                    <?php echo nl2br($post['content_bn']); ?>
                </div>
                
                <!-- Social Share Buttons -->
                <div class="social-share mb-4 py-3 border-top border-bottom">
                    <h5>শেয়ার করুন:</h5>
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/news.php?slug=' . $post['slug']); ?>" 
                           target="_blank" class="btn btn-primary btn-sm">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/news.php?slug=' . $post['slug']); ?>&text=<?php echo urlencode($post['title_bn']); ?>" 
                           target="_blank" class="btn btn-info btn-sm text-white">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($post['title_bn'] . ' - ' . SITE_URL . '/news.php?slug=' . $post['slug']); ?>" 
                           target="_blank" class="btn btn-success btn-sm">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </article>
            
            <!-- Comments Section -->
            <div class="comments-section mt-5">
                <h3 class="widget-title">মন্তব্য (<?php echo count($comments); ?>)</h3>
                
                <!-- Comment Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>মন্তব্য করুন</h5>
                        <?php $flash = getFlashMessage(); if ($flash): ?>
                        <div class="alert alert-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <?php echo '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">'; ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">আপনার নাম</label>
                                    <input type="text" name="user_name" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ইমেইল (ঐচ্ছিক)</label>
                                    <input type="email" name="user_email" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">মন্তব্য</label>
                                <textarea name="comment" class="form-control" rows="4" required></textarea>
                            </div>
                            <button type="submit" name="submit_comment" class="btn btn-danger">
                                <i class="fas fa-paper-plane"></i> জমা দিন
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Comments List -->
                <div class="comments-list">
                    <?php if (empty($comments)): ?>
                    <p class="text-muted">কোনো মন্তব্য নেই। প্রথম মন্তব্য করুন!</p>
                    <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <strong><i class="fas fa-user-circle"></i> <?php echo $comment['user_name']; ?></strong>
                                <small class="text-muted"><?php echo date('d M Y, h:i A', strtotime($comment['created_at'])); ?></small>
                            </div>
                            <p class="mb-0"><?php echo nl2br($comment['comment']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Related News -->
            <div class="sidebar-widget">
                <h4 class="widget-title">সম্পর্কিত সংবাদ</h4>
                <?php foreach ($related_posts as $related): ?>
                <div class="mb-3 pb-3 border-bottom">
                    <a href="<?php echo SITE_URL; ?>/news.php?slug=<?php echo $related['slug']; ?>" 
                       class="text-decoration-none text-dark">
                        <h6 class="mb-1"><?php echo $related['title_bn']; ?></h6>
                    </a>
                    <small class="text-muted"><i class="fas fa-clock"></i> <?php echo date('d M Y', strtotime($related['created_at'])); ?></small>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Ad Space -->
            <div class="sidebar-widget text-center">
                <div style="background: #e9ecef; height: 600px; display: flex; align-items: center; justify-content: center;">
                    <p class="text-muted">Advertisement (300x600)</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
