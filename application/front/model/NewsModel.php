<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/6/14
 * Time: 12:06
 */

namespace app\front\model;

use  think\Model;

use think\Db;

class NewsModel extends Model
{
    protected $pk = 'id';
    protected $table = 'app_message';


    public function escape($value)
    {
        return strip_tags($value);
    }

    /**
    *     添加消息
     **/
    function addNew($condtions){
        $m_new = Db::name('app_message');

        try{
            $result = $m_new->insert($condtions);
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
     * 分页查找消息
     * @param array $page
     * @param array $conditionList
     * @param string $sort
     * @param array $orList
     */
    function pagingNews($page,$conditionList,$sort = '',$orList = null){
        $m_new = Db::name('app_message');
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
            $sql = "SELECT * FROM app_message WHERE{$whereString} ";
            if($orString){
                $sql = "{$sql}AND {$orString} ";
            }
        }else{
            if($orString){
                $sql = "SELECT * FROM app_message WHERE {$orString}";
            }else{
                $sql = "SELECT * FROM app_message ";
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
            var_dump($sqlLimit);
            $result['dataList'] =$m_new->query($sqlLimit);

            $result['sum'] = $m_new->query($sqlTotal);
            $sql = "SELECT count(*) as total_record_num  from ( {$sql} ) as count_table";
            $count = $m_new->query($sql);

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