<?php
/**
 * PHP专业博客程序 - 添加新分类页面
 */

// 包含管理员验证
require_once 'admin_auth.php';

// 定义页面标题
$page_title = '添加新分类';
$page_description = '创建新的博客分类';

// 初始化变量
$name = $description = '';
$errors = array();

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取和验证表单数据
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    // 简单验证
    if (empty($name)) {
        $errors[] = '请输入分类名称';
    } else {
        // 检查分类名称是否已存在
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                $query = "SELECT id FROM categories WHERE name = ? LIMIT 1";
                $stmt = $db->prepare($query);
                $stmt->execute([$name]);
                $category = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($category) {
                    $errors[] = '分类名称已存在';
                }
            }
        } catch(PDOException $e) {
            $errors[] = '数据库错误: ' . $e->getMessage();
        }
    }
    
    // 如果没有错误，保存分类
    if (empty($errors)) {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                // 准备SQL查询
                $query = "INSERT INTO categories (name, slug, description, created_at, updated_at) 
                         VALUES (?, ?, ?, NOW(), NOW())";
                
                // 生成slug（URL友好的字符串）
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
                
                $stmt = $db->prepare($query);
                $stmt->execute([$name, $slug, $description]);
                
                // 重定向到分类管理页面
                header('Location: categories.php?success=添加分类成功');
                exit;
            } else {
                $errors[] = '无法连接到数据库';
            }
        } catch(PDOException $e) {
            $errors[] = '保存分类失败: ' . $e->getMessage();
        }
    }
}

// 包含头部
include 'includes/header.php';
?>

<!-- 内容区域 -->
<div class="content-wrapper">
    <div class="content-header">
        <h1>添加新分类</h1>
        <p>创建一个新的文章分类</p>
    </div>
    
    <!-- 错误信息显示 -->
    <?php if (!empty($errors)): ?>
        <div class="error-message" style="margin-bottom: 20px;">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- 添加分类表单 -->
    <div style="background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); padding: 20px; max-width: 600px;">
        <form method="POST" action="add_category.php">
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">分类名称 <span style="color: red;">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="请输入分类名称" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="description" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">分类描述</label>
                <textarea id="description" name="description" rows="3" placeholder="请输入分类描述" 
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" style="
                    background-color: #4a90e2;
                    color: white;
                    border: none;
                    padding: 12px 20px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 16px;
                    margin-right: 10px;
                ">保存分类</button>
                <a href="categories.php" style="
                    display: inline-block;
                    background-color: #95a5a6;
                    color: white;
                    border: none;
                    padding: 12px 20px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 16px;
                    text-decoration: none;
                ">取消</a>
            </div>
        </form>
    </div>
</div>

<?php
// 包含页脚
include 'includes/footer.php';
?>