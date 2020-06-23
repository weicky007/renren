<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
function app_error($errcode = 0,$message = '') {
    return json_encode(array(
        'error'=>$errcode,
        'message'=>empty($message)?AppError::getError( $errcode ):$message
    ));

}

function app_json($result = null,$openid) {
    global $_GPC,$_W;
    $ret = array();
    if(!is_array($result)){
        $result = array();
    }
    $ret['error'] = 0;
    $key = time().'@'.$openid;
    $auth =array('authkey'=>base64_encode(authcode($key,'ENCODE', "ewei_shopv2_wxapp")));
    m('cache')->set($auth['authkey'],1);
    return json_encode( array_merge($ret,$auth,$result));
}
require EWEI_SHOPV2_PLUGIN . 'app/core/error_code.php';
require EWEI_SHOPV2_PLUGIN . 'app/core/wxapp/wxBizDataCrypt.php';


class Wxapp_EweiShopV2Page extends Page
{
    protected $appid;
    protected $appsecret;

    public function __construct()
    {
        $data = m('common')->getSysset('app');
        $this->appid = $data['appid'];
        $this->appsecret = $data['secret'];
    }

    public function login() {
        global $_GPC,$_W;

        $code = trim($_GPC['code']);
        if (empty($code)) {
            return app_error(AppError::$ParamsError);
        }
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$this->appid}&secret={$this->appsecret}&js_code={$code}&grant_type=authorization_code";
        
        load()->func('communication');
        $resp = ihttp_request($url);

        if(is_error($resp)){
            return app_error(AppError::$SystemError , $resp['message']);;
        }
        $arr = @json_decode($resp['content'],true);
        $arr['isclose'] = $_W['shopset']['app']['isclose'];
        if(!empty($_W['shopset']['app']['isclose'])){
            $arr['closetext'] = $_W['shopset']['app']['closetext'];
        }

        if(!is_array($arr) || !isset($arr['openid'])){
            return app_error(AppError::$WxAppLoginError);
        }
        return app_json($arr,$arr['openid']);

    }


    public function auth() {
        global $_GPC,$_W;

        $encryptedData = trim($_GPC['data']);
        $iv = trim($_GPC['iv']);
        $sessionKey = trim($_GPC['sessionKey']);
        if (empty($encryptedData) || empty($iv)) {
            return app_error(AppError::$ParamsError);
        }
        $pc = new WXBizDataCrypt($this->appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        if ($errCode == 0) {
            $data = json_decode($data,true);
            $this->refine($data['openId']);
            $member = m('member')->getMember('sns_wa_'.$data['openId']);
            if (empty($member)){
                $member = array(
                    'uniacid' => $_W['uniacid'],
                    'uid' => 0,
                    'openid' => 'sns_wa_'.$data['openId'],
                    'nickname' => !empty($data['nickName']) ? $data['nickName'] : '',
                    'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '',
                    'gender' => !empty($data['gender']) ? $data['gender'] : '-1',
                    'openid_wa' =>$data['openId'],
                    'comefrom' =>'sns_wa',
                    'createtime' => time(),
                    'status' => 0
                );
                pdo_insert('ewei_shop_member', $member);
                $id = pdo_insertid();
                $data['id'] = $id;
                $data['uniacid'] = $_W['uniacid'];
                if(method_exists(m('member'),'memberRadisCountDelete')) {
                    m('member')->memberRadisCountDelete(); 
                }
            }else{
                $updateData = array(
                    'nickname' => !empty($data['nickName']) ? $data['nickName'] : '',
                    'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '',
                    'gender' => !empty($data['gender']) ? $data['gender'] : '-1',
                );
                pdo_update('ewei_shop_member',$updateData,array('id'=>$member['id'],'uniacid'=>$member['uniacid']));
                $data['id'] = $member['id'];
                $data['uniacid'] = $member['uniacid'];
                $data['isblack'] = $member['isblack'];
            }
            if (p('commission')) {
                p('commission')->checkAgent($member['openid']);
            }
            return app_json($data,$data['openId']);
        }
        return app_error(AppError::$WxAppError , '登录错误, 错误代码: '.$errCode);
    }

    public function getmobile()
    {
        global $_GPC;
        $aesKey = base64_decode($_GPC['sessionkey']);
        $aesIV = base64_decode($_GPC['iv']);
        $aesCipher = base64_decode($_GPC['encryptedData']);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $result = json_decode($result,true);
        if($result){
            return $result['phoneNumber'];
        }
        return '';
    }


    protected function refine(&$openid)
    {
        if (substr($openid, 0, 7) == 'sns_wa_') {
            $openid = substr($openid, 7);
        }
    }
}