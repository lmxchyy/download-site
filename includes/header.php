<?php
// 网站头部
if (!isset($page_title)) {
    $page_title = '资源下载站';
}
$base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($base_path === '/' || $base_path === '.') {
    $base_path = '';
}
$asset_path = $base_path === '' ? 'assets/css/style.css' : $base_path . '/assets/css/style.css';
$home_path = $base_path === '' ? 'index.php' : $base_path . '/index.php';
$resources_path = $base_path === '' ? 'resources.php' : $base_path . '/resources.php';
$login_path = $base_path === '' ? 'login.php' : $base_path . '/login.php';
$register_path = $base_path === '' ? 'register.php' : $base_path . '/register.php';
$logout_path = $base_path === '' ? 'logout.php' : $base_path . '/logout.php';
$user_dashboard_path = $base_path === '' ? 'user/dashboard.php' : $base_path . '/user/dashboard.php';
$user_favorites_path = $base_path === '' ? 'user/favorites.php' : $base_path . '/user/favorites.php';
$user_downloads_path = $base_path === '' ? 'user/downloads.php' : $base_path . '/user/downloads.php';
$admin_index_path = $base_path === '' ? 'admin/index.php' : $base_path . '/admin/index.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - 资源下载站</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($asset_path); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <div class="header-inner">
            <div class="logo">
                <a href="<?php echo htmlspecialchars($home_path); ?>">
                    <i class="fas fa-cloud-download-alt"></i>
                    <span>资源<span class="highlight">下载站</span></span>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="<?php echo htmlspecialchars($home_path); ?>"><i class="fas fa-home"></i> 首页</a></li>
                    <li><a href="<?php echo htmlspecialchars($resources_path); ?>"><i class="fas fa-th-large"></i> 全部资源</a></li>
                    <li class="dropdown">
                        <a href="#"><i class="fas fa-folder"></i> 分类 <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <?php
                            $cats = $db->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order")->fetchAll();
                            foreach ($cats as $cat):
                            ?>
                            <li><a href="category.php?id=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            </nav>
            <div class="header-actions">
                <?php if (isLoggedIn()): ?>
                    <div class="user-menu">
                        <a href="javascript:void(0)" class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="user-dropdown">
                            <li><a href="<?php echo htmlspecialchars($user_dashboard_path); ?>"><i class="fas fa-tachometer-alt"></i> 个人中心</a></li>
                            <li><a href="<?php echo htmlspecialchars($user_favorites_path); ?>"><i class="fas fa-heart"></i> 我的收藏</a></li>
                            <li><a href="<?php echo htmlspecialchars($user_downloads_path); ?>"><i class="fas fa-download"></i> 下载记录</a></li>
                            <?php if (isAdmin()): ?>
                            <li><a href="<?php echo htmlspecialchars($admin_index_path); ?>"><i class="fas fa-cog"></i> 管理后台</a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo htmlspecialchars($logout_path); ?>"><i class="fas fa-sign-out-alt"></i> 退出登录</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo htmlspecialchars($login_path); ?>" class="btn-login"><i class="fas fa-sign-in-alt"></i> 登录</a>
                    <a href="<?php echo htmlspecialchars($register_path); ?>" class="btn-register">注册</a>
                <?php endif; ?>
            </div>
            <button class="mobile-toggle"><i class="fas fa-bars"></i></button>
        </div>
    </div>
</header>
