<?php
// 登录页面
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = '请输入用户名和密码';
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_avatar'] = $user['avatar'];

            // 更新最后登录时间
            $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

            redirect('index.php');
        } else {
            $error = '用户名或密码错误';
        }
    }
}

$page_title = '登录';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - 资源下载站</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="index.php" class="auth-logo">
                    <i class="fas fa-cloud-download-alt"></i>
                    <span>资源下载站</span>
                </a>
                <h2>欢迎回来</h2>
                <p>登录您的账户，发现更多精彩资源</p>
            </div>
            <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">用户名 / 邮箱</label>
                    <input type="text" id="username" name="username" required placeholder="请输入用户名或邮箱">
                    <i class="fas fa-user"></i>
                </div>
                <div class="form-group">
                    <label for="password">密码</label>
                    <input type="password" id="password" name="password" required placeholder="请输入密码">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="form-options">
                    <label class="checkbox">
                        <input type="checkbox" name="remember"> 记住我
                    </label>
                    <a href="forgot-password.php" class="forgot-link">忘记密码？</a>
                </div>
                <button type="submit" class="btn-auth">登录</button>
            </form>
            <div class="auth-footer">
                还没有账户？ <a href="register.php">立即注册</a>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>
