<?php
session_start();
require_once '../config/database.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) redirect('index.php');

$stmt = $pdo->prepare("SELECT * FROM resources WHERE id = ? AND status = 1");
$stmt->execute([$id]);
$resource = $stmt->fetch();

if (!$resource) redirect('index.php');

// 更新下载次数
$pdo->prepare("UPDATE resources SET download_count = download_count + 1 WHERE id = ?")->execute([$id]);

// 记录下载日志
$stmt = $pdo->prepare("INSERT INTO downloads (resource_id, user_id, ip_address, user_agent) VALUES (?, ?, ?, ?)");
$stmt->execute([
    $id,
    $_SESSION['user_id'] ?? null,
    $_SERVER['REMOTE_ADDR'],
    $_SERVER['HTTP_USER_AGENT']
]);

if ($resource['download_link']) {
    header("Location: " . $resource['download_link']);
    exit;
}

$filePath = trim((string)($resource['file_path'] ?? ''));
if ($filePath === '') {
    die('文件不存在');
}

if (preg_match('#^(?:[a-zA-Z]:[\\/]|/)#', $filePath)) {
    $absolutePath = $filePath;
} else {
    $normalizedPath = ltrim(str_replace('\\', '/', $filePath), '/');
    $absolutePath = __DIR__ . '/' . $normalizedPath;
}

if (!is_file($absolutePath)) {
    die('文件不存在');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . rawurlencode(basename($absolutePath)) . '"; filename*=UTF-8\'\'' . rawurlencode(basename($absolutePath)));
header('Content-Length: ' . filesize($absolutePath));
readfile($absolutePath);
exit;
