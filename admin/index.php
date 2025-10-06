<?php
/**
 * PHP专业博客程序 - 管理主页
 */

// 包含管理员验证
require_once 'admin_auth.php';
require_once '../includes/blog.php';

// 创建博客实例
$blog = new Blog();

// 获取博客统计信息
$stats = $blog->getStats();

// 定义页面标题
$page_title = '管理主页';
$page_description = '博客管理后台控制面板';

// 包含头部
include 'includes/header.php';
?>

<!-- 内容区域 -->
<div class="content-wrapper">
    <div class="content-header">
        <h1>欢迎回来，<?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <p>这是您的博客管理控制面板</p>
    </div>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-content">
                <h3>文章总数</h3>
                <p class="stat-number"><?php echo isset($stats['total_posts']) ? (int)$stats['total_posts'] : 0; ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📂</div>
            <div class="stat-content">
                <h3>分类总数</h3>
                <p class="stat-number"><?php echo isset($stats['total_categories']) ? (int)$stats['total_categories'] : 0; ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">💬</div>
            <div class="stat-content">
                <h3>评论总数</h3>
                <p class="stat-number"><?php echo isset($stats['total_comments']) ? (int)$stats['total_comments'] : 0; ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-content">
                <h3>最新文章</h3>
                <p class="stat-title"><?php echo (isset($stats['latest_post']) && isset($stats['latest_post']['title'])) ? truncate($stats['latest_post']['title'], 20) : '暂无文章'; ?></p>
                <p class="stat-date"><?php echo (isset($stats['latest_post']) && !empty($stats['latest_post']['created_at'])) ? formatDate($stats['latest_post']['created_at']) : '—'; ?></p>
            </div>
        </div>
    </div>
    
    <div class="quick-actions">
        <h2>快速操作</h2>
        <div class="action-buttons">
            <a href="posts.php" class="action-btn">
                <span class="action-icon">✏️</span>
                <span>管理文章</span>
            </a>
            
            <a href="categories.php" class="action-btn">
                <span class="action-icon">📁</span>
                <span>管理分类</span>
            </a>
            
            <a href="comments.php" class="action-btn">
                <span class="action-icon">💬</span>
                <span>管理评论</span>
            </a>
            
            <a href="settings.php" class="action-btn">
                <span class="action-icon">⚙️</span>
                <span>系统设置</span>
            </a>
        </div>
    </div>
</div>

<?php
// 包含页脚
include 'includes/footer.php';
?>