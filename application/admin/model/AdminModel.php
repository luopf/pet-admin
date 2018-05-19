<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/17
 * Time: 15:57
 */

namespace app\admin\model;

use  think\Model;

use think\Db;
class AdminModel extends Model
{
    protected $pk = 'id';
    protected $table = 'base_admin';

    /**
     * 管理员登录
     * @param array $loginInfo
     * @return array $result
     */
    public function adminLogin($loginInfo){
        $m_admin = Db::name('base_admin');
        $loginInfo = array(
            "account" => $loginInfo['account'],
            "password" => md5($loginInfo['password']),
        );
        try{
            $result = $m_admin->where($loginInfo)->find();
            if(true == $result ){
                return \common::errorArray(0, "登录成功", $result);
            }else{
                return \common::errorArray(1, "密码错误", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败",$ex);
        }
    }

}