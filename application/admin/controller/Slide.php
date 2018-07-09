<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/18
 * Time: 16:30
 */

namespace app\admin\controller;

use app\admin\controller\BaseAdminController;
use app\admin\model\store\SlideModel;

class Slide extends BaseAdminController
{

    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();
        error_reporting ( E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT & ~ E_WARNING );


        $this->lib_slide = new SlideModel();



    }

    /**
     * 幻灯片列表页面
     */
    function slideList(){

        $result = $this->lib_slide->findAllSlide ();

        $this->assign('slideList',$result['data']);
        $this->assign(config('config.view_replace_str'));
        return $this->fetch("slideList");
    }
    /**
    *  删除幻灯片
     */
    public function deleteSlide(){
        $id = input('id');
        $config = config('config.view_replace_str');
        $__LOD__ = $config['__LOD__'];
        $slide = $this->lib_slide->findSlide(array('id'=>$id));
        if($slide['errorCode'] == 0){
            $img_url = $__LOD__.$slide['data']['img_url'];
            @unlink($img_url);
            $thumb = $__LOD__.$slide['data']['thumb'];
            @unlink($thumb);
        }
        $result = $this->lib_slide->deleteSlide(array('id'=>$id));
        echo json_encode($result);

    }
    /**
     * 编辑幻灯片页面
     */
    function editSlide(){

        $result = $this->lib_slide->findSlide (array('id' => input('id')));
        $this->slideInfo = $result['data'];
        $this->assign('slideInfo',$result['data']);
        $this->assign(config('config.view_replace_str'));
        return $this->fetch("editSlide");
    }


    /**
     * 修改幻灯片
     */
    function updateSlide(){
        //验证图片能否上传
        if($_FILES) $verify = \UtilImage::verifyImage();
        if($verify['errorCode'] == 1) exit(json_encode($verify));
        $conditions = array('id' => input('id'));
        $slideInfo = $this->getArgsList($this, array(name,url,sort,type,target));
        if(input('imgFlag') == 1){//判断是否需要重新上传图片
            //更新图片
            $prevUrl = input('prevurl');
            if($prevUrl){
                $resultImg = \UtilImage::uploadPhoto('imgurl',"upload/image/store/slide/",86.4,48);
                if($resultImg){
                    $slideInfo['img_url'] = $resultImg['url'];
                    $slideInfo['thumb'] = $resultImg['thumb'];
                    $prevThumbUrl = substr($prevUrl, 0,strripos($prevUrl,'.')) . "_thumb" .  substr($prevUrl, strripos($prevUrl,'.'));
                    unlink($prevUrl);//delete url
                    unlink($prevThumbUrl);//delete thumb

                }else{
                    echo json_encode(\common::errorArray(1, "修改图片失败", 0));
                }
            }else{
                echo json_encode(\common::errorArray(1, "原图url未传，不能删除", 0));
            }
        }
        $result = $this->lib_slide->updateSlide($conditions,$slideInfo);
        echo json_encode($result);
    }


    /**
     * 添加商城幻灯片页面
     */
    function addSlide(){

        $this->assign(config('config.view_replace_str'));
        return $this->fetch("addSlide");

    }


    /**
     * 添加幻灯片
     */
    function insertSlide(){
        //验证图片能否上传
        if($_FILES) $verify = \UtilImage::verifyImage();
        if($verify['errorCode'] == 1) exit(json_encode($verify));
        $slidenfo = $this->getArgsList($this, array('name','url','sort','type','target')) ;
        //上传图片
        $resultImg = \UtilImage::uploadPhoto('imgurl',"upload/image/store/slide/",86.4,48);
        if($resultImg){
            $slidenfo['img_url'] = $resultImg['url'];
            $slidenfo['thumb'] = $resultImg['thumb'];
            $result = $this->lib_slide->addSlide ($slidenfo );

            echo json_encode($result);
        }else{
            echo json_encode(common::errorArray(1, "上传图片失败", 0));
        }
    }



}