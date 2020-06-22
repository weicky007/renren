<?php


/*
 * 人人商城
 *
 * 青岛易联互动网络科技有限公司
 * http://www.we7shop.cn
 * TEL: 4000097827/18661772381/15865546761
 */

error_reporting(0);
define('IN_MOBILE', true);
$input = file_get_contents('php://input');
libxml_disable_entity_loader(true);
if (!empty($input) && empty($_GET['out_trade_no'])) {
	$obj = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
	$data = json_decode(json_encode($obj), true);

	if (empty($data)) {
		exit('fail');
	}
	if (empty($data['version']) && ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS')) {
		$result = array(
			'return_code' => 'FAIL',
			'return_msg' => empty($data['return_msg']) ? $data['err_code_des'] : $data['return_msg']
		);
		echo array2xml($result);
		exit;
	}

    if (!empty($data['version']) && ($data['result_code'] != '0' || $data['status'] != '0')) {
        exit('fail');
    }
	$get = $data;
} else {
	$get = $_GET;
}


require dirname(__FILE__).'/../../../../framework/bootstrap.inc.php';
require IA_ROOT.'/addons/ewei_shopv2/defines.php';
require IA_ROOT.'/addons/ewei_shopv2/core/inc/functions.php';
require IA_ROOT.'/addons/ewei_shopv2/core/inc/plugin_model.php';
require IA_ROOT.'/addons/ewei_shopv2/core/inc/com_model.php';

class EweiShopWechatPay
{
    public $get;
    public $type;
    public $total_fee;
    public $set;
    public $setting;
    public $sec;
    public $sign;
    public $isapp = false;
    public $is_jie = false;

    public function __construct($get)
    {
        global $_W;
        $this->get = $get;
        $strs = explode(':', $this->get['attach']);
        $this->type = intval($strs[1]); //类型 0 购物 1 充值 2积分兑换 3 积分兑换运费 4 优惠券购买 5 拼团 11门店充值积分 12门店消费 13收银台
        $this->total_fee= round($this->get['total_fee']/100,2);
        $GLOBALS['_W']['uniacid'] = intval($strs[0]);
        $_W['uniacid'] = intval($strs[0]);
        $this->init();
    }

    public function success()
    {
        $result = array(
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK'
        );
        echo array2xml($result);
        exit;
    }

    public function fail()
    {
        $result = array(
            'return_code' => 'FAIL',
            'return_msg' => '签名失败'
        );
        echo array2xml($result);
        exit;
    }

    public function init()
    {
        if ($this->type == '0'){
            $this->order();
        }elseif ($this->type == '1'){
            $this->recharge();
        }elseif ($this->type == '2'){
            $this->creditShop();
        }elseif ($this->type == '3'){
            $this->creditShopFreight();
        }elseif ($this->type == '4'){
            $this->coupon();
        }elseif ($this->type == '5'){
            $this->groups();
        }elseif ($this->type == '6'){
            $this->threen();
        }elseif ($this->type == '10'){
            $this->mr();
        }elseif ($this->type == '11'){
            $this->pstoreCredit();
        }elseif ($this->type == '12'){
            $this->pstore();
        }elseif ($this->type == '13'){
            $this->cashier();
        }elseif ($this->type == '14'){
            $this->wxapp_order();
        }elseif ($this->type == '15'){
            $this->wxapp_recharge();
        }elseif ($this->type == '16'){
            $this->wxapp_coupon();
        }elseif ($this->type == '17'){
            $this->grant();
        }elseif ($this->type == '18'){
            $this->plugingrant();
        }elseif( $this->type == '19' ){
            $this->wxapp_groups();
        }
        elseif( $this->type == '20' ){
            $this->wxapp_membercard();
        }
        elseif( $this->type == '21' ){
            $this->membercard();
        }
        $this->success();
    }

    /**
     * 订单支付
     */
    public function order()
    {
        global $_W;
        if (!$this->publicMethod()){
            exit(__FUNCTION__);
        }
        
        $ordersn = $tid = $this->get['out_trade_no'];
        $count_ordersn = m('order')->countOrdersn($tid);

        $isborrow = 0;
        $borrowopenid = '';

        if (strpos($tid,'_borrow')!==false){
            $tid = str_replace('_borrow','',$tid);
            $isborrow = 1;
            $borrowopenid = $this->get['openid'];
        }

        if (strpos($tid,'_B')!==false){
            $tid = str_replace('_B','',$tid);
            $isborrow = 1;
            $borrowopenid = $this->get['openid'];
        }

        if (strexists($tid, 'GJ')) {
            $tids = explode("GJ", $tid);
            $tid = $tids[0];
            $ordersn2 = $tids[1];
            $sub_openid = $this->get['sub_openid'];
            $openid = $this->get['openid'];
            $openid = empty($sub_openid)?$openid:$sub_openid;
            if($ordersn2 >= 100){
                pdo_update('ewei_shop_order', array('ordersn2' =>$ordersn2), array('ordersn' => $tid, 'openid' => $openid));
            }
        }
        $ispeerpay = 0;
        
        
        if (strlen($tid)>22 && $count_ordersn != 2){
            $tid2 = $tid;
            $ispeerpay = 1;
        }
        $paytype = 21;
        if (strexists($borrowopenid,'2088') || is_numeric($borrowopenid))
        {
            $paytype = 22;
        }
        $tid = substr($tid, 0, 22);
        $order = pdo_fetch("SELECT * FROM ".tablename('ewei_shop_order')." WHERE ordersn = :ordersn AND uniacid = :uniacid",array(':ordersn'=>$tid,':uniacid'=>$_W['uniacid']));
        $sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `module`=:module AND `tid`=:tid  limit 1';
        $params = array();

        $params[':tid'] = $tid;
        $params[':module'] = 'ewei_shopv2';
        $log = pdo_fetch($sql, $params);
        if (!empty($log) && ($log['status'] == '0' || $ispeerpay)  && ($log['fee']==$this->total_fee || $ispeerpay) ) {

            $transaction_id = $this->get['transaction_id'];
            $out_transaction_id = $this->get['out_transaction_id'];
            $transaction_id = empty($transaction_id)?$out_transaction_id:$transaction_id;
            if ($count_ordersn == 2) {
                pdo_update('ewei_shop_order', array('tradepaytype' => 21,'isborrow'=>$isborrow,'borrowopenid'=>$borrowopenid,'apppay'=>$this->isapp?1:0,'transid'=>$transaction_id), array('ordersn_trade' => $log['tid'],'uniacid'=>$log['uniacid']));
            } else {
                pdo_update('ewei_shop_order', array('paytype' => 21,'isborrow'=>$isborrow,'borrowopenid'=>$borrowopenid,'apppay'=>$this->isapp?1:0,'transid'=>$transaction_id), array('ordersn' => $log['tid'],'uniacid'=>$log['uniacid']));
            }

            $site = WeUtility::createModuleSite($log['module']);
            m('order')->setOrderPayType($order['id'], $paytype);
            if (!empty($ispeerpay)) {
                $ispeerpay = m('order')->checkpeerpay($order['id']);

                if (!empty($ispeerpay)){//是代付

                    $openid = $this->get['openid'];

                    $member = m('member')->getInfo($openid);
                    m('order')->peerStatus(array('pid' => $ispeerpay['id'], 'uid' => $member['id'], 'uname' => $member['nickname'], 'usay' => '支持一下，么么哒!', 'price' => $this->total_fee, 'createtime' => time(), 'openid' => $openid, 'headimg' => $member['avatar'], 'tid' => $tid2));
                    m('order')->setOrderPayType($order['id'], $paytype);
                    //高并发代付时支付完成清除
                    $open_redis = function_exists('redis') && !is_error(redis());
                    if( $open_redis ) {
                        $redis_key = "{$_W['uniacid']}_peerpay_order__pay_{$ispeerpay['id']}";
                        $redis = redis();
                        $redis->delete($redis_key);
                    }
                    //如果开启读写分离，则延迟一秒钟
                    if($_W['config']['db']['slave_status'] == true) {
                        sleep(1);
                    }
                    $peerpay_info = (float)pdo_fetchcolumn("select SUM(price) from " . tablename('ewei_shop_order_peerpay_payinfo') . ' where pid=:pid limit 1', array(':pid' => $ispeerpay['id']));
                    if ($ispeerpay['peerpay_realprice'] > $peerpay_info) {
                        $this->success();
                    }
                }
            }

            if (!is_error($site)) {
                $method = 'payResult';
                if (method_exists($site, $method)) {
                    $ret = array();
                    $ret['acid'] = $log['acid'];
                    $ret['uniacid'] = $log['uniacid'];
                    $ret['result'] = 'success';
                    $ret['type'] = $log['type'];
                    $ret['from'] = 'return';
                    $ret['tid'] = $log['tid'];
                    $ret['user'] = $log['openid'];
                    $ret['fee'] = $log['fee'];
                    $ret['tag'] = $log['tag'];
                    $result = $site->$method($ret);
                    if ($result) {
                        $log['tag'] = iunserializer($log['tag']);
                        $log['tag']['transaction_id'] = $this->get['transaction_id'];
                        $record = array();
                        $record['status'] = '1';
                        $record['tag'] = iserializer($log['tag']);
                        pdo_update('core_paylog', $record, array('plid' => $log['plid']));
                    }
                }
            }
        }else{
            $this->fail();
        }
    }

    /**
     * 会员充值
     */
    public function recharge()
    {
        global $_W;
        if (!$this->publicMethod()){
            exit(__FUNCTION__);
        }
        //充值
        $logno = trim($this->get['out_trade_no']);

        $isborrow = 0;
        $borrowopenid = '';
        if (strpos($logno,'_borrow')!==false){
            $logno = str_replace('_borrow','',$logno);
            $isborrow = 1;
            $borrowopenid = $this->get['openid'];
        }

        if (empty($logno)) {
            $this->fail();
        }
        $log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member_log') . ' WHERE `uniacid`=:uniacid and `logno`=:logno limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logno));
        $OK= !empty($log) && empty($log['status']) && $log['money']==$this->total_fee;
        if ($OK) {
            //充值状态
            pdo_update('ewei_shop_member_log', array('status' => 1, 'rechargetype' => 'wechat','isborrow'=>$isborrow,'borrowopenid'=>$borrowopenid, 'apppay'=>$this->isapp?1:0), array('id' => $log['id']));
            //增加会员余额
            $shopset = m('common')->getSysset('shop');
            m('member')->setCredit($log['openid'], 'credit2', $log['money'], array(0, $shopset['name'].'会员充值:微信充值:余额:' . $log['money']));
            //充值积分
            m('member')->setRechargeCredit($log['openid'], $log['money']);

            //充值活动
            com_run('sale::setRechargeActivity', $log);

            //优惠券
            com_run('coupon::useRechargeCoupon', $log);

            //模板消息
            m('notice')->sendMemberLogMessage($log['id']);


            //充值打印小票
            $member =m('member')->getMember($log['openid']);
            $params=array(
                'nickname'=>empty($member['nickname'])?'未更新':$member['nickname'],
                'price'=>$log['money'],
                'paytype'=>'微信支付',
                'paytime'=>date("Y-m-d H:i:s",time()),
            );
            com_run('printer::sendRechargeMessage',$params);
        }
    }

    /**
     * 积分商城兑换
     */
    public function creditShop()
    {
        global $_W;
        if (!$this->publicMethod()){
            exit(__FUNCTION__);
        }
        //积分兑换
        $logno = trim($this->get['out_trade_no']);
        if (empty($logno)) {
            exit;
        }
        $logno = str_replace('_borrow','',$logno);
        /*$log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_creditshop_log') . ' WHERE `logno`=:logno and `uniacid`=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logno));
        if (!empty($log) && empty($log['status'])) {
            $goods = pdo_fetch("SELECT * FROM" .tablename("ewei_shop_creditshop_goods") . "WHERE id=:id and uniacid=:uniacid limit 1 ",array(":id"=>$log['goodsid'], ":uniacid"=>$_W['uniacid']));
            if(!empty($goods) && $this->total_fee==$goods['money']){
                pdo_update('ewei_shop_creditshop_log', array('paystatus' => 1, 'paytype' => 1), array('id' => $log['id']));
            }
        }*/

        if(p('creditshop')){
            p('creditshop')->payResult($logno,'wechat',$this->total_fee, $this->isapp?true:false);
        }
    }

    /**
     * 积分兑换运费问题
     */
    public function creditShopFreight()
    {
        global $_W;
        if (!$this->publicMethod()){
            exit(__FUNCTION__);
        }
        //积分兑换运费支付
        $dispatchno = trim($this->get['out_trade_no']);
        $dispatchno = str_replace('_borrow','',$dispatchno);
        if (empty($dispatchno)) {
            exit;
        }
        $log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_creditshop_log') . ' WHERE `dispatchno`=:dispatchno and `uniacid`=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':dispatchno' => $dispatchno));
        if (!empty($log) && $log['dispatchstatus'] < 0) {
            pdo_update('ewei_shop_creditshop_log', array('dispatchstatus' => 1), array('dispatchno' => $dispatchno));
        }
    }

    /**
     * 优惠券支付
     */
    public function coupon()
    {
        global $_W;
        if (!$this->publicMethod()){
            exit(__FUNCTION__);
        }
        $logno = str_replace('_borrow','',$this->get['out_trade_no']);
        $log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_coupon_log') . ' WHERE `logno`=:logno and `uniacid`=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logno));
        $coupon = pdo_fetchcolumn('select money from ' . tablename('ewei_shop_coupon') . ' where id=:id limit 1', array(':id' => $log['couponid']));
        if ($coupon==$this->total_fee){
            com_run('coupon::payResult', $logno);
        }
    }/**
     * 优惠券支付
     */
    public function wxapp_coupon()
    {
        global $_W;

        $logno = str_replace('_borrow','',$this->get['out_trade_no']);
        $log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_coupon_log') . ' WHERE `logno`=:logno and `uniacid`=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logno));
        $coupon = pdo_fetchcolumn('select money from ' . tablename('ewei_shop_coupon') . ' where id=:id limit 1', array(':id' => $log['couponid']));
        if ($coupon==$this->total_fee){
            com_run('coupon::payResult', $logno);
        }
    }

    /**
     * 拼团支付
     */
    public function groups()
    {
        global $_W;
        if (!$this->publicMethod()){
            exit(__FUNCTION__);
        }
        //拼团支付
        $orderno = trim($this->get['out_trade_no']);
        $orderno = str_replace('_borrow','',$orderno);
        if (empty($orderno)) {
            exit;
        }
        if ($this->is_jie){
            pdo_update('ewei_shop_groups_order', array('isborrow'=>'1','borrowopenid'=>$this->get['openid']), array('orderno'=>$orderno, 'uniacid' => $_W['uniacid']));
        }
        if(p('groups')){
            p('groups')->payResult($orderno,'wechat', $this->isapp?true:false);
        }
    }
    /**
     * 3N营销支付
     */
    public function threen()
    {
        global $_W;
        if (!$this->publicMethod()){
            exit(__FUNCTION__);
        }
        //3n营销支付
        $orderno = trim($this->get['out_trade_no']);
        $orderno = str_replace('_borrow','',$orderno);
        if (empty($orderno)) {
            exit;
        }
        if ($this->is_jie){
            pdo_update('ewei_shop_threen_log', array('isborrow'=>'1','borrowopenid'=>$this->get['openid']), array('logno'=>$orderno, 'uniacid' => $_W['uniacid']));
        }
        if(p('threen')){
            p('threen')->payResult($orderno,'wechat', $this->isapp?true:false);
        }
    }
    /**
     * 应用授权中心（定制）
     */
    public function grant()
    {
        global $_W;
        $setting = pdo_fetch("select * from ".tablename('ewei_shop_system_grant_setting')." where id = 1 limit 1 ");
        if ($setting['weixin']>0) {
            ksort($this->get);
            $string1 = '';
            foreach ($this->get as $k => $v) {
                if ($v != '' && $k != 'sign') {
                    $string1 .= "{$k}={$v}&";
                }
            }
            $this->sign = strtoupper(md5($string1 . "key={$setting['apikey']}"));
            if ( $this->sign ==  $this->get['sign']) {
                $order = pdo_fetch("select * from ".tablename('ewei_shop_system_grant_order')." where logno = '".$this->get['out_trade_no']."'");
                pdo_update('ewei_shop_system_grant_order', array('paytime'=>time(),'paystatus'=>1), array('logno' => $this->get['out_trade_no']));
                $plugind = explode(",",$order['pluginid']);
                $data = array(
                    'logno'=>$order['logno'],
                    'uniacid'=>$order['uniacid'],
                    'code'=>$order['code'],
                    'type'=>'pay',
                    'month'=>$order['month'],
                    'isagent'=>$order['isagent'],
                    'createtime'=>time()
                );
                foreach ($plugind as $key => $value){
                    $plugin = pdo_fetch("select `identity` from ".tablename('ewei_shop_plugin')." where id = ".$value." ");
                    $data['identity'] = $plugin['identity'];
                    $data['pluginid'] = $value;
                    pdo_insert('ewei_shop_system_grant_log', $data);
                    $id = pdo_insertid();
                    if(m('grant')){
                        m('grant')->pluginGrant($id);
                    }
                }
            }
        }
    }
    /**
     * 应用授权中心
     */
    public function plugingrant()
    {
        global $_W;
        $setting = pdo_fetch("select * from ".tablename('ewei_shop_system_plugingrant_setting')." where 1 = 1 limit 1 ");
        if ($setting['weixin']>0) {
            ksort($this->get);
            $string1 = '';
            foreach ($this->get as $k => $v) {
                if ($v != '' && $k != 'sign') {
                    $string1 .= "{$k}={$v}&";
                }
            }
            $this->sign = strtoupper(md5($string1 . "key={$setting['apikey']}"));
            if ( $this->sign ==  $this->get['sign']) {
                $order = pdo_fetch("select * from ".tablename('ewei_shop_system_plugingrant_order')." where logno = '".$this->get['out_trade_no']."'");
                pdo_update('ewei_shop_system_plugingrant_order', array('paytime'=>time(),'paystatus'=>1), array('logno' => $this->get['out_trade_no']));
                $plugind = explode(",",$order['pluginid']);
                $data = array(
                    'logno'=>$order['logno'],
                    'uniacid'=>$order['uniacid'],
                    'type'=>'pay',
                    'month'=>$order['month'],
                    'createtime'=>time()
                );
                foreach ($plugind as $key => $value){
                    $plugin = pdo_fetch("select `identity` from ".tablename('ewei_shop_plugin')." where id = ".$value." ");
                    $data['identity'] = $plugin['identity'];
                    $data['pluginid'] = $value;
                    pdo_query('update ' . tablename('ewei_shop_system_plugingrant_plugin') . " set sales = sales + 1 where pluginid = ".$value." ");
                    pdo_insert('ewei_shop_system_plugingrant_log', $data);
                    $id = pdo_insertid();
                    if(p('grant')){
                        p('grant')->pluginGrant($id);
                    }
                }
            }
        }
    }

    /**
     * 话费充值
     */
    public function mr()
    {
        global $_W;
        if (!$this->publicMethod()){
            exit(__FUNCTION__);
        }
        $ordersn = trim($this->get['out_trade_no']);
        $isborrow = 0;
        $borrowopenid = '';
        if (strpos($ordersn,'_borrow')!==false){
            $ordersn = str_replace('_borrow','',$ordersn);
            $isborrow = 1;
            $borrowopenid = $this->get['openid'];
        }
        if (empty($ordersn)) {
            exit;
        }
        if(p('mr')){
            $price = pdo_fetchcolumn('select payprice from ' . tablename('ewei_shop_mr_order') . ' where ordersn=:ordersn limit 1', array(':ordersn' =>$ordersn));
            if($price==$this->total_fee){
                if($isborrow==1){
                    pdo_update('ewei_shop_order', array('isborrow'=>$isborrow,'borrowopenid'=>$borrowopenid), array('ordersn' => $ordersn));
                }
                p('mr')->payResult($ordersn,'wechat');
            }
        }
    }

    /**
     * 门店积分充值
     */
    public function pstoreCredit()
    {
        global $_W;
        if (!$this->publicMethod()){
            exit(__FUNCTION__);
        }
        $ordersn = trim($this->get['out_trade_no']);
        $ordersn = str_replace('_borrow','',$ordersn);
        if (empty($ordersn)) {
            exit;
        }
        if(p('pstore')){
            p('pstore')->payResult($ordersn,$this->total_fee);
        }
    }

    /**
     * 门店支付
     */
    public function pstore()
    {
        global $_W;
        if (!$this->publicMethod()){
            exit(__FUNCTION__);
        }
        $ordersn = trim($this->get['out_trade_no']);
        $ordersn = str_replace('_borrow','',$ordersn);
        if (empty($ordersn)) {
            exit;
        }
        if(p('pstore')){
            p('pstore')->wechat_complete($ordersn);
        }
    }

    /**
     * 收银台支付
     */
    public function cashier()
    {
        global $_W;
        $ordersn = trim($this->get['out_trade_no']);
        if (empty($ordersn)) {
            exit;
        }
        if(p('cashier')){
            p('cashier')->payResult($ordersn);
        }
    }

    /**
     * 小程序 订单支付
     */
    public function wxapp_order()
    {
        $tid = $this->get['out_trade_no'];
        $transaction_id = $this->get['transaction_id'];
        if (strexists($tid, 'GJ')) {
            $tids = explode("GJ", $tid);
            $tid = $tids[0];
        }

        $sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `module`=:module AND `tid`=:tid  limit 1';
        $params = array();
        $params[':tid'] = $tid;
        $params[':module'] = 'ewei_shopv2';
        $log = pdo_fetch($sql, $params);
        if (!empty($log) && $log['status'] == '0'  && $log['fee']==$this->total_fee ) {
            $site = WeUtility::createModuleSite($log['module']);
            if (!is_error($site)) {
                $method = 'payResult';
                if (method_exists($site, $method)) {
                    $ret = array();
                    $ret['acid'] = $log['acid'];
                    $ret['uniacid'] = $log['uniacid'];
                    $ret['result'] = 'success';
                    $ret['type'] = $log['type'];
                    $ret['from'] = 'return';
                    $ret['tid'] = $log['tid'];
                    $ret['user'] = $log['openid'];
                    $ret['fee'] = $log['fee'];
                    $ret['tag'] = $log['tag'];
                    pdo_update('ewei_shop_order', array('paytype' => 21,'apppay'=>2,'transid'=>$transaction_id), array('ordersn' => $log['tid'],'uniacid'=>$log['uniacid']));
                    $result = $site->$method($ret);
                    if ($result) {
                        $log['tag'] = iunserializer($log['tag']);
                        $log['tag']['transaction_id'] = $this->get['transaction_id'];
                        $record = array();
                        $record['status'] = '1';
                        $record['tag'] = iserializer($log['tag']);
                        pdo_update('core_paylog', $record, array('plid' => $log['plid']));
                    }
                }
            }
        }else{
            $this->fail();
        }
    }

    /**
     * 小程序 会员充值
     */
    public function wxapp_recharge()
    {
        global $_W;
        //充值
        $logno = trim($this->get['out_trade_no']);

        if (empty($logno)) {
            $this->fail();
        }
        $log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member_log') . ' WHERE `uniacid`=:uniacid and `logno`=:logno limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logno));
        $OK= !empty($log) && empty($log['status']) && $log['money']==$this->total_fee;
        if ($OK) {
            //充值状态
            pdo_update('ewei_shop_member_log', array('status' => 1, 'rechargetype' => 'wechat', 'apppay'=>2), array('id' => $log['id']));
            //增加会员余额
            $shopset = m('common')->getSysset('shop');
            m('member')->setCredit($log['openid'], 'credit2', $log['money'], array(0, $shopset['name'].'会员充值:微信充值:余额:' . $log['money']));
            //充值积分
            m('member')->setRechargeCredit($log['openid'], $log['money']);

            //充值活动
            com_run('sale::setRechargeActivity', $log);

            //优惠券
            com_run('coupon::useRechargeCoupon', $log);

            //模板消息
            m('notice')->sendMemberLogMessage($log['id']);
        }elseif ($log['money']==$this->total_fee){
            pdo_update('ewei_shop_member_log', array('rechargetype' => 'wechat', 'apppay'=>2), array('id' => $log['id']));
        }
    }

    /**
     * 使用商城自带支付 公用方法
     * @return bool
     */
    public function publicMethod()
    {
        global $_W;
        if (empty($_W['uniacid'])){
            return false;
        }
        list($set,$payment) = m('common')->public_build();
        $this->set = $set;
        if (empty($payment['is_new']) || $this->get['trade_type']=='APP'){
            $this->setting = uni_setting($_W['uniacid'], array('payment'));
            if (is_array($this->setting['payment']) || $this->set['weixin_jie'] == 1 || $this->set['weixin_sub'] ==1 || $this->set['weixin_jie_sub'] ==1 || $this->get['trade_type']=='APP') {

                $this->is_jie = strpos($this->get['out_trade_no'],'_B')!==false || strpos($this->get['out_trade_no'],'_borrow')!==false;
                $sec_yuan = m('common')->getSec();
                $this->sec =iunserializer($sec_yuan['sec']);
                if (($this->set['weixin_jie'] == 1 && $this->is_jie) || $this->set['weixin_sub'] ==1 || ($this->set['weixin_jie_sub'] ==1 && $this->is_jie)){

                    if($this->set['weixin_sub'] ==1){
                        $wechat = array(
                            'version'=>1,
                            'key'=>$this->sec['apikey_sub'],
                            'apikey'=>$this->sec['apikey_sub'],
                        );
                    }
                    if ($this->set['weixin_jie'] == 1 && $this->is_jie){
                        $wechat = array(
                            'version'=>1,
                            'key'=>$this->sec['apikey'],
                            'apikey'=>$this->sec['apikey'],
                        );
                    }
                    if($this->set['weixin_jie_sub'] ==1 && $this->is_jie){
                        $wechat = array(
                            'version'=>1,
                            'key'=>$this->sec['apikey_jie_sub'],
                            'apikey'=>$this->sec['apikey_jie_sub'],
                        );
                    }

                }
                else if($this->set['weixin'] ==1){
                    $wechat = $this->setting['payment']['wechat'];
                    if (IMS_VERSION<=0.8){
                        $wechat['apikey'] = $wechat['signkey'];
                    }
                }

                if ($this->get['trade_type']=='APP' && $this->set['app_wechat']==1){
                    $this->isapp = true;
                    $wechat = array(
                        'version'=>1,
                        'key'=>$this->sec['app_wechat']['apikey'],
                        'apikey'=>$this->sec['app_wechat']['apikey'],
                        'appid'=>$this->sec['app_wechat']['appid'],
                        'mchid'=>$this->sec['app_wechat']['merchid']
                    );
                }
                if (!empty($wechat)) {
                    ksort($this->get);
                    $string1 = '';
                    foreach ($this->get as $k => $v) {
                        if ($v != '' && $k != 'sign') {
                            $string1 .= "{$k}={$v}&";
                        }
                    }
                    $wechat['apikey'] = $wechat['version'] == 1 ? $wechat['key'] : $wechat['apikey'];
                    $this->sign = strtoupper(md5($string1 . "key={$wechat['apikey']}"));
                    $this->get['openid'] = isset($this->get['sub_openid']) ? $this->get['sub_openid'] : $this->get['openid'];
                    if ( $this->sign ==  $this->get['sign']) {
                        return true;
                    }
                }
            }
        }else{
            if (!is_error($payment)) {

                if ($this->get['sign_type'] == 'RSA_1_1' || $this->get['sign_type'] == 'RSA_1_256') {
                    $signPars = "";
                    ksort($this->get);
                    foreach($this->get as $k => $v) {
                        if("sign" != $k && "" != $v) {
                            $signPars .= $k . "=" . $v . "&";
                        }
                    }
                    $signPars = substr($signPars, 0, strlen($signPars) - 1);
                    $res = openssl_pkey_get_public(m('common')->chackKey($payment['app_qpay_public_key']));
                    if ($this->get['sign_type'] == 'RSA_1_1') {
                        $result = (bool)openssl_verify($signPars, base64_decode($this->get['sign']), $res);
                        openssl_free_key($res);
                        return $result;
                    } else if($this->get['sign_type'] == 'RSA_1_256') {
                        $result = (bool)openssl_verify($signPars, base64_decode($this->get['sign']), $res, OPENSSL_ALGO_SHA256);
                        openssl_free_key($res);
                        return $result;
                    }

                }else{

                    ksort($this->get);
                    $string1 = '';
                    foreach ($this->get as $k => $v) {
                        if ($v != '' && $k != 'sign') {
                            $string1 .= "{$k}={$v}&";
                        }
                    }
                    $this->sign = strtoupper(md5($string1 . "key={$payment['apikey']}"));
                    $this->get['openid'] = isset($this->get['sub_openid']) ? $this->get['sub_openid'] : $this->get['openid'];
                    if ( $this->sign ==  $this->get['sign']) {
                        return true;
                    }

                }
            }
        }

        return false;

    }

    /**
     * 小程序拼团支付
     * @author 青椒
     * @date 5/19/2018
     * @time 15:00
     */
    public function wxapp_groups()
    {
        $orderno = $this->get['out_trade_no'];
        $sql = 'SELECT * FROM ' . tablename('ewei_shop_groups_paylog') . ' WHERE `tid`=:orderno  limit 1';
        $params = array();
        $params[':orderno'] = $orderno;
        $log = pdo_fetch($sql, $params);
        if (!empty($log) && $log['status'] == '0'  && $log['fee']==$this->total_fee ) {
            if(p('groups')){
                pdo_update('ewei_shop_groups_paylog', array('status' => '1'), array('id' => $log['id']));
                p('groups')->payResult($orderno,'wxapp');
            }
        }else{
            $this->fail();
        }

    }

    /**
     * 小程序购买会员卡支付
     * @author 洋葱
     * @date 07/02/2018
     * @time 10:00
     */
    public function wxapp_membercard(){
        $orderno = $this->get['out_trade_no'];

        $sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `module`=:module AND `tid`=:tid  limit 1';
        $params = array();
        $params[':tid'] = $orderno;
        $params[':module'] = 'ewei_shopv2';
        $log = pdo_fetch($sql, $params);
        if (!empty($log) && $log['status'] == '0'  && $log['fee']==$this->total_fee ) {
            $plugin_membercard = p('membercard');
            if($plugin_membercard){
                $log['tag'] = iunserializer($log['tag']);
                $log['tag']['transaction_id'] = $this->get['transaction_id'];
                $log['tag']['pay_time'] = time();
                $record = array();
                $record['status'] = '1';
                $record['tag'] = iserializer($log['tag']);
                pdo_update('core_paylog', $record, array('plid' => $log['plid']));
                $plugin_membercard->payResult($orderno,'wechat');
            }
        }else{
            $this->fail();
        }
    }

    /**
     * 会员卡购买支付
     */
    public function membercard()
    {
        global $_W;
        if (!$this->publicMethod()){
            exit(__FUNCTION__);
        }
        //拼团支付
        $orderno = trim($this->get['out_trade_no']);
        $orderno = str_replace('_borrow','',$orderno);
        if (empty($orderno)) {
            exit;
        }
        if ($this->is_jie){
            pdo_update('ewei_shop_member_card_order', array('isborrow'=>'1','borrowopenid'=>$this->get['openid']), array('orderno'=>$orderno, 'uniacid' => $_W['uniacid']));
        }
        if(p('membercard')){
            p('membercard')->payResult($orderno,'wechat', $this->isapp?true:false);
        }
    }
}
new EweiShopWechatPay($get);
exit('fail');
