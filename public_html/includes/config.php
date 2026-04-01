<?php
/**
 * Database Configuration
 * Radio FM Cumilla
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'radio_fm_cumilla');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_URL', 'http://localhost/radiofmcumilla/public_html');
define('ADMIN_URL', 'http://localhost/radiofmcumilla/admin');
define('SITE_TITLE', 'Radio FM Cumilla');
define('TIMEZONE', 'Asia/Dhaka');

date_default_timezone_set(TIMEZONE);

// Security Settings
define('HASH_COST', 10);
define('SESSION_LIFETIME', 3600);

// Upload Settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_AUDIO_TYPES', ['audio/mpeg', 'audio/wav', 'audio/ogg']);

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
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

function checkRole($allowedRoles = []) {
    if (!isLoggedIn()) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
    if (!empty($allowedRoles) && !in_array($_SESSION['user_role'], $allowedRoles)) {
        die('Access Denied: Insufficient permissions.');
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

/**
 * File Upload Handler
 */
function uploadFile($file, $targetDir, $allowedTypes) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error occurred'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds limit'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $targetPath = $targetDir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $targetPath];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

?>
