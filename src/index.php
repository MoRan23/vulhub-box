<?php
function getSubdirectories($path) {
    $subdirectories = array_filter(glob($path . '/*', GLOB_ONLYDIR), function($dir) {
        return basename($dir)[0] !== '.';  // 排除隐藏文件夹.
    });
    return $subdirectories;
}

$rootPath = __DIR__; // 设置为你需要扫描的目录的路径
$directories = getSubdirectories($rootPath);

$selectedDir = '';
$selectedSubDir = '';

// 检查表单是否提交并且目录是正确的
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['queryDirectory'])) {
    // 读取用户选择的目录
    $selectedDir = $_POST['subdirSelect'] ?? '';
    $selectedSubDir = $_POST['childSubdirSelect'] ?? '';
    $selectedMdDir = $selectedDir . '/' . $selectedSubDir;
    // 你可以在这里使用 $selectedDir 和 $selectedSubDir 变量去做更多的逻辑处理

}
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="UTF-8">
<title>Vulhub - CVE 靶场</title>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background-color: #f7f7f7;
    }
    .container {
        text-align: center;
    }
    h1, h2 {
        margin: 0;
    }
    h1 {
        margin-top: -150px;
        font-size: 2.5em;
        margin-bottom: 0.5em;
    }
    h2 {
        font-size: 1.5em;
        color: #666;
        margin-bottom: 1em;
    }
    .styled-btn {
        padding: 10px 15px;
        margin: 10px 0;
        font-size: 1em;
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;

    }
    select, input[type=submit], input[type=button] {
        padding: 10px 15px;
        margin: 10px 0;
        font-size: 1em;
    }
    input[type=submit], input[type=button] {
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    input[type=submit]:hover, input[type=submit]:focus, input[type=button]:hover, input[type=button]:focus {
        background-color: #0056b3;
    }
    .results {
        margin-top: 20px;
    }
</style>
<script>
function updateSubdirectories() {
    var rootSelect = document.getElementById('subdirSelect');
    var childSubdirSelect = document.getElementById('childSubdirSelect');

    // 清空第二个下拉选项
    while (childSubdirSelect.options.length > 0) {
        childSubdirSelect.remove(0);
    }

    var selectedSubdir = rootSelect.value;
    if (!selectedSubdir) return;  // 如果没有选中，则不执行

    // 使用 fetch API 发出异步请求
    fetch('get_subdirectories.php?subdir=' + encodeURIComponent(selectedSubdir))
    .then(response => response.json())
    .then(options => {
        options.forEach(function(subdir) {
            var opt = document.createElement('option');
            opt.value = subdir;
            opt.text = subdir;
            childSubdirSelect.appendChild(opt);
        });
        if (options.length > 0) {
            childSubdirSelect.disabled = false;
        } else {
            childSubdirSelect.disabled = true;
        }
    })
    .catch(error => console.error('An error occurred:', error));
}
function submitForm() {
    document.getElementById("directoryForm").submit();
}
window.addEventListener('load', function() {
    setTimeout(function() {
    document.getElementById('refer').click();
    }, 2000);
});
</script>
</head>
<body>

<div class="container">
    <h1>Vulhub</h1>
    <?php
        $getram = './getram.sh';
        $outputgetram = shell_exec($getram);
        if($outputgetram >= 95){
            echo '<h2>当前内存占用: '.$outputgetram.'%</h2>';
            echo '<h2>内存占用过高, 请勿再开启靶机!!!</h2>';
        }else{
            echo '<h2>当前内存占用: '.$outputgetram.'%</h2>';
        }
    ?>

    <form id="directoryForm" action="index.php" method="post">
        <select id="subdirSelect" name="subdirSelect" onchange="updateSubdirectories()">
            <option value="">选择类</option>
            <?php foreach ($directories as $dir):
                $dirName = basename($dir);
            ?>
                <option value="<?php echo htmlspecialchars($dirName); ?>" <?php echo $isSelected; ?>>
                    <?php echo htmlspecialchars($dirName); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select id="childSubdirSelect" name="childSubdirSelect" disabled>
            <option value="">选择CVE</option>
            <!-- 子目录的子目录选项将在这里动态生成 -->
            <?php if (isset($subdirectoriesOfSelectedDir)) {
                foreach ($subdirectoriesOfSelectedDir as $subdir):
                    $subdirName = basename($subdir);
                ?>
                    <option value="<?php echo htmlspecialchars($subdirName); ?>" <?php echo $isSelected; ?>>
                        <?php echo htmlspecialchars($subdirName); ?>
                    </option>
                <?php endforeach;}?>
        </select>

        <!-- 查询按钮 -->
        <input type="submit" name="queryDirectory" value="查询靶机">
    </form>

    <?php
        // 如果有选择，显示用户的选择
        function forsearch($array){
            foreach($array as $port){
                $checkport = './checkport.sh '.$port;
                $outputcheckport = shell_exec($checkport);
                if($outputcheckport!=NULL){
                    echo '<p>端口被占用!</p>';
                    echo '<p>占用靶机:</p><p>'.htmlspecialchars($outputcheckport).'</p>';
                    echo '<p>请询问后查询对应靶机关闭</p>';
                    return 0;
                }else{
                    continue;
                }
            }
            return 1;
        }

        if ($selectedDir !== '' && $selectedSubDir !== '') {
            echo "<div class=\"results\">";
            echo '<a href="view_markdown.php?directory=' . urlencode($selectedMdDir) . '" target="_blank" class="styled-btn">查看题解</a>';
            echo '<p>所有按钮请不要多次点击!!!请耐心等待页面刷新!!!</p>';
            echo "<p>选择的靶机是: " . htmlspecialchars($selectedDir) . "  ---  " . htmlspecialchars($selectedSubDir) . "</p>";
            $checklive = './checklive.sh '.$selectedDir.' '.$selectedSubDir;
            $outarraychecklive = array_filter(explode("\n", shell_exec($checklive)));
            if($outarraychecklive!=NULL){
                echo '<p>靶机已存在!</p>';
                echo '<p>靶机剩余时间:</p>';
                $checktime = './checktime.sh '.$selectedDir.' '.$selectedSubDir;
                $outputchecktime = shell_exec($checktime);
                echo '<p>'.htmlspecialchars($outputchecktime).'</p>';
                echo '<p>靶机IP: 10.1.5.115</p>';
                echo '<p>已开放端口:</p>';
                $findports = './findports.sh '.$selectedDir.' '.$selectedSubDir;
                $outarrayfindports = array_filter(explode("\n", shell_exec($findports)));
                foreach($outarrayfindports as $port){
                    echo '<p>'.htmlspecialchars($port).'</p>';
                }
                echo '<form id="directoryForm" action="index.php" method="post">';
                echo '<input type="hidden" name="subdirSelect" value="'.$selectedDir.'">';
                echo '<input type="hidden" name="childSubdirSelect" value="'.$selectedSubDir.'">';
                echo '<input type="hidden" name="queryDirectory" value="1">';
                echo '<input type="submit" name="add" value="靶机续期"><br>';
                echo '<input type="submit" name="stop" value="销毁靶机">';
                echo '</form>';
                if(isset($_POST['stop'])){
                    $stopdocker = './stopdocker.sh '.$selectedDir.' '.$selectedSubDir;
                    shell_exec($stopdocker);
                    while(array_filter(explode("\n", shell_exec($checklive)))!=NULL){
                        sleep(5);
                    }
                    if(array_filter(explode("\n", shell_exec($checklive)))==NULL){
                        echo '<p>靶机已销毁!</p>';
                        echo '<form id="directoryForm" action="index.php" method="post">';
                        echo '<input type="hidden" name="subdirSelect" value="'.$selectedDir.'">';
                        echo '<input type="hidden" name="childSubdirSelect" value="'.$selectedSubDir.'">';
                        echo '<input type="hidden" name="queryDirectory" value="1">';
                        echo '<input type="submit" id="refer" value="跳转中......">';
                        echo '</form>';
                    }
                }else if(isset($_POST['add'])){
                    $addtime = './addtime.sh '.$selectedDir.' '.$selectedSubDir;
                    $outputaddtime = shell_exec($addtime);
                    if($outputaddtime=="OK"){
                        echo '<p>靶机续期成功!</p>';
                        echo '<form id="directoryForm" action="index.php" method="post">';
                        echo '<input type="hidden" name="subdirSelect" value="'.$selectedDir.'">';
                        echo '<input type="hidden" name="childSubdirSelect" value="'.$selectedSubDir.'">';
                        echo '<input type="hidden" name="queryDirectory" value="1">';
                        echo '<input type="submit" id="refer" value="跳转中......">';
                        echo '</form>';
                    }
                }
            }else{
                echo '<p>靶机不存在!</p>';
                $findports = './findports.sh '.$selectedDir.' '.$selectedSubDir;
                $outarrayfindports = array_filter(explode("\n", shell_exec($findports)));
                if(forsearch($outarrayfindports)){
                    echo '<p>端口未被占用!</p>';
                    echo '<form id="directoryForm" action="index.php" method="post">';
                    echo '<input type="hidden" name="subdirSelect" value="'.$selectedDir.'">';
                    echo '<input type="hidden" name="childSubdirSelect" value="'.$selectedSubDir.'">';
                    echo '<input type="hidden" name="queryDirectory" value="1">';
                    echo '<input type="submit" name="start" value="启动靶机">';
                    echo '</form>';
                    if(isset($_POST['start'])){
                        $startdocker='./startdocker.sh '.$selectedDir.' '.$selectedSubDir;
                        shell_exec($startdocker);
                        while(array_filter(explode("\n", shell_exec($checklive)))==NULL){
                            sleep(5);
                        }
                        if(array_filter(explode("\n", shell_exec($checklive)))!=NULL){
                            echo '<p>靶机启动成功!</p>';
                            echo '<form id="directoryForm" action="index.php" method="post">';
                            echo '<input type="hidden" name="subdirSelect" value="'.$selectedDir.'">';
                            echo '<input type="hidden" name="childSubdirSelect" value="'.$selectedSubDir.'">';
                            echo '<input type="hidden" name="queryDirectory" value="1">';
                            echo '<input type="submit" id="refer" value="跳转中......">';
                            echo '</form>';
                        }
                    }
                }
                echo "</div>";
            }
        }
    ?>
</div>
</body>
</html>
