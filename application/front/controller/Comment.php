<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/23
 * Time: 17:10
 */

namespace app\front\controller;

use think\Controller;

use app\front\model\CommentModel;
use app\front\model\UserModel;
use app\front\model\MessageModel;
use app\front\model\NewsModel;
class Comment extends Controller
{
    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();


        $this->lib_comment = new CommentModel();
        $this->lib_user = new UserModel();
        $this->lib_message = new MessageModel();
        $this->lib_new = new NewsModel();
    }


    public function findAllComment(){
        $page = array();
        $mid = input('mid');


        $sort = "is_reply asc,add_time desc";

        $result =  $this->lib_comment->findAllComment(array('mid'=>$mid),$sort);
        
        foreach ($result['data'] as &$comment){
            if($comment['is_reply']){//有回复的留言

                $reply_account = $comment['reply_account'];
                $user = $this->lib_user->findUser(array('id'=>$reply_account));
                $comment['replay_user'] =$user['data'];
            }
        }

        echo json_encode($result);
    }
    /**
     * 分页查询评价
     */
    function pagingComment(){
        $page = array();
        $page['pageIndex'] = input('pageIndex');
        $page['pageSize'] = input('pageSize');
        $conditionList = array();
        if(input('mid') != null || input('mid') != ''){
            array_push($conditionList,array('field'=>'mid','operator'=> '=','value'=>input('mid')));
        }
        $sort = "is_reply asc,add_time desc";
        
        $result =  $this->lib_comment->pagingComment($page, $conditionList, $sort);

        foreach ($result['data']['dataList'] as &$comment){
            if($comment['is_reply']){//有回复的留言

                $reply_account = $comment['reply_account'];
                $user = $this->lib_user->findUser(array('id'=>$reply_account));
                $comment['replay_user'] =$user['data'];
            }
        }

        echo json_encode($result);
    }
    /**
    * 添加评论
     */
    public function addComment(){
        $is_reply = input('is_reply');
        $cid = input('cid');
        $messageInfo = $this->lib_message->findMessage(array('id'=>input('mid')));
        if($is_reply){// 回复评论
            if($cid){
                $oldComment = $this->lib_comment->findComment(array('id'=>$cid));
                $commentInfo = [];
                $commentInfo['mid'] = input('mid') ?  input('mid') : 0;
                $commentInfo['mess_num'] = input('mess_num') ? input('mess_num') : 0;
                $commentInfo['content'] = $oldComment['data']['content'] ;
                $commentInfo['user_id'] = $oldComment['data']['user_id'] ;
                $commentInfo['nick_name'] = $oldComment['data']['nick_name'] ;
                $commentInfo['add_time'] = $oldComment['data']['add_time'] ;
                $commentInfo['is_reply'] = $is_reply;
                $commentInfo['reply_content'] = input('content') ? input('content') : '';
                $commentInfo['reply_time'] = \common::getTime();
                $commentInfo['reply_account'] = input('user_id') ? input('user_id') : '';

            } else {// 找不到需要回复的评论

            }


        } else{// 留言


            $commentInfo['mid'] = input('mid') ?  input('mid') : 0;
            $commentInfo['mess_num'] = input('mess_num') ? input('mess_num') : 0;
            $commentInfo['content'] = input('content') ? input('content') :  '';
            $commentInfo['user_id'] = input('user_id') ? input('user_id') :  0;
            $commentInfo['nick_name'] = input('nick_name') ? input('nick_name') : 0;
            $commentInfo['add_time'] = \common::getTime();
            $commentInfo['is_reply'] = $is_reply;
        }

        $result = $this->lib_comment->addComment($commentInfo);
        if($result['errorCode'] == 0){
            // 更新发布列表的数据
            $mid =$commentInfo['mid'];
            $this->lib_message->increaseField(array('id'=>$mid),'message_num',1);
            // 添加消息
            $newInfo['user_id'] = $messageInfo['data']['user_id'];
            $newInfo['account'] = $messageInfo['data']['account'];
            $newInfo['type'] = 3;
            $newInfo['add_time'] = \common::getTime();
            $newInfo['time_desc'] = \common::getDescTime();
            $result = $this->lib_new->addNew($newInfo);

        }

        echo json_encode($result);
    }


    /**
     * 构造页面提交数据
     * @param object $controller
     * @param array $keyList array('name','title','sort')
     * @param bool $nullAllowed true defalut 允许传入空值 该参数在update的时候会用到
     * @return array $argsList
     */
    protected function getArgsList($keyList,$nullAllowed = true){

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