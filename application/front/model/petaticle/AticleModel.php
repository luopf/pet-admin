<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/29
 * Time: 11:17
 */

namespace app\front\model\petaticle;
use  think\Model;

use think\Db;

class AticleModel extends Model
{
    protected $pk = 'id';
    protected $table = 'pet_aticle';



    /**
    *   查找单个文章
     */
    public function findAticle($conditions){
        $m_aticle = Db::name('pet_aticle');
        try{
            $result = $m_aticle->where($conditions)->find();
            if($result){
                return  \common::errorArray(0, "查找成功", $result);
            }else{
                return  \common::errorArray(1, "查找失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }


    /**
    * 修改文章
     */
    public function updateAticle($conditions,$aticleInfo){
        $m_aticle = Db::name('pet_aticle');
        try{
            $result = $m_aticle->where($conditions)->update($aticleInfo);
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
    * 插入文章
     */
    public function insertAticle($aticleInfo){
        $m_aticle = Db::name('pet_aticle');
        try{
            $result = $m_aticle->insert( $aticleInfo );
            if($result){
                return  \common::errorArray(0, "添加成功", $result);
            }else{
                return  \common::errorArray(1, "添加失败", $result);
            }
        }catch (Exception $ex){

            return  \common::errorArray(1, "数据库操作失败", $ex);
        }
    }
    public function escape($value)
    {
        return strip_tags($value);
    }

    /**
     * 分页查找发布页文章
     * @param array $page
     * @param array $conditionList
     * @param string $sort
     * @param array $orList
     */
    function pagingAticle($page,$conditionList,$sort = '',$orList = null){
        $m_aticle = Db::name('pet_aticle');
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
            $sql = "SELECT * FROM pet_aticle WHERE{$whereString} ";
            if($orString){
                $sql = "{$sql}AND {$orString} ";
            }
        }else{
            if($orString){
                $sql = "SELECT * FROM pet_aticle WHERE {$orString}";
            }else{
                $sql = "SELECT * FROM pet_aticle ";
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

            $result['dataList'] =$m_aticle->query($sqlLimit);

            $result['sum'] = $m_aticle->query($sqlTotal);
            $sql = "SELECT count(*) as total_record_num  from ( {$sql} ) as count_table";
            $count = $m_aticle->query($sql);

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