<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$error = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        header('Location: categories.php');
        exit;
    }
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE categories SET status = IF(status = 1, 0, 1) WHERE id = ?");
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        header('Location: categories.php');
        exit;
    }
}

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$form = [
    'name' => '',
    'slug' => '',
    'icon' => 'fa-folder',
    'sort_order' => 0,
    'status' => 1,
];

if ($editId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
    $stmt->bindValue(1, $editId, PDO::PARAM_INT);
    $stmt->execute();
    $existing = $stmt->fetch();

    if ($existing) {
        $form = array_merge($form, $existing);
    } else {
        $editId = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = (int)($_POST['id'] ?? 0);
    $form['name'] = trim($_POST['name'] ?? '');
    $form['slug'] = trim($_POST['slug'] ?? '');
    $form['icon'] = trim($_POST['icon'] ?? '') ?: 'fa-folder';
    $form['sort_order'] = (int)($_POST['sort_order'] ?? 0);
    $form['status'] = isset($_POST['status']) ? 1 : 0;

    if ($form['name'] === '') {
        $error = '请输入分类名称';
    } elseif ($form['slug'] === '') {
        $error = '请输入分类标识';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? AND id != ? LIMIT 1");
        $stmt->execute([$form['slug'], $editId]);

        if ($stmt->fetch()) {
            $error = '分类标识已存在';
        } else {
            if ($editId > 0) {
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, icon = ?, sort_order = ?, status = ? WHERE id = ?");
                $stmt->execute([
                    $form['name'],
                    $form['slug'],
                    $form['icon'],
                    $form['sort_order'],
                    $form['status'],
                    $editId,
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, sort_order, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $form['name'],
                    $form['slug'],
                    $form['icon'],
                    $form['sort_order'],
                    $form['status'],
                ]);
            }

            header('Location: categories.php');
            exit;
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>分类管理 - 管理后台</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="page-shell admin-page">
    <section class="admin-container tech-section">
        <div class="container admin-shell">
            <aside class="admin-sidebar">
                <div class="admin-brand">
                    <i class="fas fa-folder-tree fa-2x"></i>
                    <h3>分类管理</h3>
                    <p class="muted">维护前台资源分类与展示顺序。</p>
                </div>
                <div class="nav-group-label">Overview</div>
                <a href="index.php"><i class="fas fa-chart-line"></i> 仪表盘</a>
                <a href="resources.php"><i class="fas fa-database"></i> 资源管理</a>
                <div class="nav-group-label">Modules</div>
                <a href="categories.php" class="active"><i class="fas fa-folder-tree"></i> 分类管理</a>
                <a href="users.php"><i class="fas fa-users-gear"></i> 用户管理</a>
                <a href="settings.php"><i class="fas fa-sliders"></i> 系统设置</a>
                <div class="nav-group-label">Access</div>
                <a href="../index.php"><i class="fas fa-arrow-left"></i> 返回前台</a>
            </aside>

            <main class="admin-content">
                <div class="admin-content-header">
                    <div>
                        <span class="pill"><i class="fas fa-folder-tree"></i> Category Control</span>
                        <h1>分类管理</h1>
                        <p>创建分类、调整图标与排序，并控制前台可见状态。</p>
                    </div>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="table-panel admin-form-panel">
                    <div class="panel-heading">
                        <div>
                            <h2><?= $editId > 0 ? '编辑分类' : '添加分类' ?></h2>
                            <p>填写分类名称、标识与图标类名。</p>
                        </div>
                    </div>
                    <form method="POST" class="admin-form-grid">
                        <input type="hidden" name="id" value="<?= $editId ?>">
                        <div class="form-field">
                            <label for="name">分类名称</label>
                            <input id="name" type="text" name="name" value="<?= htmlspecialchars($form['name']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="slug">分类标识</label>
                            <input id="slug" type="text" name="slug" value="<?= htmlspecialchars($form['slug']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="icon">图标类名</label>
                            <input id="icon" type="text" name="icon" value="<?= htmlspecialchars($form['icon']) ?>" placeholder="fa-folder">
                        </div>
                        <div class="form-field">
                            <label for="sort_order">排序</label>
                            <input id="sort_order" type="number" name="sort_order" value="<?= htmlspecialchars((string)$form['sort_order']) ?>">
                        </div>
                        <div class="form-switches form-field-span-2">
                            <label class="switch-chip">
                                <input type="checkbox" name="status" value="1" <?= (int)$form['status'] === 1 ? 'checked' : '' ?>>
                                <span>前台显示</span>
                            </label>
                        </div>
                        <div class="form-actions form-field-span-2">
                            <button type="submit"><i class="fas fa-floppy-disk"></i> 保存分类</button>
                            <?php if ($editId > 0): ?>
                            <a href="categories.php" class="btn-sm">取消编辑</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="table-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>分类列表</h2>
                            <p>当前已配置的资源分类。</p>
                        </div>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr><th>ID</th><th>名称</th><th>标识</th><th>图标</th><th>排序</th><th>状态</th><th>操作</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['id'] ?></td>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= htmlspecialchars($category['slug']) ?></td>
                                <td><i class="fas <?= htmlspecialchars($category['icon']) ?>"></i> <?= htmlspecialchars($category['icon']) ?></td>
                                <td><?= (int)$category['sort_order'] ?></td>
                                <td><span class="status-pill <?= (int)$category['status'] === 1 ? 'status-active' : '' ?>"><?= (int)$category['status'] === 1 ? '启用' : '停用' ?></span></td>
                                <td class="table-actions">
                                    <a href="?edit=<?= $category['id'] ?>" class="btn-sm">编辑</a>
                                    <a href="?toggle=<?= $category['id'] ?>" class="btn-sm"><?= (int)$category['status'] === 1 ? '禁用' : '启用' ?></a>
                                    <a href="?delete=<?= $category['id'] ?>" class="btn-sm" onclick="return confirm('确定删除该分类？')">删除</a>
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
