<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/6/4
 * Time: 9:17
 */

namespace app\admin\controller;

use  think\Controller;
use think\Request;

use app\admin\controller\BaseAdminController;
use think\facade\Session;

use app\admin\model\store\DiseaseModel;
class Doctor extends BaseAdminController
{

    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();
        error_reporting ( E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT & ~ E_WARNING );
        $this->rightVerify(Session::get('admin'), __HOST__."/index.php/admin/login/login");
        $this->lib_disease = new DiseaseModel();
    }


    public function deleteDisease(){
        $id = input('id');
        $result = $this->lib_disease->deleteDisease(array('id'=>$id));
        echo  json_encode($result);
    }

    /***
    *  添加病例
     */
    public function addDisease(){

    }

    /**
    * 疾病咨询列表
     */
    public function diseaseList(){
        $this->assign(config('config.view_replace_str'));
        return $this->fetch('diseaseList');
    }

    /**
    * 疾病详情页
     */
    public function diseaseDetail(){
        $id = input('id');
        $diseaseInfo = $this->lib_disease->findDisease(array('id'=>$id));
        if($diseaseInfo['data']['img_list']){
            $diseaseInfo['data']['img_list'] = json_decode( $diseaseInfo['data']['img_list'],true);
        }
        $this->assign('diseaseInfo',$diseaseInfo['data']);
        $this->assign(config('config.view_replace_str'));
        return $this->fetch('diseaseDetail');
    }



    /**
     * 分页查询疾病列表
     */
    function pagingDisease(){
        $page = $this->getPageInfo($this);
        $sort = "add_time desc";
        $conditionList = [];
         if(input('user_name') !== ''&& input('user_name') != null){
             array_push($conditionList,  array("field" => 'user_name',"operator" => 'like',"value" => input('user_name')));
         }
         if(input('phone') !== ''&& input('phone') != null){
             array_push($conditionList,  array("field" => 'phone',"operator" => 'like',"value" => input('phone')));
         }
          if(input('cate_name') !== ''&& input('cate_name') != null){
              array_push($conditionList,  array("field" => 'cate_name',"operator" => 'like',"value" => input('cate_name')));
          }
        if(input('from') !== ''&& input('from') != null){
            array_push($conditionList,  array("field" => 'add_time',"operator" => '>=',"value" => input('from')));
        }
        if(input('to') !== '' && input('to') != null){
            array_push($conditionList,  array("field" => 'end_time',"operator" => '<=',"value" => input('end_time')));
        }
        $result = $this->lib_disease->pagingDisease($page,$conditionList,$sort);
        \ChromePhp::INFO($result);
        echo json_encode($result);
    }

}