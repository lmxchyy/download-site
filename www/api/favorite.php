<?php
session_start();
require_once '../../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => '请先登录']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$resourceId = $data['id'] ?? 0;

if (!$resourceId) {
    echo json_encode(['status' => 'error', 'message' => '参数错误']);
    exit;
}

$userId = $_SESSION['user_id'];

// 检查是否已收藏
$stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND resource_id = ?");
$stmt->execute([$userId, $resourceId]);
$exists = $stmt->fetch();

if ($exists) {
    // 取消收藏
    $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND resource_id = ?")->execute([$userId, $resourceId]);
    echo json_encode(['status' => 'success', 'action' => 'remove']);
} else {
    // 添加收藏
    $pdo->prepare("INSERT INTO favorites (user_id, resource_id) VALUES (?, ?)")->execute([$userId, $resourceId]);
    echo json_encode(['status' => 'success', 'action' => 'add']);
}
