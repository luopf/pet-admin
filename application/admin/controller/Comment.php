<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/16
 * Time: 15:01
 */

namespace app\admin\controller;

use  think\Controller;
use think\Request;

use app\admin\controller\BaseAdminController;

use app\admin\model\store\CommentModel;
use app\admin\model\store\MessageModel;
use  app\admin\model\store\UserModel;

use think\facade\Session;
class Comment extends  BaseAdminController
{

    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();
        error_reporting ( E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT & ~ E_WARNING );
        $this->rightVerify(Session::get('admin'), __HOST__."/index.php/admin/login/login");
        $this->lib_comment = new CommentModel();
        $this->lib_message = new MessageModel();
        $this->lib_user = new UserModel();
    }

    public function commentList(){
        $this->assign(config('config.view_replace_str'));
        return $this->fetch('commentList');
    }

    /**
     * 分页查询评价
     */
    function pagingComment(){
        $page = $this->getPageInfo($this);
        $keyValueList = array('nick_name'=>'like','goods_name'=>'like','is_anonym'=>'=','is_reply'=>'=','has_image'=>'=','level'=>'=','from_add_time'=>'>=','to_add_time'=>'<=');
        $conditionList = $this->getPagingList($this, $keyValueList);
        $sort = "is_reply asc,add_time desc";
        $result =  $this->lib_comment->pagingComment($page, $conditionList, $sort);
        \ChromePhp::INFO($result);
        echo json_encode($result);
    }

    /**
    *   删除评论
     */
    function deleteComment(){
        $id = input('id');
        $result = $this->lib_comment->deleteComment(array('id'=>$id));
        echo json_encode($result);
    }
    /**
     * 评价详情页面
     */
    function commentDetail(){

        $id = input('cid');
        $result = $this->lib_comment->findComment(array('id' => $id));
        $this->assign('commentInfo',$result['data']);
        $mid = $result['data']['mid'];
        $message = $this->lib_message->findMessage(array('id'=>$mid));
        $this->assign('messageInfo',$message['data']);
        \ChromePhp::INFO($message['data'],11);
        if($result['data']['is_reply'] == 1){// 已回复
            $reply_account = $result['data']['reply_account'];
            $replay_user = $this->lib_user->findUser(array('id'=>$reply_account));
            $this->assign('replayuserInfo',$replay_user['data']);
        }

        $this->assign(config('config.view_replace_str'));
        return $this->fetch('commentDetail');
    }

    /**
     * 单个回复评价
     */
    function replyComment(){
        $cid = input('id');
        $reply =input('reply');
        $session_account = Session::get('admin')['account'];
        $result = $this->lib_comment->replyComment($cid, $reply,$session_account );
        echo json_encode($result);
    }


}