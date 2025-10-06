# 数据库设置指南

## 错误分析

您遇到的错误是：`Fatal error: Uncaught PDOException: SQLSTATE[42S02]: Base table or view not found: 1146 Table 'test_9zcnb_com.posts' doesn't exist`

这表明系统正在尝试访问 `test_9zcnb_com` 数据库，但该数据库中不存在 `posts` 表。

## 解决方案

### 步骤1：数据库配置已更新

根据您提供的信息，我们已经为您更新了 `includes/config.php` 文件中的数据库连接信息：

```php
// 数据库配置 - 已根据您提供的信息更新
define('DB_HOST', 'localhost');  // 数据库主机，通常是 localhost
define('DB_NAME', 'test_9zcnb_com'); // 数据库名称
define('DB_USER', 'test_9zcnb_com'); // 数据库用户名
define('DB_PASS', '4j5Dt4hFxrkrPFtt'); // 数据库密码
```

**注意：** 配置已正确设置，您无需再修改。

### 步骤2：创建数据库表结构

使用以下 SQL 语句在您的数据库中创建所需的表结构和示例数据：

```sql
-- 创建文章表
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    category_id INT,
    author VARCHAR(100) DEFAULT 'admin',
    view_count INT DEFAULT 0,
    comment_count INT DEFAULT 0,
    status ENUM('published', 'draft', 'pending') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建分类表
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    post_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建评论表
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    website VARCHAR(255),
    content TEXT NOT NULL,
    status ENUM('approved', 'pending', 'spam') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建标签表
CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    post_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建文章标签关联表
CREATE TABLE IF NOT EXISTS post_tags (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建用户表
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor', 'author', 'subscriber') DEFAULT 'subscriber',
    display_name VARCHAR(100),
    bio TEXT,
    avatar VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建页面表
CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    author_id INT,
    status ENUM('published', 'draft', 'pending') DEFAULT 'draft',
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建配置表
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    value TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 插入示例分类数据
INSERT INTO categories (name, slug, description, created_at) VALUES
('技术', 'tech', '技术相关的文章', NOW()),
('生活', 'life', '生活随笔', NOW()),
('经验分享', 'experience', '经验心得分享', NOW());

-- 插入示例文章数据
INSERT INTO posts (title, content, excerpt, category_id, status, created_at) VALUES
('PHP 8.0 新特性详解', 
'<p>PHP 8.0 带来了许多令人兴奋的新特性，包括 JIT 编译器、命名参数、联合类型、匹配表达式等。</p>
<p>JIT 编译器可以显著提高 PHP 代码的执行性能，特别是在计算密集型任务中。命名参数允许开发者在调用函数时指定参数名，使代码更加清晰易懂。</p>
<p>联合类型扩展了 PHP 的类型系统，允许一个变量接受多种类型的值。匹配表达式提供了一种更强大的 switch 语句替代方案。</p>',
'PHP 8.0 带来了许多令人兴奋的新特性，包括 JIT 编译器、命名参数、联合类型、匹配表达式等。', 
1, 'published', NOW()),
('如何设计一个可扩展的博客系统', 
'<p>设计一个可扩展的博客系统需要考虑多个方面，包括数据库设计、代码架构、性能优化等。</p>
<p>在数据库设计方面，应该采用规范化的设计原则，合理划分表结构，避免数据冗余。同时，也要考虑查询性能，适当添加索引。</p>
<p>在代码架构方面，应该采用 MVC 或类似的设计模式，将业务逻辑、数据访问和视图展示分离，便于维护和扩展。</p>',
'设计一个可扩展的博客系统需要考虑多个方面，包括数据库设计、代码架构、性能优化等。', 
1, 'published', NOW()),
('我的编程学习之路', 
'<p>每个人的编程学习之路都是不同的，但都会遇到各种挑战和困难。分享一下我的编程学习经验，希望能对初学者有所帮助。</p>
<p>首先，选择一门适合自己的编程语言入门非常重要。对于零基础的人来说，可以从 Python 或 JavaScript 开始，这两门语言相对容易上手。</p>
<p>其次，实践是学习编程的关键。不要只看书或视频，要多动手写代码，解决实际问题。</p>',
'每个人的编程学习之路都是不同的，但都会遇到各种挑战和困难。分享一下我的编程学习经验。', 
3, 'published', NOW());

-- 更新分类的文章数量
UPDATE categories SET post_count = (SELECT COUNT(*) FROM posts WHERE posts.category_id = categories.id);

-- 插入示例评论数据
INSERT INTO comments (post_id, name, email, content, status, created_at) VALUES
(1, '张三', 'zhangsan@example.com', '非常好的文章，学到了很多关于 PHP 8.0 的新特性。', 'approved', NOW()),
(1, '李四', 'lisi@example.com', '期待更多关于 PHP 新特性的文章！', 'approved', NOW()),
(2, '王五', 'wangwu@example.com', '这篇文章对我设计博客系统很有帮助，谢谢分享！', 'approved', NOW());

-- 更新文章的评论数量
UPDATE posts SET comment_count = (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id AND comments.status = 'approved');

-- 创建索引以提高查询性能
CREATE INDEX idx_posts_title ON posts(title);
CREATE INDEX idx_posts_created_at ON posts(created_at);
CREATE INDEX idx_comments_post_id ON comments(post_id);
CREATE INDEX idx_comments_created_at ON comments(created_at);
```

### 步骤3：执行SQL语句

您可以使用phpMyAdmin、MySQL Workbench或其他数据库管理工具执行上述SQL语句，创建所需的表结构和示例数据。

### 步骤4：验证设置

完成上述步骤后，刷新您的博客页面，系统应该能够正常连接到数据库并显示内容了。

## 注意事项

1. 如果您的数据库有权限限制，请确保您使用的数据库用户具有创建表和插入数据的权限
2. 示例数据仅供参考，您可以根据需要修改或删除
3. 定期备份您的数据库，以防止数据丢失

如果您有任何问题，请查看详细的错误日志以获取更多信息。