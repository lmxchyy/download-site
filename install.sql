CREATE DATABASE IF NOT EXISTS `download_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `download_db`;

-- 分类表
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `icon` VARCHAR(50) DEFAULT 'fa-folder',
    `sort_order` INT DEFAULT 0,
    `status` TINYINT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 资源表
CREATE TABLE `resources` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(200) NOT NULL UNIQUE,
    `description` TEXT,
    `file_path` VARCHAR(500) NOT NULL,
    `file_size` BIGINT DEFAULT 0,
    `file_type` VARCHAR(50) DEFAULT NULL,
    `download_link` VARCHAR(500) DEFAULT NULL,
    `cover_image` VARCHAR(500) DEFAULT NULL,
    `category_id` INT NOT NULL,
    `download_count` INT DEFAULT 0,
    `view_count` INT DEFAULT 0,
    `is_featured` TINYINT DEFAULT 0,
    `status` TINYINT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
);

-- 用户表
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- 下载记录表
CREATE TABLE `downloads` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `resource_id` INT NOT NULL,
    `user_id` INT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `downloaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`resource_id`) REFERENCES `resources`(`id`) ON DELETE CASCADE
);

-- 插入默认分类
INSERT INTO `categories` (`name`, `slug`, `icon`, `sort_order`) VALUES
('软件工具', 'software', 'fa-laptop-code', 1),
('设计素材', 'design', 'fa-palette', 2),
('开发资源', 'development', 'fa-code', 3),
('电子书籍', 'books', 'fa-book', 4),
('视频教程', 'tutorials', 'fa-video', 5),
('办公模板', 'templates', 'fa-file-alt', 6);

-- 插入示例资源
INSERT INTO `resources` (`title`, `slug`, `description`, `file_path`, `category_id`, `download_count`, `view_count`, `is_featured`) VALUES
('Visual Studio Code 编辑器', 'vscode-editor', '强大的轻量级代码编辑器，支持多种编程语言和扩展。', '/uploads/vscode.exe', 1, 1250, 3420, 1),
('Adobe Photoshop 2024', 'photoshop-2024', '专业图像处理软件，设计创作必备工具。', '/uploads/photoshop.zip', 2, 890, 2100, 1),
('Python 3.12 入门教程', 'python-basics', 'Python编程语言入门到精通视频教程。', '/uploads/python-course.mp4', 5, 560, 1450, 0),
('企业PPT模板合集', 'ppt-templates', '100+套专业PPT模板，适用于各种商务场景。', '/uploads/ppt-templates.zip', 6, 2340, 4120, 1);

-- 插入管理员账户 (密码: admin123)
-- 密码哈希值: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');