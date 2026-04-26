<?php
// 全局函数库
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function formatNumber($num) {
    if ($num >= 10000) {
        return round($num / 10000, 1) . 'w';
    }
    return $num;
}

function getCategoryIcon($categoryName) {
    $icons = [
        '软件工具' => 'fa-laptop-code',
        '设计素材' => 'fa-palette',
        '开发资源' => 'fa-code',
        '电子书籍' => 'fa-book',
        '视频教程' => 'fa-video',
        '办公模板' => 'fa-file-alt',
        '游戏娱乐' => 'fa-gamepad',
        '其他' => 'fa-folder'
    ];
    return $icons[$categoryName] ?? 'fa-download';
}

function getFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}

function timeAgo($timestamp) {
    $time = strtotime($timestamp);
    $diff = time() - $time;
    if ($diff < 60) return '刚刚';
    if ($diff < 3600) return floor($diff / 60) . '分钟前';
    if ($diff < 86400) return floor($diff / 3600) . '小时前';
    if ($diff < 2592000) return floor($diff / 86400) . '天前';
    return date('Y-m-d', $time);
}

function generateToken() {
    return bin2hex(random_bytes(32));
}

function verifyToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
