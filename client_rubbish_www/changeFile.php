<?php
/**
 * Created by PhpStorm.
 * User: phoenix
 * Date: 5/28/17
 * Time: 1:50 PM
 */

$dir = '/data/wwwroot/client';
$dirs = array(
  'css','images','js','sass'
);

// 需要替换引用其他文件的模板文件目录
$tmp_file = '/data/wwwroot/mge/App/Qwadmin/View/Client/front/1';
$map_arr = array(

);

$files = array();

// 获取指定目录的全部文件
getAllFile($dir, $files, $dirs);

foreach ($files as $file) {
    $fileinfo = pathinfo($file);
    $md5 = md5_file($file);

    if ($md5){
        $new_dir = $dir . '/' . get_img_upload_firesfile($md5) . '/' . get_img_upload_basefile($md5);
        $new_file_name = get_img_upload_filename($md5);
        if (!file_exists($new_dir)){
            mkdirs($new_dir);
        }
        copy($file, $new_dir . '/' . $new_file_name . '.' . $fileinfo['extension']);
        $map_arr[str_replace($dir, '', $file)] = str_replace($dir, '', $new_dir) . '/' . $new_file_name . '.' . $fileinfo['extension'];
    }
}

echo '<pre>';
var_dump($map_arr);
exit;
// 替换文件
foreach ($map_arr as $key=>$item) {
    listDir($tmp_file, $key, $item);
}


function listDir($dir,$find_str,$replace_str)
{
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ((is_dir($dir . "/" . $file)) && $file != "." && $file != "..") {
                    $this->listDir($dir . "/" . $file . "/");
                } else {
                    if ($file != "." && $file != "..") {
                        $str = file_get_contents($dir . "/" . $file);
                        $str = str_replace($find_str, $replace_str, $str);
                        file_put_contents($dir . "/" . $file, $str);
                    }
                }
            }
            closedir($dh);
        }
    }
}

function getAllFile($dir, &$files, $dirs = array()){
    if(is_dir($dir))
    {
        if ($dh = opendir($dir))
        {
            while (($file = readdir($dh)) !== false)
            {
                $f = $dir;
                if((is_dir($dir."/".$file)) && $file!="." && $file!=".." && in_array($file, $dirs))
                {
                    // echo "<b><font color='red'>文件名：</font></b>",$file,"<br><hr>";
                    getAllFile($dir."/".$file."/", $files, $dirs);
                }
                else
                {
                    if($file!="." && $file!=".." && is_file($f . $file))
                    {
                        $files[] = $f . $file;
                        // echo $file."<br>";
                    }
                }
            }
            closedir($dh);
        }
    }
}

function get_img_upload_basefile($param){
    return substr($param, 2, 4);
}


function get_img_upload_firesfile($param){
    return substr($param, 0, 2);
}

function get_img_upload_filename($param){
    return substr($param, 6);
}

function mkdirs($dir)
{
    if (!is_dir($dir)) {
        if (!mkdirs(dirname($dir))) {
            return FALSE;
        }
        if (!mkdir($dir, 0777)) {
            return FALSE;
        }
    }
    return TRUE;
}
