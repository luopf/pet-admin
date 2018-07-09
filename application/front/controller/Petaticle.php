<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/28
 * Time: 17:55
 */

namespace app\front\controller;

use think\Controller;

use app\front\model\petaticle\SlideModel;

use app\front\model\petaticle\LabelModel;

use app\front\model\petaticle\AticleModel;
class Petaticle extends Controller
{

    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();

        $this->lib_slide = new SlideModel();
        $this->lib_label = new LabelModel();
        $this->lib_aticle = new AticleModel();
    }

    /***
     * 获取所有幻灯片
     */
    public function getAllSlide(){
        $result = $this->lib_slide->findAllSlide();
        echo json_encode($result);
    }

    /***
     * 获取所有标签
     */
    public function getAllLabel(){
        $result = $this->lib_label->findAllLabel();
        echo json_encode($result);
    }

    /**
    *   获取文章详情
     */
    public function findAticle(){
        $aid = input('aid');
        $aticleInfo = $this->lib_aticle->findAticle(array('id'=>$aid));
        echo json_encode($aticleInfo);
    }


    /**
     * 分页展示文章列表
     */
    public function paggingAticle(){
        $page = array();
        $page['pageIndex'] = input('pageIndex');
        $page['pageSize'] = input('pageSize');
        $conditionList = [];
        $sort = "add_time desc";
        if(input('cid') != null && input('cid') != ''){
            array_push($conditionList,array("field" => 'label_id',"operator" => '=',"value" => input('cid')));
        }
        if(input('keyWord') != null && input('keyWord') != ''){
            array_push($conditionList,array("field" => 'title',"operator" => 'like',"value" => input('keyWord')));
        }
        $result = $this->lib_aticle->pagingAticle($page,$conditionList,$sort);
        echo  json_encode($result);
    }



}