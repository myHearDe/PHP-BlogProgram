<!-- 侧边栏 -->
            <aside class="sidebar">
                <!-- 博主信息 -->
                <div class="widget">
                    <h3 class="widget-title">博主信息</h3>
                    <div class="author-widget">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(BLOG_AUTHOR); ?>&background=random&size=128" alt="博主头像" class="author-avatar">
                        <h4 class="author-name"><?php echo BLOG_AUTHOR; ?></h4>
                        <p class="author-bio"><?php echo BLOG_DESCRIPTION; ?></p>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-weixin"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-weibo"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 分类列表 -->
                <div class="widget">
                    <h3 class="widget-title">文章分类</h3>
                    <ul class="category-list">
                        <?php 
                        $blog = new Blog();
                        $categories = $blog->getAllCategories();
                        foreach ($categories as $category): 
                        ?>
                        <li>
                            <a href="<?php echo getUrl('index.php?page=category&id=' . $category['id']); ?>">
                                <?php echo escapeHtml($category['name']); ?>
                                <span class="category-count">(<?php echo $category['post_count']; ?>)</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- 热门文章 -->
                <div class="widget">
                    <h3 class="widget-title">热门文章</h3>
                    <ul class="hot-posts">
                        <?php 
                        $hotPosts = $blog->getRecentPosts(5);
                        foreach ($hotPosts as $key => $hotPost): 
                        ?>
                        <li>
                            <span class="post-number"><?php echo $key + 1; ?></span>
                            <a href="<?php echo getUrl('index.php?page=post&id=' . $hotPost['id']); ?>">
                                <?php echo escapeHtml($hotPost['title']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- 标签云 -->
                <div class="widget">
                    <h3 class="widget-title">标签云</h3>
                    <div class="tag-cloud">
                        <a href="#">PHP</a>
                        <a href="#">JavaScript</a>
                        <a href="#">HTML</a>
                        <a href="#">CSS</a>
                        <a href="#">编程</a>
                        <a href="#">技术</a>
                        <a href="#">Web开发</a>
                    </div>
                </div>

                <!-- 统计信息 -->
                <?php 
                // 确保$stats为数组，避免Notice
                if (!isset($stats) || !is_array($stats)) {
                    if (!isset($blog) || !($blog instanceof Blog)) {
                        $blog = new Blog();
                    }
                    $statsData = method_exists($blog, 'getStats') ? $blog->getStats() : null;
                    $stats = is_array($statsData) ? $statsData : [
                        'total_posts' => 0,
                        'total_categories' => 0,
                        'total_comments' => 0,
                    ];
                }
                ?>
                <div class="widget">
                    <h3 class="widget-title">博客统计</h3>
                    <div class="stats-widget">
                        <div class="stat-row">
                            <span class="stat-label">文章总数：</span>
                            <span class="stat-value"><?php echo $stats['total_posts']; ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">分类总数：</span>
                            <span class="stat-value"><?php echo $stats['total_categories']; ?></span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">评论总数：</span>
                            <span class="stat-value"><?php echo $stats['total_comments']; ?></span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <!-- 页脚 -->
    <footer class="blog-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <h3><?php echo BLOG_NAME; ?></h3>
                    <p><?php echo BLOG_DESCRIPTION; ?></p>
                </div>
                <div class="footer-links">
                    <h4>快速链接</h4>
                    <ul>
                        <li><a href="<?php echo getUrl(); ?>">首页</a></li>
                        <li><a href="<?php echo getUrl('index.php?page=about'); ?>">关于</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>联系我</h4>
                    <p><i class="far fa-envelope"></i> <?php echo BLOG_EMAIL; ?></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo BLOG_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- 返回顶部按钮 -->
    <button id="back-to-top" class="back-to-top" title="返回顶部">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- JavaScript -->
    <script src="<?php echo getUrl('templates/default/script.js'); ?>"></script>
</body>
</html>