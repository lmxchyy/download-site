<?php
// 首页
session_start();
require_once '../config/database.php';
require_once 'includes/functions.php';

// 获取分类
$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order")->fetchAll();

// 置顶资源
$featured = $pdo->query("SELECT r.*, c.name as cat_name FROM resources r
                         LEFT JOIN categories c ON r.category_id = c.id
                         WHERE r.status = 1 AND r.is_featured = 1
                         ORDER BY r.created_at DESC LIMIT 6")->fetchAll();

// 最新资源
$latest = $pdo->query("SELECT r.*, c.name as cat_name FROM resources r
                       LEFT JOIN categories c ON r.category_id = c.id
                       WHERE r.status = 1
                       ORDER BY r.created_at DESC LIMIT 10")->fetchAll();

// 热门资源
$popular = $pdo->query("SELECT r.*, c.name as cat_name FROM resources r
                        LEFT JOIN categories c ON r.category_id = c.id
                        WHERE r.status = 1
                        ORDER BY r.download_count DESC LIMIT 8")->fetchAll();

$page_title = '首页';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= getSetting('site_name') ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="page-shell">
    <?php include 'includes/header.php'; ?>

    <section class="hero tech-section">
        <div class="container">
            <div class="hero-panel">
                <div class="hero-grid">
                    <div class="hero-copy">
                        <span class="pill"><i class="fas fa-sparkles"></i> AI · Design · Dev Assets</span>
                        <h1>发现精品资源<br>轻松下载</h1>
                        <p>以更快的搜索、更清晰的分类和更具未来感的交互，探索软件工具、设计素材、开发资源与实用模板。</p>
                        <form class="search-form" action="search.php" method="get">
                            <input type="text" name="q" placeholder="搜索资源、工具、教程或关键词" autocomplete="off">
                            <button type="submit"><i class="fas fa-magnifying-glass"></i> 搜索</button>
                        </form>
                        <div class="hero-stats">
                            <div class="metric-card glass-panel">
                                <span>资源分类</span>
                                <strong><?= count($categories) ?></strong>
                            </div>
                            <div class="metric-card glass-panel">
                                <span>精选推荐</span>
                                <strong><?= count($featured) ?></strong>
                            </div>
                            <div class="metric-card glass-panel">
                                <span>热门资源</span>
                                <strong><?= count($popular) ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="hero-orbit">
                        <div class="orbit-ring ring-2"></div>
                        <div class="orbit-ring ring-1"></div>
                        <div class="orbit-core"><i class="fas fa-microchip"></i></div>
                        <div class="orbit-node node-1"><i class="fas fa-code"></i></div>
                        <div class="orbit-node node-2"><i class="fas fa-palette"></i></div>
                        <div class="orbit-node node-3"><i class="fas fa-download"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($featured): ?>
    <section class="featured tech-section">
        <div class="container">
            <div class="section-title">
                <div>
                    <h2>精选推荐</h2>
                    <p>优先查看编辑精选与高质量热门资源。</p>
                </div>
                <a href="search.php?q=" class="view-all">查看全部 <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="resource-grid">
                <?php foreach ($featured as $item): ?>
                <div class="resource-card">
                    <span class="card-badge"><i class="fas fa-star"></i> 推荐</span>
                    <div class="card-icon"><i class="fas <?= getCategoryIcon($item['cat_name']) ?>"></i></div>
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p><?= htmlspecialchars(mb_substr($item['description'], 0, 60)) ?>...</p>
                    <div class="card-meta">
                        <span><i class="fas fa-download"></i> <?= formatNumber($item['download_count']) ?></span>
                        <span><i class="fas fa-eye"></i> <?= formatNumber($item['view_count']) ?></span>
                        <span class="meta-pill"><?= htmlspecialchars($item['cat_name']) ?></span>
                    </div>
                    <a href="resource.php?id=<?= $item['id'] ?>" class="btn-card">查看详情</a>
                    <a href="download.php?id=<?= $item['id'] ?>" class="btn-sm"><i class="fas fa-download"></i> 直接下载</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($latest): ?>
    <section class="tech-section">
        <div class="container">
            <div class="section-title">
                <div>
                    <h2>最新资源</h2>
                    <p>持续更新，第一时间获取新上架的内容。</p>
                </div>
                <span class="pill"><i class="fas fa-clock"></i> Fresh Updates</span>
            </div>
            <div class="resource-grid">
                <?php foreach ($latest as $item): ?>
                <div class="resource-card">
                    <div class="card-icon"><i class="fas <?= getCategoryIcon($item['cat_name']) ?>"></i></div>
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p><?= htmlspecialchars(mb_substr($item['description'], 0, 60)) ?>...</p>
                    <div class="card-meta">
                        <span><i class="fas fa-calendar"></i> <?= date('Y-m-d', strtotime($item['created_at'])) ?></span>
                        <span class="meta-pill"><?= htmlspecialchars($item['cat_name']) ?></span>
                    </div>
                    <a href="resource.php?id=<?= $item['id'] ?>" class="btn-card">查看详情</a>
                    <a href="download.php?id=<?= $item['id'] ?>" class="btn-sm"><i class="fas fa-download"></i> 直接下载</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
