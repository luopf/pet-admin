<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/23
 * Time: 15:54
 */

namespace app\front\model;

use  think\Model;

use think\Db;
class MessageModel extends Model
{
    protected $pk = 'id';
    protected $table = 'store_message';


    /**
    * 增加某个字段的值
     */
    public function increaseField($conditions,$field,$value){

        $m_message = Db::name('store_message');

        try{
            $result = $m_message->where($conditions)->setInc($field,$value);
            if(true == $result){
                return \common::errorArray(0, "修改成功", $result);
            }else{
                return \common::errorArray(1, "修改失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }
    /**
     * 减少某个字段的值
     */
    public function decreaseField($conditions,$field,$value){

        $m_message = Db::name('store_message');

        try{
            $result = $m_message->where($conditions)->setDec($field,$value);
            if(true == $result){
                return \common::errorArray(0, "修改成功", $result);
            }else{
                return \common::errorArray(1, "修改失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }


    // 添加发布消息
    public function  addMessage($messageInfo){
        $m_message = Db::name('store_message');
        try{
            $result = $m_message->insert($messageInfo);

            if(true == $result){
                return \common::errorArray(0, "添加成功", $result);
            }else{
                return \common::errorArray(1, "添加失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }


    // 删除消息
    public function deleteMessage($conditions){
        $m_message = Db::name('store_message');
        try{
            $message = $this->findMessage($conditions);
            if($message['data']['img_list']){
                $imgs =  json_decode($message['data']['img_list'],true);
                foreach ($imgs as $img){
                    @unlink(__LOD__.$img['thumb']);
                }
            }

            $result = $m_message->where($conditions)->delete();

            if(true == $result){
                return \common::errorArray(0, "删除成功", $result);
            }else{
                return \common::errorArray(1, "删除失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败",$ex);
        }

    }

    public function updateMessage($conditions,$messageInfo){

        $m_message = Db::name('store_message');
        try{
            $result = $m_message->where($conditions)->update ($messageInfo );
            \ChromePhp::info($m_message->getLastSql());
            if(true == $result){
                return \common::errorArray(0, "修改成功", $result);
            }else{
                return \common::errorArray(1, "修改失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    //查找单个发布消息标签
    public function findMessage($conditions){
        $m_message = Db::name('store_message');
        try{
            $result = $m_message->where($conditions)->find();
            if(true == $result ){
                return \common::errorArray(0, "查找成功", $result);
            }else{
                return \common::errorArray(1, "查找失败", $result);
            }
        }catch (Exception $ex){
            return \common::errorArray(1, "数据库操作失败", $ex);
        }
    }



    public function escape($value)
    {
        return strip_tags($value);
    }





    /**
     * 分页查找消息
     * @param array $page
     * @param array $conditionList
     * @param string $sort
     * @param array $orList
     */
    function pagingMessage($page,$conditionList,$sort = '',$orList = null){
        $m_message = Db::name('store_message');
        $page['pageIndex'] ? $pageIndex = $page['pageIndex'] : $pageIndex = 1;
        $page['pageSize'] ? $pageSize = $page['pageSize'] : $pageSize = 10;
        //与连接条件
        $orString = '';
        if(null != $orList && '' != $orList && count($orList) > 0){
            foreach ($orList as $orConditionList){
                if(empty($orConditionList)){//如果为空跳出循环
                    $orString = "";
                    break;
                }
                $per = "(";
                foreach ($orConditionList as $orCondition){
                    if('like' == $orCondition['operator']){
                        $per .= " {$orCondition['field']} like  '%{$orCondition['value']}%' OR";
                    }else if('in' == $orCondition['operator']){
                        $per .= " {$orCondition['field']} in  ({$orCondition['value']}) OR";
                    }else{
                        $per .= " {$orCondition['field']} {$orCondition['operator']}  {$this->escape($orCondition['value'])} OR";
                    }
                }
                $per = rtrim($per,"OR") . ")";
                $orString .= "{$per} AND";
            }
            $orString = rtrim($orString,"AND");
        }
        //和连接条件
        $whereString = "";
        if(count($conditionList) > 0){
            foreach ($conditionList as $condition){
                if('like' == $condition['operator']){
                    $whereString .= " {$condition['field']} like '%{$condition['value']}%' AND";
                }else if('in' == $condition['operator']){
                    $whereString .= " {$condition['field']} in ({$condition['value']}) AND";
                }else{
                    $value = $this->escape($condition['value']);
                    if(is_string($value)){
                        $whereString .= " {$condition['field']} {$condition['operator']} '".$value."' AND";
                    } else {
                        $whereString .= " {$condition['field']} {$condition['operator']} {$value} AND";
                    }

                }
            }
            $whereString = rtrim($whereString,"AND");
            $sql = "SELECT * FROM store_message WHERE{$whereString} ";
            if($orString){
                $sql = "{$sql}AND {$orString} ";
            }
        }else{
            if($orString){
                $sql = "SELECT * FROM store_message WHERE {$orString}";
            }else{
                $sql = "SELECT * FROM store_message ";
            }
        }
        //排序
        if(null != $sort && '' != $sort){
            $sort = "ORDER BY {$sort}";
        }
        //分页
        $m = ($pageIndex -1) * $pageSize;
        $n =  $pageSize;
        $sqlLimit =  "{$sql}{$sort} LIMIT {$m}, {$n}";
        $sqlTotal =  "{$sql}{$sort}";
        try {
            \ChromePhp::INFO($sqlLimit);
            $result['dataList'] =$m_message->query($sqlLimit);

            $result['sum'] = $m_message->query($sqlTotal);
            $sql = "SELECT count(*) as total_record_num  from ( {$sql} ) as count_table";
            $count = $m_message->query($sql);

            $result['pageInfo'] =[];
            //如果之后1页，手动添加分页信息
            if($result['pageInfo']==NULL){
                $result['pageInfo']['current_page'] = $pageIndex;
                $result['pageInfo']['first_page'] = 1;
                $result['pageInfo']['prev_page']=$pageIndex - 1;
                $result['pageInfo']['next_page']=$pageIndex + 1;
                $result['pageInfo']['last_page']=ceil ($count[0]['total_record_num'] / $pageSize);
                $result['pageInfo']['total_count']= $count[0]['total_record_num'];
                $result['pageInfo']['total_page'] = ceil ($count[0]['total_record_num'] / $pageSize);
                $result['pageInfo']['page_size'] = $pageSize;
                $result['pageInfo']['all_pages'] = ceil ($count[0]['total_record_num'] / $pageSize);
            }

            return \common::errorArray(0, "查询成功", $result);
        } catch (Exception $ex) {return \common::errorArray(1, "数据库操作失败", $ex);}
    }
}