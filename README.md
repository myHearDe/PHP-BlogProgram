# PHP专业博客程序

这是一款PHP博客程序，采用了模块化设计，具有清晰的代码结构和良好的扩展性。程序包含文章管理、分类管理、评论系统等核心功能，并提供了清新美观的默认模板。

## 系统特性

- **模块化设计**：清晰的代码结构，便于维护和扩展
- **响应式布局**：适配各种设备屏幕，包括桌面、平板和手机
- **文章管理**：支持发布、编辑、删除文章
- **分类系统**：支持文章分类管理
- **评论功能**：支持读者评论和评论管理
- **搜索功能**：支持按关键词搜索文章
- **统计信息**：提供博客文章、分类、评论数量统计
- **清新模板**：默认使用现代化的清新设计风格
- **SEO优化**：支持自定义页面标题和描述

## 技术栈

- PHP 7.0+（推荐PHP 7.4或更高版本）
- MySQL 5.6+（推荐MySQL 8.0或更高版本）
- HTML5 + CSS3 + JavaScript
- Font Awesome 图标库
- Chart.js 图表库

## 安装指南

### 1. 环境准备

确保您的服务器满足以下要求：
- PHP 7.0或更高版本
- MySQL 5.6或更高版本
- 开启PDO扩展
- 开启mbstring扩展

### 2. 数据库设置

1. **创建数据库**
   使用phpMyAdmin或其他MySQL管理工具创建一个新的数据库，建议使用utf8mb4字符集。
   
2. **导入数据库表结构**
   导入`database.sql`文件到您创建的数据库中，这个文件包含了博客所需的所有表结构和示例数据。
   
   ```bash
   # 也可以使用命令行导入数据库
   mysql -u username -p database_name < database.sql
   ```

### 3. 配置文件设置

根据您提供的信息，我们已经为您更新了`includes/config.php`文件中的数据库连接信息：

```php
// 数据库配置
define('DB_HOST', 'localhost'); // 数据库主机，通常是localhost
define('DB_NAME', ''); // 数据库名
define('DB_USER', ''); // 数据库用户名
define('DB_PASS', ''); // 数据库密码

// 博客基本设置
define('BLOG_NAME', '我的专业博客');          // 博客名称
define('BLOG_DESCRIPTION', '分享我的知识和经验'); // 博客描述
define('BLOG_AUTHOR', '博主');               // 博主名称
define('BLOG_EMAIL', 'admin@example.com');   // 博主邮箱

define('BLOG_URL', 'http://localhost/blog'); // 博客URL，修改为您的实际域名
```

```

### 4. 部署到Web服务器

将所有文件上传到您的Web服务器根目录或子目录中。确保Web服务器（如Apache、Nginx）配置正确，能够解析PHP文件。

### 5. 访问博客

在浏览器中输入您的博客URL，即可访问博客首页。

## 目录结构

```
├── index.php             # 博客入口文件
├── includes/             # 核心功能文件目录
│   ├── config.php        # 配置文件
│   ├── functions.php     # 辅助函数
│   ├── database.php      # 数据库连接类
│   └── blog.php          # 博客核心业务逻辑
├── templates/            # 模板文件目录
│   └── default/          # 默认模板
│       ├── header.php    # 模板头部
│       ├── home.php      # 首页模板
│       ├── single.php    # 文章详情模板
│       ├── category.php  # 分类模板
│       ├── about.php     # 关于页面模板
│       ├── footer.php    # 模板页脚
│       ├── style.css     # 样式文件
│       └── script.js     # JavaScript文件
├── database.sql          # 数据库表结构
└── README.md             # 项目说明
```
