<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/25
 * Time: 17:50
 */

namespace app\admin\controller;

use app\admin\controller\BaseAdminController;

use think\facade\Session;

use app\admin\model\petAticle\SliderModel;

use app\admin\model\petAticle\LabelModel;

use app\admin\model\petAticle\AticleModel;
class Petaticle extends  BaseAdminController

{
    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();
        error_reporting ( E_ALL & ~ E_NOTICE & ~ E_DEPRECATED & ~ E_STRICT & ~ E_WARNING );
        $this->rightVerify(Session::get('admin'), __HOST__."/index.php/admin/login/login");
        $this->lib_slide = new SliderModel();
        $this->lib_label = new LabelModel();
        $this->lib_aticle = new AticleModel();
    }

    /**
    * 文章列表
     */
    public function aticleList(){
        $labels = $this->lib_label->findAllLabel();
        $this->assign('labels',$labels['data']);

        $this->assign(config('config.view_replace_str'));

        return $this->fetch('aticleList');
    }

    /**
    * 添加文章页
     */
    public function addAticle(){
        $labels = $this->lib_label->findAllLabel();
        $this->assign('labels',$labels['data']);

        $this->assign(config('config.view_replace_str'));

        return $this->fetch('addAticle');
    }
    /**
    *删除文章
     **/
    public function deleteAticle(){
        $id = input('id');
        $aticleInfo = $this->lib_aticle->findAticle(array(id=>$id));
        $img_url_path = $aticleInfo['data']['img_url'];
        $thumb_path = $aticleInfo['data']['thumb'];
        @unlink(__LOD__.$img_url_path);
        @unlink(__LOD__.$thumb_path);
        $conditions = array('id'=>$id);
        $result = $this->lib_aticle->deleteAticle($conditions);
        echo json_encode($result);
    }


    /**
    *    插入文章数据
     */
    public function insertAticle(){
        $aticleInfo = [];
        if(input('label_id') != null && input('label_id') != ''){
            $aticleInfo['label_id'] = input('label_id');
            $label = $this->lib_label->findLabel(array('id'=>input('label_id')));
            $aticleInfo['label_name'] = $label['data']['name'];
        }
        if(input('title') != null && input('title') != ''){
            $aticleInfo['title'] = input('title');
        }
        if(input('aticle_desc') != null && input('aticle_desc') != ''){
            $aticleInfo['aticle_desc'] = input('aticle_desc');
        }
        if(input('author') != null && input('author') != ''){
            $aticleInfo['author'] = input('author');
        }
        if(input('content') != null && input('content') != ''){
            $aticleInfo['content'] = input('content');
        }
        if(input('path') != null && input('path') != ''){
            $aticleInfo['path'] = input('path');
        }
        if($_FILES) $verify = \UtilImage::verifyImage();
        if($verify['errorCode'] == 1) exit(json_encode($verify));
        //上传图片
        $resultImg = \UtilImage::uploadPhoto('imgurl',"upload/image/petaticle/aticle/",86.4,48);
        if($resultImg){
            $aticleInfo['img_url'] = $resultImg['url'];
            $aticleInfo['thumb'] = $resultImg['thumb'];

        }else{
            echo json_encode(\common::errorArray(1, "上传标签图片失败", 0));
            return;
        }
        $aticleInfo['add_time'] = \common::getTime();
        $result = $this->lib_aticle->insertAticle($aticleInfo);
        echo json_encode($result);
    }

    /**
    * 分页展示文章列表
     */
    public function paggingAticle(){
        $page = $this->getPageInfo();
        $conditionList = [];
        $sort = "add_time desc";

        if(input('author') != null && input('author') != ''){
            array_push($conditionList,array("field" => 'author',"operator" => 'like',"value" => input('author')));
        }
        if(input('title') != null && input('title') != ''){
            array_push($conditionList,array("field" => 'title',"operator" => 'like',"value" => input('title')));
        }
        if(input('label_id') != null && input('label_id') != ''){
            array_push($conditionList,array("field" => 'label_id',"operator" => '=',"value" => input('label_id')));
        }
        if(input('from') !== '' && input('from') != null){
            array_push($conditionList,  array("field" => 'add_time',"operator" => '>=',"value" => input('from')));
        }
        if(input('to') !== '' && input('to') != null){
            array_push($conditionList,  array("field" => 'end_time',"operator" => '<=',"value" => input('end_time')));
        }

        $result = $this->lib_aticle->pagingAticle($page,$conditionList,$sort);
        \ChromePhp::info($result);
        echo  json_encode($result);
    }

    /**
    *   查看文章详情
     */
    public function aticleDetail(){
        $id = input('id');
        $result = $this->lib_aticle->findAticle(array('id'=>$id));
//        $result['data']['content'] = strip_tags($result['data']['content']);
        $this->assign('aticleInfo',$result['data']);
        $labels = $this->lib_label->findAllLabel();
        $this->assign('labels',$labels['data']);
        \ChromePhp::info($result['data']);
        $this->assign(config('config.view_replace_str'));
        return $this->fetch('aticleDetail');
    }

    /***
    * 修改文章
     */
    public function updateAticle(){
        $id = input('aid');
        $aticleInfo = $this->getArgsList($this,array('label_id','title','aticle_desc','author','content','path'),false);
        $label_id = input('label_id');
        $label = $this->lib_label->findLabel(array('id'=>$label_id));
        if($label['errorCode'] == 0){
            $aticleInfo['label_name'] = $label['data']['name'];
        }
        if($_FILES)
            $verify = \UtilImage::verifyImage();
        if($verify['errorCode'] == 1) exit(json_encode($verify));
        if(input('imgFlag_1') == 1){//判断是否需要重新上传图片
            //更新图片
            $prevUrl = input('prevurl');
            if($prevUrl){
                $resultImg = \UtilImage::uploadPhoto('imgurl','upload/image/petaticle/aticle/',100,100);
                if($resultImg){
                    $aticleInfo[0]['img_url'] = $resultImg['url'];
                    $aticleInfo[0]['thumb'] = $resultImg['thumb'];
                    $prevThumbUrl = substr($prevUrl, 0,strripos($prevUrl,'.')) . "_thumb" .  substr($prevUrl, strripos($prevUrl,'.'));
                    \ChromePhp::INFO("虚拟删除".$prevUrl);
                    \ChromePhp::INFO("虚拟删除".$prevThumbUrl);
//                    @unlink($prevUrl);//delete url
//                    @unlink($prevThumbUrl);//delete thumb
                }else{
                    echo json_encode(\common::errorArray(1, "修改图片失败", 0));
                    exit;
                }
            }else{
                echo json_encode(\common::errorArray(1, "原图路径未传，不能删除", 0));
                exit;
            }
        }
        $result = $this->lib_aticle->updateAticle(array('id'=>$id),$aticleInfo);
        echo json_encode($result);
    }




    /**
     * 标签列表页面
     */
    function labelList(){

        $labelList = $this->lib_label->findAllLabel();
        $this->assign('labelList',$labelList['data']);
        $this->assign(config('config.view_replace_str'));
        return $this->fetch('labelList');

    }
    /**
     * 绑定商品页面
     **/
    function showBindGoods(){
        //一级分类
        $this->getMenu($this);
        $conditions = array('rank' => 1);
        if(!class_exists('lib_category')) include'model/store/lib_category.php';
        $lib_category = new lib_category();
        $firCateResult = $lib_category->getCategorys($conditions, "order_index asc");
        $this->categoryList = $firCateResult['data'];
        $this->log(__CLASS__, __FUNCTION__, "绑定商品页面", 1, 'view');
        $this->display("../template/admin/{$this->theme}/store/page/label/bindGoods.html");
    }

    /**
     * 添加标签页面
     */
    function addLabel(){

        $this->assign(config('config.view_replace_str'));
        return $this->fetch();
    }

    /**
     * 编辑标签页面
     */
    function editLabel(){
        $id = input("id");
        $conditions = array('id' => $id);
        $result = $this->lib_label->findLabel ($conditions);

        $this->assign('labelInfo',$result['data']);
        \ChromePhp::info($result['data']);
        $this->assign(config('config.view_replace_str'));
        return $this->fetch();
    }

    /**
     * 标签商品列表页面
     */
    function labelGoodsList(){
        $this->getMenu($this);
        $conditions = array( 'id' => $this->spArgs('id'));
        $result = $this->lib_label->findLabel ($conditions);
        $this->labelInfo = $result['data'];
        $this->log(__CLASS__, __FUNCTION__, "标签商品列表页面", 1, 'view');
        $this->display("../template/admin/{$this->theme}/store/page/label/labelGoodsList.html");
    }

    /**
     * 更新标签
     */
    function updateLabel(){
        //验证图片能否上传
        if($_FILES) $verify = \UtilImage::verifyImage();
        if($verify['errorCode'] == 1) exit(json_encode($verify));
        $conditions = array('id' => input('id'));
        $labelInfo = $this->getArgsList($this,array(name,order_index,gids,link,goods_num));
        if(input('imgFlag') == 1){//判断是否需要重新上传图片
            //更新图片
            $prevUrl = input('prevurl');
            if($prevUrl){
                $resultImg = \UtilImage::uploadPhoto('imgurl','upload/image/store/label/',100,100);

                if($resultImg){
                    $labelInfo['image'] = $resultImg['url'];
                    $labelInfo['thumb'] = $resultImg['thumb'];
                    $prevThumbUrl = substr($prevUrl, 0,strripos($prevUrl,'.')) . "_thumb" .  substr($prevUrl, strripos($prevUrl,'.'));
                    @unlink($prevUrl);//delete url
                    @unlink($prevThumbUrl);//delete thumb
                }else{
                    echo json_encode(\common::errorArray(1, "11修改标签图片失败", 0));
                    exit;
                }
            }else{
                echo json_encode(\common::errorArray(1, "原图路径未传，不能删除", 0));
                exit;
            }
        }
        if(input('imgFlag2') == 1){//判断广告图片是否需要重新上传图片
            //更新图片
            $prevUrl = input('prevurl2');
            $resultImg = \UtilImage::uploadPhotoJust('ad_image','upload/image/store/label/');
            if($resultImg){
                $labelInfo['ad_image'] = $resultImg;
                @unlink($prevUrl);//delete url
            }
        }
        $result = $this->lib_label->updateLabel($conditions,$labelInfo);

        echo json_encode($result);
    }

    public function test(){
        var_dump(config('config.view_replace_str'));
    }

    /**
     * 添加标签
     */
    function insertLabel(){
        //验证图片能否上传
        if($_FILES)
            $verify = \UtilImage::verifyImage();
        if($verify['errorCode'] == 1) exit(json_encode($verify));
        $labelInfo = $this->getArgsList($this, array(name,order_index,gids,link,goods_num));
        //广告图
        $url = \UtilImage::uploadPhotoJust('ad_image','upload/image/store/label/');
        if($url){
            $labelnfo['ad_image'] = $url;
        }
        //上传图片
        $resultImg =  \UtilImage::uploadPhoto('imgurl','upload/image/store/label/',100,100);
        if($resultImg){
            $labelInfo['image'] = $resultImg['url'];
            $labelInfo['thumb'] = $resultImg['thumb'];
            $result = $this->lib_label->addLabel ($labelInfo );

            echo json_encode($result);
        }else{
            echo json_encode(common::errorArray(1, "上传标签图片失败", 0));
        }
    }

    /**
     * 删除标签
     */
    function delLabel(){
        $conditions = array('id' => input('id'));
        $result = $this->lib_label->findLabel ($conditions);
        if($result['errCode'] == 0){
            @unlink($result['data']['image']);//delete url
            @unlink($result['data']['thumb']);//delete thumb
            @unlink($result['data']['ad_image']);//delete thumb
        }
        $result = $this->lib_label->deleteLabel ($conditions);
        echo json_encode($result);
    }





    /**
     * 幻灯片列表页面
     */
    function sliderlist(){

        $result = $this->lib_slide->findAllSlide ();

        $this->assign('slideList',$result['data']);
        $this->assign(config('config.view_replace_str'));
        return $this->fetch("slideList");
    }

    /**
     * 编辑幻灯片页面
     */
    function editSlide(){

        $result = $this->lib_slide->findSlide (array('id' => input('id')));
        $this->slideInfo = $result['data'];
        $this->assign('slideInfo',$result['data']);
        $this->assign(config('config.view_replace_str'));
        return $this->fetch("editSlide");
    }


    /**
     * 修改幻灯片
     */
    function updateSlide(){
        //验证图片能否上传
        if($_FILES) $verify = \UtilImage::verifyImage();
        if($verify['errorCode'] == 1) exit(json_encode($verify));
        $conditions = array('id' => input('id'));
        $slideInfo = $this->getArgsList($this, array(name,url,sort,type,target));
        if(input('imgFlag') == 1){//判断是否需要重新上传图片
            //更新图片
            $prevUrl = input('prevurl');
            if($prevUrl){
                $resultImg = \UtilImage::uploadPhoto('imgurl',"upload/image/petaticle/slide/",86.4,48);
                if($resultImg){
                    $slideInfo['img_url'] = $resultImg['url'];
                    $slideInfo['thumb'] = $resultImg['thumb'];
                    $prevThumbUrl = substr($prevUrl, 0,strripos($prevUrl,'.')) . "_thumb" .  substr($prevUrl, strripos($prevUrl,'.'));
                    unlink($prevUrl);//delete url
                    unlink($prevThumbUrl);//delete thumb

                }else{
                    echo json_encode(\common::errorArray(1, "修改图片失败", 0));
                }
            }else{
                echo json_encode(\common::errorArray(1, "原图url未传，不能删除", 0));
            }
        }
        $result = $this->lib_slide->updateSlide($conditions,$slideInfo);
        echo json_encode($result);
    }


    /**
     * 添加商城幻灯片页面
     */
    function addSlide(){

        $this->assign(config('config.view_replace_str'));
        return $this->fetch("addSlide");

    }

    /**
     *  删除幻灯片
     */
    public function deleteSlide(){
        $id = input('id');
        $config = config('config.view_replace_str');
        $__LOD__ = $config['__LOD__'];
        $slide = $this->lib_slide->findSlide(array('id'=>$id));
        if($slide['errorCode'] == 0){
            $img_url = $__LOD__.$slide['data']['img_url'];
            @unlink($img_url);
            $thumb = $__LOD__.$slide['data']['thumb'];
            @unlink($thumb);
        }
        $result = $this->lib_slide->deleteSlide(array('id'=>$id));
        echo json_encode($result);

    }


    /**
     * 添加幻灯片
     */
    function insertSlide(){
        //验证图片能否上传
        if($_FILES) $verify = \UtilImage::verifyImage();
        if($verify['errorCode'] == 1) exit(json_encode($verify));
        $slidenfo = $this->getArgsList($this, array('name','url','sort','type','target')) ;
        //上传图片
        $resultImg = \UtilImage::uploadPhoto('imgurl',"upload/image/petaticle/slide/",86.4,48);
        if($resultImg){
            $slidenfo['img_url'] = $resultImg['url'];
            $slidenfo['thumb'] = $resultImg['thumb'];
            $result = $this->lib_slide->addSlide ($slidenfo );

            echo json_encode($result);
        }else{
            echo json_encode(common::errorArray(1, "上传图片失败", 0));
        }
    }
}