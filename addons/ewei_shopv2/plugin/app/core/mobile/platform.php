<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
require_once EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

class Platform_EweiShopV2Page extends AppMobilePage
{
    public function get_wx_list() {
        global $_GPC;
        $this->verifySign();

        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;

        list($list,$total) = $this->oldAccount($pindex,$psize);

        if(!empty($list)) {
            foreach($list as &$account) {
                if (function_exists('uni_account_list')) {
                    $account_details = uni_accounts($account['uniacid']);
                }else{
                    $account_details = $this->uni_accounts($account['uniacid']);
                }

                if(!empty($account_details)) {
                    $account_detail = $account_details[$account['uniacid']];
                    $account['thumb'] = tomedia('headimg_'.$account_detail['acid']. '.jpg').'?time='.time();
                    $account['appid'] = $account_detail['key'];
                }
            }
            unset($account_val);
            unset($account);
        }
    
        die(app_json(array(
            'list'=>$list,
            'pagesize'=>$psize,
            'total'=>$total
        )));
    }

    private function uni_accounts($uniacid = 0) {
        global $_W;
        $uniacid = empty($uniacid) ? $_W['uniacid'] : intval($uniacid);
        $account_info = pdo_get('account', array('uniacid' => $uniacid));
        if (!empty($account_info)) {
            $account_tablename = uni_account_type($account_info['type']);
            $account_tablename = $account_tablename['table_name'];
            $accounts = pdo_fetchall("SELECT w.*, a.type, a.isconnect FROM " . tablename('account') . " a INNER JOIN " . tablename($account_tablename) . " w USING(acid) WHERE a.uniacid = :uniacid AND a.isdeleted <> 1 ORDER BY a.acid ASC", array(':uniacid' => $uniacid), 'acid');
        }
        return !empty($accounts) ? $accounts : array();
    }
    public function verifydomain() {
        $this->verifySign();

        load()->func('communication');
        $result = ihttp_post(EWEI_SHOPV2_AUTH_WXAPP. 'auth/auth', array(
            'host'=>$_SERVER['HTTP_HOST']
        ));
    }
    private function verifySign() {
        global $_GPC;

        $time = trim($_GPC['time']);
        if(empty($time)){
            return app_error(AppError::$ParamsError, '参数错误(time)');
        }
        if(($time + 300) < time()) {
            return app_error(AppError::$ParamsError, 'sign失效');
        }

        $sign = trim($_GPC['sign']);
        if(empty($time)){
            return app_error(AppError::$ParamsError, '参数错误(sign)');
        }
        $setting = setting_load('site');

        $site_id = isset($setting['site']['key']) ? $setting['site']['key'] : (isset($setting['key']) ? $setting['key'] : '0');
        if(empty($site_id)){
            return app_error(AppError::$ParamsError, '参数错误(site_id)');
        }

        $sign_str = md5(md5('site_id='. $site_id. '&request_time='. $time. '&salt=FOXTEAM'));
        if($sign != $sign_str){
            return app_error(AppError::$RequestError);
        }
    }


    private function oldAccount($pindex,$psize){
        global $_GPC,$_W;

        $start = ($pindex - 1) * $psize;
        $condition = '';
        $param = array();
        $keyword = trim($_GPC['keyword']);

        $condition .= " WHERE a.default_acid <> 0 AND b.isdeleted <> 1 AND (b.type = ".ACCOUNT_TYPE_OFFCIAL_NORMAL." OR b.type = ".ACCOUNT_TYPE_OFFCIAL_AUTH.")";
        $order_by = " ORDER BY a.`rank` DESC";

        if(!empty($keyword)) {
            $condition .=" AND a.`name` LIKE :name";
            $param[':name'] = "%{$keyword}%";
        }
        $tsql = "SELECT COUNT(*) FROM " . tablename('uni_account'). " as a LEFT JOIN". tablename('account'). " as b ON a.default_acid = b.acid {$condition} {$order_by}, a.`uniacid` DESC";
        $total = pdo_fetchcolumn($tsql, $param);

        $list = array();
        if(!empty($total)){
            $sql = "SELECT a.name, a.uniacid FROM ". tablename('uni_account'). " as a LEFT JOIN". tablename('account'). " as b ON a.default_acid = b.acid {$condition} {$order_by}, a.`uniacid` DESC LIMIT {$start}, {$psize}";
            $list = pdo_fetchall($sql, $param);
        }

        return array($list,$total);
    }

}