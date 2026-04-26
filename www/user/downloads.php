<?php
session_start();
require_once '../../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) redirect('../login.php');

$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM downloads WHERE user_id = ?");
$stmt->execute([$userId]);
$downloadCount = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM favorites WHERE user_id = ?");
$stmt->execute([$userId]);
$favoriteCount = $stmt->fetch()['total'];

$downloadsStmt = $pdo->prepare("SELECT d.resource_id, d.downloaded_at, r.title, r.id as rid, r.file_type, c.name as cat_name
                               FROM downloads d
                               LEFT JOIN resources r ON d.resource_id = r.id
                               LEFT JOIN categories c ON r.category_id = c.id
                               WHERE d.user_id = ?
                               ORDER BY d.downloaded_at DESC");
$downloadsStmt->execute([$userId]);
$downloads = $downloadsStmt->fetchAll();

$page_title = '下载记录';
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
                    <a href="downloads.php" class="active"><i class="fas fa-download"></i> 下载记录</a>
                    <a href="profile.php"><i class="fas fa-user-pen"></i> 个人资料</a>
                </nav>
            </aside>

            <div class="dashboard-content">
                <div class="page-heading">
                    <div>
                        <span class="pill"><i class="fas fa-clock-rotate-left"></i> Downloads</span>
                        <h1>下载记录</h1>
                        <p>按时间顺序查看你已经下载过的资源，并快速再次访问。</p>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-download"></i>
                        <h3><?= $downloadCount ?></h3>
                        <p>总下载次数</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-heart"></i>
                        <h3><?= $favoriteCount ?></h3>
                        <p>收藏的资源</p>
                    </div>
                </div>

                <div class="table-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>下载历史</h2>
                            <p>保留你自己的下载时间线，方便回访资源。</p>
                        </div>
                    </div>

                    <?php if ($downloads): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>资源名称</th>
                                <th>分类</th>
                                <th>文件类型</th>
                                <th>下载时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($downloads as $item): ?>
                            <tr>
                                <td>
                                    <?php if ($item['rid']): ?>
                                    <a href="../resource.php?id=<?= $item['rid'] ?>"><?= htmlspecialchars($item['title']) ?></a>
                                    <?php else: ?>
                                    <span>资源已不存在</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($item['cat_name'] ?: '未分类') ?></td>
                                <td><?= htmlspecialchars($item['file_type'] ?: '未知') ?></td>
                                <td><?= date('Y-m-d H:i', strtotime($item['downloaded_at'])) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <?php if ($item['rid']): ?>
                                        <a href="../resource.php?id=<?= $item['rid'] ?>" class="btn-sm">查看详情</a>
                                        <a href="../download.php?id=<?= $item['rid'] ?>" class="btn-sm"><i class="fas fa-download"></i> 再次下载</a>
                                        <?php else: ?>
                                        <span class="btn-sm is-disabled">不可用</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-results">
                        <p>暂无下载记录。</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
