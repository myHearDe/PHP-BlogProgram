<?php
/**
 * PHP专业博客程序 - 删除评论页面
 */

// 包含管理员验证
require_once 'admin_auth.php';

// 检查是否提供了评论ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: comments.php?error=无效的评论ID');
    exit;
}

$comment_id = (int)$_GET['id'];

// 尝试删除评论
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        // 检查评论是否存在
        $query = "SELECT id FROM comments WHERE id = ? LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$comment_id]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$comment) {
            header('Location: comments.php?error=评论不存在');
            exit;
        }
        
        // 删除评论
        $query = "DELETE FROM comments WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$comment_id]);
        
        // 重定向到评论管理页面
        header('Location: comments.php?success=删除评论成功');
        exit;
    } else {
        header('Location: comments.php?error=无法连接到数据库');
        exit;
    }
} catch(PDOException $e) {
    header('Location: comments.php?error=删除评论失败: ' . urlencode($e->getMessage()));
    exit;
}