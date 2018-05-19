<?php
namespace app\admin\controller;

use  think\Controller;
use think\Request;
use app\admin\model\store\OrderModel;

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
class Order extends BaseAdminController{
    private $lib_order;
	/**
	 * 构造函数
	 */
	function __construct() {
		parent::__construct ();
		error_reporting ( E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT & ~ E_WARNING );
        $this->rightVerify(Session::get('admin'), __HOST__."/index.php/admin/login/login");
	    $this->lib_order = new OrderModel();
	}
	
	/**
	 * 订单列表页面
	 */
	function orderList(){
        $this->assign(config('config.view_replace_str'));
        return $this->fetch('orderlist');
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
	    ChromePhp::INFO($contactTimeResult,"contactTimeResult");
	    $this->log(__CLASS__, __FUNCTION__, "管理员修改补全订单界面", 1, 'edit');
	    $this->display("../template/admin/{$this->theme}/store/page/order/orderModify.html");
	}
	
	/**
	 * 管理员修改订单 ,添加快递信息
	 */
	function modifyOrder(){
		$oid = $this->spArgs('oid');
		$valueList = $this->getArgsList($this, array(user_name,total_price,address_text,phone,content,user_type));
		if(!class_exists(UtilConfig)) include_once "include/UtilConfig.php";
		$lib_config = new UtilConfig('store_config');
	    $contactTimeResult = $lib_config->findConfig(array('id'=>$this->spArgs('contact_time')));
		if($contactTimeResult['data']['item_value'] == null || $contactTimeResult['data']['item_value'] == ''){
			
		} else{
			$valueList['contact_time'] = $contactTimeResult['data']['item_value'];
		}
		$orderResult = $this->lib_order->getOrderInfo(array('id' => $oid));
	    if($this->spArgs('total_price') && $this->spArgs('total_price') != $orderResult['data']['total_price']){
	        $valueList['price_times'] = $orderResult['data']['price_times'] + 1;
	        $valueList['total_price'] = $this->spArgs('total_price');
	    }
	    $result = $this->lib_order->updateOrder(array('id'=>$oid), $valueList);
	    $this->log(__CLASS__, __FUNCTION__, "管理员修改订单 ,添加快递信息", 0, 'edit');
	    echo json_encode($result);
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
			ChromePhp::INFO($parsentOrder);
		}
//		ChromePhp::INFO($orderResult['data'],"orderInfo");
		$this->display("../template/admin/{$this->theme}/store/page/order/orderDetailList.html");
	}
	/**
	 * 设置订单状态
	 * @param string $state 订单状态：0待付款 1待发货 2已发货 3已收货 4交易关闭 5交易成功
	 */
	function setOrderState(){
		$state = $this->spArgs('state');
	    $oid = $this->spArgs('oid');
		$lib_config = new UtilConfig('store_config');
		$config = $lib_config->findConfigKeyValue();
	    //微信模板通知
	    $orderInfo = $this->lib_order->getOrderInfo(array('id' => $oid));
	    $userCondition = array('account'=>$orderInfo['data']['account']);
	    if(!class_exists('TemplateMessage')) include 'include/wechatUtil/TemplateMessage.php';
		if('3' == $state){
			if($config['data']['order_template_notify'] == '1'){
				$data = array(
		                'first'=>array('value'=> "订单交易完成，感谢您的支持", 'color'=>'#32CD32'),
		                'keyword1'=>array('value' => $orderInfo['data']['total_price'],'color'=>'#173177'),//姓名
		                'keyword2'=>array('value' => $orderInfo['data']['content'], 'color'=>'#173177'),//电话
		                'keyword3'=>array('value' => $orderInfo['data']['order_num'], 'color'=>'#173177'),//受理时间
		                'remark'=>array('value' => "订单交易完成，感谢你的支持与信任！(汗青名片墙)", 'color'=>'#808080')//备注
		        );
				$templateId = "1gJzJapNUlS7fHXouVvtpVMf_yiXBg2LGMP9xTm4LGA";
				$res = TemplateMessage::sendTemplateMessage($data, $orderInfo['data']['account'], $templateId);
		    	//$res = TemplateMessage::RealSendTemplateMessage($oid, 6, 'store', $userCondition);
			}
//		    //统计总消费额
//			$orderInfo = $this->lib_order->getOrderInfo(array('id' => $oid));
//			if(!class_exists('lib_user')) include 'model/base/lib_user.php';
//			$lib_user = new lib_user();
//			$orderInfo = $this->lib_order->getOrderInfo(array('id' => $this->spArgs('oid')));
//			$userResult = $lib_user->findUser(array('account' => $orderInfo['data']['account']));
//			$userInfoData = array(
//			    'total_fee' =>$orderInfo['data']['total_price'] + $userResult['data']['total_fee']
//			);
//			//分配分销佣金
//			if($config['data']['is_fen'] == '1'){				
//				if(!class_exists('lib_deal_record')) include 'model/fen/lib_deal_record.php';
//				$lib_deal_record = new lib_deal_record();
//				$lib_deal_record->getDistributorRank('store', $orderInfo, $userResult);
//			}
			
			
		}
		    $result = $this->lib_order->setOrderState ($oid,$state);
		    if('2' == $state && $result['errorCode'] ==0){  //已发货模板消息
		        TemplateMessage::RealSendTemplateMessage($oid, 3, 'store', $userCondition);
		    }
		$this->log(__CLASS__, __FUNCTION__, "管理员列表页面", 0, 'edit');
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
	function pagingOrder(){
	    $page = $this->getPageInfo($this);
		$sort = "add_time desc";
		$conditions = $this->getArgsList($this,array(state,pay_method),false);
		$keywords = $this->getArgsList($this,array(address_text,order_num,nick_name,contact_time,put_into),false);
		$createTime = $this->getArgsList($this,array('from','to'),false);
		$order_type = $this->spArgs('order_type');
		$parent_id = null;
		if($order_type == null){ //全部
			
		}else if($order_type == 0 || $order_type == '0'){//原始订单
			$conditions['parent_id'] = 0;
		}else{// 续费订单
			$parent_id = $order_type;
		}
		if(!$conditions){
			$conditions['tag'] = 1;
		}
		$result = $this->lib_order->pagingOrder ($page, $conditions, $sort, $keywords,$createTime,$endTime =null,$parent_id);
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
	 * 删除订单 真删
	 */
	function deleteOrder(){
		$result = $this->lib_order->deleteOrder ( $this->spArgs('id') );
		//日志记录
		$this->log(__CLASS__, __FUNCTION__, "管理员列表页面", 0, 'del');
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