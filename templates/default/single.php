<!-- 主要内容区域 -->
            <div class="main-content">
                <?php if (!empty($post)): ?>
                    <article class="single-post">
                        <header class="post-header">
                            <h1 class="post-title"><?php echo escapeHtml($post['title']); ?></h1>
                            <div class="post-meta">
                                <span class="post-date"><i class="far fa-calendar-alt"></i> <?php echo formatDate($post['created_at']); ?></span>
                                <span class="post-category"><i class="far fa-folder"></i> 
                                    <a href="<?php echo getUrl('index.php?page=category&id=' . $post['category_id']); ?>">
                                        <?php echo escapeHtml($post['category_name']); ?>
                                    </a>
                                </span>
                            </div>
                        </header>
                        <div class="post-content">
                            <?php echo $post['content']; ?>
                        </div>
                        <footer class="post-footer">
                            <div class="post-tags">
                                <i class="fas fa-tags"></i>
                                <span>标签：暂无标签</span>
                            </div>
                            <div class="post-share">
                                <i class="fas fa-share-alt"></i>
                                <span>分享：</span>
                                <a href="#" class="share-link"><i class="fab fa-weixin"></i></a>
                                <a href="#" class="share-link"><i class="fab fa-weibo"></i></a>
                                <a href="#" class="share-link"><i class="fab fa-twitter"></i></a>
                            </div>
                        </footer>
                    </article>

                    <!-- 评论区 -->
                    <section class="comments-section">
                        <h3 class="section-title">评论 (<?php echo count($comments); ?>)</h3>
                        
                        <?php if (!empty($comments)): ?>
                            <div class="comments-list">
                                <?php foreach ($comments as $comment): ?>
                                    <div class="comment">
                                        <div class="comment-author">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($comment['name']); ?>&background=random" alt="<?php echo escapeHtml($comment['name']); ?>" class="avatar">
                                            <div class="author-info">
                                                <h4 class="author-name"><?php echo escapeHtml($comment['name']); ?></h4>
                                                <time class="comment-time"><?php echo formatDate($comment['created_at'], 'Y-m-d H:i'); ?></time>
                                            </div>
                                        </div>
                                        <div class="comment-content">
                                            <?php echo nl2br(escapeHtml($comment['content'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="no-comments">暂无评论，快来抢沙发吧！</p>
                        <?php endif; ?>

                        <!-- 评论表单 -->
                        <div class="comment-form">
                            <h4>发表评论</h4>
                            <form action="<?php echo getUrl('index.php?page=post&id=' . $post['id']); ?>" method="post">
                                <div class="form-group">
                                    <label for="name">姓名 *</label>
                                    <input type="text" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">邮箱 *</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="content">评论内容 *</label>
                                    <textarea id="content" name="content" rows="5" required></textarea>
                                </div>
                                <button type="submit" class="submit-comment">提交评论</button>
                            </form>
                        </div>
                    </section>
                <?php else: ?>
                    <div class="not-found">
                        <h2>文章不存在</h2>
                        <p>抱歉，您访问的文章不存在或已被删除。</p>
                        <a href="<?php echo getUrl(); ?>" class="back-home">返回首页</a>
                    </div>
                <?php endif; ?>
            </div>