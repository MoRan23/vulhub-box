<?php
require 'vendor/autoload.php'; // 引入 Composer 生成的 autoload 文件
$markdownFile = $_GET['directory'] .'/README.zh-cn.md'; // 设置要展示的 Markdown 文件
$parsedown = new Parsedown(); // 创建 Parsedown 对象

// 读取 Markdown 文件的内容并解析为 HTML
$markdownContent = file_get_contents($markdownFile);
$htmlContent = $parsedown->text($markdownContent);

// 查找HTML中的图片标签并修改图片路径
$htmlContent = preg_replace_callback('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/', function($matches) {
    $getdir = $_GET['directory'];
    $oldPath = $matches[1];
    // 在这里可以根据实际需求修改图片路径
    // 例如，将相对路径转换为绝对路径
    $newPath = $getdir . '/' . $oldPath;
    return str_replace($oldPath, $newPath, $matches[0]);
}, $htmlContent);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>题解</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php echo $htmlContent; ?>
</body>
</html>