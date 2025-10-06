<?php
/**
 * PHP专业博客程序 - 辅助函数文件
 */

/**
 * 格式化日期时间
 * @param string $date 日期字符串
 * @param string $format 格式
 * @return string 格式化后的日期
 */
function formatDate($date, $format = 'Y年m月d日') {
    return date($format, strtotime($date));
}

/**
 * 截取字符串长度
 * @param string $str 原始字符串
 * @param int $length 截取长度
 * @param string $suffix 后缀
 * @return string 截取后的字符串
 */
function truncate($str, $length, $suffix = '...') {
    if (mb_strlen($str, 'UTF-8') <= $length) {
        return $str;
    }
    return mb_substr($str, 0, $length, 'UTF-8') . $suffix;
}

/**
 * 转义HTML特殊字符
 * @param string $str 原始字符串
 * @return string 转义后的字符串
 */
function escapeHtml($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * 获取相对URL
 * @param string $path 路径
 * @return string URL
 */
function getUrl($path = '') {
    return BLOG_URL . '/' . ltrim($path, '/');
}

/**
 * 验证表单输入
 * @param string $input 输入内容
 * @return string 验证后的内容
 */
function validateInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

/**
 * 获取当前页面URL
 * @return string 当前页面URL
 */
function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    return "$protocol://$host$uri";
}

/**
 * 生成随机字符串
 * @param int $length 字符串长度
 * @return string 随机字符串
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * 从settings表获取指定键的值（带缓存）
 * @param string $name 键名
 * @param mixed $default 默认值，如果不存在则返回
 * @return string|null 设置值
 */
function getSetting($name, $default = null) {
    static $settingsCache = null;
    if ($settingsCache === null) {
        // 如有必要，加载数据库类
        if (!class_exists('Database')) {
            if (defined('INCLUDES_DIR')) {
                require_once INCLUDES_DIR . '/database.php';
            } else {
                // 兜底：相对路径
                require_once __DIR__ . '/database.php';
            }
        }
        $dbObj = new Database();
        $conn = $dbObj->getConnection();
        $settingsCache = array();
        if ($conn) {
            try {
                $stmt = $conn->prepare('SELECT name, value FROM settings');
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $settingsCache[$row['name']] = $row['value'];
                }
            } catch (Exception $e) {
                // 忽略错误，保持空缓存
            }
        }
    }
    return isset($settingsCache[$name]) ? $settingsCache[$name] : $default;
}