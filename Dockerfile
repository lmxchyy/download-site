FROM php:8.2-apache

# 安装系统依赖
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mysqli zip gd

# 启用 Apache 重写模块
RUN a2enmod rewrite

# 设置工作目录
WORKDIR /var/www/html

# 复制项目文件
COPY . /var/www/html/

# 设置权限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 配置 Apache 虚拟主机
COPY vhost.conf /etc/apache2/sites-available/000-default.conf

# 暴露端口
EXPOSE 80

# 启动 Apache
CMD ["apache2-foreground"]