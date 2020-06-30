<?php
/**
 * Created by PhpStorm.
 * User: yingjun
 * Date: 2019-01-30
 * Time: 18:53
 */

namespace upload_util;
class UploadExtend
{
//    public static function upload_one($file)
//    {
//        $info = $file->validate(['size' => 4194304, 'ext' => 'jpg,png,gif'])->rule('uniqid')->move('upload/cover');
//        if ($info) {
//            $name = $info->getFilename();
//        } else {
//            return $info->getError();
//        }
//        return $name;
//    }

    public static function upload_more($files,$url)
    {
        $i = 0;
        $data =[count($files)];
        foreach ($files as $file) {
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->validate(['size' => 4194304, 'ext' => 'jpg,png,gif'])->rule('uniqid')->move($url);
            if ($info) {
                $data[$i] = $info->getFilename();
            } else {
                // 上传失败获取错误信息
                //echo $file->getError();
                return 1;
            }
            $i++;
        }
        return $data;
    }
}
