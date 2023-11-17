<?php
if (isset($_GET['subdir'])) {
    $subdir = $_GET['subdir'];
    $rootPath = __DIR__; // 使用与index.php文件中相同的根路径
    $subdirPath = realpath($rootPath . DIRECTORY_SEPARATOR . $subdir);

    // 防止目录遍历
    if (strpos($subdirPath, $rootPath) !== 0 || !is_dir($subdirPath)) {
        echo json_encode([]);
        exit;
    }

    $subdirectories = getSubdirectories($subdirPath);
    $subdirectoriesBasename = array_map('basename', $subdirectories);
    echo json_encode($subdirectoriesBasename);
    exit;
}
  
// 这个函数应该与index.php文件中的定义相同
function getSubdirectories($path) {
    $subdirectories = array_filter(glob($path . '/*', GLOB_ONLYDIR), function($dir) {
        return basename($dir)[0] !== '.'; // 排除隐藏目录
    });
    return $subdirectories;
}