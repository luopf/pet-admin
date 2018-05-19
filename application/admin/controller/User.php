<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/15
 * Time: 17:54
 */

namespace app\admin\controller;

use  think\Controller;
use think\Request;

use app\admin\controller\BaseAdminController;

use think\facade\Session;
use app\admin\model\store\UserModel;

class User extends  BaseAdminController
{

    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();
        error_reporting ( E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT & ~ E_WARNING );
        $this->rightVerify(Session::get('admin'), __HOST__."/index.php/admin/login/login");
        $this->lib_user = new UserModel();

    }

    public function userList(){


        $this->assign(config('config.view_replace_str'));
        return $this->fetch('userList');
    }

    public function pagingUser(){
        $page = $this->getPageInfo($this);
        $keyValueList = array('name'=>'like','nick_name'=>'like','subscribe'=>'=','country'=>'like','province'=>'like','city'=>'like','phone'=>'=','sex'=>'=','remark'=>'like','from_add_time'=>'>=','to_add_time'=>'<=');
        $conditionList = $this->getPagingList($this, $keyValueList);
        $sort = "subscribe_time desc,add_time desc";
        $result =  $this->lib_user->pagingUser($page,$conditionList,$sort);
        echo json_encode($result);
    }
    public function userDetail(){
        $uid = input('uid');
        $user = $this->lib_user->findUser(array('id'=>$uid));
        \ChromePhp::INFO($user);
        $this->assign('userInfo',$user['data']);
        $this->assign(config('config.view_replace_str'));
        return $this->fetch('userDetail');

    }



}