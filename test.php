<?php
// 简单的数据库查询测试页面
require_once 'config/database.php';

echo "<h1>资源下载站 - 数据库测试</h1>";

// 测试分类查询
try {
    $stmt = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order");
    $categories = $stmt->fetchAll();

    echo "<h2>分类列表 (共 " . count($categories) . " 个)</h2>";
    echo "<ul>";
    foreach ($categories as $cat) {
        echo "<li>" . htmlspecialchars($cat['name']) . " - " . htmlspecialchars($cat['slug']) . "</li>";
    }
    echo "</ul>";

    // 测试资源查询
    $stmt = $pdo->query("SELECT r.*, c.name as cat_name FROM resources r LEFT JOIN categories c ON r.category_id = c.id WHERE r.status = 1 LIMIT 5");
    $resources = $stmt->fetchAll();

    echo "<h2>资源列表 (共 " . count($resources) . " 个)</h2>";
    echo "<ul>";
    foreach ($resources as $res) {
        echo "<li>" . htmlspecialchars($res['title']) . " - " . htmlspecialchars($res['cat_name']) . " (下载: " . $res['download_count'] . ")</li>";
    }
    echo "</ul>";

    echo "<p style='color: green;'>✅ 数据库连接成功！所有查询正常。</p>";
    echo "<p><a href='index.php'>返回首页</a></p>";

} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ 查询失败: " . $e->getMessage() . "</p>";
}
?>