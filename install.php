<?php
/**
 * 博客系统安装脚本
 * 此文件负责引导用户完成博客系统的首次安装过程
 */

// 检查是否已安装
function check_installed() {
    // 检查配置文件是否存在
    if (file_exists('includes/config.php')) {
        require_once 'includes/config.php';
        // 检查是否有必要的常量定义
        if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
            try {
                // 尝试连接数据库
                $pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
                
                // 检查必要的表是否存在
                $tables = ['users', 'posts', 'categories', 'comments', 'settings'];
                $all_tables_exist = true;
                
                foreach ($tables as $table) {
                    $stmt = $pdo->prepare("SHOW TABLES LIKE :table");
                    $stmt->execute([':table' => $table]);
                    if (!$stmt->fetch()) {
                        $all_tables_exist = false;
                        break;
                    }
                }
                
                return $all_tables_exist;
            } catch (PDOException $e) {
                // 连接失败，可能是配置错误或数据库不可用
                return false;
            }
        }
    }
    return false;
}

// 如果已安装，显示已安装页面
if (check_installed()) {
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>系统已安装 - 博客安装向导</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                text-align: center;
            }
            .container {
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                padding: 40px;
                margin-top: 50px;
            }
            h1 {
                color: #4a5568;
                margin-bottom: 20px;
            }
            p {
                color: #718096;
                margin-bottom: 30px;
            }
            .btn {
                display: inline-block;
                background: #4299e1;
                color: white;
                text-decoration: none;
                padding: 10px 20px;
                border-radius: 4px;
                font-weight: 500;
                transition: background-color 0.2s;
            }
            .btn:hover {
                background: #3182ce;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>系统已安装</h1>
            <p>博客系统已经完成安装。如果您需要重新安装，请先删除 config.php 文件并清空数据库。</p>
            <a href="index.php" class="btn">访问博客首页</a>
            <a href="admin/login.php" class="btn" style="margin-left: 10px;">登录管理后台</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 初始化安装步骤
$step = 1;
if (isset($_POST['step'])) {
    $step = intval($_POST['step']);
}

// 定义默认变量
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'blog';
$db_port = '3306';
$admin_username = '';
$admin_email = '';
$admin_password = '';
$admin_confirm_password = '';
$blog_title = '我的博客';
$blog_description = '欢迎访问我的博客';
$author_name = '博主';
$author_email = 'admin@example.com';
$errors = [];

// 步骤1：数据库配置
if ($step == 1 && isset($_POST['submit_db'])) {
    $db_host = $_POST['db_host'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $db_name = $_POST['db_name'];
    $db_port = $_POST['db_port'];
    
    // 保存到会话中，确保在整个安装过程中可用
    session_start();
    $_SESSION['install_db_host'] = $db_host;
    $_SESSION['install_db_user'] = $db_user;
    $_SESSION['install_db_pass'] = $db_pass;
    $_SESSION['install_db_name'] = $db_name;
    $_SESSION['install_db_port'] = $db_port;
    
    // 验证表单
    if (empty($db_host)) {
        $errors[] = '数据库主机不能为空';
    }
    if (empty($db_user)) {
        $errors[] = '数据库用户名不能为空';
    }
    if (empty($db_name)) {
        $errors[] = '数据库名称不能为空';
    }
    if (empty($db_port) || !is_numeric($db_port)) {
        $errors[] = '数据库端口必须是数字';
    }
    
    // 尝试连接数据库
    if (empty($errors)) {
        try {
            $pdo = new PDO(
                "mysql:host=$db_host;port=$db_port;charset=utf8mb4",
                $db_user,
                $db_pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // 检查数据库是否存在，如果不存在则创建
            try {
                $pdo->query("CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $step = 2;
            } catch (PDOException $e) {
                $errors[] = '创建数据库失败：' . $e->getMessage();
            }
        } catch (PDOException $e) {
            $errors[] = '数据库连接失败：' . $e->getMessage();
        }
    }
}

// 步骤2：管理员账户设置
if ($step == 2 && isset($_POST['submit_admin'])) {
    $admin_username = $_POST['admin_username'];
    $admin_email = $_POST['admin_email'];
    $admin_password = $_POST['admin_password'];
    $admin_confirm_password = $_POST['admin_confirm_password'];
    
    // 确保会话已启动
    if (!isset($_SESSION)) {
        session_start();
    }
    
    // 保存到会话中
    $_SESSION['install_admin_username'] = $admin_username;
    $_SESSION['install_admin_email'] = $admin_email;
    $_SESSION['install_admin_password'] = $admin_password;
    
    // 验证表单
    if (empty($admin_username)) {
        $errors[] = '用户名不能为空';
    }
    if (empty($admin_email) || !filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '请输入有效的电子邮箱';
    }
    if (empty($admin_password)) {
        $errors[] = '密码不能为空';
    }
    if (strlen($admin_password) < 6) {
        $errors[] = '密码长度不能少于6个字符';
    }
    if ($admin_password !== $admin_confirm_password) {
        $errors[] = '两次输入的密码不一致';
    }
    
    if (empty($errors)) {
        $step = 3;
    }
}

// 步骤3：博客基本信息
if ($step == 3 && isset($_POST['submit_blog'])) {
    $blog_title = $_POST['blog_title'];
    $blog_description = $_POST['blog_description'];
    $author_name = $_POST['author_name'];
    $author_email = $_POST['author_email'];
    
    // 确保会话已启动
    if (!isset($_SESSION)) {
        session_start();
    }
    
    // 保存到会话中
    $_SESSION['install_blog_title'] = $blog_title;
    $_SESSION['install_blog_description'] = $blog_description;
    $_SESSION['install_author_name'] = $author_name;
    $_SESSION['install_author_email'] = $author_email;
    
    // 验证表单
    if (empty($blog_title)) {
        $errors[] = '博客标题不能为空';
    }
    if (empty($author_name)) {
        $errors[] = '作者名称不能为空';
    }
    if (empty($author_email) || !filter_var($author_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '请输入有效的电子邮箱';
    }
    
    if (empty($errors)) {
        $step = 4;
    }
}

// 步骤4：完成安装
// 确保会话已启动
if (!isset($_SESSION)) {
    session_start();
}

// 从会话中恢复所有步骤的信息
if ($step == 4) {
    // 恢复步骤1的数据库信息
    if (isset($_SESSION['install_db_host'])) $db_host = $_SESSION['install_db_host'];
    if (isset($_SESSION['install_db_user'])) $db_user = $_SESSION['install_db_user'];
    if (isset($_SESSION['install_db_pass'])) $db_pass = $_SESSION['install_db_pass'];
    if (isset($_SESSION['install_db_name'])) $db_name = $_SESSION['install_db_name'];
    if (isset($_SESSION['install_db_port'])) $db_port = $_SESSION['install_db_port'];
    
    // 恢复步骤2的管理员信息
    if (isset($_SESSION['install_admin_username'])) $admin_username = $_SESSION['install_admin_username'];
    if (isset($_SESSION['install_admin_email'])) $admin_email = $_SESSION['install_admin_email'];
    if (isset($_SESSION['install_admin_password'])) $admin_password = $_SESSION['install_admin_password'];
    
    // 恢复步骤3的博客信息
    if (isset($_SESSION['install_blog_title'])) $blog_title = $_SESSION['install_blog_title'];
    if (isset($_SESSION['install_blog_description'])) $blog_description = $_SESSION['install_blog_description'];
    if (isset($_SESSION['install_author_name'])) $author_name = $_SESSION['install_author_name'];
    if (isset($_SESSION['install_author_email'])) $author_email = $_SESSION['install_author_email'];
}

if ($step == 4 && isset($_POST['submit_install'])) {
    // 从隐藏字段中获取并更新数据库信息
    $db_host = $_POST['db_host'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $db_name = $_POST['db_name'];
    $db_port = $_POST['db_port'];
    
    try {
        // 创建配置文件
        $config_content = "<?php\n";
        $config_content .= "/**\n";
        $config_content .= " * PHP专业博客程序 - 配置文件\n";
        $config_content .= " * 重要：请根据您的实际环境修改以下配置！\n";
        $config_content .= " * 此文件由安装向导自动生成\n";
        $config_content .= " */\n\n";
        
        // 数据库配置
        $config_content .= "// 数据库配置\n";
        $config_content .= "define('DB_HOST', '" . addslashes($db_host) . "'); // 数据库主机，通常是localhost\n";
        $config_content .= "define('DB_NAME', '" . addslashes($db_name) . "'); // 数据库名\n";
        $config_content .= "define('DB_USER', '" . addslashes($db_user) . "'); // 数据库用户名\n";
        $config_content .= "define('DB_PASS', '" . addslashes($db_pass) . "'); // 数据库密码\n";
        $config_content .= "define('DB_PORT', '" . addslashes($db_port) . "'); // 数据库端口\n\n";
        
        // 博客基本设置
        $config_content .= "// 博客基本设置\n";
        $config_content .= "define('BLOG_NAME', '" . addslashes($blog_title) . "');\n";
        $config_content .= "define('BLOG_DESCRIPTION', '" . addslashes($blog_description) . "');\n";
        $config_content .= "define('BLOG_AUTHOR', '" . addslashes($author_name) . "');\n";
        $config_content .= "define('BLOG_EMAIL', '" . addslashes($author_email) . "');\n";
        $config_content .= "define('POSTS_PER_PAGE', 5);\n\n";
        
        // URL设置
        $config_content .= "// URL设置\n";
        $config_content .= "define('BLOG_URL', 'http://' . \$_SERVER['HTTP_HOST']);\n\n";
        
        // 时区设置
        $config_content .= "// 时区设置\n";
        $config_content .= "date_default_timezone_set('Asia/Shanghai');\n\n";
        
        // 错误报告设置
        $config_content .= "// 错误报告设置\n";
        $config_content .= "error_reporting(E_ALL);\n";
        $config_content .= "ini_set('display_errors', 1);\n";
        $config_content .= "\n?>";
        
        // 写入配置文件
        if (!file_put_contents('includes/config.php', $config_content)) {
            throw new Exception('无法创建配置文件，请确保服务器有写入权限');
        }
        
        // 设置文件权限
        @chmod('includes/config.php', 0644);
        
        // 连接数据库并创建表
        require_once 'includes/config.php';
        
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        
        // 创建用户表
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建分类表
        $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            post_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建文章表
        $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建评论表
        $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建标签表
        $pdo->exec("CREATE TABLE IF NOT EXISTS tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            slug VARCHAR(100) NOT NULL UNIQUE,
            post_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建文章标签关联表
        $pdo->exec("CREATE TABLE IF NOT EXISTS post_tags (
            post_id INT NOT NULL,
            tag_id INT NOT NULL,
            PRIMARY KEY (post_id, tag_id),
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
            FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建页面表
        $pdo->exec("CREATE TABLE IF NOT EXISTS pages (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建配置表
        $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            value TEXT NOT NULL,
            type VARCHAR(50) DEFAULT 'text',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 插入管理员用户（检查是否已存在）
        $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $check_stmt->execute([$admin_username, $admin_email]);
        $user_exists = $check_stmt->fetchColumn();
        
        if ($user_exists == 0) {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$admin_username, $admin_email, $password_hash]);
        } else {
            // 如果用户已存在，更新密码
            $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin' WHERE username = ? OR email = ?");
            $stmt->execute([$password_hash, $admin_username, $admin_email]);
        }
        
        // 插入默认分类（未分类）- 检查是否已存在
        $check_cat_stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE slug = ?");
        $check_cat_stmt->execute(['uncategorized']);
        $cat_exists = $check_cat_stmt->fetchColumn();
        
        if ($cat_exists == 0) {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
            $stmt->execute(['未分类', 'uncategorized', '默认分类']);
        }
        
        // 插入博客基本设置
        $blog_url = 'http://' . $_SERVER['HTTP_HOST'];
        $settings = [
            ['name' => 'blog_name', 'value' => $blog_title],
            ['name' => 'blog_description', 'value' => $blog_description],
            ['name' => 'blog_author', 'value' => $author_name],
            ['name' => 'blog_email', 'value' => $author_email],
            ['name' => 'blog_url', 'value' => $blog_url],
            ['name' => 'posts_per_page', 'value' => '5'],
            ['name' => 'comments_per_page', 'value' => '10'],
            ['name' => 'enable_comments', 'value' => '1'],
            ['name' => 'comment_moderation', 'value' => '1'],
            ['name' => 'date_format', 'value' => 'Y-m-d'],
            ['name' => 'time_format', 'value' => 'H:i:s'],
            ['name' => 'timezone', 'value' => 'Asia/Shanghai'],
            ['name' => 'cache_enabled', 'value' => '0']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
        foreach ($settings as $setting) {
            $stmt->execute([$setting['name'], $setting['value']]);
        }
        
        // 插入示例文章（检查是否已存在）
        $check_posts_stmt = $pdo->prepare("SELECT COUNT(*) FROM posts");
        $check_posts_stmt->execute();
        $posts_count = $check_posts_stmt->fetchColumn();
        
        if ($posts_count == 0) {
            $category_id = 1; // 未分类的ID
            $stmt = $pdo->prepare("INSERT INTO posts (title, content, excerpt, category_id, author, status) VALUES (?, ?, ?, ?, ?, ?)");
            $content = "<p>这是一篇示例文章，用于展示博客系统的功能。您可以在安装完成后登录管理后台编辑或删除这篇文章。</p><p>博客系统功能包括：</p><ul><li>文章管理</li><li>分类管理</li><li>评论管理</li><li>用户管理</li><li>系统设置</li></ul><p>感谢使用本博客系统！</p>";
            $stmt->execute(['欢迎使用博客系统', $content, '这是一篇欢迎文章，介绍博客系统的基本功能。', $category_id, $admin_username, 'published']);
            
            // 插入更多示例文章
            $stmt->execute(['如何开始使用博客', '本教程将帮助您快速上手博客系统的基本操作。', '了解如何发布文章、管理评论和修改设置。', $category_id, $admin_username, 'published']);
            $stmt->execute(['博客优化技巧', '学习如何优化您的博客，提升用户体验和搜索引擎排名。', 'SEO优化、性能调优和内容创作建议。', $category_id, $admin_username, 'draft']);
        }
        
        // 插入示例评论（检查是否已存在）
        $check_comments_stmt = $pdo->prepare("SELECT COUNT(*) FROM comments");
        $check_comments_stmt->execute();
        $comments_count = $check_comments_stmt->fetchColumn();
        
        if ($comments_count == 0 && $posts_count == 0) {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, name, email, content, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([1, '访客', 'visitor@example.com', '这是一条示例评论，展示博客的评论功能。', 'approved']);
            $stmt->execute([1, '张三', 'zhangsan@example.com', '这个博客系统看起来很不错！', 'approved']);
            $stmt->execute([2, '李四', 'lisi@example.com', '感谢分享这些使用技巧。', 'pending']);
        }
        
        // 更新分类的文章数量
        $pdo->exec("UPDATE categories SET post_count = (SELECT COUNT(*) FROM posts WHERE category_id = categories.id)");
        
        // 更新文章的评论数量
        $pdo->exec("UPDATE posts SET comment_count = (SELECT COUNT(*) FROM comments WHERE post_id = posts.id AND status = 'approved')");
        
        // 安装成功
        $success = true;
    } catch (Exception $e) {
        $errors[] = '安装过程中发生错误：' . $e->getMessage();
        // 清理已创建的配置文件
        if (file_exists('includes/config.php')) {
            unlink('includes/config.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>博客安装向导</title>
    <style>
        :root {
            --primary-color: #4299e1;
            --primary-hover: #3182ce;
            --secondary-color: #4a5568;
            --background-color: #f7fafc;
            --card-background: #ffffff;
            --error-color: #e53e3e;
            --success-color: #38a169;
            --border-color: #e2e8f0;
            --text-color: #2d3748;
            --text-secondary: #718096;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--background-color);
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--card-background);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        .content {
            padding: 30px;
        }
        
        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        
        .steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--border-color);
            transform: translateY(-50%);
            z-index: 0;
        }
        
        .steps::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: <?php echo ($step - 1) * 25; ?>%;
            height: 2px;
            background: var(--primary-color);
            transform: translateY(-50%);
            z-index: 1;
            transition: width 0.3s ease;
        }
        
        .step {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 25%;
        }
        
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--border-color);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }
        
        .step.active .step-number {
            background: var(--primary-color);
            color: white;
        }
        
        .step.completed .step-number {
            background: var(--success-color);
            color: white;
        }
        
        .step-label {
            font-size: 12px;
            color: var(--text-secondary);
            text-align: center;
        }
        
        .step.active .step-label,
        .step.completed .step-label {
            color: var(--text-color);
            font-weight: 500;
        }
        
        h2 {
            margin-bottom: 20px;
            color: var(--secondary-color);
            font-size: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--text-color);
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        
        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn:hover {
            background: var(--primary-hover);
        }
        
        .btn-back {
            background: var(--text-secondary);
            margin-right: 10px;
        }
        
        .btn-back:hover {
            background: var(--secondary-color);
        }
        
        .errors {
            background: #fed7d7;
            color: var(--error-color);
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .errors ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .success {
            background: #c6f6d5;
            color: var(--success-color);
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .success h2 {
            color: var(--success-color);
            margin-bottom: 10px;
        }
        
        .info-box {
            background: #ebf8ff;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 4px 4px 0;
        }
        
        .info-box p {
            margin: 0;
            color: var(--primary-color);
        }
        
        .summary {
            background: #f7fafc;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .summary-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .summary-label {
            font-weight: 500;
            color: var(--text-secondary);
        }
        
        .summary-value {
            color: var(--text-color);
        }
        
        @media (max-width: 600px) {
            .steps {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .steps::before,
            .steps::after {
                display: none;
            }
            
            .step {
                flex-direction: row;
                width: 100%;
                margin-bottom: 10px;
            }
            
            .step-number {
                margin-bottom: 0;
                margin-right: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>博客系统安装向导</h1>
        </div>
        
        <div class="content">
            <!-- 步骤指示器 -->
            <div class="steps">
                <div class="step <?php echo $step >= 1 ? 'active' : ''; ?>">
                    <div class="step-number">1</div>
                    <div class="step-label">数据库配置</div>
                </div>
                <div class="step <?php echo $step >= 2 ? 'active' : ''; ?>">
                    <div class="step-number">2</div>
                    <div class="step-label">管理员账户</div>
                </div>
                <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">
                    <div class="step-number">3</div>
                    <div class="step-label">博客信息</div>
                </div>
                <div class="step <?php echo $step >= 4 ? 'active' : ''; ?>">
                    <div class="step-number">4</div>
                    <div class="step-label">完成安装</div>
                </div>
            </div>
            
            <!-- 错误信息 -->
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- 安装成功 -->
            <?php if (isset($success) && $success): ?>
                <div class="success">
                    <h2>安装成功！</h2>
                    <p>博客系统已成功安装完成。</p>
                    <p><strong>管理员账户：</strong><?php echo $admin_username; ?></p>
                    <p><strong>博客标题：</strong><?php echo $blog_title; ?></p>
                    <p style="margin-top: 20px;">
                        <a href="index.php" class="btn" style="text-decoration: none;">访问博客首页</a>
                        <a href="admin/login.php" class="btn" style="text-decoration: none; margin-left: 10px;">登录管理后台</a>
                    </p>
                </div>
            <?php else: ?>
                <!-- 步骤1：数据库配置 -->
                <?php if ($step == 1): ?>
                    <h2>步骤1：数据库配置</h2>
                    <div class="info-box">
                        <p>请输入您的MySQL数据库连接信息。如果您不确定，请联系您的服务器管理员。</p>
                    </div>
                    <form method="POST" action="install.php">
                        <input type="hidden" name="step" value="1">
                        
                        <div class="form-group">
                            <label for="db_host">数据库主机</label>
                            <input type="text" id="db_host" name="db_host" value="<?php echo htmlspecialchars($db_host); ?>" placeholder="通常是 localhost">
                        </div>
                        
                        <div class="form-group">
                            <label for="db_port">数据库端口</label>
                            <input type="number" id="db_port" name="db_port" value="<?php echo htmlspecialchars($db_port); ?>" placeholder="默认是 3306">
                        </div>
                        
                        <div class="form-group">
                            <label for="db_user">数据库用户名</label>
                            <input type="text" id="db_user" name="db_user" value="<?php echo htmlspecialchars($db_user); ?>" placeholder="例如：root">
                        </div>
                        
                        <div class="form-group">
                            <label for="db_pass">数据库密码</label>
                            <input type="password" id="db_pass" name="db_pass" value="<?php echo htmlspecialchars($db_pass); ?>" placeholder="如果没有密码，请留空">
                        </div>
                        
                        <div class="form-group">
                            <label for="db_name">数据库名称</label>
                            <input type="text" id="db_name" name="db_name" value="<?php echo htmlspecialchars($db_name); ?>" placeholder="例如：blog">
                        </div>
                        
                        <button type="submit" name="submit_db" class="btn">下一步</button>
                    </form>
                <?php endif; ?>
                
                <!-- 步骤2：管理员账户设置 -->
                <?php if ($step == 2): ?>
                    <h2>步骤2：管理员账户设置</h2>
                    <div class="info-box">
                        <p>请创建博客系统的管理员账户。此账户将拥有系统的所有权限。</p>
                    </div>
                    <form method="POST" action="install.php">
                        <input type="hidden" name="step" value="2">
                        <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($db_host); ?>">
                        <input type="hidden" name="db_user" value="<?php echo htmlspecialchars($db_user); ?>">
                        <input type="hidden" name="db_pass" value="<?php echo htmlspecialchars($db_pass); ?>">
                        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($db_name); ?>">
                        <input type="hidden" name="db_port" value="<?php echo htmlspecialchars($db_port); ?>">
                        
                        <div class="form-group">
                            <label for="admin_username">用户名</label>
                            <input type="text" id="admin_username" name="admin_username" value="<?php echo htmlspecialchars($admin_username); ?>" placeholder="请输入管理员用户名">
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_email">电子邮箱</label>
                            <input type="email" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($admin_email); ?>" placeholder="请输入管理员电子邮箱">
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_password">密码</label>
                            <input type="password" id="admin_password" name="admin_password" placeholder="请设置管理员密码（至少6个字符）">
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_confirm_password">确认密码</label>
                            <input type="password" id="admin_confirm_password" name="admin_confirm_password" placeholder="请再次输入密码">
                        </div>
                        
                        <button type="submit" name="submit_admin" class="btn">下一步</button>
                        <button type="submit" name="back" class="btn btn-back">上一步</button>
                    </form>
                <?php endif; ?>
                
                <!-- 步骤3：博客基本信息 -->
                <?php if ($step == 3): ?>
                    <h2>步骤3：博客基本信息</h2>
                    <div class="info-box">
                        <p>请设置您的博客基本信息，这些信息将显示在您的博客首页和搜索引擎结果中。</p>
                    </div>
                    <form method="POST" action="install.php">
                        <input type="hidden" name="step" value="3">
                        <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($db_host); ?>">
                        <input type="hidden" name="db_user" value="<?php echo htmlspecialchars($db_user); ?>">
                        <input type="hidden" name="db_pass" value="<?php echo htmlspecialchars($db_pass); ?>">
                        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($db_name); ?>">
                        <input type="hidden" name="db_port" value="<?php echo htmlspecialchars($db_port); ?>">
                        <input type="hidden" name="admin_username" value="<?php echo htmlspecialchars($admin_username); ?>">
                        <input type="hidden" name="admin_email" value="<?php echo htmlspecialchars($admin_email); ?>">
                        <input type="hidden" name="admin_password" value="<?php echo htmlspecialchars($admin_password); ?>">
                        <input type="hidden" name="admin_confirm_password" value="<?php echo htmlspecialchars($admin_confirm_password); ?>">
                        
                        <div class="form-group">
                            <label for="blog_title">博客标题</label>
                            <input type="text" id="blog_title" name="blog_title" value="<?php echo htmlspecialchars($blog_title); ?>" placeholder="请输入博客标题">
                        </div>
                        
                        <div class="form-group">
                            <label for="blog_description">博客描述</label>
                            <textarea id="blog_description" name="blog_description" rows="3" placeholder="请输入博客简短描述"><?php echo htmlspecialchars($blog_description); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="author_name">作者名称</label>
                            <input type="text" id="author_name" name="author_name" value="<?php echo htmlspecialchars($author_name); ?>" placeholder="请输入作者名称">
                        </div>
                        
                        <div class="form-group">
                            <label for="author_email">作者电子邮箱</label>
                            <input type="email" id="author_email" name="author_email" value="<?php echo htmlspecialchars($author_email); ?>" placeholder="请输入作者电子邮箱">
                        </div>
                        
                        <button type="submit" name="submit_blog" class="btn">下一步</button>
                        <button type="submit" name="back" class="btn btn-back">上一步</button>
                    </form>
                <?php endif; ?>
                
                <!-- 步骤4：完成安装 -->
                <?php if ($step == 4): ?>
                    <h2>步骤4：确认安装</h2>
                    <div class="info-box">
                        <p>请确认以下安装信息，点击"完成安装"按钮开始安装博客系统。</p>
                    </div>
                    
                    <div class="summary">
                        <h3 style="margin-bottom: 15px; color: var(--secondary-color);">安装信息摘要</h3>
                        
                        <div class="summary-item">
                            <div class="summary-label">数据库主机</div>
                            <div class="summary-value"><?php echo htmlspecialchars($db_host); ?></div>
                        </div>
                        
                        <div class="summary-item">
                            <div class="summary-label">数据库端口</div>
                            <div class="summary-value"><?php echo htmlspecialchars($db_port); ?></div>
                        </div>
                        
                        <div class="summary-item">
                            <div class="summary-label">数据库名称</div>
                            <div class="summary-value"><?php echo htmlspecialchars($db_name); ?></div>
                        </div>
                        
                        <div class="summary-item">
                            <div class="summary-label">管理员用户名</div>
                            <div class="summary-value"><?php echo htmlspecialchars($admin_username); ?></div>
                        </div>
                        
                        <div class="summary-item">
                            <div class="summary-label">博客标题</div>
                            <div class="summary-value"><?php echo htmlspecialchars($blog_title); ?></div>
                        </div>
                    </div>
                    
                    <form method="POST" action="install.php">
                        <input type="hidden" name="step" value="4">
                        <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($db_host); ?>">
                        <input type="hidden" name="db_user" value="<?php echo htmlspecialchars($db_user); ?>">
                        <input type="hidden" name="db_pass" value="<?php echo htmlspecialchars($db_pass); ?>">
                        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($db_name); ?>">
                        <input type="hidden" name="db_port" value="<?php echo htmlspecialchars($db_port); ?>">
                        <input type="hidden" name="admin_username" value="<?php echo htmlspecialchars($admin_username); ?>">
                        <input type="hidden" name="admin_email" value="<?php echo htmlspecialchars($admin_email); ?>">
                        <input type="hidden" name="admin_password" value="<?php echo htmlspecialchars($admin_password); ?>">
                        <input type="hidden" name="blog_title" value="<?php echo htmlspecialchars($blog_title); ?>">
                        <input type="hidden" name="blog_description" value="<?php echo htmlspecialchars($blog_description); ?>">
                        <input type="hidden" name="author_name" value="<?php echo htmlspecialchars($author_name); ?>">
                        <input type="hidden" name="author_email" value="<?php echo htmlspecialchars($author_email); ?>">
                        
                        <button type="submit" name="submit_install" class="btn">完成安装</button>
                        <button type="submit" name="back" class="btn btn-back">上一步</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>