<!-- 主要内容区域 -->
            <div class="main-content">
                <article class="about-page">
                    <header class="about-header">
                        <h2 class="section-title">关于博客</h2>
                    </header>
                    <div class="about-content">
                        <div class="about-intro">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(BLOG_AUTHOR); ?>&background=random&size=128" alt="博主头像" class="author-avatar">
                            <div class="intro-text">
                                <h3>你好，我是 <?php echo BLOG_AUTHOR; ?></h3>
                                <p>欢迎来到我的个人博客！这里是我分享知识、经验和思考的地方。</p>
                            </div>
                        </div>
                        
                        <div class="about-details">
                            <h4>关于博客</h4>
                            <p><?php echo escapeHtml(getSetting('blog_description', BLOG_DESCRIPTION)); ?></p>
                            <p><?php echo nl2br(escapeHtml(getSetting('about_content', '我会定期更新博客内容，分享我在学习和工作中的心得和体会。希望这些内容能够帮助到你，也欢迎你在评论区留言交流。'))); ?></p>
                        </div>
                        
                        <div class="blog-stats">
                            <h4>博客统计</h4>
                            <div class="stats-container">
                                <?php 
                                $blog = new Blog();
                                $statsData = method_exists($blog, 'getStats') ? $blog->getStats() : null;
                                $stats = is_array($statsData) ? $statsData : [];
                                $total_posts = isset($stats['total_posts']) ? (int)$stats['total_posts'] : 0;
                                $total_categories = isset($stats['total_categories']) ? (int)$stats['total_categories'] : 0;
                                $total_comments = isset($stats['total_comments']) ? (int)$stats['total_comments'] : 0;
                                ?>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $total_posts; ?></div>
                                    <div class="stat-label">文章总数</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $total_categories; ?></div>
                                    <div class="stat-label">分类总数</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $total_comments; ?></div>
                                    <div class="stat-label">评论总数</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="contact-info">
                            <h4>联系我</h4>
                            <p>如果你有任何问题或建议，欢迎随时联系我：</p>
                            <ul class="contact-links">
                                <li><a href="mailto:<?php echo BLOG_EMAIL; ?>"><i class="far fa-envelope"></i> <?php echo BLOG_EMAIL; ?></a></li>
                                <li><a href="#"><i class="fab fa-weixin"></i> 微信公众号</a></li>
                                <li><a href="#"><i class="fab fa-weibo"></i> 微博</a></li>
                            </ul>
                        </div>
                    </div>
                </article>
            </div>