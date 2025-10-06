<?php
/**
 * PHP专业博客程序 - 博客核心类
 */

class Blog {
    private $db;

    /**
     * 构造函数
     */
    public function __construct() {
        $this->db = new Database();
        $this->db->getConnection();
    }

    /**
     * 获取最新文章列表
     * @param int $limit 获取数量
     * @return array 文章列表
     */
    public function getRecentPosts($limit = POSTS_PER_PAGE) {
        $query = "SELECT p.*, c.name as category_name FROM posts p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  ORDER BY p.created_at DESC LIMIT ?";
        return $this->db->query($query, array($limit));
    }

    /**
     * 通过ID获取文章
     * @param int $id 文章ID
     * @return array|null 文章数据或null
     */
    public function getPostById($id) {
        $query = "SELECT p.*, c.name as category_name FROM posts p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = ?";
        return $this->db->queryOne($query, array($id));
    }

    /**
     * 获取所有分类
     * @return array 分类列表
     */
    public function getAllCategories() {
        $query = "SELECT c.*, COUNT(p.id) as post_count FROM categories c 
                  LEFT JOIN posts p ON c.id = p.category_id 
                  GROUP BY c.id 
                  ORDER BY c.name";
        return $this->db->query($query);
    }

    /**
     * 通过ID获取分类
     * @param int $id 分类ID
     * @return array|null 分类数据或null
     */
    public function getCategoryById($id) {
        $query = "SELECT * FROM categories WHERE id = ?";
        return $this->db->queryOne($query, array($id));
    }

    /**
     * 获取分类下的文章
     * @param int $categoryId 分类ID
     * @param int $limit 获取数量
     * @return array 文章列表
     */
    public function getPostsByCategoryId($categoryId, $limit = POSTS_PER_PAGE) {
        $query = "SELECT p.*, c.name as category_name FROM posts p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.category_id = ? 
                  ORDER BY p.created_at DESC LIMIT ?";
        return $this->db->query($query, array($categoryId, $limit));
    }

    /**
     * 获取文章的评论
     * @param int $postId 文章ID
     * @return array 评论列表
     */
    public function getCommentsByPostId($postId) {
        $query = "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC";
        return $this->db->query($query, array($postId));
    }

    /**
     * 添加评论
     * @param int $postId 文章ID
     * @param string $name 评论者名称
     * @param string $email 评论者邮箱
     * @param string $content 评论内容
     * @return int 评论ID
     */
    public function addComment($postId, $name, $email, $content) {
        $query = "INSERT INTO comments (post_id, name, email, content, created_at) 
                  VALUES (?, ?, ?, ?, NOW())";
        return $this->db->insert($query, array($postId, $name, $email, $content));
    }

    /**
     * 搜索文章
     * @param string $keyword 搜索关键词
     * @return array 搜索结果
     */
    public function searchPosts($keyword) {
        $query = "SELECT p.*, c.name as category_name FROM posts p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.title LIKE ? OR p.content LIKE ? 
                  ORDER BY p.created_at DESC";
        $searchTerm = "%" . $keyword . "%";
        return $this->db->query($query, array($searchTerm, $searchTerm));
    }

    /**
     * 获取统计信息
     * @return array 统计数据
     */
    public function getStats() {
        $stats = array();
        $stats['total_posts'] = $this->db->queryOne("SELECT COUNT(*) as count FROM posts")['count'];
        $stats['total_categories'] = $this->db->queryOne("SELECT COUNT(*) as count FROM categories")['count'];
        $stats['total_comments'] = $this->db->queryOne("SELECT COUNT(*) as count FROM comments")['count'];
        $stats['latest_post'] = $this->db->queryOne("SELECT title, created_at FROM posts ORDER BY created_at DESC");
        return $stats;
    }
}