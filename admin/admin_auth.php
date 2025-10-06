<?php
/**
 * PHP专业博客程序 - 管理员会话验证
 */

// 启动会话
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // 如果未登录，重定向到登录页面
    header('Location: login.php');
    exit;
}

// 包含数据库连接和配置
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// 获取当前登录用户信息
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        $query = "SELECT * FROM users WHERE id = ? LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$_SESSION['user_id']]);
        $current_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$current_user) {
            // 如果用户不存在，销毁会话并重定向到登录页面
            session_destroy();
            header('Location: login.php');
            exit;
        }
    }
} catch(PDOException $e) {
    // 数据库错误处理
    die('数据库错误: ' . $e->getMessage());
}