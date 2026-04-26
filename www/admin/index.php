<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// 检查登录状态和管理员权限
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// 获取统计数据
$stats = [];

// 资源总数
$stmt = $pdo->query("SELECT COUNT(*) as total FROM resources WHERE status = 1");
$stats['resources'] = $stmt->fetch()['total'];

// 分类总数
$stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
$stats['categories'] = $stmt->fetch()['total'];

// 用户总数
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$stats['users'] = $stmt->fetch()['total'];

// 今日下载量
$stmt = $pdo->query("SELECT COUNT(*) as total FROM downloads WHERE DATE(downloaded_at) = CURDATE()");
$stats['today_downloads'] = $stmt->fetch()['total'];

$page_title = '管理后台';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - 资源下载站</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="page-shell admin-page">
    <section class="admin-container tech-section">
        <div class="container admin-shell">
            <aside class="admin-sidebar">
                <div class="admin-brand">
                    <i class="fas fa-shield-halved fa-2x"></i>
                    <h3>管理控制台</h3>
                    <p class="muted">欢迎回来，<?= htmlspecialchars($_SESSION['username']) ?></p>
                </div>
                <div class="nav-group-label">Overview</div>
                <a href="index.php" class="active"><i class="fas fa-chart-line"></i> 仪表盘</a>
                <a href="resources.php"><i class="fas fa-database"></i> 资源管理</a>
                <div class="nav-group-label">Modules</div>
                <a href="categories.php"><i class="fas fa-folder-tree"></i> 分类管理</a>
                <a href="users.php"><i class="fas fa-users-gear"></i> 用户管理</a>
                <a href="settings.php"><i class="fas fa-sliders"></i> 系统设置</a>
                <div class="nav-group-label">Access</div>
                <a href="../index.php"><i class="fas fa-arrow-left"></i> 返回前台</a>
            </aside>

            <main class="admin-content">
                <div class="page-heading">
                    <div>
                        <span class="pill"><i class="fas fa-satellite-dish"></i> Admin Overview</span>
                        <h1>仪表盘</h1>
                        <p>集中查看资源规模、分类分布、用户增长与今日下载动态。</p>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-database"></i>
                        <h3><?= $stats['resources'] ?></h3>
                        <p>资源总数</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-folder"></i>
                        <h3><?= $stats['categories'] ?></h3>
                        <p>分类数量</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <h3><?= $stats['users'] ?></h3>
                        <p>注册用户</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-download"></i>
                        <h3><?= $stats['today_downloads'] ?></h3>
                        <p>今日下载</p>
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>运行状态</h2>
                            <p>当前后台已采用统一深色科技风样式，后续模块可直接复用同一套设计系统。</p>
                        </div>
                    </div>
                    <div class="resource-meta">
                        <span class="status-pill"><i class="fas fa-circle"></i> Online</span>
                        <span><i class="fas fa-shield"></i> Admin Session Active</span>
                        <span><i class="fas fa-layer-group"></i> Shared UI System Enabled</span>
                    </div>
                </div>
            </main>
        </div>
    </section>
</body>
</html>
