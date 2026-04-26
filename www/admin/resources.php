<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// 处理删除
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM resources WHERE id = ?")->execute([$id]);
    header('Location: resources.php');
    exit;
}

// 处理置顶
if (isset($_GET['featured']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $featured = (int)$_GET['featured'];
    $pdo->prepare("UPDATE resources SET is_featured = ? WHERE id = ?")->execute([$featured, $id]);
    header('Location: resources.php');
    exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 15;
$offset = ($page - 1) * $perPage;

$total = $pdo->query("SELECT COUNT(*) FROM resources")->fetchColumn();
$stmt = $pdo->prepare("SELECT r.*, c.name as cat_name FROM resources r LEFT JOIN categories c ON r.category_id = c.id ORDER BY r.created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$resources = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>资源管理</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="page-shell admin-page">
    <section class="admin-container tech-section">
        <div class="container admin-shell">
            <aside class="admin-sidebar">
                <div class="admin-brand">
                    <i class="fas fa-server fa-2x"></i>
                    <h3>资源管理</h3>
                    <p class="muted">统一管理平台内的资源内容。</p>
                </div>
                <div class="nav-group-label">Overview</div>
                <a href="index.php"><i class="fas fa-chart-line"></i> 仪表盘</a>
                <a href="resources.php" class="active"><i class="fas fa-database"></i> 资源管理</a>
                <div class="nav-group-label">Modules</div>
                <a href="categories.php"><i class="fas fa-folder-tree"></i> 分类管理</a>
                <a href="users.php"><i class="fas fa-users-gear"></i> 用户管理</a>
                <a href="settings.php"><i class="fas fa-sliders"></i> 系统设置</a>
                <div class="nav-group-label">Access</div>
                <a href="../index.php"><i class="fas fa-arrow-left"></i> 返回前台</a>
            </aside>

            <main class="admin-content">
                <div class="admin-content-header">
                    <div>
                        <span class="pill"><i class="fas fa-database"></i> Resource Control</span>
                        <h1>资源管理</h1>
                        <p>查看资源列表、设置置顶状态并执行基础管理操作。</p>
                    </div>
                    <a href="resource_edit.php" class="btn-add"><i class="fas fa-plus"></i> 添加资源</a>
                </div>

                <div class="table-panel">
                    <table class="admin-table">
                        <thead>
                            <tr><th>ID</th><th>标题</th><th>分类</th><th>下载量</th><th>置顶</th><th>创建时间</th><th>操作</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resources as $item): ?>
                            <tr>
                                <td><?= $item['id'] ?></td>
                                <td><?= htmlspecialchars(mb_substr($item['title'], 0, 30)) ?></td>
                                <td><?= htmlspecialchars($item['cat_name']) ?></td>
                                <td><?= $item['download_count'] ?></td>
                                <td>
                                    <?php if ($item['is_featured']): ?>
                                    <a href="?featured=0&id=<?= $item['id'] ?>" class="btn-sm">取消</a>
                                    <?php else: ?>
                                    <a href="?featured=1&id=<?= $item['id'] ?>" class="btn-sm">置顶</a>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('Y-m-d', strtotime($item['created_at'])) ?></td>
                                <td>
                                    <a href="resource_edit.php?id=<?= $item['id'] ?>" class="btn-sm">编辑</a>
                                    <a href="?delete=<?= $item['id'] ?>" class="btn-sm" onclick="return confirm('确定删除？')">删除</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if ($total > $perPage): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                        <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </section>
</body>
</html>
