<?php
/**
 * Admin Dashboard
 */

require_once 'includes/config.php';
requireLogin();

// Get dashboard statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM posts");
$total_posts = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM posts WHERE status = 'published'");
$published_posts = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM posts WHERE status = 'draft'");
$draft_posts = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$total_users = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM comments WHERE status = 'pending'");
$pending_comments = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(views) as total_views FROM posts");
$total_views = $stmt->fetch()['total_views'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM listeners WHERE listen_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
$active_listeners = $stmt->fetch()['total'];

$live_program = null;
$now = date('Y-m-d H:i:s');
$stmt = $pdo->prepare("SELECT rp.*, u.name as rj_name FROM radio_programs rp 
                       LEFT JOIN users u ON rp.created_by = u.id 
                       WHERE rp.is_live = 1 AND ? BETWEEN start_time AND end_time 
                       LIMIT 1");
$stmt->execute([$now]);
$live_program = $stmt->fetch();

// Recent posts
$stmt = $pdo->query("SELECT p.*, u.name as author_name, c.name_bn as category_name 
                     FROM posts p 
                     LEFT JOIN users u ON p.author_id = u.id 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     ORDER BY p.created_at DESC LIMIT 5");
$recent_posts = $stmt->fetchAll();

// Pending comments
$stmt = $pdo->query("SELECT c.*, p.title as post_title FROM comments c 
                     LEFT JOIN posts p ON c.post_id = p.id 
                     WHERE c.status = 'pending' 
                     ORDER BY c.created_at DESC LIMIT 5");
$pending_comment_list = $stmt->fetchAll();

$page_title = 'Dashboard';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-3"><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Total Posts</h6>
                            <h2 class="card-title mb-0"><?php echo $total_posts; ?></h2>
                        </div>
                        <i class="fas fa-newspaper fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="posts.php" class="text-white text-decoration-none small">View all <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Published</h6>
                            <h2 class="card-title mb-0"><?php echo $published_posts; ?></h2>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <small>Published posts</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Draft Posts</h6>
                            <h2 class="card-title mb-0"><?php echo $draft_posts; ?></h2>
                        </div>
                        <i class="fas fa-file-alt fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <small>Draft articles</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Total Views</h6>
                            <h2 class="card-title mb-0"><?php echo number_format($total_views); ?></h2>
                        </div>
                        <i class="fas fa-eye fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <small>All time views</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Pending Comments</h6>
                            <h2 class="card-title mb-0"><?php echo $pending_comments; ?></h2>
                        </div>
                        <i class="fas fa-comments fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="comments.php" class="text-white text-decoration-none small">Review <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Active Listeners</h6>
                            <h2 class="card-title mb-0"><?php echo $active_listeners; ?></h2>
                        </div>
                        <i class="fas fa-headphones fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <small>Last hour</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-dark text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Users</h6>
                            <h2 class="card-title mb-0"><?php echo $total_users; ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="users.php" class="text-white text-decoration-none small">Manage <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card <?php echo $live_program ? 'bg-success' : 'bg-light text-dark'; ?> h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Live Radio</h6>
                            <h4 class="card-title mb-0">
                                <?php echo $live_program ? 'ON AIR' : 'OFFLINE'; ?>
                            </h4>
                        </div>
                        <i class="fas fa-broadcast-tower fa-3x opacity-50"></i>
                    </div>
                    <?php if ($live_program): ?>
                    <small class="mt-2 d-block"><?php echo $live_program['title']; ?></small>
                    <small><?php echo $live_program['rj_name']; ?></small>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="radio.php" class="text-decoration-none small">Manage Radio <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="post_add.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add News
                        </a>
                        <a href="categories.php" class="btn btn-outline-primary">
                            <i class="fas fa-folder"></i> Manage Categories
                        </a>
                        <a href="radio.php" class="btn btn-danger">
                            <i class="fas fa-microphone"></i> Go Live Radio
                        </a>
                        <a href="comments.php" class="btn btn-warning">
                            <i class="fas fa-comments"></i> Review Comments (<?php echo $pending_comments; ?>)
                        </a>
                        <a href="settings.php" class="btn btn-secondary">
                            <i class="fas fa-cog"></i> Site Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Posts -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Posts</h5>
                    <a href="posts.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_posts as $post): ?>
                                <tr>
                                    <td>
                                        <a href="post_edit.php?id=<?php echo $post['id']; ?>" class="text-decoration-none">
                                            <?php echo mb_substr($post['title_bn'], 0, 50); ?>...
                                        </a>
                                    </td>
                                    <td><span class="badge bg-info"><?php echo $post['category_name']; ?></span></td>
                                    <td><?php echo $post['author_name']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $post['status'] == 'published' ? 'success' : ($post['status'] == 'draft' ? 'warning' : 'secondary'); ?>">
                                            <?php echo ucfirst($post['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($post['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pending Comments -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-comment-dots"></i> Pending Comments</h5>
                    <a href="comments.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($pending_comment_list)): ?>
                    <div class="p-3 text-muted text-center">No pending comments</div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($pending_comment_list as $comment): ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo $comment['user_name']; ?></h6>
                                <small><?php echo date('d M', strtotime($comment['created_at'])); ?></small>
                            </div>
                            <p class="mb-1 small text-muted"><?php echo mb_substr($comment['comment'], 0, 80); ?>...</p>
                            <small class="text-primary">On: <?php echo mb_substr($comment['post_title'], 0, 30); ?>...</small>
                            <div class="mt-2">
                                <a href="comments.php?action=approve&id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i>
                                </a>
                                <a href="comments.php?action=reject&id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-danger">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
