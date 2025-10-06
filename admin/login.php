<?php
/**
 * PHP专业博客程序 - 管理员登录页面
 */

// 启动会话
session_start();

// 如果用户已登录，重定向到管理主页
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

// 包含数据库连接和配置
require_once '../includes/config.php';
require_once '../includes/database.php';

// 错误信息
$error = '';

// 处理登录表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取和验证表单数据
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    // 简单验证
    if (empty($username) || empty($password)) {
        $error = '请输入用户名和密码';
    } else {
        // 连接数据库
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            try {
                // 准备SQL查询
                $query = "SELECT * FROM users WHERE username = ? LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->execute([$username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // 验证用户和密码
                if ($user && password_verify($password, $user['password'])) {
                    // 设置会话变量
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // 更新最后登录时间
                    $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = ?";
                    $updateStmt = $db->prepare($updateQuery);
                    $updateStmt->execute([$user['id']]);
                    
                    // 重定向到管理主页
                    header('Location: index.php');
                    exit;
                } else {
                    $error = '用户名或密码错误';
                }
            } catch(PDOException $e) {
                $error = '数据库错误: ' . $e->getMessage();
            }
        } else {
            $error = '无法连接到数据库';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>博客管理系统 - 登录</title>
    <style>
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-header h2 {
            color: #333;
            margin: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-group input:focus {
            border-color: #4a90e2;
            outline: none;
            box-shadow: 0 0 5px rgba(74, 144, 226, 0.3);
        }
        .btn-login {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        .btn-login:hover {
            background-color: #357abd;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>博客管理系统</h2>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" id="username" name="username" placeholder="请输入用户名" required>
            </div>
            
            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" id="password" name="password" placeholder="请输入密码" required>
            </div>
            
            <button type="submit" class="btn-login">登录</button>
        </form>
    </div>
</body>
</html>