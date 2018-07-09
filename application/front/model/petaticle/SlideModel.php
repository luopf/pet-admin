<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/18
 * Time: 16:34
 */

namespace app\front\model\petaticle;
use  think\Model;

use think\Db;



class SlideModel extends Model
{

    protected $pk = 'id';
    protected $table = 'aticle_slide';

    /**
     * 获取幻灯片列表
     * @param array $conditions
     * @param array $sort
     * @return array $result
     */
    public function findAllSlide($conditions=null,$sort=null){
        $m_slide = Db::name('aticle_slide');
        try{
            $result = $m_slide->where($conditions)->select();
            if(true == $result ){
                return \common::errorArray(0, "查找成功", $result);
            }else{
                return \common::errorArray(1, "查找失败", $result);
            }
        }catch (Exception $ex){

            return \common::errorArray(1, "数据库操作失败", $ex);
        }
    }


    /**
     * 获取单个幻灯片信息
     * @param array $conditions
     * @return array $result
     */
    public function findSlide($conditions){
        $m_slide = Db::name('aticle_slide');
        try{
            $result = $m_slide->where($conditions)->find();
            if(true == $result){
                return \common::errorArray(0, "查找成功", $result);
            }else{
                return \common::errorArray(1, "查找失败", $result);
            }
        }catch (Exception $ex){

            return \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
     * 幻灯片添加
     * @param array $slideInfo
     * @return array $result
     */
    public function addSlide($slideInfo){
        $m_slide = Db::name('aticle_slide');
        try{
            $result = $m_slide->insert( $slideInfo );
            if($result){
                return  \common::errorArray(0, "添加成功", $result);
            }else{
                return  \common::errorArray(1, "添加失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }


    /**
     * 删除幻灯片 真删
     * @param array $conditions
     * @return array $result
     */
    public function deleteSlide($conditions){
        $m_slide = Db::name('aticle_slide');
        try{
            $result = $m_slide->where($conditions)->delete();
            if(true == $result){
                return \common::errorArray(0, "删除成功", $result);
            }else{
                return \common::errorArray(1, "删除失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }


    /**
     * 幻灯片信息修改
     * @param array $conditions
     * @param array $slideInfo
     * @return array $result
     */
    public function updateSlide($conditions,$slideInfo){
        $m_slide = Db::name('aticle_slide');
        try{
            $result = $m_slide->where($conditions)->update ($slideInfo );
            if(true == $result){
                return \common::errorArray(0, "修改成功", $result);
            }else{
                return \common::errorArray(1, "修改失败", $result);
            }
        }catch (Exception $ex){

            return  common::errorArray(1, "数据库操作失败",$ex);
        }
    }


}