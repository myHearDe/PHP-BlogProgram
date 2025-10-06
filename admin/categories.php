<?php
/**
 * PHP专业博客程序 - 分类管理页面
 */

// 包含管理员验证
require_once 'admin_auth.php';
require_once '../includes/blog.php';

// 创建博客实例
$blog = new Blog();

// 定义页面标题
$page_title = '分类管理';
$page_description = '管理博客分类，包括新增、编辑与删除';

// 获取分类列表
$categories = array();
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        $query = "SELECT c.*, COUNT(p.id) as post_count FROM categories c 
                 LEFT JOIN posts p ON c.id = p.category_id 
                 GROUP BY c.id 
                 ORDER BY c.name";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1>分类管理</h1>
        <p>对博客分类进行新增、编辑和删除</p>
    </div>

    <div class="dashboard-stats" style="margin-bottom: 20px;">
        <a href="add_category.php" class="action-btn" style="display:inline-flex; align-items:center; gap:8px;">
            <span class="action-icon">📁</span>
            <span>添加新分类</span>
        </a>
    </div>

    <!-- 保持现有表格 -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
            <thead>
                <tr style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                    <th style="padding: 12px 15px; text-align: left; font-weight: 600;">ID</th>
                    <th style="padding: 12px 15px; text-align: left; font-weight: 600;">分类名称</th>
                    <th style="padding: 12px 15px; text-align: left; font-weight: 600;">描述</th>
                    <th style="padding: 12px 15px; text-align: left; font-weight: 600;">文章数量</th>
                    <th style="padding: 12px 15px; text-align: left; font-weight: 600;">创建时间</th>
                    <th style="padding: 12px 15px; text-align: left; font-weight: 600;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($categories) > 0): ?>
                    <?php foreach ($categories as $category): ?>
                        <tr style="border-bottom: 1px solid #dee2e6; transition: background-color 0.2s;">
                            <td style="padding: 12px 15px;"><?php echo $category['id']; ?></td>
                            <td style="padding: 12px 15px;"><?php echo htmlspecialchars($category['name']); ?></td>
                            <td style="padding: 12px 15px;"><?php echo truncate($category['description'], 50); ?></td>
                            <td style="padding: 12px 15px;"><?php echo $category['post_count']; ?></td>
                            <td style="padding: 12px 15px;"><?php echo formatDate($category['created_at']); ?></td>
                            <td style="padding: 12px 15px;">
                                <a href="edit_category.php?id=<?php echo $category['id']; ?>" style="
                                    display: inline-block;
                                    padding: 6px 12px;
                                    background-color: #4a90e2;
                                    color: white;
                                    text-decoration: none;
                                    border-radius: 4px;
                                    margin-right: 5px;
                                    font-size: 14px;
                                ">编辑</a>
                                <a href="delete_category.php?id=<?php echo $category['id']; ?>" style="
                                    display: inline-block;
                                    padding: 6px 12px;
                                    background-color: #e74c3c;
                                    color: white;
                                    text-decoration: none;
                                    border-radius: 4px;
                                    font-size: 14px;
                                " onclick="return confirm('确定要删除这个分类吗？');">删除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding: 20px; text-align: center; color: #7f8c8d;">
                            暂无分类
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