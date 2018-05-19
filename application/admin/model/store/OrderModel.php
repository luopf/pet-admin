<?php
/**
 * Created by PhpStorm.
 * User: luopengfei
 * Date: 2018/5/9
 * Time: 8:55
 */



namespace app\admin\model\store;
use  think\Model;

use think\Db;

class OrderModel extends  Model
{

    protected $pk = 'id';
    protected $table = 'store_order';




    // 添加label标签
    public function  addLabel($labelInfo){
        $m_label = Db::name('store_label');
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
        $m_label = Db::name('store_label');
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
        $m_label = Db::name('store_label');
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
        $m_label = Db::name('store_label');
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
    public  function findAllLabel($conditions,$sort){
        $m_label = Db::name('store_label');
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



    /**
     * 分页查询订单
     * @param array$page 分页信息
     * @param array $conditions 条件查询条件
     * @param string $sort 排序字段及方式
     * @param string $keywords 模糊查询
     * @param datetime $createTime 创建时间
     * @param datetime $endTime 完成时间
     * @return multitype:boolean |multitype:number string Ambigous <boolean, multitype:boolean >
     */
    public function pagingOrder($page, $conditions, $sort = null, $keywords = null,$createTime = null,$endTime = null){
        $m_order = Db::name('store_order');
        $pageIndex=$page['pageIndex'];
        $pageSize=$page['pageSize'];
        if(empty($sort)){
            $sortstr = "";
        }
        else if(is_array($sort)){
            if(count($sort) == 0){
                $sortstr = "";
            }
            else{
                $sortstr = "ORDER BY";
                foreach ($sort as $key => $sortitem){
                    $sortarray[] = " {$sortitem['field']} {$sortitem['orderby']}";
                }
                $sortstr = $sortstr.join(',', $sortarray);
            }
        }
        else if(is_string($sort)){
            $sortstr = "ORDER BY ".$sort;
        }
        if(empty($conditions)&&(empty($keywords)&&(empty($createTime))&&(empty($endTime)))){
            $sql = "SELECT * FROM store_order {$sortstr}";
        }
        else {
            //循环conditions数组，生成执行查询操作的sql语句
            $where = "";
            if (is_array ( $conditions ) || is_array($keywords)|| is_array($createTime)|| is_array($endTime)) {
                $join = array ();
                if (!empty($conditions)) {
                    foreach ( $conditions as $key => $condition ) {
                        //检测具体条件是否为数组，如果是则拆分条件并用OR连接，两边加上括号
                        if(is_array($condition)){
                            $join2=array();
                            foreach ($condition as $key2 => $value){
                                $value=strip_tags($value);
                                $join2[]="{$key} = {$value}";
                            }
                            $join[] = '('.join( " OR ", $join2 ).')';
                        }
                        else{
                            //如果具体条件不是数组，则过滤字符串之后直接赋值
                            $condition = $this->strip_tags( $condition );
                            $join [] = "{$key} = {$condition}";
                        }
                    }
                }

                //模糊查询条件
                if(!empty($keywords)){
                    foreach ($keywords as $key3 => $keyword){
                        $join [] = $key3." LIKE CONCAT('%','$keyword','%')";
                    }
                }

                //时间段条件
                if(!empty($createTime)){
                    if ($createTime['from']!='') {
                        $join [] = "date_format(add_time,'%Y-%m-%d')>='".$createTime['from']."'";
                    }
                    if ($createTime['to']!='') {
                        $join [] = "date_format(add_time,'%Y-%m-%d')<='".$createTime['to']."'";
                    }
                }
                //时间段条件
                if(!empty($endTime)){
                    if ($endTime['from']!='') {
                        $join [] = "date_format(end_time,'%Y-%m-%d')>='".$endTime['from']."'";
                    }
                    if ($endTime['to']!='') {
                        $join [] = "date_format(end_time,'%Y-%m-%d')<='".$endTime['to']."'";
                    }
                }
                //将所有的条件用AND连接起来
                $where = "WHERE " . join ( " AND ", $join );
            } else {
                if (null != $conditions)
                    $where = "WHERE " . $conditions;
            }

            //根据$sort的值 选择要排序的字段
            $sql = "SELECT * FROM store_order {$where} {$sortstr}";

        }
        //查询数据库
        try {
            $result ['orderList'] = $m_order->spPager ( $pageIndex, $pageSize )->findSql($sql);
            $result['pageInfo']=$m_order->spPager()->getPager();
            $mfb_getOrderGoods =  new  m_store_order_goods();
            $count = 0;
            foreach ($result ['orderList'] as &$orderInfo){
                //decode地址信息
                $orderInfo['address'] = json_decode($orderInfo['address']);
                //decode商品信息
                $orderInfo['goods_list'] = json_decode($result['orderList'][$count]['goods_list']);
                foreach ($orderInfo['goods_list'] as &$goods){
                    $goods->property = json_decode($goods->property);
                    //返回给页面ogid
                    $orderGoodsConditions = array(
                        "oid" => $orderInfo['id'],
                        "gid" => $goods->gid,
                        "gpid" => $goods->gpid
                    );
                    $resultTemp = $mfb_getOrderGoods->find($orderGoodsConditions);
                    $goods->ogid = $resultTemp['id'];
                }
                $orderInfo['goods_count'] = count($orderInfo['goods_list']);
                $count = $count + 1;
            }
        } catch (Exception $ex) {
            $result ["errorCode"] = 2;
            $result ["errorInfo"] = '数据库操作失败';
            $result ["result"] = array (
                "isSuccess" => FALSE
            );
            return $result;
        }
        //如果之后1页，手动添加分页信息
        if($result['pageInfo']==NULL){
            $result['pageInfo']['current_page']=1;
            $result['pageInfo']['first_page']=1;
            $result['pageInfo']['prev_page']=1;
            $result['pageInfo']['next_page']=1;
            $result['pageInfo']['last_page']=1;
            $result['pageInfo']['total_count']=count($result['orderList']);
            $result['pageInfo']['total_page']=1;
            $result['pageInfo']['page_size']=$pageSize;
            $result['pageInfo']['all_pages']=array(1);
        }
        if($result === FALSE) { // 如果数据库查无数据
            $errorCode = 1;
            $errorInfo = '获取分页数据失败';
            $result['isSuccess'] = FALSE;
        } else {
            $errorCode = 0;
            $errorInfo = '获取分页数据成功';
            $result['isSuccess'] = TRUE;
        }
        if(errorCode == 1){
            $this->errorLog(__CLASS__, __FUNCTION__, $result);
        }
        return common::errorArray(
            $errorCode, $errorInfo, $result
        );
    }

}