<?php $siteName = getSetting('site_name'); ?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-about">
                <span class="pill"><i class="fas fa-wave-square"></i> DIGITAL RESOURCE HUB</span>
                <h3><?= htmlspecialchars($siteName) ?></h3>
                <p>用更快的检索、更稳定的下载体验和更现代的界面，帮助你高效发现软件工具、设计素材与开发资源。</p>
            </div>
            <div class="footer-links">
                <h4>快速链接</h4>
                <ul>
                    <li><a href="index.php">首页总览</a></li>
                    <li><a href="search.php?q=">资源探索</a></li>
                    <li><a href="login.php">账号登录</a></li>
                    <li><a href="register.php">创建账号</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© <?= date('Y') ?> <?= htmlspecialchars($siteName) ?> · Powered by a futuristic dark interface.</p>
        </div>
    </div>
</footer>
