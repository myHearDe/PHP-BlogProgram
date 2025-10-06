<?php
/**
 * PHP专业博客程序 - 评论管理页面
 */

// 包含管理员验证
require_once 'admin_auth.php';

// 定义页面标题
$page_title = '评论管理';
$page_description = '审核与管理文章评论';

// 初始化变量
$comments = array();
$error = '';
$success = '';

// 处理URL参数中的状态消息
if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}

// 获取评论列表（连接到文章表以显示文章标题）
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        $query = "SELECT c.*, p.title AS post_title FROM comments c 
                 LEFT JOIN posts p ON c.post_id = p.id 
                 ORDER BY c.created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error = '无法连接到数据库';
    }
} catch(PDOException $e) {
    $error = '获取评论列表失败: ' . $e->getMessage();
}

// 包含头部
include 'includes/header.php';
?>

<!-- 内容区域 -->
<div class="content-wrapper">
    <div class="content-header">
        <h1>评论管理</h1>
        <p>审核与管理文章评论</p>
    </div>

    <div class="dashboard-stats" style="margin-bottom: 20px;">
        <a href="posts.php" class="action-btn" style="display:inline-flex; align-items:center; gap:8px;">
            <span class="action-icon">📝</span>
            <span>去文章列表</span>
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="success-message" style="background-color:#d4edda; color:#155724; padding:10px 12px; border-radius:6px; margin-bottom: 20px;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error-message" style="background-color:#f8d7da; color:#721c24; padding:10px 12px; border-radius:6px; margin-bottom: 20px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- 保持现有表格 -->
    <div style="background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); padding: 20px;">
        <div style="margin-bottom: 20px;">
            <h3 style="display: inline;">评论列表</h3>
            <span style="color: #7f8c8d; margin-left: 10px;">(共 <?php echo count($comments); ?> 条评论)</span>
        </div>
        
        <?php if (count($comments) > 0): ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; font-weight: bold; color: #495057;">ID</th>
                        <th style="padding: 12px; text-align: left; font-weight: bold; color: #495057;">评论内容</th>
                        <th style="padding: 12px; text-align: left; font-weight: bold; color: #495057;">评论者</th>
                        <th style="padding: 12px; text-align: left; font-weight: bold; color: #495057;">文章</th>
                        <th style="padding: 12px; text-align: left; font-weight: bold; color: #495057;">日期</th>
                        <th style="padding: 12px; text-align: left; font-weight: bold; color: #495057;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $comment): ?>
                        <tr style="border-bottom: 1px solid #dee2e6; transition: background-color 0.2s;">
                            <td style="padding: 12px; color: #212529;"><?php echo $comment['id']; ?></td>
                            <td style="padding: 12px; color: #212529;">
                                <div style="max-width: 300px;">
                                    <?php echo htmlspecialchars(truncate($comment['content'], 100)); ?>
                                </div>
                            </td>
                            <td style="padding: 12px; color: #212529;">
                                <?php echo htmlspecialchars($comment['name']); ?><br>
                                <small style="color: #6c757d;"><?php echo htmlspecialchars($comment['email']); ?></small>
                            </td>
                            <td style="padding: 12px; color: #212529;">
                                <a href="../blog.php?id=<?php echo $comment['post_id']; ?>" target="_blank" style="color: #007bff; text-decoration: none;">
                                    <?php echo htmlspecialchars(truncate($comment['post_title'], 50)); ?>
                                </a>
                            </td>
                            <td style="padding: 12px; color: #212529;">
                                <?php echo formatDate($comment['created_at']); ?>
                            </td>
                            <td style="padding: 12px;">
                                <a href="delete_comment.php?id=<?php echo $comment['id']; ?>" 
                                   onclick="return confirm('确定要删除这条评论吗？');"
                                   style="
                                        display: inline-block;
                                        background-color: #e74c3c;
                                        color: white;
                                        border: none;
                                        padding: 6px 12px;
                                        border-radius: 4px;
                                        cursor: pointer;
                                        font-size: 14px;
                                        text-decoration: none;
                                        margin-right: 5px;
                                   ">删除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 20px; color: #6c757d;">
                <p>暂无评论</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// 包含页脚
include 'includes/footer.php';
?>