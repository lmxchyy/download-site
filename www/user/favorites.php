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

$favoritesStmt = $pdo->prepare("SELECT f.resource_id, r.title, r.id as rid, r.download_count, r.created_at, c.name as cat_name
                               FROM favorites f
                               INNER JOIN resources r ON f.resource_id = r.id
                               LEFT JOIN categories c ON r.category_id = c.id
                               WHERE f.user_id = ? AND r.status = 1
                               ORDER BY f.id DESC");
$favoritesStmt->execute([$userId]);
$favorites = $favoritesStmt->fetchAll();

$page_title = '我的收藏';
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
                    <a href="favorites.php" class="active"><i class="fas fa-heart"></i> 我的收藏</a>
                    <a href="downloads.php"><i class="fas fa-download"></i> 下载记录</a>
                    <a href="profile.php" class="is-disabled"><i class="fas fa-user-pen"></i> 个人资料</a>
                </nav>
            </aside>

            <div class="dashboard-content">
                <div class="page-heading">
                    <div>
                        <span class="pill"><i class="fas fa-heart-pulse"></i> Favorites</span>
                        <h1>我的收藏</h1>
                        <p>集中查看你已经收藏的资源，并快速返回详情或直接下载。</p>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-heart"></i>
                        <h3><?= $favoriteCount ?></h3>
                        <p>收藏的资源</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-download"></i>
                        <h3><?= $downloadCount ?></h3>
                        <p>总下载次数</p>
                    </div>
                </div>

                <div class="table-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>收藏列表</h2>
                            <p>你标记过的资源会显示在这里。</p>
                        </div>
                    </div>

                    <?php if ($favorites): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>资源名称</th>
                                <th>分类</th>
                                <th>下载量</th>
                                <th>发布时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($favorites as $item): ?>
                            <tr data-row-id="<?= $item['rid'] ?>">
                                <td><a href="../resource.php?id=<?= $item['rid'] ?>"><?= htmlspecialchars($item['title']) ?></a></td>
                                <td><?= htmlspecialchars($item['cat_name'] ?: '未分类') ?></td>
                                <td><?= formatNumber($item['download_count']) ?></td>
                                <td><?= date('Y-m-d', strtotime($item['created_at'])) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="../resource.php?id=<?= $item['rid'] ?>" class="btn-sm">查看详情</a>
                                        <a href="../download.php?id=<?= $item['rid'] ?>" class="btn-sm"><i class="fas fa-download"></i> 直接下载</a>
                                        <button type="button" class="btn-sm favorite-remove" data-id="<?= $item['rid'] ?>"><i class="fas fa-heart-crack"></i> 取消收藏</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-results">
                        <p>你还没有收藏任何资源。</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
    <script>
    document.querySelectorAll('.favorite-remove').forEach((button) => {
        button.addEventListener('click', async function() {
            const resourceId = this.dataset.id;
            const row = document.querySelector(`[data-row-id="${resourceId}"]`);

            const response = await fetch('../api/favorite.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: resourceId })
            });

            const result = await response.json();
            if (result.status !== 'success') {
                alert(result.message || '操作失败');
                return;
            }

            row?.remove();
            if (!document.querySelector('[data-row-id]')) {
                location.reload();
            }
        });
    });
    </script>
</body>
</html>
