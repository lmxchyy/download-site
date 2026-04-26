<?php
session_start();
require_once '../config/database.php';
require_once 'includes/functions.php';

if (isLoggedIn()) redirect('index.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (strlen($username) < 3) {
        $error = '用户名至少3个字符';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '邮箱格式错误';
    } elseif (strlen($password) < 6) {
        $error = '密码至少6位';
    } elseif ($password !== $confirm) {
        $error = '两次密码不一致';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = '用户名或邮箱已存在';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hash])) {
                $success = '注册成功！请登录';
                header("refresh:2;url=login.php");
            } else {
                $error = '注册失败';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>注册 - <?= getSetting('site_name') ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <span class="pill"><i class="fas fa-user-astronaut"></i> Create Profile</span>
        <h2>创建账号</h2>
        <p class="muted">注册后即可收藏资源、查看下载记录并进入专属控制台。</p>
        <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="设置用户名" required>
            <input type="email" name="email" placeholder="邮箱地址" required>
            <input type="password" name="password" placeholder="输入密码" required>
            <input type="password" name="confirm_password" placeholder="再次输入密码" required>
            <button type="submit"><i class="fas fa-user-plus"></i> 立即注册</button>
        </form>
        <p class="muted">已有账号？ <a href="login.php">前往登录</a></p>
    </div>
</body>
</html>
