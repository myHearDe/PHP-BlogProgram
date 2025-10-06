<?php
/**
 * PHP专业博客程序 - 文章管理页面
 */

// 包含管理员验证
require_once 'admin_auth.php';
require_once '../includes/blog.php';

// 创建博客实例
$blog = new Blog();

// 定义页面标题
$page_title = '文章管理';
$page_description = '查看、编辑和删除博客文章';

// 获取文章列表
$posts = array();
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        $query = "SELECT p.*, c.name as category_name FROM posts p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 ORDER BY p.created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch(PDOException $e) {
    $error = '数据库错误: ' . $e->getMessage();
}

// 包含头部
include 'includes/header.php';
?>

<!-- 内容区域 -->
<div class="content-wrapper">
    <div class="content-header">
        <h1>文章管理</h1>
        <p>管理您的博客文章</p>
    </div>
    
    <div class="dashboard-stats" style="margin-bottom: 20px;">
        <a href="add_post.php" class="action-btn" style="display:inline-flex; align-items:center; gap:8px;">
            <span class="action-icon">✏️</span>
            <span>添加新文章</span>
        </a>
    </div>
    
    <!-- 保持现有表格 -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
            <thead>
                <tr style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                    <th style="padding: 12px 15px; text-align: left; font-weight: 600;">ID</th>
                    <th style="padding: 12px 15px; text-align: left; font-weight: 600;">标题</th>
                    <th style="padding: 12px 15px; text-align: left; font-weight: 600;">分类</th>
                    <th style="padding: 12px 15px; text-align: left; font-weight: 600;">状态</th>
                    <th style="padding: 12px 15px; text-align: left; font-weight: 600;">创建时间</th>
                    <th style="padding: 12px 15px; text-align: left; font-weight: 600;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as $post): ?>
                        <tr style="border-bottom: 1px solid #dee2e6; transition: background-color 0.2s;">
                            <td style="padding: 12px 15px;"><?php echo $post['id']; ?></td>
                            <td style="padding: 12px 15px;"><?php echo truncate($post['title'], 50); ?></td>
                            <td style="padding: 12px 15px;"><?php echo $post['category_name'] ?? '未分类'; ?></td>
                            <td style="padding: 12px 15px;">
                                <span style="
                                    display: inline-block;
                                    padding: 4px 8px;
                                    border-radius: 4px;
                                    font-size: 12px;
                                    background-color: <?php echo $post['status'] === 'published' ? '#d4edda' : ($post['status'] === 'draft' ? '#fff3cd' : '#f8d7da'); ?>
                                ">
                                    <?php 
                                        $statusText = '';
                                        switch ($post['status']) {
                                            case 'published':
                                                $statusText = '已发布';
                                                break;
                                            case 'draft':
                                                $statusText = '草稿';
                                                break;
                                            case 'pending':
                                                $statusText = '待审核';
                                                break;
                                        }
                                        echo $statusText;
                                    ?>
                                </span>
                            </td>
                            <td style="padding: 12px 15px;"><?php echo formatDate($post['created_at']); ?></td>
                            <td style="padding: 12px 15px;">
                                <a href="edit_post.php?id=<?php echo $post['id']; ?>" style="
                                    display: inline-block;
                                    padding: 6px 12px;
                                    background-color: #4a90e2;
                                    color: white;
                                    text-decoration: none;
                                    border-radius: 4px;
                                    margin-right: 5px;
                                    font-size: 14px;
                                ">编辑</a>
                                <a href="delete_post.php?id=<?php echo $post['id']; ?>" style="
                                    display: inline-block;
                                    padding: 6px 12px;
                                    background-color: #e74c3c;
                                    color: white;
                                    text-decoration: none;
                                    border-radius: 4px;
                                    font-size: 14px;
                                " onclick="return confirm('确定要删除这篇文章吗？');">删除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding: 20px; text-align: center; color: #7f8c8d;">
                            暂无文章
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// 包含页脚
include 'includes/footer.php';
?>