-- 创建settings表（键值存储）
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128) NOT NULL UNIQUE,
    value TEXT,
    type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 插入默认设置（如果不存在）
INSERT INTO settings (name, value)
VALUES
('blog_name', '我的专业博客'),
('blog_description', '分享我的知识和经验'),
('blog_author', '博主'),
('blog_email', 'admin@example.com'),
('posts_per_page', '5'),
('about_content', '我会定期更新博客内容，分享我在学习和工作中的心得和体会。希望这些内容能够帮助到你，也欢迎你在评论区留言交流。')
ON DUPLICATE KEY UPDATE value = VALUES(value);