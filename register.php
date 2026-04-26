<?php
// 注册页面
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = '请填写所有必填项';
    } elseif (!preg_match('/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]{3,20}$/u', $username)) {
        $error = '用户名只能包含字母、数字、下划线和中文字符，长度为3-20位';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '请输入有效的邮箱地址';
    } elseif (strlen($password) < 6) {
        $error = '密码长度至少为6位';
    } elseif ($password !== $confirm_password) {
        $error = '两次输入的密码不一致';
    } else {
        // 检查用户名和邮箱是否已存在
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $error = '用户名或邮箱已被注册';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");

            if ($stmt->execute([$username, $email, $hashed_password])) {
                $success = '注册成功！即将跳转到登录页面...';
                header("refresh:2;url=login.php");
            } else {
                $error = '注册失败，请稍后重试';
            }
        }
    }
}

$page_title = '注册';
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
                <h2>创建账户</h2>
                <p>加入我们，发现更多精彩资源</p>
            </div>
            <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">用户名</label>
                    <input type="text" id="username" name="username" required placeholder="3-20位字母、数字或中文">
                    <i class="fas fa-user"></i>
                </div>
                <div class="form-group">
                    <label for="email">邮箱</label>
                    <input type="email" id="email" name="email" required placeholder="请输入有效的邮箱地址">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="form-group">
                    <label for="password">密码</label>
                    <input type="password" id="password" name="password" required placeholder="至少6位字符">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="form-group">
                    <label for="confirm_password">确认密码</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="请再次输入密码">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="form-agree">
                    <label class="checkbox">
                        <input type="checkbox" required> 我已阅读并同意 <a href="#">用户协议</a> 和 <a href="#">隐私政策</a>
                    </label>
                </div>
                <button type="submit" class="btn-auth">注册</button>
            </form>
            <div class="auth-footer">
                已有账户？ <a href="login.php">立即登录</a>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>
