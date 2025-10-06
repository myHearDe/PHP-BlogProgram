<!-- 主要内容区域 -->
            <div class="main-content">
                <?php if (!empty($category)): ?>
                    <div class="category-header">
                        <h2 class="section-title">分类：<?php echo escapeHtml($category['name']); ?></h2>
                        <p class="category-description"><?php echo !empty($category['description']) ? escapeHtml($category['description']) : '该分类暂无描述'; ?></p>
                    </div>
                    
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
                            <p>该分类下暂无文章</p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="not-found">
                        <h2>分类不存在</h2>
                        <p>抱歉，您访问的分类不存在或已被删除。</p>
                        <a href="<?php echo getUrl(); ?>" class="back-home">返回首页</a>
                    </div>
                <?php endif; ?>
            </div>