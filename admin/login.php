<?php
/**
 * Admin Login Page
 */

require_once 'includes/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } elseif (checkLoginAttempts($_SERVER['REMOTE_ADDR'])) {
        $error = 'Too many failed attempts. Please try again later.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && verifyPassword($password, $user['password'])) {
            // Successful login
            session_regenerate_id(true);
            $_SESSION['admin_user_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            recordLoginAttempt($_SERVER['REMOTE_ADDR'], true);
            logActivity('Login', 'User logged in successfully');
            
            redirect('index.php');
        } else {
            recordLoginAttempt($_SERVER['REMOTE_ADDR'], false);
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo ADMIN_TITLE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .login-body {
            padding: 40px;
        }
        .form-control:focus {
            border-color: #c0392b;
            box-shadow: 0 0 0 0.2rem rgba(192, 57, 43, 0.25);
        }
        .btn-login {
            background: #c0392b;
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: #e74c3c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(192, 57, 43, 0.4);
        }
        .back-link {
            color: #666;
            text-decoration: none;
            transition: all 0.3s;
        }
        .back-link:hover {
            color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-broadcast-tower"></i>
            <h3>RADIO FM CUMILLA</h3>
            <p class="mb-0">Admin Panel</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" class="form-control" required autofocus 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-danger btn-login w-100">
                    <i class="fas fa-sign-in-alt"></i> LOGIN
                </button>
            </form>
            
            <div class="text-center mt-4">
                <a href="<?php echo SITE_URL; ?>" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Website
                </a>
            </div>
            
            <hr class="my-4">
            
            <div class="text-center small text-muted">
                <p class="mb-0">Default Login:</p>
                <p class="mb-0"><strong>Email:</strong> admin@radiofmcumilla.com</p>
                <p><strong>Password:</strong> admin123</p>
                <p class="text-danger"><strong>⚠️ Change immediately after first login!</strong></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
