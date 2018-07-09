<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/6/15
 * Time: 10:25
 */

namespace app\front\model;
use  think\Model;

use think\Db;

class DianzanModel extends Model
{
    protected $pk = 'id';
    protected $table = 'message_dianzan';

    /**
     *   添加点赞信息
     */
    public function addDianzan($dianzanInfo){
        $m_disease = Db::name('message_dianzan');

        try{
            $result = $m_disease->insert($dianzanInfo);
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
    public function deleteDianzan($dianzanInfo){
        $m_disease = Db::name('message_dianzan');

        try{
            $result = $m_disease->where($dianzanInfo)->delete();
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
    public function findDianzan($dianzanInfo){
        $m_disease = Db::name('message_dianzan');

        try{
            $result = $m_disease->where($dianzanInfo)->find();
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