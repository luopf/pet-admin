<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/8
 * Time: 16:24
 */

namespace app\admin\controller;

use think\Controller;


class BaseAdminController extends Controller
{

    private $go = __HOST__."/index.php/admin/login/login";
    //---------------------------------菜单管理-----------------------------------
    /**
     * 获取菜单
     * @param object $controler
     */
    protected function getMenu($controler){
        //加入缓存
        $menuCache = spAccess('r' , 'menuCache');
        if(!$menuCache){
            if(!class_exists('lib_menu')) include 'model/base/lib_menu.php';
            $lib_menu = new lib_menu();
            $result = $lib_menu->getMenuList();
            spAccess('w' , 'menuCache', $result['data'], 3600);
            $controler->menuList = $result['data'];
        }else{
            $controler->menuList = $menuCache;
        }
        $controler->theme = $this->getConfigValue('admin_theme');//后台主题
        $controler->skin = $this->getConfigValue('admin_skin');//后台主题
    }

    /**
     * 获取并设置菜单
     * @param object $controler
     */
    protected function getSetMenu($controler){
        $this->getMenu($controler);
        $this->setMenu($controler);
    }

    /**
     * 设置选中菜单
     * @param object $controller
     */
    private function setMenu($controller){
        if(!class_exists('lib_menu')) include 'model/base/lib_menu.php';
        $lib_menu = new lib_menu();
        $mid = $controller->spArgs('mid');
        if(!$mid){
            $mid = 1;//默认菜单主键
        }
        $menuResult = $lib_menu->findMenu(array('id'=>$mid));
        $menuTopResult = $lib_menu->findMenuTop(array('id'=>$menuResult['data']['menu_top_id']));
        $_SESSION['currentMenu'] = array(
            "navName"=>$menuTopResult['data']['alias'],
            "navTitle"=>$menuTopResult['data']['name'],
            "menuName"=>$menuResult['data']['unique_name'],
            "menuTitle"=>$menuResult['data']['name'],
            "icon"=>$menuResult['data']['icon'],
            "mid"=>$menuResult['data']['id'],
            "menu_top_id"=>$menuResult['data']['menu_top_id']
        );
    }


    /**
     * 构造页面提交数据
     * @param object $controller
     * @param array $keyList array('name','title','sort')
     * @param bool $nullAllowed true defalut 允许传入空值 该参数在update的时候会用到
     * @return array $argsList
     */
    protected function getArgsList($controller,$keyList,$nullAllowed = true){

        foreach ($keyList as $key){
            if($nullAllowed){
                $argsList[$key] = input($key);
            }else{
                if(null != input($key) && '' != input($key)){
                    $argsList[$key] = input($key);
                }
            }
        }
        return $argsList;
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

    //----------------------------------------消息通知-------------------------------

    /**
     * 发送通知信息
     * @param String $title
     * @param String $content(link:超链接)
     * @param int $type
     * @return array $result
     */
    function sendMessage($title,$content,$type = 0){
        $message = array(
            'title' => $title,
            'content' => $content,
            'type' => $type
        );
        if(!class_exists('lib_admin_message')) include 'model/base/lib_admin_message.php';
        $lib_message = new lib_admin_message();
        $result = $lib_message->sendMessage($message);
        return $result;
    }

    /**
     * 判断是否是对象格式
     * @param string $key
     * @return boolean
     */
    private function isObjArea($key){
        $result = strpos($key,'.');
        if($result !== false){
            $keyList = explode('.',$key);
            $newKey = $keyList[1];
            return $newKey;
        }else{
            return false;
        }
    }

    /**
     * 判断是否是时间区域
     * @param string $key
     * @return boolean
     */
    private function isDateArea($key){
        $keyList = explode('_',$key);
        if($keyList[0] == 'from' || $keyList[0] == 'to'){
            unset($keyList[0]);
            $newKey = implode('_',$keyList);
            return $newKey;
        }else{
            return false;
        }
    }

    /**
     * 控制器权限验证
     * @param array $session
     * @param string $go
     */
    protected function rightVerify($session,$go = ''){
        if (!isset($session)){
            if('' == $go){
                $go = $this->go;
            }
            echo "<html><head><meta http-equiv='refresh' content='0;url=".$go."'></head><body></body><ml>";
            echo "<script type='text/javascript'>window.location.href('".$go."');</script>";
            exit;
        }
    }


    /**
     * 判断获取参数
     * @param array $conditionList
     * @param obj $controller
     * @param string $operator
     * @param string $key
     * @return unknown
     */
    private function getList($conditionList,$controller,$operator,$key){
        if(null != input($key) && '' != input($key)){
            $dateKey = $this->isDateArea($key);
            if($dateKey){//判断是否时间段
                array_push($conditionList,  array("field" => $dateKey,"operator" => $operator,"value" => input($key)));
            }else{
                array_push($conditionList,  array("field" => $key,"operator" => $operator,"value" => input($key)));
            }
        }
        return $conditionList;
    }


    /**
     * 构造分页页面提交数据
     * @param object $controller
     * @param array $keyValueList array('name'=>'like','add_time'=>'>=','sort'=>'=')
     * @return array
     */
    protected function getPagingList($controller,$keyValueList){
        $conditionList = array();
        foreach ($keyValueList as $key=>$operator){
            $objKey = $this->isObjArea($key);
            if($objKey){//判断是否是.格式a.name
                $conditionList = $this->getList($conditionList, $controller, $operator, $objKey);
            }else{
                $conditionList = $this->getList($conditionList, $controller, $operator, $key);
            }
        }
        return $conditionList;
    }



}