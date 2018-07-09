<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/19
 * Time: 16:18
 */

namespace app\front\controller;


use think\Controller;

use app\front\model\SlideModel;
class Slide extends Controller
{

    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();

        $this->lib_slide = new SlideModel();

    }

    /***
    * 获取所有幻灯片
     */
    public function getAllSlide(){
        $result = $this->lib_slide->findAllSlide();
        echo json_encode($result);
    }

    /**
    *     获取单张幻灯片的信息
     **/
    public function getSlideInfo(){
        $id = input('id');
        $result = $this->lib_slide->findSlide(array('id'=>$id));
        echo json_encode($result);
    }


}