<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/6/20
 * Time: 17:58
 */

namespace app\front\model;
use  think\Model;

use think\Db;

class FavoriteModel extends  Model
{
    protected $pk = 'id';
    protected $table = 'message_favorite';
    /**
     *   添加收藏信息
     */
    public function addFavorite($favoriteInfo){
        $m_favorite = Db::name('message_favorite');

        try{
            $result = $m_favorite->insert($favoriteInfo);
            if(true == $result ){
                return \common::errorArray(0, "添加成功", $result);
            }else{
                return \common::errorArray(1, "添加失败", $result);
            }
        }catch (Exception $ex){

            return \common::errorArray(1, "数据库操作失败", $ex);
        }
    }
    /**
    删除点赞记录
     */
    public function deleteFavorite($favoriteInfo){
        $m_favorite = Db::name('message_favorite');

        try{
            $result = $m_favorite->where($favoriteInfo)->delete();
            if(true == $result ){
                return \common::errorArray(0, "删除成功", $result);
            }else{
                return \common::errorArray(1, "删除失败", $result);
            }
        }catch (Exception $ex){

            return \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
    查找点赞记录
     */
    public function findFavorite($favoriteInfo){
        $m_favorite = Db::name('message_favorite');

        try{
            $result = $m_favorite->where($favoriteInfo)->select();
            if(true == $result ){
                return \common::errorArray(0, "查找成功", $result);
            }else{
                return \common::errorArray(1, "查找失败", $result);
            }
        }catch (Exception $ex){

            return \common::errorArray(1, "数据库操作失败", $ex);
        }

    }




}