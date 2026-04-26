<?php
// 网站底部
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3><i class="fas fa-cloud-download-alt"></i> 资源下载站</h3>
                <p>提供高质量的免费资源下载，包括软件工具、设计素材、开发资源等，助力您的创作之路。</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-weixin"></i></a>
                    <a href="#"><i class="fab fa-weibo"></i></a>
                    <a href="#"><i class="fab fa-github"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>快速链接</h4>
                <ul>
                    <li><a href="index.php">首页</a></li>
                    <li><a href="resources.php">全部资源</a></li>
                    <li><a href="about.php">关于我们</a></li>
                    <li><a href="contact.php">联系我们</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>热门分类</h4>
                <ul>
                    <?php
                    $hotCats = $db->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order LIMIT 5")->fetchAll();
                    foreach ($hotCats as $cat):
                    ?>
                    <li><a href="category.php?id=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="footer-section">
                <h4>联系我们</h4>
                <ul>
                    <li><i class="fas fa-envelope"></i> admin@example.com</li>
                    <li><i class="fas fa-phone"></i> 400-123-4567</li>
                    <li><i class="fas fa-clock"></i> 周一至周日 9:00-18:00</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2024 资源下载站. All rights reserved. <a href="#">隐私政策</a> | <a href="#">用户协议</a></p>
        </div>
    </div>
</footer>
