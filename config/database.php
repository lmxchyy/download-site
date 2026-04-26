<?php
// 数据库配置 - 支持 Docker 和本地环境
// Docker 环境使用 'db'，本地环境使用 '127.0.0.1'
$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_port = 3306;
$db_name = getenv('DB_NAME') ?: 'download_db';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';
$db_charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=$db_charset";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db = $pdo;
} catch(PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

// 获取配置函数
function getSetting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['value'] : $default;
    } catch(Exception $e) {
        return $default;
    }
}
?>