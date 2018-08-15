<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/14
 * Time: 11:55
 */

namespace app\admin\model;


use think\Db;

class ConfigModel
{
    protected $pk = 'id';
    protected $table = 'admin_config';


    /**
     * 获取所有配置 不带标题
     * @param array $conditions
     * @return array $result
     */
    public function findAllConfigComplete($item_value = "all"){
        $adminConfigModel = Db::name('admin_config');
        try {
            if(is_array($item_value)){
                $strCondition=null;
                foreach ($item_value as $value) {
                    $strCondition.="'".$value."',";
                }
                $strCondition = rtrim($strCondition,',');
                $sql = "SELECT * FROM {$this->table} WHERE type != 'hidden' AND item_group in ({$strCondition})  ORDER BY sort ASC";
            }else{
                if($item_value == 'all'){
                    $sql = "SELECT * FROM {$this->table} WHERE type != 'hidden'  ORDER BY sort ASC";
                }else{
                    $sql = "SELECT * FROM {$this->table} WHERE type != 'hidden' AND item_group = '{$item_value}'  ORDER BY sort ASC";
                }
            }

            $result = $adminConfigModel->query($sql);

            if(true == $result){
                return \common::errorArray(0, "查询成功",$result);
            }else{
                return \common::errorArray(1, "查询为空", $result);
            }
        }catch(Exception $ex){

            return \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    public function updateConfig($configInfo){
        $adminConfigModel = Db::name('admin_config');
        $result = array();
        try{
            foreach($configInfo as  $key =>$perConfig){
                $condition = array("item_key" =>$key);
                $row = array("item_value" => $perConfig);
                $result = $adminConfigModel->where($condition)->update($row );
            }
            if(false !== $result){
                return \common::errorArray(0, "修改成功", $result);
            }else{
                return \common::errorArray(1, "修改失败", $result);
            }
        }catch (Exception $ex){
            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }


}