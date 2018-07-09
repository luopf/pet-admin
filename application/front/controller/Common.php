<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/6/1
 * Time: 18:05
 */

namespace app\front\controller;

use think\Controller;
class Common extends Controller
{
    // 图片上传
    public 	function uploadImage(){
		//验证图片能否上传
		if($_FILES) $verify = \UtilImage::verifyImage();
        if($verify['errorCode'] == 1) return $verify;
		//上传图片
        $resultImg = \UtilImage::uploadPhoto('file', 'upload/image/post/',200,140);

        if(!$resultImg) die(json_encode(\common::errorArray(1, "上传失败",$resultImg)));

        $result['img_url'] = $resultImg['url'];
        $result['thumb'] = $resultImg['thumb'];

		echo json_encode(\common::errorArray(0, "上传成功",$result));
	}

	

	// 删除图片
    public function deleteUpLoadImage(){


        $img_url = input('img_url');

        $img_file_path = 'upload'.$img_url;

        if(file_exists($img_file_path)){
            if(unlink($img_file_path)){
                echo json_encode(\common::errorArray(0, "删除成功",true));

            } else {
                echo json_encode(\common::errorArray(0, "删除失败",false));
            }
        } else {
            echo json_encode(\common::errorArray(1, "文件不存",$img_file_path));
        }

    }
}