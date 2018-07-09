<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/6/14
 * Time: 12:06
 */

namespace app\front\controller;

use think\Controller;
use app\front\model\NewsModel;
use app\front\model\UserModel;
class News extends  Controller
{

    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();

        $this->lib_new = new NewsModel();
        $this->lib_user = new UserModel();
    }

    /**
     * 获取分页参数
     * @param object $controller key pageIndex ,pageSize
     * @return array
     */
    protected function getPageInfo($controller){

        if(null != input('pageIndex') && '' != input('pageIndex')){
            $page['pageIndex'] = input('pageIndex');
        }else{
            $page['pageIndex'] = 1;
        }
        if(null != input('pageSize') && '' != input('pageSize')){
            $page['pageSize'] = input('pageSize');
        }else{
            $page['pageSize'] = 10;
        }
        return $page;
    }

    /**
    *   添加消息
     */
    public function addNew(){
        $newInfo = array();
        if(input('user_id') != '' && input('user_id') != null){
            $newInfo['user_id'] = input('user_id');
        }
        $userInfo = $this->lib_user->findUser(array('id'=>input('user_id')));
        if($userInfo['errorCode'] == 0){
            $newInfo['account'] = $userInfo['data']['account'];
        }

        if(input('type') !== '' && input('type') != null){
            $newInfo['type'] = input('type');
        }
        $newInfo['add_time'] = \common::getTime();
        $newInfo['time_desc'] = \common::getDescTime();
        $result = $this->lib_new->addNew($newInfo);
        echo json_encode($result);
    }

    function testNew (){
        $page = $this->getPageInfo($this);
        $sort = "add_time desc";
        $conditionList = [];
        array_push($conditionList,  array("field" => 'user_id',"operator" => '=',"value" => 1));
        array_push($conditionList,  array("field" => 'type',"operator" => '=',"value" => 1));
        if(input('type_id') != '' && input('type_id') != null){

        }
        if(input('user_id') !== '' && input('user_id') != null){

        }

        $result = $this->lib_new->pagingNews($page,$conditionList,$sort);
    }
    /**
     * 分页查询消息
     */
    function pagingNews(){
        $page = $this->getPageInfo($this);
        $sort = "add_time desc";
        $conditionList = [];

        if(input('type_id') != '' && input('type_id') != null){
            array_push($conditionList,  array("field" => 'type',"operator" => '=',"value" => input('type_id')));
        }
        if(input('user_id') !== '' && input('user_id') != null){
            array_push($conditionList,  array("field" => 'user_id',"operator" => '=',"value" => input('user_id')));
        }

        $result = $this->lib_new->pagingNews($page,$conditionList,$sort);
        \ChromePhp::INFO($result);
        echo json_encode($result);
    }
}