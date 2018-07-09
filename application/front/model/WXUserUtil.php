<?php
namespace app\front\model;

use app\front\model\WXUtil;

/**
 * 微信小程序获取用户信息
 * @name WXUserUtil
 * @package cws
 * @category include
 * @link http://www.chanekeji.com
 * @author jeky
 * @version 2.0
 * @copyright CHANGE INC
 * @since 2017-01-23
 */
class WXUserUtil{
    
    /**
     * 生成3rd_session
     * code 换取 session_key  这是一个 HTTPS 接口，开发者服务器使用登录凭证 code 获取 session_key 和 openid。
     * 其中 session_key 是对用户数据进行加密签名的密钥。为了自身应用安全，session_key 不应该在网络上传输。
     * @param string $appid 小程序唯一标识
     * @param string $secret 小程序的 app secret
     * @param string $js_code 登录时获取的 code
     * @param string $grant_type 填写为 authorization_code
     * @return boolean|mixed
     */
    public static function get3rd_session($appid,$secret,$js_code,$grant_type='authorization_code'){
        $result = WXUtil::getSessionKey($appid, $secret, $js_code,$grant_type);

//        if($result['errmsg']) die(json_encode($result));
        $openId = $result['openid'];
        $session_key = $result['session_key'];
        $userInfo['openid'] = $openId; 
        $userInfo['session_key'] = $session_key;
        $userInfo['third_session'] = WXUserUtil::random_str(16);
//         $thirdSessionValue = $session_key.$openId;
//         $userInfo[$thirdSessionKey] = $thirdSessionValue;
//         $userInfo = json_decode($result, true);
        return $userInfo;
    }




    /**
     * 
     * @param string $appid 小程序唯一标识
     * @param string $thridRDSession 3rd_session数据包含sessionKey（本次登录的 会话密钥）和openId（用户openid）
     * @param string $encryptedData 包括敏感数据在内的完整用户信息的加密数据
     * @param string $iv 加密算法的初始向量
     * @return mixed
     */
    public static function decryptUserInfo($appid,$sessionKey,$encryptedData,$iv){
        $result = WXUtil::decryptUserInfo($appid, $sessionKey, $encryptedData, $iv);
        $result = json_decode($result,true);
        return $result;
    }
    
    
    
    /**
     *  生成指定长度的随机字符串(包含大写英文字母, 小写英文字母, 数字)
     * @author jeky
     * @param int $length 需要生成的字符串的长度
     * @return string 包含 大小写英文字母 和 数字 的随机字符串
     */
    public static function random_str($length){
        //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++)
        {
            $rand = mt_rand(0, $arr_len-1);
            $str.=$arr[$rand];
        }
        return $str;
    }
    
}