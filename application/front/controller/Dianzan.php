<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/6/15
 * Time: 10:24
 */

namespace app\front\controller;

use think\Controller;

use app\front\model\DianzanModel;
use app\front\model\MessageModel;
class Dianzan extends Controller
{

    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();


        $this->lib_dianzan = new DianzanModel();
        $this->lib_message = new MessageModel();
    }

    /**
    *        添加点赞记录
     **/
    public function addDianzan(){
        $mid = input('mid');
        $user_id = input('user_id');
        $dianzanInfo = array(
            'mid'=> $mid,
            'user_id'=> $user_id
        );
        $result = $this->lib_dianzan->addDianzan($dianzanInfo);
        if($result['errorCode'] == 0){// 添加点赞成功
            $result = $this->lib_message->increaseField(array('id'=>$mid,),'like_num',1);

        }
        echo  json_encode($result);
    }
    /**
    *       取消点赞
     **/
    public function deleteDianzan(){
        $mid = input('mid');
        $user_id = input('user_id');
        $dianzanInfo = array(
            'mid'=> $mid,
            'user_id'=> $user_id
        );
        $result = $this->lib_dianzan->deleteDianzan($dianzanInfo);
        if($result['errorCode'] == 0){
            $result = $this->lib_message->decreaseField(array('id'=>$mid,),'like_num',1);
        }
        echo  json_encode($result);
    }

    /**
    *       查找点赞记录
     **/
    public function findUserDianzan(){
        $mid = input('mid');
        $user_id = input('user_id');
        $dianzanInfo = array(
            'mid'=> $mid,
            'user_id'=> $user_id
        );
        $result = $this->lib_dianzan->findDianzan($dianzanInfo);
        echo json_encode($result);
    }
}