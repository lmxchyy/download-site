<?php
$siteName = getSetting('site_name');
$currentDir = trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$basePrefix = $currentDir === '' ? '' : str_repeat('../', substr_count($currentDir, '/'));
$siteTagline = 'NEXT GEN RESOURCE PORTAL';
$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order")->fetchAll();
?>
<header class="site-header">
    <div class="container header-inner">
        <div class="logo">
            <a href="<?= $basePrefix ?>index.php">
                <span class="logo-mark"><i class="fas fa-satellite-dish"></i></span>
                <span class="logo-text">
                    <small><?= htmlspecialchars($siteTagline) ?></small>
                    <strong><?= htmlspecialchars($siteName) ?></strong>
                </span>
            </a>
        </div>

        <nav class="main-nav">
            <ul>
                <li><a href="<?= $basePrefix ?>index.php"><i class="fas fa-house-signal"></i> 首页</a></li>
                <li><a href="<?= $basePrefix ?>search.php?q="><i class="fas fa-grid-2"></i> 全部资源</a></li>
                <li class="dropdown category-dropdown">
                    <button type="button" class="dropdown-toggle" aria-expanded="false"><i class="fas fa-layer-group"></i> 分类 <i class="fas fa-chevron-down"></i></button>
                    <ul class="dropdown-menu">
                        <?php foreach ($categories as $cat): ?>
                        <li><a href="<?= $basePrefix ?>category.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
        </nav>

        <div class="header-actions">
            <?php if (isLoggedIn()): ?>
            <div class="user-menu">
                <button type="button" class="user-menu-toggle" aria-expanded="false"><i class="fas fa-circle-user"></i> <?= htmlspecialchars($_SESSION['username']) ?> <i class="fas fa-chevron-down"></i></button>
                <div class="user-dropdown">
                    <a href="<?= $basePrefix ?>user/dashboard.php"><i class="fas fa-chart-line"></i> 个人中心</a>
                    <a href="<?= $basePrefix ?>user/favorites.php"><i class="fas fa-heart"></i> 我的收藏</a>
                    <a href="<?= $basePrefix ?>user/downloads.php"><i class="fas fa-download"></i> 下载记录</a>
                    <?php if (isAdmin()): ?>
                    <a href="<?= $basePrefix ?>admin/"><i class="fas fa-shield-halved"></i> 管理后台</a>
                    <?php endif; ?>
                    <a href="<?= $basePrefix ?>logout.php"><i class="fas fa-right-from-bracket"></i> 退出登录</a>
                </div>
            </div>
            <?php else: ?>
            <a href="<?= $basePrefix ?>login.php" class="btn-login secondary"><i class="fas fa-right-to-bracket"></i> 登录</a>
            <a href="<?= $basePrefix ?>register.php" class="btn-register"><i class="fas fa-user-plus"></i> 注册</a>
            <?php endif; ?>
        </div>

        <button class="menu-toggle"><i class="fas fa-bars"></i></button>
    </div>
</header>
<script>
(() => {
    const dropdown = document.querySelector('.category-dropdown');
    const toggle = dropdown?.querySelector('.dropdown-toggle');
    const userMenu = document.querySelector('.user-menu');
    const userToggle = userMenu?.querySelector('.user-menu-toggle');

    toggle?.addEventListener('click', (event) => {
        event.stopPropagation();
        const isOpen = dropdown.classList.toggle('open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    userToggle?.addEventListener('click', (event) => {
        event.stopPropagation();
        const isOpen = userMenu.classList.toggle('open');
        userToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', (event) => {
        if (dropdown && toggle && !dropdown.contains(event.target)) {
            dropdown.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }

        if (userMenu && userToggle && !userMenu.contains(event.target)) {
            userMenu.classList.remove('open');
            userToggle.setAttribute('aria-expanded', 'false');
        }
    });
})();
</script>
