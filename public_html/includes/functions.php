<?php
/**
 * Database Functions
 * Radio FM Cumilla
 */

require_once __DIR__ . '/config.php';

/**
 * Get all categories
 */
function getCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name_bn ASC");
    return $stmt->fetchAll();
}

/**
 * Get category by slug
 */
function getCategoryBySlug($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get published posts with filters
 */
function getPosts($filters = []) {
    global $pdo;
    
    $sql = "SELECT p.*, c.name_bn as category_name_bn, c.name_en as category_name_en, 
            c.slug as category_slug, u.name as author_name 
            FROM posts p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN users u ON p.author_id = u.id 
            WHERE p.status = 'published'";
    
    $params = [];
    
    if (!empty($filters['category_id'])) {
        $sql .= " AND p.category_id = ?";
        $params[] = $filters['category_id'];
    }
    
    if (!empty($filters['is_breaking'])) {
        $sql .= " AND p.is_breaking = 1";
    }
    
    if (!empty($filters['limit'])) {
        $sql .= " LIMIT " . (int)$filters['limit'];
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get single post by slug
 */
function getPostBySlug($slug) {
    global $pdo;
    
    // Increment view count
    $stmt = $pdo->prepare("UPDATE posts SET views = views + 1 WHERE slug = ?");
    $stmt->execute([$slug]);
    
    $stmt = $pdo->prepare("SELECT p.*, c.name_bn as category_name_bn, c.name_en as category_name_en, 
                           c.slug as category_slug, u.name as author_name 
                           FROM posts p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           LEFT JOIN users u ON p.author_id = u.id 
                           WHERE p.slug = ? AND p.status = 'published'");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get trending posts (by views)
 */
function getTrendingPosts($limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.slug as category_slug 
                           FROM posts p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.status = 'published' 
                           ORDER BY p.views DESC 
                           LIMIT ?");
    $stmt->execute([(int)$limit]);
    return $stmt->fetchAll();
}

/**
 * Get breaking news
 */
function getBreakingNews() {
    return getPosts(['is_breaking' => true, 'limit' => 10]);
}

/**
 * Add comment to post
 */
function addComment($post_id, $user_name, $user_email, $comment) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_name, user_email, comment, status) 
                           VALUES (?, ?, ?, ?, 'pending')");
    return $stmt->execute([$post_id, sanitize($user_name), sanitize($user_email), sanitize($comment)]);
}

/**
 * Get approved comments for a post
 */
function getComments($post_id, $limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM comments 
                           WHERE post_id = ? AND status = 'approved' 
                           ORDER BY created_at DESC 
                           LIMIT ?");
    $stmt->execute([$post_id, (int)$limit]);
    return $stmt->fetchAll();
}

/**
 * Get site settings
 */
function getSettings() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
    return $stmt->fetch();
}

/**
 * Search posts
 */
function searchPosts($keyword) {
    global $pdo;
    $keyword = "%{$keyword}%";
    $stmt = $pdo->prepare("SELECT p.*, c.slug as category_slug 
                           FROM posts p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.status = 'published' 
                           AND (p.title_bn LIKE ? OR p.title_en LIKE ? OR p.content_bn LIKE ? OR p.content_en LIKE ?)
                           ORDER BY p.created_at DESC");
    $stmt->execute([$keyword, $keyword, $keyword, $keyword]);
    return $stmt->fetchAll();
}

/**
 * Get radio programs
 */
function getRadioPrograms($limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT rp.*, u.name as rj_name 
                           FROM radio_programs rp 
                           LEFT JOIN users u ON rp.created_by = u.id 
                           ORDER BY rp.start_time DESC 
                           LIMIT ?");
    $stmt->execute([(int)$limit]);
    return $stmt->fetchAll();
}

/**
 * Get current live program
 */
function getCurrentLiveProgram() {
    global $pdo;
    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("SELECT rp.*, u.name as rj_name 
                           FROM radio_programs rp 
                           LEFT JOIN users u ON rp.created_by = u.id 
                           WHERE rp.is_live = 1 AND ? BETWEEN start_time AND end_time 
                           LIMIT 1");
    $stmt->execute([$now]);
    return $stmt->fetch();
}

/**
 * Log listener
 */
function logListener($ip_address, $user_agent) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO listeners (ip_address, user_agent, listen_time) 
                           VALUES (?, ?, NOW())");
    return $stmt->execute([$ip_address, $user_agent]);
}

?>
