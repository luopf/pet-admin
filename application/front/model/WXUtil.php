<?php
namespace app\front\model;

use app\front\model\ErrorCode;
use app\front\model\PKCS7Encoder;

use app\front\model\WXBizDataCrypt;

/**
 * 微信小程序工具类
 * @name WXUtil
 * @package cws
 * @category include
 * @link http://www.chanekeji.com
 * @author jeky
 * @version 2.0
 * @copyright CHANGE INC
 * @since 2017-01-23
 */
class WXUtil{
     
    /**
     * code 换取 session_key  这是一个 HTTPS 接口，开发者服务器使用登录凭证 code 获取 session_key 和 openid。
     * 其中 session_key 是对用户数据进行加密签名的密钥。为了自身应用安全，session_key 不应该在网络上传输。
     * @param string $appid 小程序唯一标识
     * @param string $secret 小程序的 app secret
     * @param string $js_code 登录时获取的 code
     * @param string $grant_type 填写为 authorization_code
     * @return boolean|mixed
     */
    public static function getSessionKey($appid,$secret,$js_code,$grant_type='authorization_code'){
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$js_code}&grant_type={$grant_type}";
        return Curl::callWebServer($url);
    }
  
    /**
     * 解密用户敏感信息
     * @param string $appid 小程序唯一标识
     * @param string $sessionKey 本次登录的 会话密钥
     * @param string $encryptedData 包括敏感数据在内的完整用户信息的加密数据
     * @param string $iv 加密算法的初始向量
     * @return $data
     */
    public static function decryptUserInfo($appid,$sessionKey,$encryptedData,$iv){
        $wxBizDataCrypt = new WXBizDataCrypt($appid, $sessionKey);
        $result = $wxBizDataCrypt->decryptData($encryptedData, $iv, $data );
        if ($result == 0) {
            return $data;
        } else {
            return $errCode;
        }
    }
    
    
}