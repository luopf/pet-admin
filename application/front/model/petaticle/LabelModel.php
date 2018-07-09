<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/9
 * Time: 8:55
 */



namespace app\front\model\petaticle;
use  think\Model;

use think\Db;

class LabelModel extends  Model
{

    protected $pk = 'id';
    protected $table = 'aticle_label';




    // 添加label标签
    public function  addLabel($labelInfo){
        $m_label = Db::name('aticle_label');
        try{
            $result = $m_label->insert($labelInfo);
            \ChromePhp::info($m_label->getLastSql());
            if(true == $result){
                return \common::errorArray(0, "修改成功", $result);
            }else{
                return \common::errorArray(1, "修改失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    // 删除label标签
    public function deleteLabel($conditions){
        $m_label = Db::name('aticle_label');
        try{
            $result = $m_label->delete($conditions);
            \ChromePhp::info($m_label->getLastSql());
            if(true == $result){
                return \common::errorArray(0, "删除成功", $result);
            }else{
                return \common::errorArray(1, "删除失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败",$ex);
        }

    }

    public function updateLabel($conditions,$labelInfo){
        $m_label = Db::name('aticle_label');
        try{
            $result = $m_label->where($conditions)->update ($labelInfo );
            \ChromePhp::info($m_label->getLastSql());
            if(true == $result){
                return \common::errorArray(0, "修改成功", $result);
            }else{
                return \common::errorArray(1, "修改失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    //查找单个label 标签
    public function findLabel($conditions){
        $m_label = Db::name('aticle_label');
        try{
            $result = $m_label->where($conditions)->find();
            if(true == $result ){
                return \common::errorArray(0, "查找成功", $result);
            }else{
                return \common::errorArray(1, "查找失败", $result);
            }
        }catch (Exception $ex){
            return \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    // 查找所有 label 标签
    public  function findAllLabel($conditions = null,$sort = null){
        $m_label = Db::name('aticle_label');
        try{
            $result = $m_label->where($conditions)->order($sort)->select();
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