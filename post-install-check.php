<?php
/**
 * 博客安装后检查脚本
 * 用于验证安装是否成功完成
 */

// 定义常量
define('BLOG_ROOT', dirname(__FILE__));
define('INCLUDE_DIR', BLOG_ROOT . '/includes');

try {
    // 检查配置文件是否存在
    if (!file_exists(INCLUDE_DIR . '/config.php')) {
        die('<div style="color: red; font-family: Arial, sans-serif;">错误：配置文件不存在，请先完成安装。</div>');
    }
    
    // 加载配置文件
    require_once INCLUDE_DIR . '/config.php';
    require_once INCLUDE_DIR . '/database.php';
    
    // 尝试连接数据库
    $db = new Database();
    $pdo = $db->getConnection();
    
    // 检查数据库表是否创建成功
    $tables = ['users', 'categories', 'posts', 'comments', 'tags', 'post_tags', 'pages', 'settings'];
    $missing_tables = [];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() === 0) {
            $missing_tables[] = $table;
        }
    }
    
    // 获取博客信息
    $stmt = $pdo->query("SELECT name, value FROM settings WHERE name IN ('blog_name', 'blog_description', 'blog_author', 'blog_email')");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // 检查管理员账户是否存在
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $admin_count = $stmt->fetchColumn();
    
    // 检查示例文章是否创建
    $stmt = $pdo->query("SELECT COUNT(*) FROM posts");
    $post_count = $stmt->fetchColumn();
    
    // 输出测试结果
    echo '<!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>博客安装测试结果</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; padding: 20px; }
            h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
            h2 { color: #34495e; }
            .success { color: #27ae60; }
            .error { color: #e74c3c; }
            .info { color: #3498db; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .step { margin: 20px 0; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #3498db; }
            .step h3 { margin-top: 0; }
        </style>
    </head>
    <body>
        <h1>博客安装测试结果</h1>
        
        <div class="test-section">
            <h2>基本检查</h2>
            <table>
                <tr>
                    <td>配置文件</td>
                    <td class="success">✓ 存在</td>
                </tr>
                <tr>
                    <td>数据库连接</td>
                    <td class="success">✓ 成功</td>
                </tr>
            </table>
        </div>
        
        <div class="test-section">
            <h2>数据库表检查</h2>";
    
    if (empty($missing_tables)) {
        echo "            <p class='success'>✓ 所有必要的数据库表都已成功创建。</p>";
    } else {
        echo "            <p class='error'>✗ 以下表缺失：" . implode(', ', $missing_tables) . "。请重新运行安装脚本。</p>";
    }
    
    echo "        </div>
        
        <div class='test-section'>
            <h2>博客信息</h2>";
    
    if (!empty($settings)) {
        echo "            <table>";
        foreach ($settings as $name => $value) {
            $display_name = ucwords(str_replace('_', ' ', $name));
            echo "                <tr>
                    <td>$display_name</td>
                    <td>$value</td>
                </tr>";
        }
        echo "            </table>";
    } else {
        echo "            <p class='error'>✗ 未找到博客配置信息。</p>";
    }
    
    echo "        </div>
        
        <div class='test-section'>
            <h2>内容统计</h2>
            <table>
                <tr>
                    <td>管理员账户数量</td>
                    <td>" . ($admin_count > 0 ? "<span class='success'>✓ $admin_count 个</span>" : "<span class='error'>✗ 0 个</span>") . "</td>
                </tr>
                <tr>
                    <td>文章数量</td>
                    <td>" . ($post_count > 0 ? "<span class='success'>✓ $post_count 篇</span>" : "<span class='error'>✗ 0 篇</span>") . "</td>
                </tr>
            </table>
        </div>
        
        <div class='test-section'>
            <h2>后续步骤</h2>
            
            <div class='step'>
                <h3>1. 访问博客前台</h3>
                <p>点击 <a href='index.php' class='info'>这里</a> 查看您的博客前台页面。</p>
            </div>
            
            <div class='step'>
                <h3>2. 登录管理后台</h3>
                <p>访问 <a href='admin/login.php' class='info'>admin/login.php</a> 使用您创建的管理员账户登录。</p>
            </div>
            
            <div class='step'>
                <h3>3. 安全建议</h3>
                <ul>
                    <li>定期更新您的密码</li>
                    <li>不要与他人分享您的管理员凭据</li>
                </ul>
            </div>
            
            <div class='step'>
                <h3>4. 个性化您的博客</h3>
                <ul>
                    <li>在管理后台修改博客设置</li>
                    <li>创建新的分类和标签</li>
                    <li>发布您的第一篇文章</li>
                    <li>根据需要自定义模板</li>
                </ul>
            </div>
        </div>
        
        <div style='margin-top: 40px; text-align: center; color: #7f8c8d;'>
            <p>博客安装检查脚本 v1.0</p>
        </div>
    </body>
    </html>";
    
} catch (PDOException $e) {
    echo '<div style="color: red; font-family: Arial, sans-serif;">数据库连接错误：' . $e->getMessage() . '</div>';
} catch (Exception $e) {
    echo '<div style="color: red; font-family: Arial, sans-serif;">错误：' . $e->getMessage() . '</div>';
}