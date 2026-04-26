<?php
session_start();
require_once '../config/database.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) redirect('index.php');

// 获取资源信息并增加浏览量
$stmt = $pdo->prepare("UPDATE resources SET view_count = view_count + 1 WHERE id = ?");
$stmt->execute([$id]);

$stmt = $pdo->prepare("SELECT r.*, c.name as cat_name, c.id as cat_id, u.username as author
                       FROM resources r
                       LEFT JOIN categories c ON r.category_id = c.id
                       LEFT JOIN users u ON r.user_id = u.id
                       WHERE r.id = ? AND r.status = 1");
$stmt->execute([$id]);
$resource = $stmt->fetch();

if (!$resource) redirect('index.php');

// 获取相关资源
$related = $pdo->prepare("SELECT * FROM resources WHERE category_id = ? AND id != ? AND status = 1 LIMIT 4");
$related->execute([$resource['cat_id'], $id]);
$related = $related->fetchAll();

// 检查是否已收藏
$isFavorited = false;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND resource_id = ?");
    $stmt->execute([$_SESSION['user_id'], $id]);
    $isFavorited = $stmt->fetch() ? true : false;
}

$page_title = $resource['title'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - <?= getSetting('site_name') ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="page-shell resource-detail">
    <?php include 'includes/header.php'; ?>

    <section class="tech-section">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">首页</a>
                <span>/</span>
                <a href="category.php?id=<?= $resource['cat_id'] ?>"><?= htmlspecialchars($resource['cat_name']) ?></a>
                <span>/</span>
                <span><?= htmlspecialchars($resource['title']) ?></span>
            </div>

            <div class="detail-content">
                <div class="detail-main">
                    <span class="pill"><i class="fas fa-bolt"></i> Resource Detail</span>
                    <h1><?= htmlspecialchars($resource['title']) ?></h1>
                    <div class="resource-meta">
                        <span><i class="fas fa-folder-tree"></i> <?= htmlspecialchars($resource['cat_name']) ?></span>
                        <span><i class="fas fa-user-astronaut"></i> <?= htmlspecialchars($resource['author']) ?></span>
                        <span><i class="fas fa-download"></i> <?= formatNumber($resource['download_count']) ?> 次</span>
                        <span><i class="fas fa-eye"></i> <?= formatNumber($resource['view_count']) ?> 次</span>
                        <span><i class="fas fa-calendar"></i> <?= date('Y-m-d', strtotime($resource['created_at'])) ?></span>
                    </div>
                    <?php if ($resource['cover_image']): ?>
                    <div class="resource-cover">
                        <img src="<?= htmlspecialchars($resource['cover_image']) ?>" alt="<?= htmlspecialchars($resource['title']) ?>">
                    </div>
                    <?php endif; ?>
                    <div class="resource-description">
                        <h3>资源介绍</h3>
                        <p><?= nl2br(htmlspecialchars($resource['description'])) ?></p>
                    </div>
                    <?php if ($resource['content']): ?>
                    <div class="resource-content">
                        <?= htmlspecialchars_decode($resource['content']) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <aside class="detail-sidebar">
                    <div class="download-box">
                        <span class="pill"><i class="fas fa-cloud-arrow-down"></i> Download Access</span>
                        <div class="file-info">
                            <p><i class="fas fa-cube"></i> 软件名称：<?= htmlspecialchars($resource['title']) ?></p>
                            <p><i class="fas fa-file"></i> 文件大小：<?= getFileSize($resource['file_size']) ?></p>
                            <p><i class="fas fa-file-code"></i> 文件类型：<?= htmlspecialchars($resource['file_type'] ?: '未知') ?></p>
                            <p><i class="fas fa-tag"></i> 版本：<?= htmlspecialchars($resource['version'] ?: '1.0') ?></p>
                        </div>
                        <a href="download.php?id=<?= $resource['id'] ?>" class="btn-download" onclick="return confirmDownload()">
                            <i class="fas fa-download"></i> 立即下载
                        </a>
                        <button class="btn-favorite <?= $isFavorited ? 'active' : '' ?>" data-id="<?= $resource['id'] ?>">
                            <i class="fas fa-heart"></i> <?= $isFavorited ? '已收藏' : '收藏' ?>
                        </button>
                    </div>
                </aside>
            </div>

            <?php if ($related): ?>
            <div class="tech-section">
                <div class="section-title">
                    <div>
                        <h2>相关推荐</h2>
                        <p>继续探索同一分类下的更多资源。</p>
                    </div>
                </div>
                <div class="resource-grid">
                    <?php foreach ($related as $item): ?>
                    <div class="resource-card">
                        <div class="card-icon"><i class="fas <?= getCategoryIcon($resource['cat_name']) ?>"></i></div>
                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                        <p><?= htmlspecialchars(mb_substr($item['description'], 0, 50)) ?>...</p>
                        <div class="card-meta">
                            <span><i class="fas fa-download"></i> <?= formatNumber($item['download_count']) ?></span>
                        </div>
                        <a href="resource.php?id=<?= $item['id'] ?>" class="btn-card">查看详情</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
    function confirmDownload() {
        return confirm('确定要下载该资源吗？');
    }

    document.querySelector('.btn-favorite')?.addEventListener('click', function() {
        const btn = this;
        const id = btn.dataset.id;
        fetch('api/favorite.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.action === 'add') {
                    btn.innerHTML = '<i class="fas fa-heart"></i> 已收藏';
                    btn.classList.add('active');
                } else {
                    btn.innerHTML = '<i class="fas fa-heart"></i> 收藏';
                    btn.classList.remove('active');
                }
            } else {
                alert(data.message || '操作失败');
            }
        });
    });
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>
