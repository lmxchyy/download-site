<?php
session_start();
require_once '../../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$userId = (int)$_SESSION['user_id'];
$error = '';
$success = '';

$stmt = $pdo->prepare("SELECT id, username, email, role, created_at, last_login, password FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    redirect('../logout.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'profile') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (strlen($username) < 3) {
            $error = '用户名至少3个字符';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = '邮箱格式错误';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ? LIMIT 1");
            $stmt->execute([$username, $email, $userId]);

            if ($stmt->fetch()) {
                $error = '用户名或邮箱已存在';
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                if ($stmt->execute([$username, $email, $userId])) {
                    $_SESSION['username'] = $username;
                    $user['username'] = $username;
                    $user['email'] = $email;
                    $success = '个人资料已更新';
                } else {
                    $error = '个人资料更新失败';
                }
            }
        }
    }

    if ($action === 'password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $user['password'])) {
            $error = '当前密码错误';
        } elseif (strlen($newPassword) < 6) {
            $error = '新密码至少6位';
        } elseif ($newPassword !== $confirmPassword) {
            $error = '两次新密码不一致';
        } else {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hash, $userId])) {
                $user['password'] = $hash;
                $success = '密码已更新';
            } else {
                $error = '密码更新失败';
            }
        }
    }
}

$page_title = '个人资料';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= getSetting('site_name') ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="page-shell user-page">
    <?php include '../includes/header.php'; ?>

    <section class="user-dashboard tech-section">
        <div class="container">
            <aside class="dashboard-sidebar">
                <div class="user-avatar-large">
                    <i class="fas fa-user-circle fa-4x"></i>
                    <h3><?= htmlspecialchars($_SESSION['username']) ?></h3>
                    <p class="muted">你的个人控制台</p>
                </div>
                <nav class="dashboard-nav">
                    <a href="dashboard.php"><i class="fas fa-chart-pie"></i> 仪表盘</a>
                    <a href="favorites.php"><i class="fas fa-heart"></i> 我的收藏</a>
                    <a href="downloads.php"><i class="fas fa-download"></i> 下载记录</a>
                    <a href="profile.php" class="active"><i class="fas fa-user-pen"></i> 个人资料</a>
                </nav>
            </aside>

            <div class="dashboard-content">
                <div class="page-heading">
                    <div>
                        <span class="pill"><i class="fas fa-id-card"></i> Profile</span>
                        <h1>个人资料</h1>
                        <p>查看账号信息，并更新用户名、邮箱与登录密码。</p>
                    </div>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-user-shield"></i>
                        <h3><?= $user['role'] === 'admin' ? '管理员' : '普通用户' ?></h3>
                        <p>账号角色</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-calendar-plus"></i>
                        <h3><?= date('Y-m-d', strtotime($user['created_at'])) ?></h3>
                        <p>注册时间</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-clock"></i>
                        <h3><?= $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : '未登录' ?></h3>
                        <p>最后登录</p>
                    </div>
                </div>

                <div class="table-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>基础资料</h2>
                            <p>修改你的公开账号信息。</p>
                        </div>
                    </div>
                    <form method="POST" class="admin-form-grid profile-form-grid">
                        <input type="hidden" name="action" value="profile">
                        <div class="form-field">
                            <label for="username">用户名</label>
                            <input id="username" type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="email">邮箱地址</label>
                            <input id="email" type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="form-actions form-field-span-2">
                            <button type="submit"><i class="fas fa-floppy-disk"></i> 保存资料</button>
                        </div>
                    </form>
                </div>

                <div class="table-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>修改密码</h2>
                            <p>更新登录密码以提升账号安全性。</p>
                        </div>
                    </div>
                    <form method="POST" class="admin-form-grid profile-form-grid">
                        <input type="hidden" name="action" value="password">
                        <div class="form-field">
                            <label for="current_password">当前密码</label>
                            <input id="current_password" type="password" name="current_password" required>
                        </div>
                        <div class="form-field">
                            <label for="new_password">新密码</label>
                            <input id="new_password" type="password" name="new_password" required>
                        </div>
                        <div class="form-field form-field-span-2">
                            <label for="confirm_password">确认新密码</label>
                            <input id="confirm_password" type="password" name="confirm_password" required>
                        </div>
                        <div class="form-actions form-field-span-2">
                            <button type="submit"><i class="fas fa-key"></i> 更新密码</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
