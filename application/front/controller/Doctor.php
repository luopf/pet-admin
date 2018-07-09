<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/6/4
 * Time: 14:23
 */

namespace app\front\controller;

use think\Controller;

use app\front\model\DiseaseModel;
class Doctor extends Controller
{
    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();

        $this->lib_disease = new DiseaseModel();

    }

    /***
     *  添加病例
     */
    public function addDisease(){
        $diseaseInfo = $this->getArgsList($this,array('user_id','account','user_name','cate_name','birthday','sex','sterilisate','last_immunedate','last_repellantdate','phone','wx_number','eat','content'));

        $img_list = array();
        $imgArr = json_decode(input('img_list'),true);

        foreach ($imgArr as $img){
            array_push($img_list,array('image'=>$img['img_url'],'thumb'=>strstr($img['img_url'],'.jpg',true)."_thumb.jpg"));
        }
        $diseaseInfo['img_list'] = stripslashes(json_encode($img_list));
      
        $result = $this->lib_disease->addDisease($diseaseInfo);
        echo json_encode($result);

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


}