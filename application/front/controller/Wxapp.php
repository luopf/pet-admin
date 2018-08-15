<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/24
 * Time: 11:55
 */
namespace app\front\controller;

use think\Controller;
use app\front\model\Curl;

use app\front\model\UserModel;

use app\front\model\WXUserUtil;
class Wxapp extends Controller
{

    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();



        $this->lib_user = new UserModel();
    }

    function getTocken(){
        //1.先读取本地的文件看有没有保存小程序的tooken
        $filename = __HOST__."/static/token.txt";
        $token = file_get_contents($filename);

            if(strlen($token) == 0){
                $appid   = "wxfba00556cd93a6a5";
                $secret     = "a60c26968102308b7600d3a0f48a75a6";
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
                $result = Curl::callWebServer($url);
                $handle = fopen('./static/token.txt', 'w+');
                fwrite($handle, $result['access_token']);
                fclose($handle);
                echo json_encode(array('errorCode'=>0,'errorInfo'=>'获取token成功','data'=>$result['access_token']));

            } else {
                echo json_encode(array('errorCode'=>0,'errorInfo'=>'获取token成功','data'=>$token));
            }




    }



    function getCode(){
        $token = file_get_contents('./static/token.txt');
        $page="pages/index/index";
        $scene = "id:123";
        $width='80';
        $post_data='{"page":"'.$page.'","scene":"'.$scene.'","width":"'.$width.'"}';
        $queryAction = 'POST';
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$token;
        $method = "post";
        $result = Curl::callWebServer($url, $post_data, $queryAction, 0);
       var_dump($result);
    }




    //*****************************************获取用户信息模块*********************************************
    /**
     * 微信小程序用户登录
     */
    function wx_login() {

        $appid      = "wxfba00556cd93a6a5";
        $secret     = "a60c26968102308b7600d3a0f48a75a6";
        $js_code    = input('js_code');
        if ($js_code != null && $js_code != "") {
            $result = WXUserUtil::get3rd_session($appid, $secret, $js_code);
            //以3rd_session为key,session_key+openid为value，写入session
            $session_key                  = $result['session_key'];
            $openid                       = $result['openid'];
            $third_session_key            = $result['third_session'];
            $thrid_session_value          = $session_key . $openid;

            //$_SESSION[$third_session_key] = $thrid_session_value;
            echo json_encode(array('data'=>$openid));
        } else {
            $_SESSION = "";
        }

    }

    /**
     * 解密用户敏感信息 包括用户openId、nick_name等信息
     */
    function decryptUserInfo() {

        $encryptedData = input('encryptedData');
        $iv            = input('iv');
        $thridRD       = input('thridRDSession');
        $appid         = "wxfba00556cd93a6a5";
        $encryptedData = trim($encryptedData, chr(239) . chr(187) . chr(191));
        $iv            = trim($iv, chr(239) . chr(187) . chr(191));
        $rdSession_key = json_decode(trim($thridRD, chr(239) . chr(187) . chr(191)), true);
        $sessionArr    = explode("==", $rdSession_key);
        $sessionKeys   = $sessionArr[0];
        $sessionKey    = $sessionKeys . '==';
        $account       = $sessionArr[1];

        //判断用户是否存在base_user表中
        $userResult = $this->lib_user->findUser(array('account' => $account));
        if ($userResult['errorCode'] == 0) {
            echo json_encode($userResult['data']);
        } else {
            $result   = WXUserUtil::decryptUserInfo($appid, $sessionKey, $encryptedData, $iv);
            $userInfo = $this->insertUser($result, $account, $sessionKey, $sessionKeyOpenId, $encryptedData, $iv, $rdSession_key, $result);
            echo json_encode($userInfo);
        }
    }

    /**
     * 插入用户信息
     * @param array $userInfo
     * @return array
     */
    function insertUser($userInfo, $account, $sessionKey, $sessionKeyOpenId, $encryptedData, $iv, $thridRD, $results) {
        $addInfo = array(
            nick_name        => $userInfo['nickName'],
            open_id          => $account,
            account          => $account,
            sex              => $userInfo['gender'],
            country          => $userInfo['country'],
            province         => $userInfo['province'],
            city             => $userInfo['city'],
            sessionKey       => $sessionKey,
            encryptedData    => $encryptedData,
            iv               => $iv,
            thridRD          => $thridRD,
            user_result      => $results,
            sessionKeyOpenId => $sessionKeyOpenId,
            head_img_url     => $userInfo['avatarUrl'],
        );

        //判断用户是否存在base_user表中
        $userResult = $this->lib_user->findUser(array('account' => $account));
        if ($userResult['errorCode'] != 0) {
            $result        = $this->lib_user->addUser($addInfo);
            $addInfo['id'] = $result['data'];
            return $addInfo;
        } else {
            //如果存在用户
            return $userResult['data']['id'];
        }
    }


    /**
     *    测试获取用户信息
     */
    public function testlogin(){
        $account = input('account');
        //判断用户是否存在base_user表中
        $userResult = $this->lib_user->findUser(array('account' => $account));

        if ($userResult['errorCode'] != 0) {
            $addInfo = array();
            $addInfo['nick_name'] = input('nickName');
            $addInfo['open_id'] = $account;
            $addInfo['account'] = $account;
            $addInfo['sex'] = input('gender');
            $addInfo['country'] = input('country');
            $addInfo['province'] = input('province');
            $addInfo['city'] = input('city');
            $addInfo['head_img_url'] = input('avatarUrl');
            $addInfo = array(
                nick_name => input('nickName'),
                open_id => $account,
                account => $account,
                sex => input('gender'),
                country => input('country'),
                province => input('province'),
                city => input('city'),
//                sessionKey       => $sessionKey,
//                encryptedData    => $encryptedData,
//                iv               => $iv,
//                thridRD          => $thridRD,
//                user_result      => $results,
//                sessionKeyOpenId => $sessionKeyOpenId,
                head_img_url => input('avatarUrl'),
            );

            $result   = $this->lib_user->addUser($addInfo);

            if($result['data']){
                $addInfo['id'] = $result['data'];
                return json_encode($addInfo);
            }

        } else {
            //如果存在用户
            return json_encode($userResult['data']);
        }
    }

    function testWx() {
        if (!class_exists('WXUserUtil')) include 'include/wxAppUtil/WXUserUtil.php';
        $appid = 'wxfbac6eba0b6376e1';
        //$sessionKey = 'tiihtNczf5v6AKRyjwEUhQ==';
        $encryptedData = "Pb5EKsmj8KsA44q5Z5p3uFMmzJxzt0aJFQWNkcxBZOKJ1Qwwb+pJuzNwmcq+QxLIiMt/cBpJR2mvVBFnpdCuvRu/WjMWuV5RvcyKTDiw2QVEC7rdmT5vXDLpMBtnZi5W7wGpNaa6FuNmHbbJeSkA5KeihnFQMopYSH2oAS92cTH47mHDbp3K3CJ9iV+iGmpJSNvYpnznVNKwvh+IxMRP9kfVS7+HfBhvWp7cQUR4s/2GJ7x6EsXFl5EzYHWTDBvAxvUhHqEuh6iDCtZxWv6OVq3OXZt9b+HfJj297Iq5trJvKlEpFo6iWGo0gDMr3EW9FGi9FzWUbXLNtrpl+uP5uI7az8YNmWpqFTzJ0xzV27ccl3fGgQ8Q3rm37yD/TErRr96jbMRYxfQM/N51Yu74PSgY7JI0IZ86xP94oC7vHwWS+ZaQ5iyEsHkE+0Swrty65jzmK0dw40LfyLjdEj111w==";
        $iv            = "sQRdH3h972rplufBruJ3Vw==";
        $secret        = '42d568158938bcb6292cdea7c6b1337c';
        $js_code       = "011GB8sr1MtCkp05GNqr1WJTrr1GB8sN";

        $result = WXUserUtil::get3rd_session($appid, $secret, $js_code);

        $openId = $result['openid'];

    }

    function testUser() {
        $userInfo['data'] = $_SESSION['user'];
    }









}