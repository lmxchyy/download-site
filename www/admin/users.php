<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$currentUserId = (int)$_SESSION['user_id'];
$error = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id === $currentUserId) {
        $error = '不能删除当前登录账号';
    } elseif ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        header('Location: users.php');
        exit;
    }
}

if (isset($_GET['toggle_role'])) {
    $id = (int)$_GET['toggle_role'];
    if ($id === $currentUserId) {
        $error = '不能修改当前登录账号的角色';
    } elseif ($id > 0) {
        $stmt = $pdo->prepare("UPDATE users SET role = CASE WHEN role = 'admin' THEN 'user' ELSE 'admin' END WHERE id = ?");
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        header('Location: users.php');
        exit;
    }
}

$users = $pdo->query("SELECT id, username, email, role, created_at, last_login FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户管理 - 管理后台</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="page-shell admin-page">
    <section class="admin-container tech-section">
        <div class="container admin-shell">
            <aside class="admin-sidebar">
                <div class="admin-brand">
                    <i class="fas fa-users-gear fa-2x"></i>
                    <h3>用户管理</h3>
                    <p class="muted">查看用户账号并维护后台角色权限。</p>
                </div>
                <div class="nav-group-label">Overview</div>
                <a href="index.php"><i class="fas fa-chart-line"></i> 仪表盘</a>
                <a href="resources.php"><i class="fas fa-database"></i> 资源管理</a>
                <div class="nav-group-label">Modules</div>
                <a href="categories.php"><i class="fas fa-folder-tree"></i> 分类管理</a>
                <a href="users.php" class="active"><i class="fas fa-users-gear"></i> 用户管理</a>
                <a href="settings.php"><i class="fas fa-sliders"></i> 系统设置</a>
                <div class="nav-group-label">Access</div>
                <a href="../index.php"><i class="fas fa-arrow-left"></i> 返回前台</a>
            </aside>

            <main class="admin-content">
                <div class="admin-content-header">
                    <div>
                        <span class="pill"><i class="fas fa-users"></i> User Control</span>
                        <h1>用户管理</h1>
                        <p>查看注册用户、管理员角色与最近登录记录。</p>
                    </div>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="table-panel">
                    <table class="admin-table">
                        <thead>
                            <tr><th>ID</th><th>用户名</th><th>邮箱</th><th>角色</th><th>注册时间</th><th>最后登录</th><th>操作</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><span class="status-pill <?= $user['role'] === 'admin' ? 'status-active' : '' ?>"><?= $user['role'] === 'admin' ? '管理员' : '普通用户' ?></span></td>
                                <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
                                <td><?= $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : '未登录' ?></td>
                                <td class="table-actions">
                                    <a href="?toggle_role=<?= $user['id'] ?>" class="btn-sm"><?= $user['role'] === 'admin' ? '设为用户' : '设为管理员' ?></a>
                                    <?php if ((int)$user['id'] !== $currentUserId): ?>
                                    <a href="?delete=<?= $user['id'] ?>" class="btn-sm" onclick="return confirm('确定删除该用户？')">删除</a>
                                    <?php else: ?>
                                    <span class="btn-sm is-disabled">当前账号</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </section>
</body>
</html>
