<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/21
 * Time: 15:57
 */

namespace app\front\controller;

use think\Controller;

use app\front\model\LabelModel;
class Label extends  Controller
{

/**
* 构造函数
*/
    function __construct() {
        parent::__construct ();

        $this->lib_label = new LabelModel();

    }
    /**
    * 获取所有的label 数据
     **/
    public function getAllLables(){
     $result = $this->lib_label->findAllLabel();
     echo json_encode($result);
    }

}