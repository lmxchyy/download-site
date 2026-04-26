<?php
session_start();
require_once '../config/database.php';
require_once 'includes/functions.php';

if (isLoggedIn()) redirect('index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
        redirect('index.php');
    } else {
        $error = '用户名或密码错误';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>登录 - <?= getSetting('site_name') ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <span class="pill"><i class="fas fa-shield"></i> Secure Access</span>
        <h2>登录控制台</h2>
        <p class="muted">进入你的资源中心、收藏内容与管理后台。</p>
        <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="用户名或邮箱" required>
            <input type="password" name="password" placeholder="输入密码" required>
            <button type="submit"><i class="fas fa-right-to-bracket"></i> 立即登录</button>
        </form>
        <p class="muted">没有账号？ <a href="register.php">立即注册</a></p>
    </div>
</body>
</html>
