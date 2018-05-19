<?php
namespace app\admin\controller;

use  think\Controller;
use think\Request;
use app\admin\model\store\LabelModel;
use think\facade\Session;


use app\admin\controller\BaseAdminController;
/**
 *首页图片轮播管理
 * @name Storelabel.php
 * @package pet
 * @category controller
 * @author luopengfei
 * @version 2.0
 * @since 2018-05-08
 */
class Storelabel extends BaseAdminController{
    private $lib_label;
	/**
	 * 构造函数
	 */
	function __construct() {
		parent::__construct ();
		error_reporting ( E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT & ~ E_WARNING );
        $this->rightVerify(Session::get('admin'), __HOST__."/index.php/admin/login/login");
	    $this->lib_label = new LabelModel();
	}
	/**
     * 标签列表页面
     */
    function labelList(){

        $labelList = $this->lib_label->findAllLabel();
        $this->assign('labelList',$labelList['data']);
        $this->assign(config('config.view_replace_str'));
       return $this->fetch();

    }
    /**
     * 绑定商品页面
     **/
    function showBindGoods(){
        //一级分类
        $this->getMenu($this);
        $conditions = array('rank' => 1);
        if(!class_exists('lib_category')) include'model/store/lib_category.php';
        $lib_category = new lib_category();
        $firCateResult = $lib_category->getCategorys($conditions, "order_index asc");
        $this->categoryList = $firCateResult['data'];
    	$this->log(__CLASS__, __FUNCTION__, "绑定商品页面", 1, 'view');
        $this->display("../template/admin/{$this->theme}/store/page/label/bindGoods.html");
    }
    
	/**
	 * 添加标签页面
	 */
	function addLabel(){

        $this->assign(config('config.view_replace_str'));
        return $this->fetch();
	}

    /**
     * 编辑标签页面
     */
    function editLabel(){
        $id = input("id");
        $conditions = array('id' => $id);
        $result = $this->lib_label->findLabel ($conditions);
        $this->assign('labelInfo',$result['data']);
      \ChromePhp::info($result['data']);
        $this->assign(config('config.view_replace_str'));
        return $this->fetch();
    }

    /**
     * 标签商品列表页面
     */
    function labelGoodsList(){
		$this->getMenu($this);
        $conditions = array( 'id' => $this->spArgs('id'));
        $result = $this->lib_label->findLabel ($conditions);
        $this->labelInfo = $result['data'];
        $this->log(__CLASS__, __FUNCTION__, "标签商品列表页面", 1, 'view');
        $this->display("../template/admin/{$this->theme}/store/page/label/labelGoodsList.html");
    }
    
	/**
	 * 更新标签
	 */
	function updateLabel(){	   
	    //验证图片能否上传
	    if($_FILES) $verify = \UtilImage::verifyImage();
	    if($verify['errorCode'] == 1) exit(json_encode($verify));
		$conditions = array('id' => input('id'));
		$labelInfo = $this->getArgsList($this,array(name,order_index,gids,link,goods_num));
		if(input('imgFlag') == 1){//判断是否需要重新上传图片
			//更新图片
			$prevUrl = input('prevurl');
			if($prevUrl){
				$resultImg = \UtilImage::uploadPhoto('imgurl','upload/image/store/label/',100,100);

				if($resultImg){
					$labelInfo['image'] = $resultImg['url'];
					$labelInfo['thumb'] = $resultImg['thumb'];
					$prevThumbUrl = substr($prevUrl, 0,strripos($prevUrl,'.')) . "_thumb" .  substr($prevUrl, strripos($prevUrl,'.'));		
					@unlink($prevUrl);//delete url
					@unlink($prevThumbUrl);//delete thumb
				}else{
					echo json_encode(\common::errorArray(1, "11修改标签图片失败", 0));
					exit;
				}
			}else{
				echo json_encode(\common::errorArray(1, "原图路径未传，不能删除", 0));
				exit;
			}
		}
		if(input('imgFlag2') == 1){//判断广告图片是否需要重新上传图片
			//更新图片
			$prevUrl = input('prevurl2');
			$resultImg = \UtilImage::uploadPhotoJust('ad_image','upload/image/store/label/');
			if($resultImg){
				$labelInfo['ad_image'] = $resultImg;
				@unlink($prevUrl);//delete url
			}
		}
		$result = $this->lib_label->updateLabel($conditions,$labelInfo);

		echo json_encode($result);
	}

	public function test(){
	    var_dump(config('config.view_replace_str'));
    }

	/**
	 * 添加标签
	 */
	function insertLabel(){
	    //验证图片能否上传
	    if($_FILES)
	        $verify = \UtilImage::verifyImage();
	    if($verify['errorCode'] == 1) exit(json_encode($verify));
	    $labelInfo = $this->getArgsList($this, array(name,order_index,gids,link,goods_num));
		//广告图
		$url = \UtilImage::uploadPhotoJust('ad_image','upload/image/store/label/');
		if($url){
			$labelnfo['ad_image'] = $url;
		}
		//上传图片
		$resultImg =  \UtilImage::uploadPhoto('imgurl','upload/image/store/label/',100,100);
		if($resultImg){
			$labelInfo['image'] = $resultImg['url'];
			$labelInfo['thumb'] = $resultImg['thumb'];
			$result = $this->lib_label->addLabel ($labelInfo );

			echo json_encode($result);	
		}else{
			echo json_encode(common::errorArray(1, "上传标签图片失败", 0));
		}
	}
	
	/**
	 * 删除标签
	 */
	function delLabel(){
		$conditions = array('id' => input('id'));
		$result = $this->lib_label->findLabel ($conditions);
		@unlink($result['data']['image']);//delete url
		@unlink($result['data']['thumb']);//delete thumb
		@unlink($result['data']['ad_image']);//delete thumb
		$result = $this->lib_label->deleteLabel ($conditions);
		echo json_encode($result);
	}
	
}