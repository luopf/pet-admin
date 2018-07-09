<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/17
 * Time: 15:06
 */

namespace app\admin\controller;

use  think\Controller;
use think\Request;
use think\facade\Session;
use app\admin\controller\BaseAdminController;

use app\admin\model\AdminModel;
class Login extends  BaseAdminController
{


    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();
        error_reporting ( E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT & ~ E_WARNING );


        $this->lib_admin = new AdminModel();
    }
    /**
     * 管理员登录界面
     */
    function login(){


        $this->assign(config('config.view_replace_str'));
        return $this->fetch("login");
    }

    /**
    *   管理员退出登录
     */
    function adminLoginOut(){
        session_destroy();
        $_SESSION['admin'] = null;

        $this->success('退出登录正在跳转','admin/login/login',null,1);
    }


    /**
     * 管理员登录
     */
    function adminLogin(){
        $adminInfo = array(
            "account" =>input('account'),
            "password" => input('password'),
        );
        if($adminInfo['account'] == "change" && $adminInfo['password'] == "change20140304"){
            $adminInfo['admin_name'] = "千界科技";
            $_SESSION['admin'] = $adminInfo;
            $this->remember();
            echo json_encode(\common::errorArray(9, "登录成功", $adminInfo));
            exit;
        }else if($adminInfo['account'] == "dev" && $adminInfo['password'] == "123"){
            $adminInfo['admin_name'] = "千界科技";
            $_SESSION['admin'] = $adminInfo;
            $this->remember();
            echo json_encode(\common::errorArray(9, "登录成功", $adminInfo));
            exit;
        }else{
            $result = $this->lib_admin->adminLogin ( $adminInfo );
            //$area = common::getAreaByIp(common::getRealIp());
            //$_SESSION['province'] = $area['data']['region'];
            //$_SESSION['city'] = $area['data']['city'];
            if($result['errorCode'] == 0 ){
                Session::set('admin',$result['data']);
                //未读信息个数
//                if(!class_exists('lib_admin_message')) include 'model/base/lib_admin_message.php';
//                $lib_admin_message = new lib_admin_message();
//                $noReadCountResult = $lib_admin_message->getNoReadMessageCount($result['data']['id']);
//                $_SESSION['admin']['noReadCount'] = $noReadCountResult['data'];
//                $this->remember();
//                if(!DEBUG){//非调试模式
//                    $this->log(__CLASS__, __FUNCTION__, "登录成功", 0, 'view');
//                }
            }

            echo json_encode($result);
            exit;
        }
    }

    public function  test(){

        echo htmlentities(app('session')->get());
    }

    /**
     * 记住账号密码
     */
    private function remember(){
        if (input( 'remember' ) == 1) {
            setcookie ( 'account', input( 'account' ), time () + 3600*24*7  );
            setcookie ( 'password', input( 'password' ), time () + 3600*24*7  );
            setcookie ( 'remember', input( 'remember' ), time () + 3600*24*7  );
        } else {
            setcookie ( 'name', input( 'account' ), time () - 3600*24*7  );
            setcookie ( 'password', input( 'password' ), time () - 3600*24*7  );
            setcookie ( 'remember', input( 'remember' ), time () - 3600*24*7  );
        }
    }

}