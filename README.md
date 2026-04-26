# 下载资源管理站

一个基于 PHP + MySQL 的资源下载管理系统，包含前台资源展示与下载、后台资源管理、分类管理、用户管理和站点设置，采用深色科技风界面。

## 功能特性

### 前台
- 首页精选推荐、最新资源、热门资源展示
- 全部资源列表与关键词搜索
- 分类浏览
- 资源详情页
- 直接下载与详情页下载入口
- 用户登录、注册

### 后台
- 管理员登录后进入仪表盘
- 资源管理：新增、编辑、删除、置顶、启用/禁用
- 资源文件上传
- 分类管理：新增、编辑、删除、启用/禁用
- 用户管理：查看、角色切换、删除普通用户
- 系统设置：站点名称、描述等配置管理

## 技术栈
- PHP 8
- MySQL 8
- PDO
- Apache
- Docker / Docker Compose
- Font Awesome

## 目录结构

```text
.
├─ config/                  # 数据库配置
├─ www/                     # Web 根目录
│  ├─ admin/                # 后台管理页面
│  ├─ api/                  # 前台接口
│  ├─ assets/               # CSS / JS / 图片等静态资源
│  ├─ includes/             # 公共头部、底部、函数
│  ├─ uploads/resources/    # 上传的资源文件
│  ├─ category.php          # 分类页
│  ├─ download.php          # 下载入口
│  ├─ index.php             # 前台首页
│  ├─ login.php             # 登录页
│  ├─ register.php          # 注册页
│  ├─ resource.php          # 资源详情页
│  └─ search.php            # 全部资源 / 搜索页
├─ docker-compose.yml       # Docker 编排
├─ Dockerfile               # PHP/Apache 镜像构建
├─ install.sql              # 数据库初始化脚本
├─ php.ini                  # PHP 配置
└─ vhost.conf               # Apache 虚拟主机配置
```

## 环境要求
- Docker Desktop（推荐）

或本地环境：
- PHP 8+
- MySQL 8+
- Apache / Nginx

## 快速开始

### 方式一：Docker 启动

在项目根目录执行：

```bash
docker compose up -d --build
```

启动后访问：
- 前台首页：`http://localhost:8080/`
- 后台入口：`http://localhost:8080/admin/`

### 方式二：本地环境运行
1. 创建 MySQL 数据库
2. 导入 `install.sql`
3. 配置 `config/database.php` 或环境变量：
   - `DB_HOST`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
4. 将站点根目录指向 `www/`

## 数据库初始化

执行：

```sql
SOURCE install.sql;
```

初始化内容包括：
- 分类表
- 资源表
- 用户表
- 下载记录表
- 默认分类
- 示例资源
- 默认管理员账号

## 默认管理员账号
- 邮箱：`admin@example.com`
- 密码：`admin123`

> 首次部署后建议立即修改管理员密码。

## 上传说明
- 后台资源上传文件保存目录：`www/uploads/resources/`
- 需要确保该目录对 PHP 进程可写
- 前台下载通过 `www/download.php` 统一处理

## 当前主要页面

### 前台页面
- `/` 首页
- `/search.php?q=` 全部资源 / 搜索
- `/category.php?id=1` 分类页
- `/resource.php?id=1` 资源详情页
- `/login.php` 登录
- `/register.php` 注册

### 后台页面
- `/admin/` 仪表盘
- `/admin/resources.php` 资源管理
- `/admin/resource_edit.php` 添加/编辑资源
- `/admin/categories.php` 分类管理
- `/admin/users.php` 用户管理
- `/admin/settings.php` 系统设置

## 注意事项
- 请确保数据库字符集为 `utf8mb4`
- 上传目录必须有写权限
- 如果使用 Docker，Web 根目录应指向 `www/`
- 生产环境请关闭调试输出并修改默认管理员密码

## 后续可扩展方向
- 收藏功能完善
- 下载记录页
- 资源封面管理
- 操作日志
- 权限粒度细分
- 对象存储上传

## License

仅供学习、演示与内部项目参考使用。
