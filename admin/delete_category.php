<?php
/**
 * PHP专业博客程序 - 删除分类页面
 */

// 包含管理员验证
require_once 'admin_auth.php';

// 检查是否提供了分类ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: categories.php?error=无效的分类ID');
    exit;
}

$category_id = (int)$_GET['id'];

// 初始化错误信息
$error = '';

// 尝试删除分类
if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            // 开始事务
            $db->beginTransaction();
            
            try {
                // 先将使用此分类的文章的category_id设置为NULL
                $query = "UPDATE posts SET category_id = NULL WHERE category_id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$category_id]);
                
                // 再删除分类
                $query = "DELETE FROM categories WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$category_id]);
                
                // 提交事务
                $db->commit();
                
                // 重定向到分类管理页面
                header('Location: categories.php?success=删除分类成功');
                exit;
            } catch(PDOException $e) {
                // 回滚事务
                $db->rollBack();
                $error = '删除分类失败: ' . $e->getMessage();
            }
        } else {
            $error = '无法连接到数据库';
        }
    } catch(PDOException $e) {
        $error = '数据库错误: ' . $e->getMessage();
    }
} else {
    // 获取分类信息用于确认对话框
    $category_name = '';
    $post_count = 0;
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            $query = "SELECT c.name, COUNT(p.id) as post_count FROM categories c 
                     LEFT JOIN posts p ON c.id = p.category_id 
                     WHERE c.id = ? 
                     GROUP BY c.id";
            $stmt = $db->prepare($query);
            $stmt->execute([$category_id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$category) {
                header('Location: categories.php?error=分类不存在');
                exit;
            }
            
            $category_name = $category['name'];
            $post_count = $category['post_count'];
        }
    } catch(PDOException $e) {
        $error = '获取分类信息失败: ' . $e->getMessage();
    }
}

// 定义页面标题
$page_title = '删除分类';
$page_description = '删除博客分类并解除关联';

// 包含头部
include 'includes/header.php';
?>

<!-- 内容区域 -->
<div class="content-wrapper">
    <div class="content-header">
        <h1>删除分类</h1>
        <p>确认删除操作</p>
    </div>
    
    <!-- 错误信息显示 -->
    <?php if (!empty($error)): ?>
        <div class="error-message" style="margin-bottom: 20px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <div style="margin-top: 20px;">
            <a href="categories.php" style="
                display: inline-block;
                background-color: #95a5a6;
                color: white;
                border: none;
                padding: 12px 20px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
                text-decoration: none;
            ">返回分类管理</a>
        </div>
    <?php else: ?>
        <!-- 确认删除表单 -->
        <div style="background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); padding: 20px; max-width: 600px;">
            <h3 style="color: #e74c3c; margin-bottom: 20px;">警告：此操作无法撤销！</h3>
            <p style="margin-bottom: 20px;">您确定要删除分类 "<?php echo htmlspecialchars($category_name); ?>" 吗？</p>
            
            <?php if ($post_count > 0): ?>
                <p style="margin-bottom: 20px; color: #7f8c8d;">该分类下有 <?php echo $post_count; ?> 篇文章，删除后这些文章将变为未分类状态。</p>
            <?php endif; ?>
            
            <div style="margin-top: 30px;">
                <a href="delete_category.php?id=<?php echo $category_id; ?>&confirm=true" style="
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
                <a href="categories.php" style="
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