<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/6/20
 * Time: 18:02
 */

namespace app\front\controller;

use think\Controller;

use app\front\model\FavoriteModel;
use app\front\model\MessageModel;

class Favorite extends  Controller
{

    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();


        $this->lib_favorite = new FavoriteModel();
        $this->lib_message = new MessageModel();
    }

    /**
     *        添加收藏记录
     **/
    public function addFavorite(){
        $mid = input('mid');
        $user_id = input('user_id');
        $favoriteInfo = array(
            'mid'=> $mid,
            'user_id'=> $user_id
        );
        $result = $this->lib_favorite->addFavorite($favoriteInfo);

        echo  json_encode($result);
    }
    /**
     *       取消点赞
     **/
    public function deleteFavorite(){
        $mid = input('mid');
        $user_id = input('user_id');
        $favoriteInfo = array(
            'mid'=> $mid,
            'user_id'=> $user_id
        );
        $result = $this->lib_favorite->deleteFavorite($favoriteInfo);

        echo  json_encode($result);
    }

    /**

     *查找点赞记录
     */
    public function findUserFavorite(){
        $user_id = input('user_id');
        $mid = input('mid');
        $user_id = 194;
        $favoriteInfo = array(
            'mid'=>$mid,
            'user_id'=> $user_id
        );
        $result = $this->lib_favorite->findFavorite($favoriteInfo);


        echo json_encode($result);
    }


    /**
     *       查找所有点赞记录
     **/
    public function findUserAllFavorite(){
//        $user_id = input('user_id');
        $user_id = 194;
        $favoriteInfo = array(

            'user_id'=> $user_id
        );
        $result = $this->lib_favorite->findFavorite($favoriteInfo);
        $arr = [];
        foreach ($result['data'] as $favorite){
            $mid = $favorite['mid'];
            $message = $this->lib_message->findMessage(array('id'=>$mid));
            if($message['data']['img_list']){
                $message['data']['img_list'] = json_decode($message['data']['img_list'],true);
            }
            array_push($arr,$message['data']);

         }


        echo json_encode($arr);
    }




}