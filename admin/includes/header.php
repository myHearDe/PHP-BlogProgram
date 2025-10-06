<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? escapeHtml($page_title) . ' - 博客管理系统' : '博客管理系统'; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? escapeHtml($page_description) : '博客管理后台'; ?>">
    <style>
        :root {
            --primary: #4a90e2;
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
            --danger: #e74c3c;
            --text: #2c3e50;
            --muted: #7f8c8d;
            --card-bg: #ffffff;
            --page-bg: #f4f6f9;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Microsoft YaHei', Arial, sans-serif; line-height: 1.6; color: #333; background-color: var(--page-bg); }
        .admin-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--sidebar-bg); color: #fff; height: 100vh; position: fixed; overflow-y: auto; transition: transform 0.3s ease; }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid var(--sidebar-hover); }
        .sidebar-header h1 { font-size: 20px; margin: 0; }
        .nav-menu { padding: 20px 0; }
        .nav-item { list-style: none; }
        .nav-link { display: block; padding: 12px 25px; color: #ecf0f1; text-decoration: none; transition: all 0.3s ease; }
        .nav-link:hover { background-color: var(--sidebar-hover); color: #fff; }
        .nav-link.active { background-color: var(--primary); color: #fff; }
        .main-content { margin-left: 250px; width: calc(100% - 250px); transition: all 0.3s ease; }
        .top-navbar { background-color: #fff; padding: 12px clamp(16px, 4vw, 30px); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 1200; }
        .top-navbar .logo { font-size: 20px; font-weight: bold; color: var(--text); }
        .sidebar-toggle { background: var(--primary); color: #fff; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; margin-right: 10px; }
        .sidebar-toggle:hover { opacity: 0.9; }
        .user-menu { display: flex; align-items: center; }
        .user-info { margin-right: 20px; color: var(--muted); }
        .logout-btn { background-color: var(--danger); color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .logout-btn:hover { background-color: #c0392b; }
        .content-wrapper { padding: 30px; margin: 0 clamp(16px, 4vw, 30px); max-width: none; }
        .content-header { margin-bottom: 30px; }
        .content-header h1 { font-size: 28px; color: var(--text); margin-bottom: 10px; }
        .dashboard-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: clamp(12px, 2vw, 20px); margin-bottom: 30px; }
        .stat-card { background-color: var(--card-bg); border-radius: 8px; padding: 20px; box-shadow: var(--shadow); display: flex; align-items: center; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        .stat-icon { font-size: 36px; margin-right: 15px; }
        .stat-content h3 { font-size: 14px; color: var(--muted); margin: 0 0 5px 0; }
        .stat-number { font-size: 28px; font-weight: bold; color: var(--text); margin: 0; }
        .stat-title { font-size: 16px; color: var(--text); margin: 0 0 5px 0; }
        .stat-date { font-size: 14px; color: var(--muted); margin: 0; }
        .quick-actions { margin-bottom: 30px; }
        .quick-actions h2 { font-size: 20px; color: var(--text); margin-bottom: 20px; }
        .action-buttons { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: clamp(10px, 2vw, 16px); }
        .action-btn { background-color: var(--card-bg); border: 1px solid #ddd; border-radius: 8px; padding: 20px; text-align: center; text-decoration: none; color: var(--text); transition: all 0.3s ease; }
        .action-btn:hover { background-color: var(--primary); color: #fff; border-color: var(--primary); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        .action-icon { font-size: 24px; display: block; margin-bottom: 10px; }
        .sidebar-collapsed .sidebar { transform: translateX(-100%); }
        .sidebar-collapsed .main-content { margin-left: 0; width: 100%; }
        @media (max-width: 992px) {
            .main-content { margin-left: 0; width: 100%; }
            .sidebar { transform: translateX(0); z-index: 1100; position: fixed; left: 0; top: 0; bottom: 0; }
            .sidebar-collapsed .sidebar { transform: translateX(-100%); }
            .content-wrapper { margin: 0 12px; padding: 20px; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- 侧边栏导航 -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1>博客管理系统</h1>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                        📊 管理主页
                    </a>
                </li>
                <li class="nav-item">
                    <a href="posts.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'posts.php' ? 'active' : ''; ?>">
                        📝 文章管理
                    </a>
                </li>
                <li class="nav-item">
                    <a href="categories.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : ''; ?>">
                        📂 分类管理
                    </a>
                </li>
                <li class="nav-item">
                    <a href="comments.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'comments.php' ? 'active' : ''; ?>">
                        💬 评论管理
                    </a>
                </li>
                <?php if (file_exists(__DIR__ . '/../users.php')): ?>
                <li class="nav-item">
                    <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>">
                        👥 用户管理
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                        ⚙️ 系统设置
                    </a>
                </li>
            </ul>
        </aside>
        
        <!-- 主内容区域 -->
        <main class="main-content">
            <!-- 顶部导航栏 -->
            <nav class="top-navbar">
                <div style="display:flex; align-items:center;">
                    <button class="sidebar-toggle" type="button" aria-label="切换菜单">☰</button>
                    <div class="logo">博客管理</div>
                </div>
                <div class="user-menu">
                    <div class="user-info">
                        <span>欢迎, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                    <a href="logout.php" class="logout-btn">退出登录</a>
                </div>
            </nav>