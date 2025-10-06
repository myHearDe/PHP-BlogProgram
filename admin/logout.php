<?php
/**
 * PHP专业博客程序 - 管理员退出登录
 */

// 启动会话
session_start();

// 销毁所有会话变量
$_SESSION = array();

// 如果使用Cookie存储会话ID，也销毁Cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 销毁会话
session_destroy();

// 重定向到登录页面
header('Location: login.php');
exit;
?>