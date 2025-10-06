<?php
/**
 * PHP专业博客程序 - 系统设置页面
 */

// 包含管理员验证
require_once 'admin_auth.php';

// 定义页面标题
$page_title = '系统设置';
$page_description = '配置站点信息与系统选项';

// 初始化变量
$blog_title = '';
$blog_description = '';
$author_name = '';
$author_email = '';
$posts_per_page = 5;
$about_content = '';
$errors = array();
$success = '';

// 获取当前设置
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        $query = "SELECT name, value FROM settings WHERE name IN ('blog_name','blog_description','blog_author','blog_email','posts_per_page','about_content')";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $blog_title = isset($settings['blog_name']) ? $settings['blog_name'] : (defined('BLOG_NAME') ? BLOG_NAME : '');
        $blog_description = isset($settings['blog_description']) ? $settings['blog_description'] : (defined('BLOG_DESCRIPTION') ? BLOG_DESCRIPTION : '');
        $author_name = isset($settings['blog_author']) ? $settings['blog_author'] : (defined('BLOG_AUTHOR') ? BLOG_AUTHOR : '');
        $author_email = isset($settings['blog_email']) ? $settings['blog_email'] : (defined('BLOG_EMAIL') ? BLOG_EMAIL : '');
        $posts_per_page = (isset($settings['posts_per_page']) && is_numeric($settings['posts_per_page'])) ? (int)$settings['posts_per_page'] : (defined('POSTS_PER_PAGE') ? (int)POSTS_PER_PAGE : 5);
        $about_content = isset($settings['about_content']) ? $settings['about_content'] : '我会定期更新博客内容，分享我在学习和工作中的心得和体会。希望这些内容能够帮助到你，也欢迎你在评论区留言交流。';
    } else {
        $errors[] = '无法连接到数据库';
    }
} catch(PDOException $e) {
    $errors[] = '获取设置失败: ' . $e->getMessage();
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取和验证表单数据
    $blog_title = isset($_POST['blog_title']) ? trim($_POST['blog_title']) : '';
    $blog_description = isset($_POST['blog_description']) ? trim($_POST['blog_description']) : '';
    $author_name = isset($_POST['author_name']) ? trim($_POST['author_name']) : '';
    $author_email = isset($_POST['author_email']) ? trim($_POST['author_email']) : '';
    $posts_per_page = isset($_POST['posts_per_page']) && is_numeric($_POST['posts_per_page']) ? (int)$_POST['posts_per_page'] : 5;
    $about_content = isset($_POST['about_content']) ? trim($_POST['about_content']) : '';
    
    // 简单验证
    if (empty($blog_title)) {
        $errors[] = '请输入博客标题';
    }
    
    if (!empty($author_email) && !filter_var($author_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '请输入有效的电子邮箱地址';
    }
    
    if ($posts_per_page < 1) {
        $errors[] = '每页显示的文章数量至少为1';
    }
    
    // 如果没有错误，更新设置
    if (empty($errors)) {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                // 采用键值方式保存设置（INSERT...ON DUPLICATE KEY UPDATE）
                $stmt = $db->prepare("INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()");
                $pairs = [
                    ['blog_name', $blog_title],
                    ['blog_description', $blog_description],
                    ['blog_author', $author_name],
                    ['blog_email', $author_email],
                    ['posts_per_page', (string)(int)$posts_per_page],
                    ['about_content', $about_content]
                ];
                foreach ($pairs as $p) { $stmt->execute([$p[0], $p[1]]); }
                
                // 更新config.php文件中的设置
                $config_file = '../includes/config.php';
                if (file_exists($config_file)) {
                    $config_content = file_get_contents($config_file);
                    
                    // 替换配置常量（config.php中使用的是常量而非变量）
                    $config_content = preg_replace('/define\(\s*[\"\']BLOG_NAME[\"\']\s*,\s*[\"\'][^\"\']*[\"\']\s*\);/', "define('BLOG_NAME', '" . addslashes($blog_title) . "');", $config_content);
                    $config_content = preg_replace('/define\(\s*[\"\']BLOG_DESCRIPTION[\"\']\s*,\s*[\"\'][^\"\']*[\"\']\s*\);/', "define('BLOG_DESCRIPTION', '" . addslashes($blog_description) . "');", $config_content);
                    $config_content = preg_replace('/define\(\s*[\"\']BLOG_AUTHOR[\"\']\s*,\s*[\"\'][^\"\']*[\"\']\s*\);/', "define('BLOG_AUTHOR', '" . addslashes($author_name) . "');", $config_content);
                    $config_content = preg_replace('/define\(\s*[\"\']BLOG_EMAIL[\"\']\s*,\s*[\"\'][^\"\']*[\"\']\s*\);/', "define('BLOG_EMAIL', '" . addslashes($author_email) . "');", $config_content);
                    $config_content = preg_replace('/define\(\s*[\"\']POSTS_PER_PAGE[\"\']\s*,\s*\d+\s*\);/', "define('POSTS_PER_PAGE', " . (int)$posts_per_page . ");", $config_content);
                    
                    // 保存更新后的配置文件
                    file_put_contents($config_file, $config_content);
                }
                
                $success = '设置已成功更新';
            } else {
                $errors[] = '无法连接到数据库';
            }
        } catch(PDOException $e) {
            $errors[] = '更新设置失败: ' . $e->getMessage();
        }
    }
}

// 包含头部
include 'includes/header.php';
?>

<!-- 内容区域 -->
<div class="content-wrapper">
    <div class="content-header">
        <h1>系统设置</h1>
        <p>修改博客的基本配置</p>
    </div>
    
    <!-- 消息显示 -->
    <?php if (!empty($success)): ?>
        <div class="success-message" style="margin-bottom: 20px;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="error-message" style="margin-bottom: 20px;">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- 系统设置表单 -->
    <div style="background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); padding: 20px; max-width: 800px;">
        <form method="POST" action="settings.php">
            <div style="margin-bottom: 20px;">
                <label for="blog_title" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">博客标题 <span style="color: red;">*</span></label>
                <input type="text" id="blog_title" name="blog_title" value="<?php echo htmlspecialchars($blog_title); ?>" placeholder="请输入博客标题" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="blog_description" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">博客描述</label>
                <textarea id="blog_description" name="blog_description" rows="3" placeholder="请输入博客描述" 
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"><?php echo htmlspecialchars($blog_description); ?></textarea>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="author_name" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">作者名称</label>
                <input type="text" id="author_name" name="author_name" value="<?php echo htmlspecialchars($author_name); ?>" placeholder="请输入作者名称" 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="author_email" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">作者邮箱</label>
                <input type="email" id="author_email" name="author_email" value="<?php echo htmlspecialchars($author_email); ?>" placeholder="请输入作者邮箱" 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="about_content" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">关于页内容</label>
                <textarea id="about_content" name="about_content" rows="4" placeholder="请输入关于页内容，将在关于页面展示" 
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"><?php echo htmlspecialchars($about_content); ?></textarea>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="posts_per_page" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">每页显示文章数</label>
                <input type="number" id="posts_per_page" name="posts_per_page" value="<?php echo $posts_per_page; ?>" min="1" max="50" 
                       style="width: 100px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" style="
                    background-color: #4a90e2;
                    color: white;
                    border: none;
                    padding: 12px 20px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 16px;
                ">保存设置</button>
            </div>
        </form>
    </div>
</div>

<?php
// 包含页脚
include 'includes/footer.php';
?>