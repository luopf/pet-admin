<?php
namespace app\admin\controller;

use  think\Controller;
use think\Request;
use app\admin\model\store\MessageModel;
use  app\admin\model\store\LabelModel;
use think\facade\Session;
use app\admin\controller\BaseAdminController;


/**
 * 订单管理
 * @name Order.php
 * @package cws
 * @category controller
 * @link http://www.chanekeji.com
 * @author linli
 * @version 2.0
 * @copyright CHANGE INC
 * @since 2016-08-04
 */
class Message extends BaseAdminController{
    private $lib_order;
	/**
	 * 构造函数
	 */
	function __construct() {
		parent::__construct ();
		error_reporting ( E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT & ~ E_WARNING );

        $this->rightVerify(Session::get('admin'), __HOST__."/index.php/admin/login/login");
	    $this->lib_message = new MessageModel();
	    $this->lib_label = new LabelModel();
	}
	
	/**
	 * 订单列表页面
	 */
	function messageList(){
        $this->assign(config('config.view_replace_str'));
        return $this->fetch('messageList');
	}

	public function test(){
	    var_dump(__PUBLIC__);
    }

	/**
	 * 测试模板消息发送
	 */
	function testTemplateNotify(){
	     if(!class_exists('TemplateMessage')) include 'include/wechatUtil/TemplateMessage.php';
	     $template = new TemplateMessage();
	     $type = 1;
	     $touser = 'ooMjDs2IM4pLWMQigdtHhAyhLmQ0';
// 	     $touser = 'ooMjDs5sOWWGLPAtq5vU7y4PLPtQ';
	     $module = 'store';
	     $userInfo = '';
	     $templateShotId = 'OPENTM202243318';
	     $template::TemplateNotifyYourself($type, $touser, $module, $userInfo, $templateShotId);
	}
	
	/**
	 * 管理员修改补全订单界面
	 */
	function orderModify(){
	    $this->getMenu($this);
	    $oid = $this->spArgs('oid');
	    $orderResult = $this->lib_order->getOrderInfo(array('id' => $oid));
	    $this->orderInfo = $orderResult['data'];
//	    if($orderResult['data']['state'] == '0' || $orderResult['data']['state'] == "1" || $orderResult['data']['state'] == "6"){
	       $this->valid = true;
//	    }
	    // 获可联系时间配置
	    if(!class_exists(UtilConfig)) include_once "include/UtilConfig.php";
		$lib_config = new UtilConfig('store_config');
	    $contactTimeResult = $lib_config->findAllConfig(array('item_group'=>"contact_time"));
		$this->contactTimeResult = $contactTimeResult['data'];

	    $this->log(__CLASS__, __FUNCTION__, "管理员修改补全订单界面", 1, 'edit');
	    $this->display("../template/admin/{$this->theme}/store/page/order/orderModify.html");
	}
	
	/**
	 * 管理员修改用户发布的消息
     *
	 */
	function modifyMessage(){
	    $oldMessage = $this->lib_message->findMessage(array('id'=>input('oid')));
	    $oldImgs = $oldMessage['data']['img_list'];
	    $oldImgArr = json_decode($oldImgs,true);
	    $imgArr = array();
        if($_FILES)
            $verify = \UtilImage::verifyImage();
        if($verify['errorCode'] == 1) exit(json_encode($verify));


        if(input('imgFlag_1') == 1){//判断是否需要重新上传图片
            //更新图片
            $prevUrl = input('prevurl_1');
            if($prevUrl){
                $resultImg = \UtilImage::uploadPhoto('imgurl_1','upload/image/store/message/',100,100);
                if($resultImg){
                    $oldImgArr[0]['image'] = $resultImg['url'];
                    $oldImgArr[0]['thumb'] = $resultImg['thumb'];
                    $prevThumbUrl = substr($prevUrl, 0,strripos($prevUrl,'.')) . "_thumb" .  substr($prevUrl, strripos($prevUrl,'.'));
                    \ChromePhp::INFO("虚拟删除".$prevUrl);
                    \ChromePhp::INFO("虚拟删除".$prevThumbUrl);
//                    @unlink($prevUrl);//delete url
//                    @unlink($prevThumbUrl);//delete thumb
                }else{
                    echo json_encode(\common::errorArray(1, "修改图片失败", 0));
                    exit;
                }
            }else{
                echo json_encode(\common::errorArray(1, "原图路径未传，不能删除", 0));
                exit;
            }
        }
        if(input('imgFlag_2') == 1){//判断是否需要重新上传图片
            //更新图片
            $prevUrl = input('prevurl_2');
            if($prevUrl){
                $resultImg = \UtilImage::uploadPhoto('imgurl_2','upload/image/store/message/',100,100);
                if($resultImg){
                    $oldImgArr[1]['image'] = $resultImg['url'];
                    $oldImgArr[1]['thumb'] = $resultImg['thumb'];
                    $prevThumbUrl = substr($prevUrl, 0,strripos($prevUrl,'.')) . "_thumb" .  substr($prevUrl, strripos($prevUrl,'.'));
                    \ChromePhp::INFO("虚拟删除".$prevUrl);
                    \ChromePhp::INFO("虚拟删除".$prevThumbUrl);
//                    @unlink($prevUrl);//delete url
//                    @unlink($prevThumbUrl);//delete thumb
                }else{
                    echo json_encode(\common::errorArray(1, "修改图片失败", 0));
                    exit;
                }
            }else{
                echo json_encode(\common::errorArray(1, "原图路径未传，不能删除", 0));
                exit;
            }
        }
        if(input('imgFlag_3') == 1){//判断是否需要重新上传图片
            //更新图片
            $prevUrl = input('prevurl_3');
            if($prevUrl){
                $resultImg = \UtilImage::uploadPhoto('imgurl_3','upload/image/store/message/',100,100);
                if($resultImg){
                    $oldImgArr[2]['image'] = $resultImg['url'];
                    $oldImgArr[2]['thumb'] = $resultImg['thumb'];
                    $prevThumbUrl = substr($prevUrl, 0,strripos($prevUrl,'.')) . "_thumb" .  substr($prevUrl, strripos($prevUrl,'.'));
                    \ChromePhp::INFO("虚拟删除".$prevUrl);
                    \ChromePhp::INFO("虚拟删除".$prevThumbUrl);
//                    @unlink($prevUrl);//delete url
//                    @unlink($prevThumbUrl);//delete thumb
                }else{
                    echo json_encode(\common::errorArray(1, "修改图片失败", 0));
                    exit;
                }
            }else{
                echo json_encode(\common::errorArray(1, "原图路径未传，不能删除", 0));
                exit;
            }
        }

		$id = input('oid');
        $conditions = array(
            'id' => $id
        );

        $messageInfo = $this->getArgsList($this,array('label_id','nick_name','phone','address_text','text_content','check_num','message_num','like_num','longitude','latitude','address_text'));
        if(input('label_id') != null && input('label_id') != ''){
            $label = $this->lib_label->findLabel(array('id'=>input('label_id')));
            $messageInfo['label_name'] = $label['data']['name'];
        }


            $messageInfo['img_list'] = json_encode($oldImgArr);

	    $result = $this->lib_message->updateMessage($conditions,$messageInfo);
	    echo json_encode($result);
	}
    /**
     * 消息详情页面
     */
	public function messageDetail(){
        $labelData = $this->lib_label->findAllLabel();

        if (input('id')!= null || input('id')!= ''){
            $conditions['id'] = input('mid');
        }

        if (input('mid')!= null || input('mid')!= ''){
            $conditions['mess_num'] = input('mid');
        }
        \ChromePhp::INFO($conditions);
	    $messageData =  $this->lib_message->findMessage($conditions);
	    if($messageData['data']['img_list']){
            $messageData['data']['img_list'] = json_decode($messageData['data']['img_list'],true);
        }
        $this->assign('message',$messageData['data']);
        $this->assign('message_imgs',$messageData['data']['img_list']);
        $this->assign('labels',$labelData['data']);

        $this->assign(config('config.view_replace_str'));
        return $this->fetch('messageDetail');
    }
	/**
	 * 订单详情页面
	 */
	function orderDetailList(){
	    $this->getMenu($this);
	    //获取订单信息
        $orderResult = $this->lib_order->getOrderInfo(array('id' => $this->spArgs('oid')));
		if(!class_exists(UtilConfig)) include_once "include/UtilConfig.php";
		$lib_config = new UtilConfig('store_config');
		$config = $lib_config->findConfigKeyValue();
		$this->config = $config['data'];
        $this->log(__CLASS__, __FUNCTION__, "订单详情页面", 1, 'view');
        $this->orderInfo = $orderResult['data'];
		$parent_id = $orderResult['data']['parent_id'];
		if($parent_id == 0){// 原始订单
			$renweOrders = $this->lib_order->getAllOrders(array('parent_id'=>$this->spArgs('oid')));
			$this->parentOrder = yes;
			$this->renweOrders = $renweOrders['data'];
			if(count($renweOrders['data']['orderList']) == 0){
				$this->chridenOeder = false;
			} else {
				$this->chridenOeder = true;
			}
			ChromePhp::INFO($renweOrders);
		} else {// 续费订单
			$parsentOrder = $this->lib_order->getOrderInfo(array('id'=>$parent_id));
			$this->parentOrder = false;
			$this->parsentOrder = $parsentOrder['data'];

		}
//		ChromePhp::INFO($orderResult['data'],"orderInfo");
		$this->display("../template/admin/{$this->theme}/store/page/order/orderDetailList.html");
	}
	/**
	 * 设置消息状态
	 * @param string $state 0未审核 1已审核（已发布） 2审核失败 3已下架
     */
	function setMessageState(){
		$state = input('state');
	    $id = input('oid');
        $conditions = array(
            'id' => $id

        );
        $messageInfo = array(
            'state'=>$state

        );
		$result = $this->lib_message->updateMessage($conditions,$messageInfo);
		echo json_encode($result);
	}
	
	/**
	 * 返还用户积分、返还余额、释放优惠券
	 * @param int $state 订单状态
	 * @param int $oid   订单id
	 */
	private function backUserMoney($state,$oid){
		if(!class_exists('lib_user')) include 'model/base/lib_user.php';
	    $lib_user = new lib_user();
		$orderInfo = $this->lib_order->getOrderInfo(array('id' => $oid));
		$orderInfo = $orderInfo['data'];
		$account = $orderInfo['account'];
		//释放优惠券
		if($orderInfo['user_coupon_id']){
//			if(!class_exists('lib_coupon')) include 'model/market/coupon/lib_coupon.php';
//			$lib_coupon = new lib_coupon();
//			$lib_coupon->updateUserCoupon(array('id'=>$orderInfo['user_coupon_id']), array('is_use'=>0));
			if(!class_exists('m_market_user_coupon')) require 'model/market/coupon/table/m_market_user_coupon.php';
			$m_user_coupon = new m_market_user_coupon();
			$m_user_coupon->decrField(array('id'=>$orderInfo['user_coupon_id']), 'coupon_used',1);
			$m_user_coupon->incrField(array('id'=>$orderInfo['user_coupon_id']), 'inventory',1);
		}
		//退还积分
		if($orderInfo['consume_points'] > 0){
		    //查找用户的总积分
		    $userResult = $lib_user->increaseField(array('account'=>$account), 'points',$orderInfo['consume_points']);
		}
		//退换余额 pb
		if($orderInfo['balance_money'] > 0){
			//查找用户的总余额
			$userResult = $lib_user->increaseField(array('account'=>$account), 'balance',$orderInfo['balance_money']);
		}	
	}
	
	
	/**
	 * 分页查询订单
	 */
	function pagingMessage(){
	    $page = $this->getPageInfo($this);
		$sort = "add_time desc";
        $conditionList = [];
        if(input('mess_num') != '' || input('mess_num') != null){
            array_push($conditionList,  array("field" => 'mess_num',"operator" => 'like',"value" => input('mess_num')));
        }
        if(input('nick_name') !== '' || input('nick_name') != null){
            array_push($conditionList,  array("field" => 'nick_name',"operator" => 'like',"value" => input('nick_name')));
        }
        if(input('phone') !== '' || input('phone') != null){
            array_push($conditionList,  array("field" => 'phone',"operator" => '=',"value" => input('phone')));
        }
        if(input('label_name') !== '' || input('label_name') != null){
            array_push($conditionList,  array("field" => 'label_name',"operator" => 'like',"value" => input('label_name')));
        }
        if(input('state') !== '' || input('state') != null){
            array_push($conditionList,  array("field" => 'state',"operator" => '=',"value" => input('state')));
        }

        if(input('address_text') !== '' || input('address_text') != null){
            array_push($conditionList,  array("field" => 'address_text',"operator" => 'like',"value" => input('address_text')));
        }

        if(input('from') !== '' || input('from') != null){
            array_push($conditionList,  array("field" => 'add_time',"operator" => '>=',"value" => input('from')));
        }
        if(input('to') !== '' || input('to') != null){
            array_push($conditionList,  array("field" => 'end_time',"operator" => '<=',"value" => input('end_time')));
        }

		$result = $this->lib_message->pagingMessage($page,$conditionList,$sort);
		\ChromePhp::INFO($result);
		echo json_encode($result);
	}
	
	/**
	 * 判断订单是否可以删除
	 */
	function isOrderCanDel(){
		$result = $this->lib_order->isOrderCanDel ( $this->spArgs('oid') );
		echo json_encode($result);
	}
	
	/**
	 * 删除消息 真删
	 */
	function deleteMessage(){
	    $id = input('id');
        $conditions = array(

            'id'=> $id
        );
		$result = $this->lib_message->deleteMessage($conditions);
		//日志记录

		echo json_encode($result);
	}
	
	/**
	 * 批量删除订单
	 */
	function batchDeleteOrder(){
		$oids = $this->spArgs('ids');
		$result = $this->lib_order->batchDeleteOrder($oids);
		echo json_encode($result);
	}
	
	/**
	 * 导出订单
	 */
	function importExcel(){	
		$sort = "add_time desc";
		$conditions = $this->getArgsList($this,array(state),false);
		$keywords = $this->getArgsList($this,array(address_text,order_num),false);
		$createTime = $this->getArgsList($this,array('from','to'),false);
		
		$result = $this->lib_order->getAllOrders ($conditions, $sort, $keywords,$createTime);
		$this->log(__CLASS__, __FUNCTION__, "导出订单", 0, 'view');
		$this->lib_order->importExcel($result['data']['orderList']);
	}
	
	/**
	 * 导出订单商品
	 */
	function exportOrderDetail(){
		$oid = $this->spArgs('oid');
		$orderResult = $this->lib_order->getOrderInfo(array('id'=>$oid));
		$fieldList = array(
					array('key'=>'user_name','name'=>'用户名','width'=>25),
					array('key'=>'total_price','name'=>'价格','width'=>20),
					array('key'=>'content','name'=>'供需内容','width'=>80),
					array('key'=>'phone','name'=>'联系方式','width'=>20),
					array('key'=>'address_text','name'=>'联系地址','width'=>50),
					array('key'=>'contact_time','name'=>'可联系时间','width'=>20),
					array('key'=>'over_time','name'=>'过期时间','width'=>20)
				);
		include 'include/UtilExcel.php';
		$utilExcel = new UtilExcel();
		$utilExcel->exportExcel($orderResult['data'], $fieldList);
	}
}