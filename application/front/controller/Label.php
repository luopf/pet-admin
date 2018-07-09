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
     * 获取所有的label 数据不分区
     **/
    public function  findAllLables(){
        $result = $this->lib_label->findAllLabel();
        echo json_encode($result);
    }

    /**
    * 获取所有的label 数据
     **/
    public function getAllLables(){
     $result = $this->lib_label->findAllLabel();
     $labels = $result['data'];
     $arr = [];
     $j = 0;
     $tempArr = [];
     for ($i = 0;$i < count($labels);$i++){
         $obj = $labels[$i];

         array_push($tempArr,$obj);

         if(($i + 1) % 8 == 0){
             array_push($arr,$tempArr);
             $tempArr = [];

         }  else {
             if(($i + 1) == count($labels)){
                 array_push($arr,$tempArr);
             }

         }



     }
        $result['data'] = $arr;

     echo json_encode($result);
    }

    /**
    * 查找单条标签信息
     */
    public function findLabelDetail(){
        $lid = input('lid');
        $result = $this->lib_label->findLabel(array('id'=>$lid));
        echo json_encode($result);
    }

}