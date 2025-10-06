<?php
/**
 * PHP专业博客程序 - 添加新文章页面
 */

// 包含管理员验证
require_once 'admin_auth.php';

// 定义页面标题
$page_title = '添加新文章';
$page_description = '创建并发布新的博客文章';

// 初始化变量
$title = $content = $excerpt = '';
$category_id = 0;
$status = 'draft';
$errors = array();

// 获取分类列表
$categories = array();
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        $query = "SELECT * FROM categories ORDER BY name";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch(PDOException $e) {
    $errors[] = '获取分类失败: ' . $e->getMessage();
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取和验证表单数据
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $excerpt = isset($_POST['excerpt']) ? trim($_POST['excerpt']) : '';
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : 'draft';
    
    // 简单验证
    if (empty($title)) {
        $errors[] = '请输入文章标题';
    }
    
    if (empty($content)) {
        $errors[] = '请输入文章内容';
    }
    
    // 如果没有错误，保存文章
    if (empty($errors)) {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                // 准备SQL查询
                $query = "INSERT INTO posts (title, content, excerpt, category_id, status, created_at, updated_at) 
                         VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
                $stmt = $db->prepare($query);
                $stmt->execute([$title, $content, $excerpt, $category_id, $status]);
                
                // 获取插入的文章ID
                $post_id = $db->lastInsertId();
                
                // 重定向到文章管理页面
                header('Location: posts.php?success=添加文章成功');
                exit;
            } else {
                $errors[] = '无法连接到数据库';
            }
        } catch(PDOException $e) {
            $errors[] = '保存文章失败: ' . $e->getMessage();
        }
    }
}

// 包含头部
include 'includes/header.php';
?>

<!-- 内容区域 -->
<div class="content-wrapper">
    <div class="content-header">
        <h1>添加新文章</h1>
        <p>创建一篇新的博客文章</p>
    </div>
    
    <!-- 错误信息显示 -->
    <?php if (!empty($errors)): ?>
        <div class="error-message" style="margin-bottom: 20px;">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- 添加文章表单 -->
    <div style="background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); padding: 20px;">
        <form method="POST" action="add_post.php">
            <div style="margin-bottom: 20px;">
                <label for="title" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">文章标题 <span style="color: red;">*</span></label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" placeholder="请输入文章标题" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="category_id" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">分类</label>
                <select id="category_id" name="category_id" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                    <option value="0">-- 请选择分类 --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $category_id === $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="excerpt" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">摘要</label>
                <textarea id="excerpt" name="excerpt" rows="3" placeholder="请输入文章摘要" 
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"><?php echo htmlspecialchars($excerpt); ?></textarea>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="content" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">文章内容 <span style="color: red;">*</span></label>
                <textarea id="content" name="content" rows="15" placeholder="请输入文章内容" required 
                          style="width: 100%; border: 1px solid #ddd; border-radius: 4px;"><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="status" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">状态</label>
                <select id="status" name="status" 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                    <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>草稿</option>
                    <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>已发布</option>
                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>待审核</option>
                </select>
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
                ">保存文章</button>
                <a href="posts.php" style="
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

<!-- TinyMCE 富文本编辑器 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" integrity="sha512-6JR4bbn8rCKvrkdoTJd/VFyXAN4CE9XMtgykPWgKiHjou56YDJxWsi90hAeMTYxNwUnKSQu9JPc3SQUg+aGCHw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
tinymce.init({
    selector: '#content',
    height: 400,
    menubar: true,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons'
    ],
    toolbar: 'undo redo | blocks | ' +
        'bold italic underline strikethrough | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | link image media table | code preview fullscreen | help',
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; line-height: 1.6; }',
    branding: false,
    promotion: false,
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    },
    // 图片上传配置
    images_upload_url: 'upload_image.php',
    images_upload_handler: function (blobInfo, success, failure, progress) {
        var xhr, formData;
        
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', 'upload_image.php');
        
        xhr.upload.onprogress = function (e) {
            progress(e.loaded / e.total * 100);
        };
        
        xhr.onload = function() {
            var json;
            
            if (xhr.status === 403) {
                failure('HTTP Error: ' + xhr.status, { remove: true });
                return;
            }
            
            if (xhr.status < 200 || xhr.status >= 300) {
                failure('HTTP Error: ' + xhr.status);
                return;
            }
            
            json = JSON.parse(xhr.responseText);
            
            if (!json || typeof json.location != 'string') {
                failure('Invalid JSON: ' + xhr.responseText);
                return;
            }
            
            success(json.location);
        };
        
        xhr.onerror = function () {
            failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
        };
        
        formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        
        xhr.send(formData);
    },
    // 自动保存
    autosave_ask_before_unload: true,
    autosave_interval: '30s',
    autosave_prefix: 'tinymce-autosave-{path}{query}-{id}-',
    autosave_restore_when_empty: false,
    autosave_retention: '2m',
    // 内容过滤
    valid_elements: '*[*]',
    extended_valid_elements: 'script[src|async|defer|type|charset]',
    // 样式配置
    style_formats: [
        {title: '标题', items: [
            {title: '标题 1', format: 'h1'},
            {title: '标题 2', format: 'h2'},
            {title: '标题 3', format: 'h3'},
            {title: '标题 4', format: 'h4'},
            {title: '标题 5', format: 'h5'},
            {title: '标题 6', format: 'h6'}
        ]},
        {title: '内联', items: [
            {title: '粗体', format: 'bold'},
            {title: '斜体', format: 'italic'},
            {title: '下划线', format: 'underline'},
            {title: '删除线', format: 'strikethrough'},
            {title: '上标', format: 'superscript'},
            {title: '下标', format: 'subscript'},
            {title: '代码', format: 'code'}
        ]},
        {title: '块', items: [
            {title: '段落', format: 'p'},
            {title: '引用', format: 'blockquote'},
            {title: '代码块', format: 'pre'}
        ]}
    ]
});
</script>

<?php
// 包含页脚
include 'includes/footer.php';
?>