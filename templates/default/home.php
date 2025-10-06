<!-- 主要内容区域 -->
            <div class="main-content">
                <h2 class="section-title">最新文章</h2>
                
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <article class="blog-post">
                            <header class="post-header">
                                <h3 class="post-title">
                                    <a href="<?php echo getUrl('index.php?page=post&id=' . $post['id']); ?>">
                                        <?php echo escapeHtml($post['title']); ?>
                                    </a>
                                </h3>
                                <div class="post-meta">
                                    <span class="post-date"><i class="far fa-calendar-alt"></i> <?php echo formatDate($post['created_at']); ?></span>
                                    <span class="post-category"><i class="far fa-folder"></i> 
                                        <a href="<?php echo getUrl('index.php?page=category&id=' . $post['category_id']); ?>">
                                            <?php echo escapeHtml($post['category_name']); ?>
                                        </a>
                                    </span>
                                    <span class="post-comments"><i class="far fa-comment"></i> 评论数</span>
                                </div>
                            </header>
                            <div class="post-content">
                                <p><?php echo truncate(strip_tags($post['content']), 200); ?></p>
                            </div>
                            <footer class="post-footer">
                                <a href="<?php echo getUrl('index.php?page=post&id=' . $post['id']); ?>" class="read-more">阅读全文 <i class="fas fa-arrow-right"></i></a>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                
                    <!-- 分页 -->
                    <div class="pagination">
                        <a href="#" class="prev-page">上一页</a>
                        <a href="#" class="page-number active">1</a>
                        <a href="#" class="next-page">下一页</a>
                    </div>
                
                <?php else: ?>
                    <div class="no-posts">
                        <p>暂无文章，敬请期待！</p>
                    </div>
                <?php endif; ?>
            </div>