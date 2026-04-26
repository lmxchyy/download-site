<?php
session_start();
require_once '../config/database.php';
require_once 'includes/functions.php';

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$resources = [];
$total = 0;

if ($keyword !== '') {
    $stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS r.*, c.name as cat_name
                           FROM resources r
                           LEFT JOIN categories c ON r.category_id = c.id
                           WHERE r.status = 1 AND (r.title LIKE ? OR r.description LIKE ?)
                           ORDER BY r.created_at DESC
                           LIMIT ? OFFSET ?");
    $searchTerm = "%$keyword%";
    $stmt->bindValue(1, $searchTerm, PDO::PARAM_STR);
    $stmt->bindValue(2, $searchTerm, PDO::PARAM_STR);
    $stmt->bindValue(3, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(4, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $resources = $stmt->fetchAll();
    $total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
} else {
    $stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS r.*, c.name as cat_name
                           FROM resources r
                           LEFT JOIN categories c ON r.category_id = c.id
                           WHERE r.status = 1
                           ORDER BY r.created_at DESC
                           LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $resources = $stmt->fetchAll();
    $total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
}

$page_title = $keyword !== '' ? "搜索结果: $keyword" : '全部资源';
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
<body class="page-shell search-results">
    <?php include 'includes/header.php'; ?>

    <section class="tech-section">
        <div class="container">
            <div class="search-header">
                <div>
                    <span class="pill"><i class="fas fa-radar"></i> Search Console</span>
                    <h1><?= htmlspecialchars($keyword !== '' ? "搜索：$keyword" : '全部资源') ?></h1>
                    <p>共找到 <?= $total ?> 个相关资源，继续使用关键词精确筛选。</p>
                </div>
                <form class="search-form-inline" action="search.php" method="get">
                    <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="重新输入关键词">
                    <button type="submit"><i class="fas fa-search"></i> 搜索</button>
                </form>
            </div>

            <?php if ($resources): ?>
            <div class="resource-list">
                <?php foreach ($resources as $item): ?>
                <div class="result-item">
                    <div class="result-info">
                        <h3><a href="resource.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['title']) ?></a></h3>
                        <p><?= htmlspecialchars(mb_substr($item['description'], 0, 100)) ?>...</p>
                        <div class="result-meta">
                            <span><i class="fas fa-folder"></i> <?= htmlspecialchars($item['cat_name']) ?></span>
                            <span><i class="fas fa-download"></i> <?= formatNumber($item['download_count']) ?> 次</span>
                            <span><i class="fas fa-calendar"></i> <?= date('Y-m-d', strtotime($item['created_at'])) ?></span>
                        </div>
                    </div>
                    <div class="table-actions">
                        <a href="resource.php?id=<?= $item['id'] ?>" class="btn-sm">查看详情</a>
                        <a href="download.php?id=<?= $item['id'] ?>" class="btn-sm"><i class="fas fa-download"></i> 直接下载</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($total > $perPage): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                <a href="?q=<?= urlencode($keyword) ?>&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="no-results glass-panel">
                <i class="fas fa-satellite"></i>
                <p>没有找到匹配结果，试试更短的关键词或切换分类浏览。</p>
                <a href="index.php" class="btn">返回首页</a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
