<?php
/**
 * Admin Panel Configuration
 * Radio FM Cumilla
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'radio_fm_cumilla');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Admin Configuration
define('ADMIN_URL', 'http://localhost/radiofmcumilla/admin');
define('SITE_URL', 'http://localhost/radiofmcumilla/public_html');
define('ADMIN_TITLE', 'Radio FM Cumilla - Admin Panel');
define('TIMEZONE', 'Asia/Dhaka');

date_default_timezone_set(TIMEZONE);

// Security Settings
define('HASH_COST', 10);
define('SESSION_LIFETIME', 7200); // 2 hours for admin
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes

// Upload Settings
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB for admin
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_AUDIO_TYPES', ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp3']);

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

/**
 * Security Helper Functions
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function isLoggedIn() {
    return isset($_SESSION['admin_user_id']) && isset($_SESSION['admin_role']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
}

function checkRole($allowedRoles = []) {
    requireLogin();
    if (!empty($allowedRoles) && !in_array($_SESSION['admin_role'], $allowedRoles)) {
        setMessage('danger', 'Access Denied: Insufficient permissions.');
        header('Location: ' . ADMIN_URL . '/index.php');
        exit;
    }
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function setMessage($type, $message) {
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * File Upload Handler
 */
function uploadFile($file, $targetDir, $allowedTypes) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error occurred (Code: ' . $file['error'] . ')'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type: ' . $mimeType];
    }
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds limit (' . round($file['size']/1024/1024, 2) . 'MB)'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $targetPath = $targetDir . '/' . $filename;
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $targetPath];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

/**
 * Get user info
 */
function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn()) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_user_id']]);
    return $stmt->fetch();
}

/**
 * Log admin activity
 */
function logActivity($action, $details = '') {
    // Could be extended to store in database
    error_log(sprintf("[%s] User %d (%s): %s - %s", 
        date('Y-m-d H:i:s'), 
        $_SESSION['admin_user_id'] ?? 0, 
        $_SESSION['admin_role'] ?? 'guest',
        $action, 
        $details
    ));
}

/**
 * Check for brute force login attempts
 */
function checkLoginAttempts($ip) {
    // Simple file-based rate limiting (use database in production)
    $file = sys_get_temp_dir() . '/login_attempts_' . md5($ip);
    $attempts = 0;
    $lastAttempt = 0;
    
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        $attempts = $data['attempts'] ?? 0;
        $lastAttempt = $data['last_attempt'] ?? 0;
        
        if (time() - $lastAttempt > LOCKOUT_TIME) {
            $attempts = 0;
        }
    }
    
    return $attempts >= MAX_LOGIN_ATTEMPTS;
}

function recordLoginAttempt($ip, $success = false) {
    $file = sys_get_temp_dir() . '/login_attempts_' . md5($ip);
    $data = ['attempts' => 0, 'last_attempt' => time()];
    
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (time() - $data['last_attempt'] > LOCKOUT_TIME) {
            $data = ['attempts' => 0, 'last_attempt' => time()];
        }
    }
    
    if (!$success) {
        $data['attempts']++;
    } else {
        $data['attempts'] = 0;
    }
    
    $data['last_attempt'] = time();
    file_put_contents($file, json_encode($data));
}

?>
