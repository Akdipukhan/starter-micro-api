<?php
/**
 * Admin Logout
 */

require_once 'includes/config.php';

// Destroy session
session_unset();
session_destroy();

// Redirect to login
redirect('login.php');

?>
