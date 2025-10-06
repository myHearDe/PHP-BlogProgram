<?php
/**
 * PHP专业博客程序 - 创建管理员用户脚本
 */

// 设置页面标题
$page_title = '创建管理员用户';
$page_description = '初始化并创建新的管理员账户';

// 初始化变量
$username = '';
$password = '';
$confirm_password = '';
$errors = array();
$success = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取和验证表单数据
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    // 简单验证
    if (empty($username)) {
        $errors[] = '请输入用户名';
    } elseif (strlen($username) < 3) {
        $errors[] = '用户名至少需要3个字符';
    }
    
    if (empty($password)) {
        $errors[] = '请输入密码';
    } elseif (strlen($password) < 6) {
        $errors[] = '密码至少需要6个字符';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = '两次输入的密码不一致';
    }
    
    // 如果没有错误，创建管理员用户
    if (empty($errors)) {
        try {
            // 包含数据库配置
            require_once '../includes/config.php';
            
            // 检查用户名是否已存在
            $query = "SELECT id FROM users WHERE username = ? LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$username]);
            $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing_user) {
                $errors[] = '用户名已存在';
            } else {
                // 哈希密码
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // 插入管理员用户
                $query = "INSERT INTO users (username, password, role, created_at, updated_at) VALUES (?, ?, 'admin', NOW(), NOW())";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$username, $hashed_password]);
                
                $success = '管理员用户创建成功！';
                $username = '';
                $password = '';
                $confirm_password = '';
            }
        } catch(PDOException $e) {
            $errors[] = '创建用户失败: ' . $e->getMessage();
        }
    }
} else {
    // 检查是否已有管理员用户
    try {
        require_once '../includes/config.php';
        
        $query = "SELECT id FROM users WHERE role = 'admin' LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $admin_exists = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin_exists) {
            echo '<!DOCTYPE html>';
            echo '<html lang="zh-CN">';
            echo '<head>';
            echo '<meta charset="UTF-8">';
            echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
            echo '<title>系统提示</title>';
            echo '<style>';
            echo 'body { font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; }';
            echo '.container { max-width: 600px; margin: 50px auto; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); }';
            echo 'h1 { color: #333; }';
            echo '.message { padding: 15px; margin: 20px 0; background-color: #f1f3f4; border-left: 4px solid #4a90e2; border-radius: 4px; }';
            echo 'a { color: #007bff; text-decoration: none; }';
            echo 'a:hover { text-decoration: underline; }';
            echo '</style>';
            echo '</head>';
            echo '<body>';
            echo '<div class="container">';
            echo '<h1>系统提示</h1>';
            echo '<div class="message">';
            echo '<p>系统中已存在管理员账户。</p>';
            echo '<p>如果您想添加新的管理员账户，请使用现有管理员账户登录后进行操作。</p>';
            echo '</div>';
            echo '<p><a href="login.php">返回登录页面</a></p>';
            echo '</div>';
            echo '</body>';
            echo '</html>';
            exit;
        }
    } catch(PDOException $e) {
        $errors[] = '检查管理员用户失败: ' . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>创建管理员用户 - PHP专业博客程序</title>
    <meta name="description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : '创建管理员用户'; ?>">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            max-width: 500px;
            width: 100%;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .header {
            background-color: #4a90e2;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        .error-message {
            background-color: #fee;
            color: #c33;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid #c33;
        }
        .success-message {
            background-color: #efe;
            color: #393;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid #393;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #357abd;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>创建管理员用户</h1>
        </div>
        <div class="content">
            <!-- 消息显示 -->
            <?php if (!empty($success)): ?>
                <div class="success-message">
                    <?php echo $success; ?><br>
                    <a href="login.php">前往登录页面</a>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- 创建管理员用户表单 -->
            <?php if (empty($success)): ?>
                <form method="POST" action="create_admin_user.php">
                    <div class="form-group">
                        <label for="username">用户名</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="请输入用户名" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">密码</label>
                        <input type="password" id="password" name="password" placeholder="请输入密码" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">确认密码</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="请再次输入密码" required>
                    </div>
                    
                    <input type="submit" value="创建管理员用户">
                </form>
                
                <div class="login-link">
                    <p>已有账户？<a href="login.php">前往登录</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>