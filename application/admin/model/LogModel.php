<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/19
 * Time: 9:32
 */

namespace app\admin\model;
use  think\Model;

use think\Db;

use app\admin\model\BaseModel;

class LogModel extends BaseModel
{
    private $m_log;
    private $m_error;
    function __construct(){
        parent::__construct();
        $this->m_log = Db::name('base_log');
        $this->m_error = Db::name('base_error');
    }

    /**
     * 获取单个日志信息
     * @param array $condition
     * @return array $result
     */
    public function findLog($condition){
        try {
            $reslut = $this->m_log->where($condition)->find();
            if(true == $reslut){
                return \common::errorArray(0, "查询成功",$reslut);
            }else{
                return \common::errorArray(1, "查询为空", $reslut);
            }
        }catch (Exception $ex){
            $this->errorLog(__CLASS__, __FUNCTION__, $ex);
            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
     * 添加日志
     * @param array $row
     * @return array $result
     */
    public function addLog($row){
        try {
            $row['add_time'] = \common::getTime();
            $addId = $this->m_log ->insert($row);
            if($addId){
                return \common::errorArray(0, "添加成功",$addId);
            }else{
                return \common::errorArray(1, "添加失败", false);
            }
        }catch (Exception $ex){
            $this->errorLog(__CLASS__, __FUNCTION__, $ex);
            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
     * 删除日志 批量
     * @param string $ids
     * @return array $resultArray
     */
    public function deleteLog($ids){
        try{
            $sql = "DELETE FROM base_log WHERE id IN ({$ids})";
            $result = $this->m_log->execute ($sql);
            if(true == $result){
                return \common::errorArray(0, "删除成功", $result);
            }else{
                return \common::errorArray(1, "删除失败", $result);
            }
        }catch (Exception $ex){
            $this->errorLog(__CLASS__, __FUNCTION__, $ex);
            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
     * 自动删除1000条之后的操作日志
     * @param int $recordsNumber;
     * @return $resultArray
     */
    public function deleteSomeLogs($logsNumber = 999){
        try{
            $sql = "DELETE FROM base_log WHERE id < (SELECT MIN(l.id) FROM (SELECT id FROM base_log ORDER BY id DESC LIMIT {$logsNumber}) AS l)";
            $result = $this->m_log->execute($sql);
            if(true == $result){
                return \common::errorArray(0, "删除成功", $result);
            }else{
                return \common::errorArray(1, "删除失败", $result);
            }
        }catch (Exception $ex){
            $this->errorLog(__CLASS__, __FUNCTION__, $ex);
            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
     * 分页查看日志
     * @param array $page
     * @param array $conditionList
     * @param string $sort
     * @param array $orList
     * @return array $result
     */
    public function pagingLog($page,$conditionList,$sort = '',$orList = null){
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
            $sql = "SELECT * FROM base_log WHERE{$whereString} ";
            if($orString){
                $sql = "{$sql}AND {$orString} ";
            }
        }else{
            if($orString){
                $sql = "SELECT * FROM base_log WHERE {$orString}";
            }else{
                $sql = "SELECT * FROM base_log ";
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
            $result['dataList'] =$this->m_log->query($sqlLimit);

            $result['sum'] =$this->m_log->query($sqlTotal);
            $sql = "SELECT count(*) as total_record_num  from ( {$sql} ) as count_table";
            $count = $this->m_log->query($sql);

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
        } catch (Exception $ex)
        {
            return \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
     * 清楚多余的日志
     * @param int $days
     * @return array $result
     */
    public function clearLog($days){
        try{
            $date = date("Y-m-d",time() - ($days * 3600 * 24));
            $sql = "DELETE FROM base_log WHERE add_time <  '{$date}'";
            $result = $this->m_log->execute ($sql);
            if(true == $result){
                return \common::errorArray(0, "删除成功", $result);
            }else{
                return \common::errorArray(1, "删除失败", $result);
            }
        }catch (Exception $ex){
            $this->errorLog(__CLASS__, __FUNCTION__, $ex);
            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    //------------------------------------错误日志管理-------------------------

    /**
     * 获取单个错误信息
     * @param array $condition
     * @return array $result
     */
    public function findError($condition){
        try {
            $reslut = $this->m_error ->where($condition)-> find();
            if(true == $reslut){
                return \common::errorArray(0, "查询成功",$reslut);
            }else{
                return \common::errorArray(1, "查询为空", $reslut);
            }
        }catch (Exception $ex){
            $this->errorLog(__CLASS__, __FUNCTION__, $ex);
            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
     * 添加错误
     * @param array $row
     * @return array $result
     */
    public function addError($row){
        try {
            $row['add_time'] = \common::getTime();
            $addId = $this->m_error -> insert($row);
            if(true == $addId){
                return \common::errorArray(0, "添加成功",$addId);
            }else{
                return \common::errorArray(1, "添加失败", false);
            }
        }catch (Exception $ex){
            $this->errorLog(__CLASS__, __FUNCTION__, $ex);
            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
     * 删除错误 批量
     * @param string $ids
     * @return array $result
     */
    public function deleteError($ids){
        try{
            $sql = "DELETE FROM base_error WHERE id IN ({$ids})";
            $result = $this->m_error->execute ($sql);
            if(true == $result){
                return \common::errorArray(0, "删除成功", $result);
            }else{
                return \common::errorArray(1, "删除失败", $result);
            }
        }catch (Exception $ex){
            $this->errorLog(__CLASS__, __FUNCTION__, $ex);
            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
     * 分页查看错误
     * @param array $page
     * @param array $conditionList
     * @param string $sort
     * @param array $orList
     * @return array $result
     */
    public function pagingError($page,$conditionList,$sort = '',$orList = null){

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
            $sql = "SELECT * FROM base_error WHERE{$whereString} ";
            if($orString){
                $sql = "{$sql}AND {$orString} ";
            }
        }else{
            if($orString){
                $sql = "SELECT * FROM base_error WHERE {$orString}";
            }else{
                $sql = "SELECT * FROM base_error ";
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
            $result['dataList'] =$this->m_error->query($sqlLimit);

            $result['sum'] = $this->m_error->query($sqlTotal);
            $sql = "SELECT count(*) as total_record_num  from ( {$sql} ) as count_table";
            $count = $this->m_error->query($sql);

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
        } catch (Exception $ex)
        {
            return \common::errorArray(1, "数据库操作失败", $ex);
        }



    }

    /**
     * 清楚多余的错误日志
     * @param int $days
     * @return array $result
     */
    public function clearError($days){
        try{
            $date = date("Y-m-d",time() - ($days * 3600 * 24));
            $sql = "DELETE FROM base_error WHERE add_time <  '{$date}'";
            $result = $this->m_log->execute ($sql);
            if(true == $result){
                return \common::errorArray(0, "删除成功", $result);
            }else{
                return \common::errorArray(1, "删除失败", $result);
            }
        }catch (Exception $ex){
            $this->errorLog(__CLASS__, __FUNCTION__, $ex);
            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }

    /**
     * 自动删除1000条之后的错误日志
     * @param int $recordsNumber;
     * @return $resultArray
     */
    public function deleteSomeErrors($errorsNumber = 999){
        try{
            $sql = "DELETE FROM base_error WHERE id < (SELECT MIN(e.id) FROM (SELECT id FROM base_error ORDER BY id DESC LIMIT {$errorsNumber}) AS e)";
            $result = $this->m_error->execute ($sql);
            if(true == $result){
                return \common::errorArray(0, "删除成功", $result);
            }else{
                return \common::errorArray(1, "删除失败", $result);
            }
        }catch (Exception $ex){
            $this->errorLog(__CLASS__, __FUNCTION__, $ex);
            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }



}