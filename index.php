<?php
/**
 * PHP专业博客程序 - 主入口文件
 */

// 定义常量
define('BLOG_ROOT', __DIR__);
define('TEMPLATE_DIR', BLOG_ROOT . '/templates/default');
define('INCLUDES_DIR', BLOG_ROOT . '/includes');

// 引入配置文件
require_once INCLUDES_DIR . '/config.php';

// 引入必要的功能文件
require_once INCLUDES_DIR . '/functions.php';
require_once INCLUDES_DIR . '/database.php';
require_once INCLUDES_DIR . '/blog.php';

// 初始化博客
$blog = new Blog();

// 获取当前页面类型
define('PAGE_TYPE', isset($_GET['page']) ? $_GET['page'] : 'home');

// 根据页面类型显示不同内容
switch (PAGE_TYPE) {
    case 'home':
        $posts = $blog->getRecentPosts();
        break;
    case 'post':
        $post = $blog->getPostById($_GET['id']);
        $comments = $blog->getCommentsByPostId($_GET['id']);
        break;
    case 'category':
        $category = $blog->getCategoryById($_GET['id']);
        $posts = $blog->getPostsByCategoryId($_GET['id']);
        break;
    case 'about':
        // 关于页面内容
        break;
    default:
        $posts = $blog->getRecentPosts();
        break;
}

// 根据页面类型设置页面标题与描述常量（SEO）
switch (PAGE_TYPE) {
    case 'home':
        if (!defined('PAGE_TITLE')) define('PAGE_TITLE', '首页');
        if (!defined('PAGE_DESCRIPTION')) define('PAGE_DESCRIPTION', BLOG_DESCRIPTION);
        break;
    case 'post':
        $postTitle = isset($post['title']) ? $post['title'] : '文章';
        $postDesc = isset($post['content']) ? truncate(strip_tags($post['content']), 160) : BLOG_DESCRIPTION;
        if (!defined('PAGE_TITLE')) define('PAGE_TITLE', $postTitle);
        if (!defined('PAGE_DESCRIPTION')) define('PAGE_DESCRIPTION', $postDesc);
        break;
    case 'category':
        $catName = isset($category['name']) ? $category['name'] : '分类';
        if (!defined('PAGE_TITLE')) define('PAGE_TITLE', $catName);
        if (!defined('PAGE_DESCRIPTION')) define('PAGE_DESCRIPTION', BLOG_DESCRIPTION);
        break;
    case 'about':
        if (!defined('PAGE_TITLE')) define('PAGE_TITLE', '关于');
        if (!defined('PAGE_DESCRIPTION')) define('PAGE_DESCRIPTION', getSetting('blog_description', BLOG_DESCRIPTION));
        break;
    default:
        if (!defined('PAGE_TITLE')) define('PAGE_TITLE', '首页');
        if (!defined('PAGE_DESCRIPTION')) define('PAGE_DESCRIPTION', BLOG_DESCRIPTION);
        break;
}

// 引入模板
require_once TEMPLATE_DIR . '/header.php';
switch (PAGE_TYPE) {
    case 'home':
        require_once TEMPLATE_DIR . '/home.php';
        break;
    case 'post':
        require_once TEMPLATE_DIR . '/single.php';
        break;
    case 'category':
        require_once TEMPLATE_DIR . '/category.php';
        break;
    case 'about':
        require_once TEMPLATE_DIR . '/about.php';
        break;
    default:
        require_once TEMPLATE_DIR . '/home.php';
        break;
}
require_once TEMPLATE_DIR . '/footer.php';