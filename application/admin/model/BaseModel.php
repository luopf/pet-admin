<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/19
 * Time: 9:18
 */

namespace app\admin\model;

use think\Db;
use think\Model;
use think\facade\Session;
use app\admin\model\LogModel;

class BaseModel extends Model
{



    public function escape($value)
    {
        return strip_tags($value);
    }
    /**
     * 添加错误日志
     * @param string $class 类名
     * @param string $function 方法名
     * @param string $info 详细信息
     * @return boolean
     */
    protected function errorLog($class,$function,$info){
        $row['class'] = $class;
        $row['function'] = $function;
        $row['info'] = $info;
        $row['account'] = Session::get('admin')['account'];
        $row['name'] = Session::get('admin')['name'];
        $row['ip'] = \common::getRealIp();
        $row['province'] = Session::get('province');
        $row['city'] = Session::get('city');
        $row['add_time'] = date("Y-m-d H:i:s",time());
        if(!is_array($row))return FALSE;
        if(empty($row))return FALSE;
        foreach($row as $key => $value){
            $cols[] = $key;
            $vals[] = $this->escape($value);
        }
        $col = join(',', $cols);
        $val = join(',', $vals);
        //自动清除第999条错误日志之后的记录

        $lib_log = new LogModel();

        $lib_log->deleteSomeErrors();
        //插入记录
        $sql = "INSERT INTO base_error ({$col}) VALUES ({$val})";
        if( FALSE != $this->_db->exec($sql) ){ // 获取当前新增的ID
            if( $newinserid = $this->_db->newinsertid() ){
                return $newinserid;
            }else{
                $this->array_remove_value($row, "");//删除传入的空值元素
                return array_pop( $this->find($row, "{$this->pk} DESC",$this->pk) );
            }
        }
        return FALSE;
    }

}