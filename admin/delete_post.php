<?php
/**
 * PHP专业博客程序 - 删除文章页面
 */

// 包含管理员验证
require_once 'admin_auth.php';

// 检查是否提供了文章ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: posts.php?error=无效的文章ID');
    exit;
}

$post_id = (int)$_GET['id'];

// 初始化错误信息
$error = '';

// 尝试删除文章
if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            // 开始事务
            $db->beginTransaction();
            
            try {
                // 先删除相关的评论
                $query = "DELETE FROM comments WHERE post_id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$post_id]);
                
                // 再删除文章
                $query = "DELETE FROM posts WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$post_id]);
                
                // 提交事务
                $db->commit();
                
                // 重定向到文章管理页面
                header('Location: posts.php?success=删除文章成功');
                exit;
            } catch(PDOException $e) {
                // 回滚事务
                $db->rollBack();
                $error = '删除文章失败: ' . $e->getMessage();
            }
        } else {
            $error = '无法连接到数据库';
        }
    } catch(PDOException $e) {
        $error = '数据库错误: ' . $e->getMessage();
    }
} else {
    // 获取文章标题用于确认对话框
    $post_title = '';
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            $query = "SELECT title FROM posts WHERE id = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute([$post_id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$post) {
                header('Location: posts.php?error=文章不存在');
                exit;
            }
            
            $post_title = $post['title'];
        }
    } catch(PDOException $e) {
        $error = '获取文章信息失败: ' . $e->getMessage();
    }
}

// 定义页面标题
$page_title = '删除文章';
$page_description = '删除博客文章及其相关评论';

// 包含头部
include 'includes/header.php';
?>

<!-- 内容区域 -->
<div class="content-wrapper">
    <div class="content-header">
        <h1>删除文章</h1>
        <p>确认删除操作</p>
    </div>
    
    <!-- 错误信息显示 -->
    <?php if (!empty($error)): ?>
        <div class="error-message" style="margin-bottom: 20px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <div style="margin-top: 20px;">
            <a href="posts.php" style="
                display: inline-block;
                background-color: #95a5a6;
                color: white;
                border: none;
                padding: 12px 20px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
                text-decoration: none;
            ">返回文章管理</a>
        </div>
    <?php else: ?>
        <!-- 确认删除表单 -->
        <div style="background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); padding: 20px; max-width: 600px;">
            <h3 style="color: #e74c3c; margin-bottom: 20px;">警告：此操作无法撤销！</h3>
            <p style="margin-bottom: 20px;">您确定要删除文章 "<?php echo htmlspecialchars($post_title); ?>" 吗？</p>
            <p style="margin-bottom: 20px; color: #7f8c8d;">删除此文章将会同时删除与之相关的所有评论。</p>
            
            <div style="margin-top: 30px;">
                <a href="delete_post.php?id=<?php echo $post_id; ?>&confirm=true" style="
                    display: inline-block;
                    background-color: #e74c3c;
                    color: white;
                    border: none;
                    padding: 12px 20px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 16px;
                    text-decoration: none;
                    margin-right: 10px;
                ">确认删除</a>
                <a href="posts.php" style="
                    display: inline-block;
                    background-color: #95a5a6;
                    color: white;
                    border: none;
                    padding: 12px 20px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 16px;
                    text-decoration: none;
                ">取消</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// 包含页脚
include 'includes/footer.php';
?>