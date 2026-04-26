<?php
// 首页 - 资源下载站入口文件
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// 获取分类列表用于导航
$categories = $db->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

// 获取置顶资源
$featured = $db->query("SELECT r.*, c.name as category_name FROM resources r
                        LEFT JOIN categories c ON r.category_id = c.id
                        WHERE r.status = 1 AND r.is_featured = 1
                        ORDER BY r.created_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

// 获取最新资源
$latest = $db->query("SELECT r.*, c.name as category_name FROM resources r
                      LEFT JOIN categories c ON r.category_id = c.id
                      WHERE r.status = 1
                      ORDER BY r.created_at DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);

// 获取热门资源
$popular = $db->query("SELECT r.*, c.name as category_name FROM resources r
                       LEFT JOIN categories c ON r.category_id = c.id
                       WHERE r.status = 1
                       ORDER BY r.download_count DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);

$page_title = '首页';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>资源下载站 - 精品资源免费下载</title>
    <meta name="description" content="提供各类精品资源下载，包括软件工具、设计素材、开发资源等，全部免费下载。">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>发现精品资源<br>轻松下载</h1>
                <p>提供高质量的软件工具、设计素材、开发资源，让创作更简单</p>
                <div class="search-box">
                    <form action="search.php" method="get">
                        <input type="text" name="q" placeholder="搜索资源，如：Photoshop、UI设计...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Section -->
    <?php if (!empty($featured)): ?>
    <section class="featured">
        <div class="container">
            <div class="section-header">
                <h2>精选推荐</h2>
                <p>编辑精选的高质量资源</p>
            </div>
            <div class="resource-grid">
                <?php foreach ($featured as $item): ?>
                <div class="resource-card">
                    <div class="card-badge">推荐</div>
                    <div class="card-icon">
                        <i class="fas <?php echo getCategoryIcon($item['category_name']); ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p><?php echo htmlspecialchars(mb_substr($item['description'], 0, 60)) . '...'; ?></p>
                    <div class="card-meta">
                        <span><i class="fas fa-download"></i> <?php echo formatNumber($item['download_count']); ?></span>
                        <span><i class="fas fa-eye"></i> <?php echo formatNumber($item['view_count']); ?></span>
                        <span class="category"><?php echo htmlspecialchars($item['category_name']); ?></span>
                    </div>
                    <a href="resource.php?id=<?php echo $item['id']; ?>" class="btn-card">查看详情</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Latest Resources -->
    <section class="latest">
        <div class="container">
            <div class="section-header">
                <h2>最新资源</h2>
                <p>每日更新，不容错过</p>
                <a href="resources.php" class="view-all">查看全部 <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="resource-list">
                <div class="list-header">
                    <span>资源名称</span>
                    <span>分类</span>
                    <span>下载量</span>
                    <span>日期</span>
                    <span></span>
                </div>
                <?php foreach ($latest as $item): ?>
                <div class="list-item">
                    <div class="item-info">
                        <i class="fas <?php echo getCategoryIcon($item['category_name']); ?>"></i>
                        <div>
                            <a href="resource.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                            <small><?php echo htmlspecialchars(mb_substr($item['description'], 0, 50)); ?></small>
                        </div>
                    </div>
                    <span class="item-category"><?php echo htmlspecialchars($item['category_name']); ?></span>
                    <span class="item-downloads"><?php echo formatNumber($item['download_count']); ?></span>
                    <span class="item-date"><?php echo date('Y-m-d', strtotime($item['created_at'])); ?></span>
                    <a href="resource.php?id=<?php echo $item['id']; ?>" class="item-link">查看</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Popular Resources -->
    <section class="popular">
        <div class="container">
            <div class="section-header">
                <h2>热门下载</h2>
                <p>大家都在下载的热门资源</p>
            </div>
            <div class="popular-grid">
                <?php foreach ($popular as $item): ?>
                <div class="popular-item">
                    <div class="popular-rank">#<?php echo $rank++; ?></div>
                    <div class="popular-content">
                        <a href="resource.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                        <span><?php echo formatNumber($item['download_count']); ?> 次下载</span>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
