<?php
/**
 * PHP专业博客程序 - 配置文件
 * 重要：请根据您的实际环境修改以下配置！
 */

// 数据库配置 - 已根据用户提供的信息更新
// 当前配置：数据库名和用户名都是test_9zcnb_com，密码已设置
define('DB_HOST', 'localhost'); // 数据库主机，通常是localhost
define('DB_NAME', 'test_9zcnb_com'); // 数据库名 - 已更新为用户提供的数据库名
define('DB_USER', 'test_9zcnb_com'); // 数据库用户名 - 已更新为用户提供的用户名
define('DB_PASS', '4j5Dt4hFxrkrPFtt'); // 数据库密码 - 已更新为用户提供的密码
define('DB_PORT', '3306'); // 数据库端口

// 博客基本设置
define('BLOG_NAME', '我的专业博客');
define('BLOG_DESCRIPTION', '分享我的知识和经验');
define('BLOG_AUTHOR', '博主');
define('BLOG_EMAIL', 'admin@example.com');
define('POSTS_PER_PAGE', 5);

// URL设置 - 根据错误日志中的路径信息更新
define('BLOG_URL', 'http://test.9zcnb.com');

// 时区设置
date_default_timezone_set('Asia/Shanghai');

// 错误报告设置
error_reporting(E_ALL);
ini_set('display_errors', 1);