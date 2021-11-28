<?php
/**
 * 获取喜马拉雅有声书单集名称
 */

$albumId=33435885; //专辑 ID；
$pageNum = 27; // 总页数；默认一页有 30 集
$folder = 'E:\BaiduNetdiskDownload\第一序列_有声小说全集钱德勒_北冥有声播讲';//本地目录

// API
$url = "https://www.ximalaya.com/revision/album/v1/getTracksList";

// 单集标题
$titleArr = [];

// 请求数据
for($i = 1;$i<=$pageNum;$i++){
    $xurl = $url."?albumId=".$albumId."&pageNum=".$i;
    $json = file_get_contents($xurl);
    $data = json_decode($json);
    $arr = $data->data->tracks;

    foreach($arr as $v){
        $title = $v->title;
        // 格式优化(去除标题中的括号)
        $title = str_replace('-','',$title);
        $title = preg_replace('/\（.*?\）/', '', $title);

        $titleArr[$v->index] = $title;
    }
}

$data = $titleArr;

// 修改本地文件
$files = getDir($folder);

// 批量修改
foreach($files as $path){
    
    updataName($path,$data,$folder);
    echo '<br>';
}

// 重命名
function updataName($path,$data,$folder){

    $name = basename($path,".mp3");
    $key = substr($name , 0 , 4);
    $newName = $key.' '.$data[(int)$key];
    $ext = pathinfo($path, PATHINFO_EXTENSION);

    if(file_exists($path)){
        if(rename($path,$folder.DIRECTORY_SEPARATOR.$newName.'.'.$ext)){
            echo '重命名成功！'.$path.'==>'.$newName;
        }else{
            echo '重命名失败！'.$path;
        }
    }else{
        echo $path.' 不存在！';
    }
}

// 遍历文件夹
function getDir($path)
{
    //判断目录是否为空
    if(!file_exists($path)) {
        return [];
    }

    $files = scandir($path);
    $fileItem = [];
    foreach($files as $v) {
        $newPath = $path .DIRECTORY_SEPARATOR . $v;
        if(is_dir($newPath) && $v != '.' && $v != '..') {
            $fileItem = array_merge($fileItem, getDir($newPath));
        }else if(is_file($newPath)){
            $fileItem[] = $newPath;
        }
    }

    return $fileItem;
}