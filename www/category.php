<?php
session_start();
require_once '../config/database.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) redirect('index.php');

// 获取分类信息
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? AND status = 1");
$stmt->execute([$id]);
$category = $stmt->fetch();
if (!$category) redirect('index.php');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// 获取资源列表
$stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS r.* FROM resources r
                       WHERE r.category_id = ? AND r.status = 1
                       ORDER BY r.created_at DESC
                       LIMIT ? OFFSET ?");
$stmt->bindValue(1, $id, PDO::PARAM_INT);
$stmt->bindValue(2, $perPage, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$resources = $stmt->fetchAll();
$total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();

$page_title = $category['name'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category['name']) ?> - 资源分类</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="page-shell category-page">
    <?php include 'includes/header.php'; ?>

    <section class="tech-section">
        <div class="container">
            <div class="category-header">
                <div class="icon-badge"><i class="fas <?= htmlspecialchars($category['icon']) ?>"></i></div>
                <div>
                    <span class="pill"><i class="fas fa-layer-group"></i> Category View</span>
                    <h1><?= htmlspecialchars($category['name']) ?></h1>
                    <p><?= htmlspecialchars($category['description'] ?: '按主题聚合浏览资源，快速定位你需要的内容。') ?></p>
                </div>
            </div>

            <?php if ($resources): ?>
            <div class="resource-grid">
                <?php foreach ($resources as $item): ?>
                <div class="resource-card">
                    <div class="card-icon"><i class="fas <?= htmlspecialchars($category['icon']) ?>"></i></div>
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p><?= htmlspecialchars(mb_substr($item['description'], 0, 60)) ?>...</p>
                    <div class="card-meta">
                        <span><i class="fas fa-download"></i> <?= formatNumber($item['download_count']) ?></span>
                        <span><i class="fas fa-eye"></i> <?= formatNumber($item['view_count']) ?></span>
                    </div>
                    <a href="resource.php?id=<?= $item['id'] ?>" class="btn-card">查看详情</a>
                    <a href="download.php?id=<?= $item['id'] ?>" class="btn-sm"><i class="fas fa-download"></i> 直接下载</a>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($total > $perPage): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                <a href="?id=<?= $id ?>&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="no-results glass-panel">
                <i class="fas fa-box-open"></i>
                <p>当前分类下暂无资源，稍后再来看看。</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
