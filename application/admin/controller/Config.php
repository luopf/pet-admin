<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/14
 * Time: 10:41
 */

namespace app\admin\controller;

use app\admin\controller\BaseAdminController;

use think\Facade\Session;
use app\admin\model\ConfigModel;
class Config extends BaseAdminController
{

    /**
     * 构造函数
     */
    function __construct()
    {
        parent::__construct();
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
        $this->rightVerify(Session::get('admin'), __HOST__ . "/index.php/admin/login/login");
        $this->libAdminConfig = new ConfigModel();
    }


    // 添加后台配置
    //--------------------------百货配置---------------------------------------
    /**
     ** 百货配置页面
     **/
    function configList(){

        $arr=array('main');
        $configResult = $this->libAdminConfig->findAllConfigComplete($arr);
        $this->assign('configInfo',$configResult['data']);
        $this->assign(config('config.view_replace_str'));
       return $this->fetch('adminConfig');
    }

    /**
     * 设置夺宝币配置
     */
    function setFeeRatioConfig(){
        $configInfo = $this->getConfigInfo();
        $result = $this->libAdminConfig->updateConfig($configInfo);
        echo json_encode($result);
    }

    /**
     * 构造修改信息
     * @param string lib_config
     * @return array
     */
     function getConfigInfo(){
        $configInfo = array();
        $arr=array('main');
        $result = $this->libAdminConfig->findAllConfigComplete($arr);         //获取所有配置$result['data']
        foreach ($result['data'] as $per){
            $configInfo[$per['item_key']] = input($per['item_key']);
        }
        return $configInfo;

    }


}