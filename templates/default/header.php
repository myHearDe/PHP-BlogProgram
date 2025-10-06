<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('PAGE_TITLE') ? escapeHtml(constant('PAGE_TITLE')) . ' - ' : ''; ?><?php echo BLOG_NAME; ?></title>
    <meta name="description" content="<?php echo defined('PAGE_DESCRIPTION') ? escapeHtml(constant('PAGE_DESCRIPTION')) : BLOG_DESCRIPTION; ?>">
    <link rel="stylesheet" href="<?php echo getUrl('templates/default/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
</head>
<body>
    <header class="blog-header">
        <div class="container">
            <div class="header-content">
                <div class="blog-logo">
                    <a href="<?php echo getUrl(); ?>">
                        <h1><?php echo BLOG_NAME; ?></h1>
                    </a>
                    <p class="blog-description"><?php echo BLOG_DESCRIPTION; ?></p>
                </div>
                
                <!-- 搜索框 -->
                <div class="search-form">
                    <form action="<?php echo getUrl('index.php?page=search'); ?>" method="get">
                        <input type="hidden" name="page" value="search">
                        <input type="text" name="keyword" placeholder="搜索文章..." required>
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- 导航栏 -->
    <nav class="blog-nav">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="<?php echo getUrl(); ?>" class="<?php echo PAGE_TYPE == 'home' ? 'active' : ''; ?>">首页</a></li>
                <?php 
                $blog = new Blog();
                $categories = $blog->getAllCategories();
                foreach ($categories as $category): 
                ?>
                <li><a href="<?php echo getUrl('index.php?page=category&id=' . $category['id']); ?>" class="<?php echo PAGE_TYPE == 'category' && $_GET['id'] == $category['id'] ? 'active' : ''; ?>"><?php echo escapeHtml($category['name']); ?></a></li>
                <?php endforeach; ?>
                <li><a href="<?php echo getUrl('index.php?page=about'); ?>" class="<?php echo PAGE_TYPE == 'about' ? 'active' : ''; ?>">关于</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="content-wrapper">