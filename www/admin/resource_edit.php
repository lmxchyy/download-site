<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$error = '';
$success = '';
$uploadDir = dirname(__DIR__) . '/uploads/resources';
$uploadBasePath = 'uploads/resources';

$categories = $pdo->query("SELECT id, name FROM categories WHERE status = 1 ORDER BY sort_order, id")->fetchAll();

$resource = [
    'title' => '',
    'slug' => '',
    'description' => '',
    'file_path' => '',
    'file_size' => 0,
    'file_type' => '',
    'download_link' => '',
    'cover_image' => '',
    'category_id' => '',
    'is_featured' => 0,
    'status' => 1,
];

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM resources WHERE id = ? LIMIT 1");
    $stmt->bindValue(1, $id, PDO::PARAM_INT);
    $stmt->execute();
    $existing = $stmt->fetch();

    if (!$existing) {
        header('Location: resources.php');
        exit;
    }

    $resource = array_merge($resource, $existing);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resource['title'] = trim($_POST['title'] ?? '');
    $resource['slug'] = trim($_POST['slug'] ?? '');
    $resource['description'] = trim($_POST['description'] ?? '');
    $resource['file_size'] = max(0, (int)($_POST['file_size'] ?? 0));
    $resource['file_type'] = '';
    $resource['download_link'] = '';
    $resource['cover_image'] = '';
    $resource['category_id'] = (int)($_POST['category_id'] ?? 0);
    $resource['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
    $resource['status'] = isset($_POST['status']) ? 1 : 0;

    if ($resource['download_link'] !== '') {
        $resource['file_path'] = '';
    }

    if (isset($_FILES['resource_file']) && ($_FILES['resource_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $uploadedFile = $_FILES['resource_file'];

        if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
            $error = '资源文件上传失败';
        } else {
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                $error = '资源目录创建失败';
            } else {
                $originalName = (string)$uploadedFile['name'];
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $safeSlug = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $resource['slug']);
                $safeSlug = trim((string)$safeSlug, '-');

                if ($safeSlug === '') {
                    $safeSlug = 'resource';
                }

                $fileName = $safeSlug . '-' . date('YmdHis');
                if ($extension !== '') {
                    $fileName .= '.' . $extension;
                }

                $targetPath = $uploadDir . '/' . $fileName;
                if (!move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
                    $error = '资源文件保存失败';
                } else {
                    $resource['file_path'] = $uploadBasePath . '/' . $fileName;
                    $resource['file_size'] = (int)$uploadedFile['size'];
                    if ($resource['file_type'] === '') {
                        $resource['file_type'] = $extension;
                    }
                }
            }
        }
    }

    if ($error === '') {
        if ($resource['title'] === '') {
            $error = '请输入资源标题';
        } elseif ($resource['slug'] === '') {
            $error = '请输入资源标识';
        } elseif ($resource['file_path'] === '') {
            $error = '请上传资源文件';
        } elseif ($resource['category_id'] <= 0) {
            $error = '请选择资源分类';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM resources WHERE slug = ? AND id != ? LIMIT 1");
            $stmt->execute([$resource['slug'], $isEdit ? $id : 0]);

            if ($stmt->fetch()) {
                $error = '资源标识已存在';
            } else {
                if ($isEdit) {
                    $stmt = $pdo->prepare("UPDATE resources SET title = ?, slug = ?, description = ?, file_path = ?, file_size = ?, file_type = ?, download_link = ?, cover_image = ?, category_id = ?, is_featured = ?, status = ? WHERE id = ?");
                    $saved = $stmt->execute([
                        $resource['title'],
                        $resource['slug'],
                        $resource['description'],
                        $resource['file_path'],
                        $resource['file_size'],
                        $resource['file_type'],
                        $resource['download_link'],
                        $resource['cover_image'],
                        $resource['category_id'],
                        $resource['is_featured'],
                        $resource['status'],
                        $id,
                    ]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO resources (title, slug, description, file_path, file_size, file_type, download_link, cover_image, category_id, user_id, is_featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $saved = $stmt->execute([
                        $resource['title'],
                        $resource['slug'],
                        $resource['description'],
                        $resource['file_path'],
                        $resource['file_size'],
                        $resource['file_type'],
                        $resource['download_link'],
                        $resource['cover_image'],
                        $resource['category_id'],
                        (int)$_SESSION['user_id'],
                        $resource['is_featured'],
                        $resource['status'],
                    ]);

                    if ($saved) {
                        $id = (int)$pdo->lastInsertId();
                        $isEdit = true;
                    }
                }

                if ($saved) {
                    header('Location: resources.php');
                    exit;
                }

                $error = $isEdit ? '资源保存失败' : '资源创建失败';
            }
        }
    }
}

$pageTitle = $isEdit ? '编辑资源' : '添加资源';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - 管理后台</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="page-shell admin-page">
    <section class="admin-container tech-section">
        <div class="container admin-shell">
            <aside class="admin-sidebar">
                <div class="admin-brand">
                    <i class="fas fa-shield-halved fa-2x"></i>
                    <h3>资源管理</h3>
                    <p class="muted">维护站点的下载资源与展示状态。</p>
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
                        <span class="pill"><i class="fas fa-pen-ruler"></i> Resource Editor</span>
                        <h1><?= $pageTitle ?></h1>
                        <p>录入资源信息、分类、展示状态和下载路径。</p>
                    </div>
                    <a href="resources.php" class="btn-sm"><i class="fas fa-arrow-left"></i> 返回列表</a>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <div class="table-panel admin-form-panel">
                    <form method="POST" enctype="multipart/form-data" class="admin-form-grid">
                        <div class="form-field form-field-span-2">
                            <label for="title">资源标题</label>
                            <input id="title" type="text" name="title" value="<?= htmlspecialchars($resource['title']) ?>" required>
                        </div>

                        <div class="form-field">
                            <label for="slug">资源标识</label>
                            <input id="slug" type="text" name="slug" value="<?= htmlspecialchars($resource['slug']) ?>" required>
                        </div>

                        <div class="form-field">
                            <label for="category_id">资源分类</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">请选择分类</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= (int)$resource['category_id'] === (int)$category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-field form-field-span-2">
                            <label for="description">资源描述</label>
                            <textarea id="description" name="description" rows="5"><?= htmlspecialchars($resource['description']) ?></textarea>
                        </div>

                        <div class="form-field form-field-span-2">
                            <label for="resource_file">上传资源文件</label>
                            <input id="resource_file" type="file" name="resource_file">
                        </div>

                        <div class="form-field">
                            <label for="file_size">文件大小（字节）</label>
                            <input id="file_size" type="number" min="0" name="file_size" value="<?= htmlspecialchars((string)$resource['file_size']) ?>">
                        </div>

                        <div class="form-switches form-field-span-2">
                            <label class="switch-chip">
                                <input type="checkbox" name="is_featured" value="1" <?= (int)$resource['is_featured'] === 1 ? 'checked' : '' ?>>
                                <span>设为置顶资源</span>
                            </label>
                            <label class="switch-chip">
                                <input type="checkbox" name="status" value="1" <?= (int)$resource['status'] === 1 ? 'checked' : '' ?>>
                                <span>启用资源</span>
                            </label>
                        </div>

                        <div class="form-actions form-field-span-2">
                            <button type="submit"><i class="fas fa-floppy-disk"></i> 保存资源</button>
                            <a href="resources.php" class="btn-sm">取消</a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </section>
</body>
</html>
