<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/16
 * Time: 15:52
 */

namespace app\front\model;
use  think\Model;

use think\Db;

class CommentModel extends Model
{
    protected $pk = 'id';
    protected $table = 'base_user';


    public function escape($value)
    {
        return strip_tags($value);
    }


    /**
    *   统计某数量
     */
    public function getCountNum($conditions){
        $m_comment = Db::name('store_comment');

        try{
            $result = $m_comment->where($conditions)->count();
            \ChromePhp::info($m_comment->getLastSql(),"==============");
           return $result;
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }
    /**
    *  获取所有的评论信息
     */
    function findAllComment($conditions,$sort){
        $m_comment = Db::name('store_comment');
        try{
            $result = $m_comment->where($conditions)->order($sort)->select();
            if(true == $result ){
                return \common::errorArray(0, "查找成功", $result);
            }else{
                return \common::errorArray(1, "查找为空", $result);
            }
        }catch (Exception $ex){

            return \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
     * 分页查找评论
     * @param array $page
     * @param array $conditionList
     * @param string $sort
     * @param array $orList
     */
    function pagingComment($page,$conditionList,$sort = '',$orList = null){
        $m_comment = Db::name('store_comment');
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
                    $whereString .= " {$condition['field']} {$condition['operator']} {$value} AND";
                }
            }
            $whereString = rtrim($whereString,"AND");
            $sql = "SELECT * FROM store_comment WHERE{$whereString} ";
            if($orString){
                $sql = "{$sql}AND {$orString} ";
            }
        }else{
            if($orString){
                $sql = "SELECT * FROM store_comment WHERE {$orString}";
            }else{
                $sql = "SELECT * FROM store_comment ";
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
            $result['dataList'] =$m_comment->query($sqlLimit);

            $result['sum'] = $m_comment->query($sqlTotal);
            $sql = "SELECT count(*) as total_record_num  from ( {$sql} ) as count_table";
            $count = $m_comment->query($sql);

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

    /**
     * 获取单个评价信息
     * @param array $conditions
     * @return array $result
     */
    public function findComment($conditions){
        $m_comment = Db::name('store_comment');
        try{
            $result = $m_comment->where($conditions)->find();
            if(true == $result ){
                return \common::errorArray(0, "查找成功", $result);
            }else{
                return \common::errorArray(1, "查找为空", $result);
            }
        }catch (Exception $ex){

            return \common::errorArray(1, "数据库操作失败", $ex);
        }
    }


    /**
     * 回复评价
     * @param int $cid 评价id
     * @param string $replyContent 回复内容
     * @param string $account 管理员账户
     * @return array $result
     */
    public function replyComment($cid,$replyContent,$account){
        $m_comment = Db::name('store_comment');
        try{
            $result = $m_comment->where( array('id'=>$cid)) ->update (
                array(
                    'is_reply'=>1,
                    'reply_content'=>$replyContent,
                    'reply_account'=>$account,
                    'reply_time'=> \common::getTime())//这里是最后回复时间
            );
            if(true == $result){
                return \common::errorArray(0, "回复成功", $result);
            }else{
                return \common::errorArray(1, "回复失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
    * 添加评论
     */
    public  function  addComment($commentInfo){
        $m_comment = Db::name('store_comment');
        try{
            $result = $m_comment->insert($commentInfo);
            if(true == $result){
                return \common::errorArray(0, "添加成功", $result);
            }else{
                return \common::errorArray(1, "添加失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }


}