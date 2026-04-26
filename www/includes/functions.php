<?php
// 全局函数
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
    if ($num >= 10000) return round($num / 10000, 1) . 'w';
    return $num;
}

function getFileSize($bytes) {
    $bytes = (int)$bytes;
    if ($bytes <= 0) return '未知';

    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $size = (float)$bytes;
    $unitIndex = 0;

    while ($size >= 1024 && $unitIndex < count($units) - 1) {
        $size /= 1024;
        $unitIndex++;
    }

    $precision = $unitIndex === 0 ? 0 : 2;
    return rtrim(rtrim(number_format($size, $precision, '.', ''), '0'), '.') . ' ' . $units[$unitIndex];
}

function getCategoryIcon($name) {
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
    return $icons[$name] ?? 'fa-download';
}

function timeAgo($timestamp) {
    $diff = time() - strtotime($timestamp);
    if ($diff < 60) return '刚刚';
    if ($diff < 3600) return floor($diff / 60) . '分钟前';
    if ($diff < 86400) return floor($diff / 3600) . '小时前';
    if ($diff < 2592000) return floor($diff / 86400) . '天前';
    return date('Y-m-d', strtotime($timestamp));
}
?>
