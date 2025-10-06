<?php
/**
 * PHP专业博客程序 - 图片上传处理
 */

// 包含管理员验证
require_once 'admin_auth.php';

// 设置响应头
header('Content-Type: application/json');

// 检查是否是POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => '只允许POST请求']);
    exit;
}

// 检查是否有文件上传
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => '文件上传失败']);
    exit;
}

$file = $_FILES['file'];

// 验证文件类型
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => '不支持的文件类型，只允许 JPEG、PNG、GIF、WebP 格式']);
    exit;
}

// 验证文件大小（最大5MB）
$maxSize = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['error' => '文件大小不能超过5MB']);
    exit;
}

// 创建上传目录
$uploadDir = '../uploads/images/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['error' => '无法创建上传目录']);
        exit;
    }
}

// 生成唯一文件名
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('img_', true) . '.' . $extension;
$filepath = $uploadDir . $filename;

// 移动文件
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    http_response_code(500);
    echo json_encode(['error' => '文件保存失败']);
    exit;
}

// 返回成功响应
$imageUrl = '../uploads/images/' . $filename;
echo json_encode([
    'location' => $imageUrl,
    'filename' => $filename,
    'size' => $file['size']
]);
?>