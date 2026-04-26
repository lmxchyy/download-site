<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

$defaults = [
    'site_name' => '资源下载站',
    'site_description' => '提供高质量的免费资源下载，助力你的创作与开发。',
];

$keys = array_keys($defaults);
$settings = [];

$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
foreach ($stmt->fetchAll() as $item) {
    $settings[$item['key']] = $item['value'];
}

foreach ($defaults as $key => $value) {
    if (!isset($settings[$key]) || $settings[$key] === '') {
        $settings[$key] = $value;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($keys as $key) {
        $settings[$key] = trim($_POST[$key] ?? '');
    }

    if ($settings['site_name'] === '') {
        $error = '站点名称不能为空';
    } else {
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
        foreach ($keys as $key) {
            $stmt->execute([$key, $settings[$key]]);
        }
        $success = '设置已保存';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统设置 - 管理后台</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="page-shell admin-page">
    <section class="admin-container tech-section">
        <div class="container admin-shell">
            <aside class="admin-sidebar">
                <div class="admin-brand">
                    <i class="fas fa-sliders fa-2x"></i>
                    <h3>系统设置</h3>
                    <p class="muted">维护前台读取的基础站点配置。</p>
                </div>
                <div class="nav-group-label">Overview</div>
                <a href="index.php"><i class="fas fa-chart-line"></i> 仪表盘</a>
                <a href="resources.php"><i class="fas fa-database"></i> 资源管理</a>
                <div class="nav-group-label">Modules</div>
                <a href="categories.php"><i class="fas fa-folder-tree"></i> 分类管理</a>
                <a href="users.php"><i class="fas fa-users-gear"></i> 用户管理</a>
                <a href="settings.php" class="active"><i class="fas fa-sliders"></i> 系统设置</a>
                <div class="nav-group-label">Access</div>
                <a href="../index.php"><i class="fas fa-arrow-left"></i> 返回前台</a>
            </aside>

            <main class="admin-content">
                <div class="admin-content-header">
                    <div>
                        <span class="pill"><i class="fas fa-sliders"></i> Site Settings</span>
                        <h1>系统设置</h1>
                        <p>维护前台头部、标题与页脚会读取的站点信息。</p>
                    </div>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <div class="table-panel admin-form-panel">
                    <form method="POST" class="admin-form-grid">
                        <div class="form-field form-field-span-2">
                            <label for="site_name">站点名称</label>
                            <input id="site_name" type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>" required>
                        </div>
                        <div class="form-field form-field-span-2">
                            <label for="site_description">站点描述</label>
                            <textarea id="site_description" name="site_description" rows="5"><?= htmlspecialchars($settings['site_description']) ?></textarea>
                        </div>
                        <div class="form-actions form-field-span-2">
                            <button type="submit"><i class="fas fa-floppy-disk"></i> 保存设置</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </section>
</body>
</html>
