<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/19
 * Time: 16:00
 */
namespace app\admin\controller;
use app\admin\controller\BaseAdminController;

class Test extends BaseAdminController
{
    public function sendTempleMess(){
        //1.接收者（用户）的 openid
        $toUser = 'o-Bor5EWfDXATuiLjZZ1fTSku6j4';
        //2.所需下发的模板消息的id
        $template_id = "shmMT7h3Y7EAzKycOkMgmy2kmoz76AaasqkS8X3Ee4o";
        //3 .page
        $page = "pages/index/index";
        //4.form_id
        $form_id = input('form_id');
        //5.data
        $data = array(

                       'keyword1'=>array('value'=>'巧克力', 'color'=>'#CCCCCC'),
                       'keyword2'=>array('value'=>'39.8元', 'color'=>'#CCCCCC'),
                       'keyword3'=>array('value'=>'2014年9月16日', 'color'=>'#CCCCCC'),

         );
        self::sendTemplateMessage($data,$toUser,$template_id,$form_id);
    }

    /**
     * 向用户推送模板消息
     * @param $data = array(
     *                  'first'=>array('value'=>'您好，您已成功消费。', 'color'=>'#0A0A0A'),
     *                  'keyword1'=>array('value'=>'巧克力', 'color'=>'#CCCCCC'),
     *                  'keyword2'=>array('value'=>'39.8元', 'color'=>'#CCCCCC'),
     *                  'keyword3'=>array('value'=>'2014年9月16日', 'color'=>'#CCCCCC'),
     *                  'keyword4'=>array('value'=>'欢迎再次购买。', 'color'=>'#173177')
     * );
     * @param $touser 接收方的OpenId。
     * @param $templateId 模板Id。在公众平台线上模板库中选用模板获得ID
     * @param $url URL
     * @return array("errcode"=>0, "errmsg"=>"ok", "msgid"=>200228332} "errcode"是0则表示没有出错
     *
     * 注意：推送后用户到底是否成功接受，微信会向公众号推送一个消息。
     */
    public static function sendTemplateMessage($data, $touser, $templateId,$formId, $url = ''){
        $queryUrl =  'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.\AccessToken::getAccessToken();

        $queryAction = 'POST';
        $template = array();
        $template['touser'] = $touser;
        $template['form_id'] = $formId;
        $template['template_id'] = $templateId;
        $template['data'] = $data;
        $template = json_encode($template);
        return \Curl::callWebServer($queryUrl, $template, $queryAction);
    }

}