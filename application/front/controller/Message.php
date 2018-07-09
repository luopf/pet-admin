<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/22
 * Time: 11:13
 */

namespace app\front\controller;

use think\Controller;

use app\front\model\MessageModel;

use app\front\model\CommentModel;

use app\front\model\UserModel;

use app\front\model\LabelModel;

use app\front\model\NewsModel;
class Message extends  Controller
{

    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();

        $this->lib_message = new MessageModel();
        $this->lib_comment = new CommentModel();
        $this->lib_user = new UserModel();
        $this->lib_label = new LabelModel();
        $this->lib_news = new NewsModel();
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
     * 生成订单号
     * @return string
     */
    private function generateOrderNum(){

        $date = date ( 'Ymd', time () );
        $serialNumber = sprintf("%s", rand(1,2));
        return "QJ" . $serialNumber.$date;
    }


    // 发布消息
    public function addMessage(){
        $labelInfo = $this->lib_label->findLabel(array('id'=>input('label_id')));
        $userInfo = $this->lib_user->findUser(array('id'=>input('user_id')));
        $messageInfo = $this->getArgsList($this,array('user_id','label_id','text_content','longitude','latitude','city','phone','wx_number','address_text','pet_name','pet_cate','pet_age','pet_sex'));
        $messageInfo['label_name'] = $labelInfo['data']['name'];
        $messageInfo['mess_num'] = $this->generateOrderNum();
        $messageInfo['nick_name'] = $userInfo['data']['nick_name'];
        $messageInfo['account'] = $userInfo['data']['account'];
        $messageInfo['add_time'] = \common::getTime();
        $messageInfo['time_desc'] = \common::getDescTime();
        $messageInfo['head_img_url'] = $userInfo['data']['head_img_url'];
        $img_list = array();
        $imgArr = json_decode(input('img_list'),true);

        foreach ($imgArr as $img){
            array_push($img_list,array('image'=>$img['img_url'],'thumb'=>strstr($img['img_url'],'.jpg',true)."_thumb.jpg"));
        }
//        var_dump(stripslashes(json_encode($img_list)));
//        die;
        $messageInfo['img_list'] = stripslashes(json_encode($img_list));
        $result = $this->lib_message->addMessage($messageInfo);
        if($result['errCode'] == 0){//发布成功 插入一条消息

            $newsInfo['user_id'] = input('user_id');
            $newsInfo['account'] = $userInfo['data']['account'];
            $newsInfo['type'] = 1;
            $newsInfo['add_time'] = \common::getTime();
            $newsInfo['time_desc'] = \common::getDescTime();
            $result = $this->lib_news->addNew($newsInfo);
        }
        echo json_encode($result);
    }

    public function addShareNum(){
        $mid = input('mid');
        $field = input('field');
        $result = $this->lib_message->increaseField(array('id'=>$mid),$field,1);
        echo json_encode($result);
    }


    public function messageDetail(){

        $mid = input('mid');
        $result = $this->lib_message->findMessage(array('id'=>$mid));
        if($result['data']['img_list']){
            $result['data']['img_list'] = json_decode($result['data']['img_list'],true);
        }
        \ChromePhp::info($result);
        echo json_encode($result);

    }
    /**
    *   增加浏览量
     */
    public function increasechecknum(){
        $id = input('id');
        $result = $this->lib_message->increaseField(array('id'=>$id),'check_num',1);
        echo json_encode($result);
    }

    /**
    *  怎加点赞数
     */
    public function increaselikenum(){
        $id = input('id');
        $result = $this->lib_message->increaseField(array('id'=>$id),'like_num',1);
        echo json_encode($result);
    }

    public function test(){
        $result = $this->lib_comment->getCountNum(array('mid'=>71));
        echo $result;
    }

    public function paggingMessage(){
        $page = array();
        $page['pageIndex'] = input('pageIndex');
        $page['pageSize'] = input('pageSize');
        $conditionList = array();
        if(input('city') != null && input('city') != ''){
            array_push($conditionList,array('field'=>'city','operator'=> '=','value'=>input('city')));
        }
        if(input('lid') != null && input('lid') != ''){
            array_push($conditionList,array('field'=>'label_id','operator'=> '=','value'=>input('lid')));
        }
        if(input('content') != null && input('content') != ''){
            array_push($conditionList,array('field'=>'text_content','operator'=> 'like','value'=>input('content')));
        }
        if(input('user_id') != null && input('user_id') != ''){
            array_push($conditionList,array('field'=>'user_id','operator'=> '=','value'=>input('user_id')));
        }
        if(input('status') != null && input('status') != ''){
            array_push($conditionList,array('field'=>'state','operator'=> '=','value'=>input('status')));
        }
        $sort = 'add_time desc';
        $order = input('order');
        if($order == 'default'){ // 默认排序
            $sort = 'add_time desc';
        } elseif ($order == 'check'){ //根据浏览量排序
            $sort = 'check_num desc';
        } elseif ($order =="message"){// 根据评论数排序
            $sort = 'message_num desc';
        }

        $result = $this->lib_message->pagingMessage($page,$conditionList,$sort);
        foreach ($result['data']['dataList'] as &$message){
            $longitude = $message['longitude'];
            $latitude = $message['latitude'];
            $distance = \common::getDistance($latitude,$longitude,input('latitude'),input('longitude'));
            $message['distance'] = $distance;
            if($message['img_list']){
                $message['img_list'] = json_decode($message['img_list'],true);
            }

        }

        if($order == "distance"){// 根据距离排序
            $arr = $result['data']['dataList'];

            for ($i = 0;$i < count($arr) - 1; $i++){
                for($j = 0;$j <count($arr)-1-$i;$j++){
                    if($arr[$j]>$arr[$j+1]){
                        $temp =$arr[$j];
                        $arr[$j] =$arr[$j+1] ;
                        $arr[$j+1] = $temp;
                    }
                }
            }

        }
        // 每次分页之前都修改一下发布消息的评论数，之后在输出
        foreach ($result['data']['dataList'] as &$message){
            $mid = $message['id'];// 消息id
            $message['message_num'] = $this->lib_comment->getCountNum(array('mid'=>$mid));
            $this->lib_message->updateMessage(array('id'=>$mid),array('message_num'=>$message['message_num']));
        }

        echo  json_encode($result);

    }



}