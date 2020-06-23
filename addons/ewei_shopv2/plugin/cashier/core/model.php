<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
function sort_cashier($a, $b) {
    $enough1 = floatval($a['enough']);
    $enough2 = floatval($b['enough']);
    if ( $enough1==$enough2) {
        return 0;
    } else {
        return ($enough1 < $enough2) ? 1 : -1;
    }
}
class CashierModel extends PluginModel
{
    const APPLY = 'apply';
    const CHECKED = 'checked';
    const APPLY_CLEARING = 'apply_clearing';
    const PAY = 'pay';
    const PAY_CASHIER = 'pay_cashier';
    const PAY_CASHIER_USER = 'pay_cashier_user';


    public static $paytype = array(
        0 => '微信',
        1 => '支付宝',
        2 => '商城余额',
        3 => '现金收款',
        101 => '系统微信',
        102 => '系统支付宝'
    );

    public static function perm()
    {
        $perm = array(
            'index' => '我要收款',
            'goods' => '商品收款',
            'order' => '收款订单',
            'statistics' => '收银统计',
            'sysset' => '设置',
            'sale' => '营销',
            'clearing' => '提现',
            'goodsmanage' => '商品管理',
        );
        if (empty($_W['cashieruser']['can_withdraw'])){
            unset($perm['clearing']);
        }
        return $perm;
    }

    public $setmeal = array('标准套餐','豪华套餐');

    public static $UserSet = array();

    public function getUserSet($name='',$cashierid)
    {
        global $_W;
        if (!isset(static::$UserSet[$cashierid])){
            $user = $this->userInfo($cashierid);
            $set = empty($user['set']) ? array() : json_decode($user['set'],true);
            static::$UserSet[$cashierid] = $set;
        }

        if (empty($name)){
            return static::$UserSet[$cashierid];
        }
        return isset(static::$UserSet[$cashierid][$name]) ? static::$UserSet[$cashierid][$name] : '';
    }

    public function updateUserSet($data = array(),$cashierid)
    {
        global $_W;
        $user = $this->userInfo($cashierid);
        $set = empty($user['set']) ? array() : json_decode($user['set'],true);
        $set = json_encode(array_merge($set,$data));
        return pdo_query("UPDATE ".tablename('ewei_shop_cashier_user')." SET `set`=:set WHERE `uniacid` = :uniacid AND `id` = :id",array(':uniacid'=>$_W['uniacid'],':id'=>$cashierid,':set'=>$set));
    }

    /**
     * 支付结果
     * @param $logid
     * @param bool $return_log
     * @return bool|int
     */
    public function payResult($logid,$return_log = false)
    {
        global $_W, $_GPC;
        $id = intval($logid);
        if ($id!=0){
            $log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_cashier_pay_log') . ' WHERE `id`=:id and `uniacid`=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
        }else{
            $log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_cashier_pay_log') . ' WHERE `logno`=:logno and `uniacid`=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':logno' => $logid));
        }
        if (!empty($log)) {
            //支付成功之后,清除随机立减金额缓存
            $money = $log['money'] + $log['randommoney'] + $log['deduction'] + $log['discountmoney'];
            $randommoney_key = $log['uniacid'].'_'.$log['cashierid'].'_money_'.$money.'_'.$log['client_ip'];
            $randommoney_key = str_replace('.','_',$randommoney_key);
            if(!empty($_SESSION[$randommoney_key])){
                unset($_SESSION[$randommoney_key]);
            }
            if(!empty($_GPC[$randommoney_key])){
                isetcookie($randommoney_key, '', -1000);
            }
            $_W['cashierid'] = $log['cashierid'];
            $res = $this->updateOrder($log);
            if ($res && $log['status'] != 1){
                $log['status'] = 1;
                $log['paytime'] = time();
            }
            return $return_log ? $log : $res;
        }
        return false;
    }

    public function categoryAll($status=1)
    {
        global $_W;
        $status = intval($status);
        $condition = " and uniacid=:uniacid  and status=" . intval($status);;
        $params = array(':uniacid' => $_W['uniacid']);
        $item = pdo_fetchall("SELECT * FROM " . tablename('ewei_shop_cashier_category') . " WHERE 1 {$condition}  ORDER BY displayorder desc, id DESC", $params);
        return $item;
    }

    public function categoryOne($id)
    {
        global $_W;
        $item = pdo_fetch("select * from " . tablename('ewei_shop_cashier_category') . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $id, ":uniacid" => $_W['uniacid']));
        return $item;
    }

    public function savaUser(array $params,$diyform=array())
    {
        global $_W;
        $diyform_flag = 0;
        $diyform_plugin = p('diyform');
        $f_data = array();
        if ($diyform_plugin&&!empty($_W['shopset']['cashier']['apply_diyform'])) {
            if (!empty($item['diyformdata'])) {
                $diyform_flag = 1;
                $fields = iunserializer($item['diyformfields']);
                $f_data = iunserializer($item['diyformdata']);
            } else {
                $diyform_id = $_W['shopset']['cashier']['apply_diyformid'];
                if (!empty($diyform_id)) {
                    $formInfo = $diyform_plugin->getDiyformInfo($diyform_id);
                    if (!empty($formInfo)) {
                        $diyform_flag = 1;
                        $fields = $formInfo['fields'];
                    }
                }
            }
        }

        $fdata = array();
        if ($diyform_flag) {
            $fdata = p('diyform')->getPostDatas($fields);
            if (is_error($fdata)) {
                show_json(0, $fdata['message']);
            }
        }
        if ($diyform_flag) {
            $params['diyformdata'] = iserializer($fdata);
            if (!empty($diyform)){
                $insert_data = $diyform_plugin->getInsertData($fields, $diyform);
                $params['diyformdata'] = $insert_data['data'];
            }
            $params['diyformfields'] = iserializer($fields);
        }
        if (empty($params['title'])) {
            show_json(0, "请填写收银台名称!");
        }
        if (empty($params['manageopenid'])) {
            show_json(0, "请填写管理微信号!");
        }
        if (empty($params['name'])) {
            show_json(0, "请填写联系人!");
        }
        if (empty($params['mobile'])) {
            show_json(0, "请填写联系电话!");
        }
        if (empty($params['username'])) {
            show_json(0, "请填写后台登录用户名!");
        }
        if (!empty($params['password'])){
            $params['salt'] = random(8);
            $params['password'] = md5(trim($params['password']) . $params['salt']);
        }else{
            unset($params['password']);
        }
        $params['storeid'] = intval($params['storeid']);
        $params['merchid'] = intval($params['merchid']);
        $params['isopen_commission'] = intval($params['isopen_commission']);
        $params['title'] = trim($params['title']);
        $params['logo'] = trim($params['logo']);
        $params['openid'] = trim($params['openid']);
        $params['manageopenid'] = trim($params['manageopenid']);
        $params['name'] = trim($params['name']);
        $params['mobile'] = trim($params['mobile']);
        $params['username'] = trim($params['username']);
        $params['withdraw'] = floatval($params['withdraw']);
        $params['wechat_status'] = intval($params['wechat_status']);
        $params['alipay_status'] = intval($params['alipay_status']);
        if (isset($params['deleted'])){
            $params['deleted'] = intval($params['deleted']);
        }
        if (!isset($params['id'])) {
            $params['createtime'] = TIMESTAMP;
            $params['deleted'] = 0;
            pdo_insert("ewei_shop_cashier_user",$params);
            $params['id'] = pdo_insertid();
        }else{
            pdo_update("ewei_shop_cashier_user",$params,array('id'=>$params['id'],'uniacid'=>$params['uniacid']));
        }
        return $params;
    }

    public function userInfo($openid)
    {
        global $_W;
        $id = intval($openid);
        $sql = "SELECT * FROM ".tablename('ewei_shop_cashier_user')." WHERE uniacid=:uniacid AND deleted=0";
        $params = array(':uniacid'=>$_W['uniacid']);
        if ($id==0){
            $sql .=" AND openid=:openid";
            $params[':openid'] = $openid;
        }else{
            $sql .=" AND id=:id";
            $params[':id'] = $id;
        }
        $res = pdo_fetch($sql." LIMIT 1",$params);
        return $res;
    }

    public function sendMessage($params,$type,$openid=null)
    {
        global $_W;
        if (isset($params['createtime'])){
            $params['createtime'] = date('Y-m-d H:i:s',$params['createtime']);
        }
        if (isset($params['paytime'])){
            $params['paytime'] = date('Y-m-d H:i:s',$params['paytime']);
        }
        $data = m('common')->getPluginset('cashier');
        $notice = $data['notice'];
        if (empty($notice[$type])){
            return false;
        }
        switch ($type){
            case self::APPLY:
                $datas = array(
                    '[联系人]'=>$params['name'],
                    '[联系电话]'=>$params['mobile'],
                    '[申请时间]'=>date('Y-m-d H:i:s',$params['createtime'])
                );
                break;
            case self::CHECKED:
                $message = array(
                    'first' => array('value' =>'收银台审核通知！' , "color" => "#ff0000"),
                    'keyword1' => array('title'=>'联系人','value' =>$params['name'] , "color" => "#ff0000"),
                    'keyword2' => array('title' => '联系电话', 'value' => $params['mobile'], "color" => "#000000"),
                    'keyword3' => array('title' => '审核状态', 'value' => $params['status'], "color" => "#000000"),
                    'keyword4' => array('title' => '审核时间', 'value' => $params['createtime'], "color" => "#000000"),
                    'keyword5' => array('title' => '驳回原因', 'value' => $params['reason'], "color" => "#000000"),
                );

                $datas = array(
                    '[联系人]'=>$params['name'],
                    '[联系电话]'=>$params['mobile'],
                    '[审核状态]'=>$params['status'],
                    '[审核时间]'=>$params['createtime'],
                    '[驳回原因]'=>$params['reason']
                );
                break;
            case self::APPLY_CLEARING:
                $message = array(
                    'first' => array('value' =>'收银台申请结算通知！' , "color" => "#ff0000"),
                    'keyword1' => array('title'=>'联系人','value' =>$params['name'] , "color" => "#ff0000"),
                    'keyword2' => array('title' => '联系电话', 'value' => $params['mobile'], "color" => "#000000"),
                    'keyword3' => array('title' => '申请时间', 'value' => $params['createtime'], "color" => "#000000"),
                    'keyword4' => array('title' => '打款时间', 'value' => $params['paytime'], "color" => "#000000"),
                    'keyword5' => array('title' => '申请金额', 'value' => $params['money'], "color" => "#000000"),
                    'keyword6' => array('title' => '打款金额', 'value' => $params['realmoney'], "color" => "#000000"),
                );
                $datas = array(
                    '[联系人]'=>$params['name'],
                    '[联系电话]'=>$params['mobile'],
                    '[申请时间]'=>$params['createtime'],
                    '[申请金额]'=>$params['money']
                );
                break;
            case self::PAY:
                $message = array(
                    'first' => array('value' =>'收银台申请结算通知！' , "color" => "#ff0000"),
                    'keyword1' => array('title'=>'联系人','value' =>$params['name'] , "color" => "#ff0000"),
                    'keyword2' => array('title' => '联系电话', 'value' => $params['mobile'], "color" => "#000000"),
                    'keyword3' => array('title' => '申请时间', 'value' => $params['createtime'], "color" => "#000000"),
                    'keyword4' => array('title' => '打款时间', 'value' => $params['paytime'], "color" => "#000000"),
                    'keyword5' => array('title' => '申请金额', 'value' => $params['money'], "color" => "#000000"),
                    'keyword6' => array('title' => '打款金额', 'value' => $params['realmoney'], "color" => "#000000"),
                );

                $datas = array(
                    '[联系人]'=>$params['name'],
                    '[联系电话]'=>$params['mobile'],
                    '[申请时间]'=>$params['createtime'],
                    '[打款时间]'=>$params['paytime'],
                    '[申请金额]'=>$params['money'],
                    '[打款金额]'=>$params['realmoney']
                );
                break;
            case self::PAY_CASHIER:

                $message = array(
                    'first' => array('value' =>'收银台付款提醒！' , "color" => "#ff0000"),
                    'keyword1' => array('title'=>'订单编号','value' =>$params['logno'] , "color" => "#ff0000"),
                    'keyword2' => array('title' => '付款金额', 'value' => $params['money'], "color" => "#000000"),
                    'keyword3' => array('title' => '余额抵扣', 'value' => $params['deduction'], "color" => "#000000"),
                    'keyword4' => array('title' => '付款时间', 'value' => $params['paytime'], "color" => "#000000"),
                    'keyword5' => array('title' => '收银台名称', 'value' => $params['cashier_title'], "color" => "#000000"),
                );

                $datas = array(
                    '[订单编号]'=>$params['logno'],
                    '[付款金额]'=>$params['money'],
                    '[余额抵扣]'=>$params['deduction'],
                    '[付款时间]'=>$params['paytime'],
                    '[收银台名称]'=>$params['cashier_title']
                );

                break;
            case self::PAY_CASHIER_USER:
                $message = array(
                    'first' => array('value' =>'收银台付款提醒！' , "color" => "#ff0000"),
                    'keyword1' => array('title'=>'订单编号','value' =>$params['logno'] , "color" => "#ff0000"),
                    'keyword2' => array('title' => '付款金额', 'value' => $params['money'], "color" => "#000000"),
                    'keyword3' => array('title' => '余额抵扣', 'value' => $params['deduction'], "color" => "#000000"),
                    'keyword4' => array('title' => '付款时间', 'value' => $params['paytime'], "color" => "#000000"),
                    'keyword5' => array('title' => '收银台名称', 'value' => $params['cashier_title'], "color" => "#000000"),
                );

                $datas = array(
                    '[订单编号]'=>$params['logno'],
                    '[付款金额]'=>$params['money'],
                    '[余额抵扣]'=>$params['deduction'],
                    '[付款时间]'=>$params['paytime'],
                    '[收银台名称]'=>$params['cashier_title']
                );
                break;
            default:
                break;
        }
        $datas = isset($datas) ? $datas : array();

        $notice['openid'] = is_null($openid) ? $notice['openid'] : $openid;
        return $this->sendNotice($notice, $type, $datas,$message);
    }

    protected function sendNotice($notice,$tag,$datas,$message)
    {
        global $_W;
        if (!empty($notice[$tag])) {
            $advanced_template = pdo_fetch('select * from ' . tablename('ewei_shop_member_message_template') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $notice[$tag], ':uniacid' => $_W['uniacid']));
            if (!empty($advanced_template)) {
                $url = !empty($advanced_template['url']) ? $this->replaceArray($datas,$advanced_template['url']) : '';
                $advanced_message = array(
                    'first' => array('value' => $this->replaceArray($datas,$advanced_template['first']), 'color' => $advanced_template['firstcolor']),
                    'remark' => array('value' => $this->replaceArray($datas,$advanced_template['remark']), 'color' => $advanced_template['remarkcolor'])
                );

                $data = iunserializer($advanced_template['data']);
                if(!empty($data)){
                    foreach ($data as $d) {
                        $advanced_message[$d['keywords']] = array('value' => $this->replaceArray($datas,$d['value']), 'color' => $d['color']);
                    }
                }
                if (!empty($notice['openid'])){
                    $notice['openid'] = is_array($notice['openid']) ? $notice['openid'] : explode(',',$notice['openid']);
                    foreach ($notice['openid'] as $openid){
                        if (!empty($notice[$tag]) && !empty($advanced_template['template_id'])) {
                            m('message')->sendTplNotice($openid, $advanced_template['template_id'], $advanced_message,$url);
                        }else {
                            if(empty($advanced_template['template_id'])){
                                $advanced_message = $this -> replaceTemplate($advanced_template['send_desc'],$datas);
                                if(empty($advanced_message) || $advanced_message == ""){
                                    $advanced_message = $message;
                                }
                                m('message')->sendCustomNotice($openid, $advanced_message,$url);
                            }else{
                                m('message')->sendCustomNotice($openid, $advanced_message,$url);
                            }
                        }
                    }
                }
            }elseif ($tag=='saler_stockwarn'){

                $advanced_template = pdo_fetch('select templateid  from ' . tablename('ewei_shop_member_message_template_default') . ' where typecode=:typecode and uniacid=:uniacid  limit 1', array(':typecode' => $tag, ':uniacid' => $_W['uniacid']));

                if (!empty($advanced_template)) {
                    if (!empty($notice['openid'])){
                        $notice['openid'] = is_array($notice['openid']) ? $notice['openid'] : explode(',',$notice['openid']);
                        foreach ($notice['openid'] as $openid){
                            if (!empty($notice[$tag]) && !empty($advanced_template['templateid'])) {
                                m('message')->sendTplNotice($openid, $advanced_template['templateid'],$datas,'');
                            }else {
                                m('message')->sendCustomNotice($openid, $datas,'');
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    protected function replaceArray(array $array,$message)
    {
        foreach ($array as $key => $value) {
            $message = str_replace($key,$value,$message);
        }
        return $message;
    }


    public function wechayPayInfo($user)
    {
        $wechatpay = json_decode($user['wechatpay'],true);
        if (empty($wechatpay['appid']) || empty($wechatpay['mch_id'])){
            $wechat = array(
                'appid' => $wechatpay['sub_appid'],
                'mch_id' => $wechatpay['sub_mch_id'],
                'apikey' => $wechatpay['apikey']
            );
        }else{
            $wechat = $wechatpay;
        }
        return $wechat;
    }


    public function wechatpay($params)
    {
        global $_W;
        $wechat = $this->wechayPayInfo($_W['cashieruser']);
        $params['old'] = true;
        return m('common')->wechat_micropay_build($params,$wechat,13);
    }


    public function wechatpay_101($params)
    {
        return m('common')->wechat_micropay_build($params,array(),13);
    }


    public function alipay($params)
    {
        global $_W;
        $_W['cashieruser'] = $this->userInfo((int)$_W['cashierid']);
        return m('common')->AliPayBarcode($params,json_decode($_W['cashieruser']['alipay'],true));
    }

 
    public function createOrder(array $array,$return = null,$can_sale=true)
    {
        global $_W;
        if (empty($array)){
            return 0;
        }
        $array['operatorid'] = isset($array['operatorid']) ? $array['operatorid'] : 0;
        $array['deduction'] = isset($array['deduction']) ? $array['deduction'] : 0;

        $realmoney = $array['money'];
        $sale = array();

        if ($realmoney>0 && $can_sale){

     
            if (!empty($array['usecoupon'])){
                $usecoupon = $this->caculatecoupon($array['usecoupon'],$realmoney);
                $realmoney = $usecoupon['new_price'];
            }

    
            $sale = $this->saleCalculate($realmoney);
            $realmoney = $sale['money'];
        }

        if ($array['deduction'] > $realmoney){
            $array['deduction'] = $realmoney;
        }
        $realmoney = round($realmoney - $array['deduction'],2);

        $title = $_W['cashieruser']['title'].'消费';
        if (!empty($array['title'])){
            $title = $array['title'];
        }
        $data = array(
            'uniacid'=>$_W['uniacid'],
            'cashierid'=>$_W['cashierid'],
            'operatorid'=>$array['operatorid'],
            'paytype'=>$array['paytype'],
            'openid'=>isset($array['openid']) ? $array['openid'] : '',
            'logno'=>'CS'.date('YmdHis').mt_rand(10000,99999),
            'title'=>$title,
            'createtime'=>time(),
            'money'=>$realmoney,
            'randommoney'=>isset($sale['randommoney']) ? $sale['randommoney'] : 0,
            'enough'=>isset($sale['enough']['money']) ? $sale['enough']['money'] : 0,
            'mobile'=>isset($array['mobile']) ? $array['mobile'] : 0,
            'deduction'=>isset($array['deduction']) ? $array['deduction'] : 0,
            'discountmoney'=>isset($sale['discount']['money']) ? $sale['discount']['money'] : 0,
            'discount'=>isset($sale['discount']['discount']) ? $sale['discount']['discount'] : 0,
            'couponpay'=>isset($array['couponpay']) ? $array['couponpay'] : 0,
            'nosalemoney'=>isset($array['nosalemoney']) ? $array['nosalemoney'] : 0,
            'coupon'=>isset($array['coupon']) ? $array['coupon'] : 0,
            'usecoupon'=>isset($array['usecoupon']) ? $array['usecoupon'] : 0,
            'usecouponprice'=>isset($usecoupon['money']) ? $usecoupon['money'] : 0,
            'status'=>0,
            'client_ip'=>CLIENT_IP,
        );

        $res = pdo_insert("ewei_shop_cashier_pay_log",$data);
        if (!$res){
            return error(-2,'数据插入异常,请重试!');
        }
        $data['id'] = pdo_insertid();

        if (!empty($usecoupon)){
            pdo_update('ewei_shop_coupon_data', array('used' => 1, 'usetime' => $data['createtime'], 'ordersn' => $data['logno']), array('id' => $array['usecoupon']));
        }
        if ($return !== null){
            return $data;
        }
        return $this->pay($data,(isset($array['auth_code']) ? $array['auth_code'] : null));
    }

    public function pay($data,$auth_code=null)
    {
        global $_W;
        if ($data['money'] <= 0  || $data['paytype'] == 3){
            $data['status'] = 1;
            $data['paytype'] = $data['paytype'] == 3 ? $data['paytype'] : 2;
            $data['paytime'] = time();

            pdo_update('ewei_shop_cashier_pay_log',$data,array('id'=>$data['id']));

            if($data['paytype'] != 3 ){
                m('member')->setCredit($data['openid'],'credit2',-$data['deduction'],array(0,'收银台 '.$_W['cashieruser']['title'],'收款'));
            }

            $user = $this->userInfo($data['cashierid']);
            $this->paySuccess($data,$user);

            return array(
                'res'=>true,
                'id' => $data['id']
            );
        }

        $params = array(
            'title' => $data['title'],
            'tid' => $data['logno'],
            'fee' => $data['money'],
        );
        $params['out_trade_no'] = $params['tid'];
        $params['total_amount'] = $params['fee'];
        $params['subject'] = $params['title'];
        $params['body'] = $_W['uniacid'] . ':' . 2;

        if ($auth_code!==null){
            $params['auth_code'] = $auth_code;
        }
        if ($data['paytype'] == 0){
            $res = $this->wechatpay($params);
        }elseif ($data['paytype'] == 1){
            $res = $this->alipay($params);
        }else{
            $res = $this->wechatpay_101($params);
        }
        return array(
            'res'=>$res,
            'id' => $data['id']
        );
    }

    public function orderQuery($pay_log)
    {
        $array = array();
        if (is_array2($pay_log)){
            foreach ($pay_log as $value){
                if ($value['status']=='0'){
                    $res = $this->updateOrder($value);
                    if ($res){
                        $array[] = $res;
                    }
                }
            }
        }elseif(is_array($pay_log)){
            $res = $this->updateOrder($pay_log);
            if ($res){
                $array[] = $res;
            }
        }else{
            $res = $this->payResult($pay_log);
            if ($res){
                $array[] = $res;
            }
        }
        return $array;
    }

  
    public function refund($id)
    {
        global $_W;
        $_W['cashieruser'] = $this->userInfo((int)$_W['cashierid']);
        $id = (int)$id;
        $pay_log = pdo_fetch("SELECT * FROM ".tablename('ewei_shop_cashier_pay_log')." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$id));
        if ($pay_log['status'] !=1){
            return error(-1,'未支付或者已退款!');
        }
        $out_trade_no = 'CST'.date('YmdHis').mt_rand(1000,9999);
        $res = array();
        switch ($pay_log['paytype'])
        {
            case '0':
                $res = $this->refundWechat($pay_log['openid'], $pay_log['logno'], $out_trade_no, $pay_log['money'] * 100, $pay_log['money'] * 100, false);
                break;
            case '1':
                $res = m('finance')->newAlipayRefund(array('out_trade_no' => $pay_log['logno'],'refund_amount'=>$pay_log['money'],'refund_reason' => $_W['cashieruser']['title'].' 收银台退款! 退款订单号: '.$out_trade_no),json_decode($_W['cashieruser']['alipay'],true));
                break;
            case '2':
                m('member')->setCredit($pay_log['openid'],'credit2',$pay_log['money']+$pay_log['deduction'],$_W['cashieruser']['title'].' 收银台退款! 退款订单号'.$out_trade_no);
                break;
            case '3':
                $res = true;
                break;
            case '101':
                $res = m('finance')->refund($pay_log['openid'], $pay_log['logno'], $out_trade_no, $pay_log['money'] * 100, $pay_log['money'] * 100, false);
                break;
            case '102':
                $res = m('finance')->refund($pay_log['openid'], $pay_log['logno'], $out_trade_no, $pay_log['money'] * 100, $pay_log['money'] * 100, false);
                break;
        }
        if (is_error($res)){
            return $res;
        }
        $refunduser = 0;
        if (isset($_W['cashieruser']['operator'])){
            $refunduser = $_W['cashieruser']['operator']['id'];
        }
        pdo_update("ewei_shop_cashier_pay_log",array('status'=>-1,'refundsn'=>$out_trade_no,'refunduser'=>$refunduser),array('uniacid'=>$_W['uniacid'],'id'=>$id));
        if (com('coupon') && !empty($pay_log['usecoupon'])) {
            com('coupon')->returnConsumeCoupon($pay_log['usecoupon']); 
        }
        if (!empty($pay_log['present_credit1'])){
            m('member')->setCredit($pay_log['openid'],'credit1',-$pay_log['present_credit1'],$_W['cashieruser']['title'].' 收银台退款收回赠送的积分! 退款订单号'.$out_trade_no);
        }
        if (!empty($pay_log['deduction']) && $pay_log['paytype'] !=2){
            m('member')->setCredit($pay_log['openid'],'credit2',$pay_log['deduction'],$_W['cashieruser']['title'].$_W['cashieruser']['title'].' 收银台退款返还余额抵扣! 退款订单号'.$out_trade_no);
        }
        if (!empty($pay_log['orderid'])){
            pdo_update("ewei_shop_order",array('status'=>-1),array('uniacid'=>$_W['uniacid'],'id'=>$pay_log['orderid']));
            $this->refundbackGoodsStocks(1,$pay_log);
        }
        if($pay_log['isgoods']==1){
            $goodsinfo = pdo_getall('ewei_shop_cashier_pay_log_goods',array('logid'=>$pay_log['id']),array('goodsid','total'));
            $this->refundbackGoodsStocks(2,$goodsinfo);
        }
        return $res;
    }

    public function refundbackGoodsStocks($status,$info=array()){
        global $_W;
        if($status ==1){
            $orderid = $info['orderid'];
            $order = pdo_fetch('select id,ordersn,price,openid,dispatchtype,addressid,carrier,status,isparent,paytype,isnewstore,storeid,istrade,status from ' . tablename('ewei_shop_order') . ' where id=:id limit 1', array(':id' => $orderid));
            $condition = " og.orderid=:orderid";
            $param = array();
            $param[':orderid'] = $orderid;
            $param[':uniacid'] = $_W['uniacid'];
            $goods = pdo_fetchall("select og.goodsid,og.seckill,og.total,g.totalcnf,og.realprice,g.credit,og.optionid,g.total as goodstotal,og.optionid,g.sales,g.salesreal,g.type from " . tablename('ewei_shop_order_goods') . " og "
                . " left join " . tablename('ewei_shop_goods') . " g on g.id=og.goodsid "
                . " where $condition and og.uniacid=:uniacid ", $param);
            foreach ($goods as $g) {
                    $goods_item = pdo_fetch("select total as goodstotal from" . tablename('ewei_shop_goods') . " where id=:id and uniacid=:uniacid limit 1", array(":id"=>$g['goodsid'],':uniacid'=>$_W['uniacid']));
                    $g['goodstotal'] = $goods_item['goodstotal'];
                $stocktype = 1; 
                if (!empty($stocktype)) {
                    if (!empty($g['optionid'])) {
                        $option = m('goods')->getOption($g['goodsid'], $g['optionid']);
                        if (!empty($option) && $option['stock'] != -1) {
                            if ($stocktype == 1) {
                                $stock = $option['stock'] + $g['total'];
                            }
                                pdo_update('ewei_shop_goods_option', array('stock' => $stock), array('uniacid' => $_W['uniacid'], 'goodsid' => $g['goodsid'], 'id' => $g['optionid']));

                        }
                    }
                    if (($stocktype ==1) && $g['goodstotal'] != -1) {
                        if ($stocktype == 1) {
                            $totalstock = $g['goodstotal'] + $g['total'];
                        }
                        if ($totalstock != -1) {
                                pdo_update('ewei_shop_goods', array('total' => $totalstock), array('uniacid' => $_W['uniacid'], 'id' => $g['goodsid']));
                        }
                    }
                }
            }
        }
        if($status ==2){
            if(!empty($info)){
                foreach($info as $key=>$value){
                    $good = pdo_get('ewei_shop_cashier_goods',array('id'=>$value['goodsid']));
                    if($good['total'] != -1){
                        $totalstock = $good['total']+$value['total'];
                        pdo_update('ewei_shop_cashier_goods', array('total' => $totalstock), array('id' => $value['goodsid']));
                    }
                }
            }
        }
    }




    public function updateOrder($log)
    {
        global $_W;
        if (!empty($log['status'])){
            return (int)$log['id'];
        }
        $realmoney = floatval($log['money']);
        $user = $this->userInfo($log['cashierid']);
        if ($log['paytype']!='101' && $log['paytype']!='102'){
            if (empty($log['paytype'])){
                $wechat = $this->wechayPayInfo($user);
                $res = m('common')->wechat_order_query($log['logno'],0,$wechat);
            }elseif ($log['paytype'] == '1'){
                $alipay = json_decode($user['alipay'],true);
                $res = m('common')->AliPayQuery($log['logno'],$alipay);
            }
        }else{
            list($set,$payment) = m('common')->public_build();

            if ($payment['is_new'] == 1){
                if ($payment['type'] == 4){
                    $res = m('pay')->query($log['logno'],$payment);
                }else{
                    if ($payment['type'] == 0 || $payment['type'] == 2){
                        $payment['appid'] = $payment['sub_appid'];
                        $payment['mch_id'] = $payment['sub_mch_id'];
                        unset($payment['sub_mch_id']);
                    }
                    $res = m('common')->wechat_order_query($log['logno'],0,$payment);
                }
            }else{
                if (isset($set) && $set['weixin'] == 1) {
                    load()->model('payment');
                    $setting = uni_setting($_W['uniacid'], array('payment'));
                    $account = pdo_get('account_wechats',array('uniacid'=>$_W['uniacid']),array('key','secret'));
                    if (is_array($setting['payment'])) {
                        $options = $setting['payment']['wechat'];
                        $options['appid'] = $account['key'];
                        $options['secret'] = $account['secret'];

                        if (IMS_VERSION<=0.8){
                            $options['apikey'] = $options['signkey'];
                        }
                        $wechat = array(
                            'appid' => $options['appid'],
                            'mch_id' => $options['mchid'],
                            'apikey' => $options['apikey']
                        );
                        $res = m('common')->wechat_order_query($log['logno'],0,$wechat);
                    }
                }elseif (isset($set) && $set['weixin_sub'] == 1){
                    $sec = m('common')->getSec();
                    $sec =iunserializer($sec['sec']);
                    $wechat = array(
                        'appid'=>$sec['appid_sub'],
                        'mch_id'=>$sec['mchid_sub'],
                        'sub_appid'=>!empty($sec['sub_appid_sub']) ? $sec['sub_appid_sub'] : '',
                        'sub_mch_id'=>$sec['sub_mchid_sub'],
                        'apikey' => $sec['apikey_sub']
                    );
                    $res = m('common')->wechat_order_query($log['logno'],0,$wechat);
                }
                if (empty($res)){
                    $sec = m('common')->getSec();
                    $sec =iunserializer($sec['sec']);
                    if (isset($set) && $set['weixin_jie'] == 1) {
                        $wechat = array(
                            'appid' => $sec['appid'],
                            'mch_id' => $sec['mchid'],
                            'apikey' => $sec['apikey']
                        );
                        $res = m('common')->wechat_order_query($log['logno'],0,$wechat);
                    }elseif (isset($set) && $set['weixin_jie_sub'] == 1) {
                        $wechat = array(
                            'appid'=>$sec['appid_jie_sub'],
                            'mch_id'=>$sec['mchid_jie_sub'],
                            'sub_appid'=>!empty($sec['sub_appid_jie_sub']) ? $sec['sub_appid_jie_sub'] : '',
                            'sub_mch_id'=>$sec['sub_mchid_jie_sub'],
                            'apikey' => $sec['apikey_jie_sub']
                        );
                        $res = m('common')->wechat_order_query($log['logno'],0,$wechat);
                    }
                }
            }
        }
        if (empty($res)){
            return false;
        }
        if ($res['trade_state'] == 'REFUND' || $res['trade_state'] == 'CLOSED' || $res['message'] == '该订单已经关闭或者已经退款'){
            pdo_update("ewei_shop_cashier_pay_log",array('status'=>-1),array('uniacid'=>$_W['uniacid'],'id'=>$log['id']));
            return false;
        }

        if($res['trade_state'] =='NOTPAY'){
            return false;
        }

        if ($res['total_fee'] == round($realmoney*100,2) || $res['total_amount'] == round($realmoney,2)){
            $log = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_cashier_pay_log') . ' WHERE `id`=:id and `uniacid`=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $log['id']));
            if (empty($log['status'])){
                $res['openid'] = isset($res['openid']) ? $res['openid'] : '';
                $log['openid'] = empty($log['openid'])?$res['openid']:$log['openid'];

                $this->paySuccess($log,$user);
            }
            return (int)$log['id'];
        }
    }

    public function paySuccess($log,$user)
    {
        global $_W;
        $coupon = 0;
        if (!empty($log['openid'])){
            if ($user['isopen_commission']){
                $this->becomeDown($log['openid'],$user['manageopenid']);
            }
            $this->sendMessage(array(
                'logno'=>$log['logno'],
                'money'=>$log['money'],
                'deduction'=>$log['deduction'],
                'paytime'=>time(),
                'cashier_title'=>$user['title']
            ),self::PAY_CASHIER_USER,$log['openid']);
            $coupon = $this->seedCoupon($log['money']+$log['deduction'],$log['openid']);
        }
        pdo_update('ewei_shop_cashier_pay_log', array('openid'=>$log['openid'],'payopenid'=>$log['openid'],'money'=>$log['money'],'status'=>1,'paytime'=>$log['paytime']>0 ? $log['paytime'] : time(),'coupon'=>$coupon), array('id' => $log['id']));
        $log['deduction'] = (float)$log['deduction'];
        if (!empty($log['deduction']) && $log['paytype'] != 2){
            $userinfo = m('member')->getMobileMember($log['mobile']);
            m('member')->setCredit($userinfo['openid'],'credit2',-$log['deduction'],array(0,'收银台 '.$_W['cashieruser']['title'],'收款'));
        }

        if (empty($user['notice_openids'])){
            $operator = false;
            if (!empty($log['operatorid'])){
                $operator = pdo_fetch('select * from ' . tablename('ewei_shop_cashier_operator') . ' WHERE id=:id AND cashierid=:cashierid limit 1', array(':id' => $log['operatorid'], ':cashierid' => $log['cashierid']));
            }
            if ($operator){
                $user['manageopenid'] = $operator['manageopenid'];
            }
        }else{
            $user['manageopenid'] =  $user['notice_openids'];
        }

        if (!empty($log['orderid'])){
            if($log['paytype'] == 3){
                $paytype = $log['paytype'];
            }else{
                $paytype = 1;
            }

            if (empty($log['paytype']) || $log['paytype']=='101'){
                $paytype = 21;
            }
            if ($log['paytype']=='1'){
                $paytype = 22;
            }
            pdo_update('ewei_shop_order',array('status'=>3,'paytype'=>$paytype,'paytime'=>time(),'sendtime'=>time(),'finishtime'=>time()),array('id'=>$log['orderid'],'uniacid'=>$_W['uniacid']));
            $this->setStocks($log['orderid'],$user['manageopenid']);
            if (p('commission')) {
                p('commission')->checkOrderPay($log['orderid']);
                p('commission')->checkOrderFinish($log['orderid']);
            }
        }
        if (!empty($log['isgoods'])){
            $this->setSelfGoodsStocks($log['id'],$user['manageopenid']);
        }

        $this->sendMessage(array(
            'logno'=>$log['logno'],
            'money'=>$log['money'],
            'deduction'=>$log['deduction'],
            'paytime'=>time(),
            'cashier_title'=>$user['title']
        ),self::PAY_CASHIER,$user['manageopenid']);
        $userset = json_decode($user['set'],true);

        if (!empty($log['openid'])){
            $present_credit1 = $this->sendCredit1($log,$userset);
            if ($present_credit1>0){
                pdo_update('ewei_shop_cashier_pay_log',array('present_credit1'=>$present_credit1),array('id'=>$log['id']));
            }
        }

        if ((!empty($log['isgoods']) || !empty($log['orderid'])) && !empty($userset['printer_status']) && !empty($userset['printer']) && !empty($userset['printer_template'])){
            com_run('printer::sendCashierMessage',$log,$userset['printer_template'],$userset['printer'],$operator);
        }elseif (!empty($userset['printer_status']) && !empty($userset['printer']) && !empty($userset['printer_template_default'])){
            com_run('printer::sendCashier',$log,$userset['printer_template_default'],$userset['printer'],$operator);
        }
    }

    public function paytype($paytype = 0,$auto_code=null)
    {
        global $_W;

        if (is_null($paytype) && $auto_code !== null && substr($auto_code,0,2) == '28'){
            return error(-101,'暂时不支持支付宝支付!');
        }
        if ($paytype == -1 && $auto_code !== null){
            $wechat = array(10,11,12,13,14,15);
            $alipay = array(28);

            $type = substr($auto_code,0,2);

            if (in_array($type,$alipay)){
                list(,$payment) = m('common')->public_build();
                if ($payment['is_new']==1 && $payment['type'] == 4){
                    $paytype = 102;
                }
                else
                {
                    $paytype = 1;
                }
                if (empty($_W['cashieruser']['alipay_status']) && $paytype != 102){
                    return error(-101,'暂时不支持支付宝支付!');
                }
            }elseif(in_array($type,$wechat) || is_weixin()){
                $paytype = 0;
            }

        }
        if (empty($paytype) && !empty($_W['cashieruser']['wechat_status'])){
            $paytype = 0;
        }elseif ($paytype == '1'){
            $paytype = 1;
        }elseif ($paytype == '2'){
            $paytype = 2;
        }elseif ($paytype == '3'){
            $paytype = 3;
        }elseif ($paytype == '102'){
            $paytype = 102;
        }else{
            $paytype = 101;
        }

        return $paytype;
    }

    public function getrandommoney($money = 0)
    {
        global $_W;
        $userset= $this->getUserSet('',$_W['cashierid']);
        if (isset($userset['randtime']) && !$this->sale_time($userset['randtime'])){
            return 0;
        }
        $probability = $userset['rand'];
        if (empty($probability)){
            return 0;
        }
        if (!empty($probability['minmoney']) && floatval($probability['minmoney']) > $money){
            return 0;
        }
        $sum = 0;
        for ($i=0; $i < count($probability['rand']); $i++) {
            $sum += $probability['rand'][$i];

            $rand_num = rand(1,100);

            if ($rand_num <= $sum) {
                $loop = $i;
                break;
            }
        }
        $min = (float)$probability['rand_left'][$loop] * 100;
        $max = (float)$probability['rand_right'][$loop] * 100;
        return rand($min,$max)/100;
    }


    public function becomeDown($openid,$manageopenid)
    {
        $member = m('member')->getMember($openid);
        if ($member){
            if (empty($member['isagent'])&&empty($member['agentid'])){
                $store_member = m('member')->getMember($manageopenid);
                pdo_query("UPDATE ".tablename('ewei_shop_member')." SET agentid={$store_member['id']} WHERE `id` = :id",array(':id'=>$member['id']));
            }
        }
    }


    public function getTodayOrder()
    {
        $starttime = strtotime(date("Y-m-d"));
        $endtime = time();
        return $this->getOrderMoney($starttime,$endtime);
    }


    public function getWeekOrder()
    {
        $starttime = strtotime(date("Y-m-d")) - (date("w")-1) * 3600*24;
        $endtime = time();
        return $this->getOrderMoney($starttime,$endtime);
    }

    public function getMonthOrder()
    {
        $starttime = strtotime(date("Y-m")."-1");
        $endtime = time();
        return $this->getOrderMoney($starttime,$endtime);
    }

    public function getOrderMoney($starttime = 0,$endtime = 0)
    {
        global $_W;
        if ($starttime == 0 && $endtime==0){
            $money = (float)pdo_fetchcolumn("SELECT SUM(money+deduction) FROM " .tablename('ewei_shop_cashier_pay_log')." WHERE uniacid=:uniacid AND status=1 AND cashierid=:cashierid ",array(":uniacid"=>$_W['uniacid'],":cashierid"=>$_W['cashierid']));
        }else{
            $money = (float)pdo_fetchcolumn("SELECT SUM(money+deduction) FROM " .tablename('ewei_shop_cashier_pay_log')." WHERE uniacid=:uniacid AND status=1 AND cashierid=:cashierid AND createtime BETWEEN :starttime AND :endtime",array(":uniacid"=>$_W['uniacid'],":cashierid"=>$_W['cashierid'],":starttime"=>$starttime,":endtime"=>$endtime));
        }
        return $money;
    }

    public function getEnoughs($price = 0) {

        global $_W;
        $set = $this->getUserSet('',$_W['cashierid']);

        if (isset($set['enoughtime']) && !$this->sale_time($set['enoughtime'])){
            return array(
                'old_price' => $price,
                'new_price' => $price,
                'enough' => 0,
                'money' => 0,
            );
        }

        $allenoughs = array();
        $enoughs = $set['enoughs'];
        if (floatval($set['enoughmoney']) > 0 && floatval($set['enoughdeduct']) > 0) {
            $allenoughs[] = array('enough' => floatval($set['enoughmoney']), 'money' => floatval($set['enoughdeduct']));
        }
        if (is_array($enoughs)) {
            foreach ($enoughs as $e) {
                if (floatval($e['enough']) > 0 && floatval($e['give']) > 0) {
                    $allenoughs[] = array('enough' => floatval($e['enough']), 'money' => floatval($e['give']));
                }
            }
        }
        usort($allenoughs, "sort_cashier");
        $new_price = $price;
        $enough = 0;
        $money = 0;
        foreach ($allenoughs as $key=>$value){
            if ($price >= $value['enough'] && $value > 0){
                $money = $value['money'];

                if($money>$price)
                {
                    $money = $price;
                }


                $new_price = $price - $money;
                $enough = $value['enough'];

                break;
            }
        }
        return array(
            'old_price' => $price,
            'new_price' => round($new_price,2),
            'enough' => $enough,
            'money' => $money,
        );
    }

    public function is_perm($text)
    {
        global $_W;
        if (isset($_W['cashieruser']['operator'])){
            $perm = json_decode($_W['cashieruser']['operator']['perm'],true);
            $routes = explode('.',$text);
            if (!in_array($routes[0],$perm)){
                return false;
            }
        }
        return true;
    }


    public function getDiscount($price = 0)
    {
        global $_W;
        $set = $this->getUserSet('',$_W['cashierid']);
        $set['discount'] = floatval($set['discount']);
        $price = floatval($price);

        if (empty($price) || empty($set['discount']) || (isset($set['discounttime']) && !$this->sale_time($set['discounttime']))){
            return array(
                'old_price' => $price,
                'new_price' => $price,
                'discount' => 0,
                'money' => 0,
            );
        }

        $new_price = 0;
        if (!empty($set['discount'])){
            $new_price = $set['discount'] * $price / 10;
        }

        return array(
            'old_price' => $price,
            'new_price' => round($new_price,2),
            'discount' => $set['discount'],
            'money' => round($price - $new_price,2),
        );
    }

    public function sale_time($sale_time)
    {
        if (!empty($sale_time) && $sale_time['start'] <= time() && $sale_time['end'] >= time())
        {
            if (empty($sale_time['start1'])){
                return true;
            }
            $hour = idate('H');
            $minute = idate('i');
            $return = false;
            foreach ($sale_time['start1'] as $key=>$value){
                if ($sale_time['start1'][$key]==$hour && $sale_time['start2'][$key]<=$minute){
                    if (($sale_time['end1'][$key]==$hour && $sale_time['end2'][$key]>=$minute) || $sale_time['end1'][$key]>$hour){
                        $return = true;
                    }
                }
                if ($sale_time['start1'][$key]<$hour){
                    if (($sale_time['end1'][$key]==$hour && $sale_time['end2'][$key]>=$minute) || $sale_time['end1'][$key]>$hour){
                        $return = true;
                    }
                }
            }
            return $return;
        }
        return false;
    }


    public function saleCalculate($money = 0,$random=true)
    {
        global $_W,$_GPC;
        $randommoney = 0;
        if ($random){
            $randommoney_log = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_cashier_randommoney_log') . " WHERE uniacid = :uniacid AND cashierid = :cashierid AND clientip = :clientip and ordermoney=:ordermoney limit 1", array(':uniacid' => $_W['uniacid'], ':cashierid' =>$_W['cashierid'],':clientip'=>ip2long(CLIENT_IP),':ordermoney'=>$money));
            $time=time();
            if(empty($randommoney_log) || $randommoney_log['expires_time']<$time){
                $randommoney = $this->getrandommoney($money);
                $data=array(
                    'uniacid'=>$_W['uniacid'],
                    'cashierid'=>$_W['cashierid'],
                    'clientip'=>ip2long(CLIENT_IP),
                    'ordermoney'=>$money,
                    'randommoney'=>$randommoney,
                    'expires_time'=>$time + 1800
                );
                pdo_insert('ewei_shop_cashier_randommoney_log', $data);
            }else{
                $randommoney=$randommoney_log['randommoney'];
            }
        }

        if($randommoney>$money)
        {
            $randommoney=$money;
        }

        $realmoney = round($money - $randommoney,2);
        $enoughs = $this->getEnoughs($realmoney);
        $discount = $this->getDiscount($enoughs['new_price']);

        return array(
            'randommoney' => $randommoney,
            'enough' => $enoughs,
            'discount' => $discount,
            'money' => $discount['new_price']
        );
    }


    public function createGoodsOrder($goods,$openid='',$setmoney=null)
    {
        global $_W,$_GPC;

        $allgoods = array();
        $realmoney = 0; 
        $goodsprice = 0; 

        foreach ($goods as $g) {
            if (empty($g)) {
                continue;
            }

            $goodsid = intval($g['goodsid']);
            $optionid = intval($g['optionid']);
            $goodstotal = intval($g['total']);
            if ($goodstotal < 1) {
                $goodstotal = 1;
            }
            if (empty($goodsid)) {
                show_json(0, '参数错误,未选择商品!');
            }
            $sql = 'SELECT id as goodsid,title,type, weight,total,issendfree,isnodiscount, thumb,marketprice,cash,isverify,verifytype,'
                . ' goodssn,productsn,sales,istime,timestart,timeend,hasoption,'
                . ' usermaxbuy,minbuy,maxbuy,unit,buylevels,buygroups,deleted,'
                . ' status,deduct,manydeduct,`virtual`,discounts,deduct2,ednum,edmoney,edareas,diyformtype,diyformid,diymode,'
                . ' dispatchtype,dispatchid,dispatchprice,merchid,merchsale,cates,'
                . ' isdiscount,isdiscount_time,isdiscount_discounts, virtualsend,'
                . ' buyagain,buyagain_islong,buyagain_condition, buyagain_sale'
                . ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid and cashier=1 limit 1';
            $data = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':id' => $goodsid));

            if (!empty($data['hasoption'])) {
                $opdata = m('goods')->getOption($data['goodsid'], $optionid);
                if (empty($opdata) || empty($optionid)) {
                    show_json(0, '商品' . $data['title'] . '的规格不存在,请到购物车删除该商品重新选择规格!');
                }
            }

            $merchid = $data['merchid'];
            $merch_array[$merchid]['goods'][] = $data['goodsid'];

            $data['stock'] = $data['total'];
            $data['total'] = $goodstotal;

            if (!empty($optionid)) {
                $option = pdo_fetch('select id,title,marketprice,goodssn,productsn,stock,`virtual`,weight from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':goodsid' => $goodsid, ':id' => $optionid));
                if (!empty($option)) {
                    if ($option['stock'] != -1) {
                        if (empty($option['stock'])) {
                            show_json(-1, $data['title'] . "<br/>" . $option['title'] . " 库存不足!");
                        }
                    }
                    $data['optionid'] = $optionid;
                    $data['optiontitle'] = $option['title'];
                    $data['marketprice'] = $option['marketprice'];

                    if (!empty($option['goodssn'])) {
                        $data['goodssn'] = $option['goodssn'];
                    }
                    if (!empty($option['productsn'])) {
                        $data['productsn'] = $option['productsn'];
                    }
                    if (!empty($option['weight'])) {
                        $data['weight'] = $option['weight'];
                    }
                }
            }
            $data['marketprice'] = isset($g['marketprice']) ? floatval($g['marketprice']) : $data['marketprice'];
            $gprice = $data['marketprice'] * $goodstotal;

            $diyprice = isset($g['price']) ? floatval($g['price']) : $data['marketprice'];

            $data['ggprice'] = $gprice;

            $data['realprice'] = $diyprice*$goodstotal;

            $realmoney +=  $data['realprice'];
            $goodsprice += $gprice;

            $allgoods[] = $data;
        }

        $ismerch = 0;
        if ($_W['cashieruser']['merchid'] > 0){
            $ismerch = 1;
        }
        if ($ismerch > 0) {
            $ordersn = m('common')->createNO('order', 'ordersn', 'ME');
        } else {
            $ordersn = m('common')->createNO('order', 'ordersn', 'SH');
        }
        $order = array();
        $order['ismerch'] = $ismerch;
        $order['parentid'] = 0;
        $order['uniacid'] = $_W['uniacid'];
        $order['openid'] = $openid;
        $order['ordersn'] = $ordersn;
        $order['price'] = $setmoney === null ? $realmoney : (float)$setmoney;
        $order['oldprice'] = $goodsprice;
        $order['grprice'] = $goodsprice; 
        $order['cash'] = 0;
        $order['status'] = 0;
        $order['remark'] = trim($_GPC['remark']);
        $order['addressid'] = 0;
        $order['goodsprice'] = $goodsprice;
        $order['storeid'] = 0;
        $order['createtime'] = time();
        $order['paytype'] = 0; 
        $order['merchshow'] = 0;

        $order['merchid'] = intval($_W['cashieruser']['merchid']);
        $order['isparent'] = 0;
        $order['transid'] = '';
        $order['is_cashier'] = 1;
        pdo_insert('ewei_shop_order', $order);
        $orderid = pdo_insertid();
        foreach ($allgoods as $goods) {
            $order_goods = array();
            $order_goods['merchid'] = $goods['merchid'];
            $order_goods['uniacid'] = $_W['uniacid'];
            $order_goods['orderid'] = $orderid;
            $order_goods['goodsid'] = $goods['goodsid'];
            $order_goods['price'] = $goods['marketprice'] * $goods['total'];
            $order_goods['total'] = $goods['total'];
            $order_goods['optionid'] = $goods['optionid'];

            $order_goods['createtime'] = time();
            $order_goods['optionname'] = $goods['optiontitle'];
            $order_goods['goodssn'] = $goods['goodssn'];
            $order_goods['productsn'] = $goods['productsn'];
            $order_goods['realprice'] = $goods['realprice'];
            $order_goods['oldprice'] = $goods['ggprice'];
            $order_goods['openid'] = $openid;
            pdo_insert('ewei_shop_order_goods', $order_goods);
        }

        $pluginc = p('commission');
        if ($pluginc) {
            $pluginc->checkOrderConfirm($orderid);
        }
        $order['id'] = $orderid;
        return $order;
    }

    public function goodsCalculate($selfgoods=array(),$shopgoods=array(),$params,$return=false)
    {
        global $_W;
        $order = $this->createOrder(array(
            'paytype'=>$params['paytype'],
            'openid'=>isset($params['openid']) ? $params['openid'] : '',
            'money'=>(float)$params['money'],
            'couponpay'=>(float)$params['couponpay'],
            'nosalemoney'=>(float)$params['nosalemoney'],
            'operatorid'=>(int)$params['operatorid'],
            'deduction'=>(float)$params['deduction'],
            'mobile'=>(int)$params['mobile'],
            'title'=>$params['title'],
        ),1,true);
        if (!empty($selfgoods)){
            foreach ($selfgoods as $key=>$val){
                $order['goodsprice'] += $val['price']*$val['total'];
                $order['isgoods'] = 1;
                $data = array(
                    'cashierid'=>$order['cashierid'],
                    'logid'=>$order['id'],
                    'goodsid'=>$val['goodsid'],
                    'price'=>$val['price']*$val['total'],
                    'total'=>$val['total'],
                );
                $g = pdo_get('ewei_shop_cashier_goods',array('cashierid'=>$data['cashierid'],'id'=>$data['goodsid']));
                if ($data['total']>$g['total'] && $g['total'] != -1){
                    pdo_delete('ewei_shop_cashier_pay_log_goods',array('cashierid'=>$data['cashierid'],'logid'=>$data['logid']));
                    return array('res'=>error('-101',$g['title']." 库存不足, 剩余库存 ".$g['total']));
                }
                pdo_insert('ewei_shop_cashier_pay_log_goods',$data);
            }
        }
        if (!empty($shopgoods)){
            $goods = array();
            foreach ($shopgoods as $val){
                $goods[] = array(
                    'goodsid' =>$val['goodsid'],
                    'optionid' =>$val['optionid'],
                    'price' =>$val['price'],
                    'total' =>$val['total'],
                    'marketprice' =>isset($val['marketprice']) ? $val['marketprice'] : null,
                );
            }
            $setmoney = $order['money'] + $order['deduction'];
            if ($params['paytype'] == 3 && !empty($_W['cashieruser']['merchid'])){
                $setmoney = 0;
            }
            $goodsOrder = $this->createGoodsOrder($shopgoods,$params['openid'],$setmoney);
            $order['orderid'] = $goodsOrder['id'];
            $order['orderprice'] = $goodsOrder['price'];
            $order['goodsprice'] = isset($order['goodsprice']) ? $order['goodsprice'] : 0;
            $order['goodsprice'] = $goodsOrder['goodsprice'];
        }
        pdo_update('ewei_shop_cashier_pay_log',$order,array('id'=>$order['id']));
        if ($return){
            return $order;
        }
        return $this->pay($order,$params['auth_code']);
    }

    public function setStocks($orderid = 0,$manageopenid='')
    {
        global $_W;
        $goodstotal = intval($_W['shopset']['shop']['goodstotal']);

        if ($orderid==0){
            return false;
        }
        $order = pdo_fetch('select id,ordersn,price,openid,dispatchtype,addressid,carrier,status,isparent from ' . tablename('ewei_shop_order') . ' where id=:id limit 1', array(':id' => $orderid));

        $param = array();
        $param[':uniacid'] = $_W['uniacid'];

        if ($order['isparent'] == 1) {
            $condition = " og.parentorderid=:parentorderid";
            $param[':parentorderid'] = $orderid;
        } else {
            $condition = " og.orderid=:orderid";
            $param[':orderid'] = $orderid;
        }

        $goods = pdo_fetchall("select og.goodsid,og.total,g.totalcnf,og.realprice,g.credit,og.optionid,g.total as goodstotal,og.optionid,g.sales,g.salesreal,g.title from " . tablename('ewei_shop_order_goods') . " og "
            . " left join " . tablename('ewei_shop_goods') . " g on g.id=og.goodsid "
            . " where $condition and og.uniacid=:uniacid ", $param);

        foreach ($goods as $g) {
            $stocktype = -1; 
            if (!empty($stocktype)) {

                if (!empty($g['optionid'])) {
                    $option = m('goods')->getOption($g['goodsid'], $g['optionid']);

                    if (!empty($option) && $option['stock'] != -1) {
                        $stock = -1;
                        if ($stocktype == 1) {
                            $stock = $option['stock'] + $g['total'];
                        } else if ($stocktype == -1) {
                            $stock = $option['stock'] - $g['total'];
                            $stock <= 0 && $stock = 0;
                        }
                        if ($stock != -1) {
                            pdo_update('ewei_shop_goods_option', array('stock' => $stock), array('uniacid' => $_W['uniacid'], 'goodsid' => $g['goodsid'], 'id' => $g['optionid']));
                            if($manageopenid !='' && $goodstotal !=0 && $stock<$goodstotal){
                                $msg = array(
                                    'first' => array('value' => "您的商品库存已经不足".$goodstotal."，请及时补货！\n", "color" => "#ff0000"),
                                    'keyword1' => array('title' => '任务名称', 'value' => '商品库存不足', "color" => "#000000"),
                                    'keyword2' => array('title' => '通知类型', 'value' => '请及时补货', "color" => "#4b9528"),
                                    'remark' => array('value' => "商品名称：".$g['title'].'-'.$option['title'], "color" => "#000000")
                                );
                                $this->sendNotice(array('openid'=>$manageopenid,'saler_stockwarn'=>'saler_stockwarn_template'), 'saler_stockwarn', $msg);
                            }

                        }
                    }
                }
                if (!empty($g['goodstotal']) && $g['goodstotal'] != -1) {
                    $totalstock = -1;
                    if ($stocktype == 1) {
                        $totalstock = $g['goodstotal'] + $g['total'];
                    } else if ($stocktype == -1) {
                        $totalstock = $g['goodstotal'] - $g['total'];
                        $totalstock <= 0 && $totalstock = 0;
                    }
                    if ($totalstock != -1) {
                        pdo_update('ewei_shop_goods', array('total' => $totalstock), array('uniacid' => $_W['uniacid'], 'id' => $g['goodsid']));
                        if($manageopenid!='' && empty($g['optionid']) && $goodstotal!=0 && $totalstock<$goodstotal){

                            $msg = array(
                                'first' => array('value' => "您的商品库存已经不足".$goodstotal."，请及时补货！\n", "color" => "#ff0000"),
                                'keyword1' => array('title' => '任务名称', 'value' => '商品库存不足', "color" => "#000000"),
                                'keyword2' => array('title' => '通知类型', 'value' => '请及时补货', "color" => "#4b9528"),
                                'remark' => array('value' => "商品名称：".$g['title'], "color" => "#000000")
                            );
                            $this->sendNotice(array('openid'=>$manageopenid,'saler_stockwarn'=>'saler_stockwarn_template'), 'saler_stockwarn', $msg);
                        }
                    }
                }
            }

            if ($order['status'] >= 1) {
                if ($g['totalcnf'] != 1){
                    pdo_update('ewei_shop_goods', array('sales' => $g['sales'] - $g['total']), array('uniacid' => $_W['uniacid'], 'id' => $g['goodsid']));
                }
                $salesreal = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('ewei_shop_order_goods') . ' og '
                    . ' left join ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid '
                    . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':goodsid' => $g['goodsid'], ':uniacid' => $_W['uniacid']));
                pdo_update('ewei_shop_goods', array('salesreal' => $salesreal), array('id' => $g['goodsid']));
            }
        }
    }

    public function setSelfGoodsStocks($logid = 0,$manageopenid='')
    {
        global $_W;
        $goodstotal = intval($_W['shopset']['shop']['goodstotal']);

        if ($logid==0){
            return false;
        }

        $goods = pdo_fetchall("SELECT log.* ,g.total as goodstotal,g.title  FROM ".tablename('ewei_shop_cashier_pay_log_goods')."log"
            ." left join".tablename('ewei_shop_cashier_goods')."g on g.id=log.goodsid "
            . " WHERE log.cashierid=:cashierid AND log.logid=:logid",array(':cashierid'=>$_W['cashierid'],':logid'=>$logid));

        foreach ($goods as $g)
        {
            pdo_query("UPDATE ".tablename('ewei_shop_cashier_goods')." SET total=total-{$g['total']} WHERE cashierid=:cashierid AND id=:id AND total<>-1",array(':cashierid'=>$_W['cashierid'],':id'=>$g['goodsid']));
            if($manageopenid!='' && $goodstotal!=0 && ($g['goodstotal']-$g['total'])<$goodstotal){

                $msg = array(
                    'first' => array('value' => "您的商品库存已经不足".$goodstotal."，请及时补货！\n", "color" => "#ff0000"),
                    'keyword1' => array('title' => '任务名称', 'value' => '商品库存不足', "color" => "#000000"),
                    'keyword2' => array('title' => '通知类型', 'value' => '请及时补货', "color" => "#4b9528"),
                    'remark' => array('value' => "商品名称：".$g['title'], "color" => "#000000")
                );
                $this->sendNotice(array('openid'=>$manageopenid,'saler_stockwarn'=>'saler_stockwarn_template'), 'saler_stockwarn', $msg);
            }

        }
    }

    public function querycoupons($kwd=''){

        global $_W, $_GPC;
        $params = array();
        $params[':uniacid'] = $_W['uniacid'];
        $params[':merchid'] = intval($_W['cashieruser']['merchid']);
        $condition=" and uniacid=:uniacid and merchid=:merchid and (total=-1 OR total>0)";
        $condition1=" and uniacid=:uniacid and merchid=:merchid and status =1";

        if (empty($_W['cashieruser']['couponid'])){
            $list =  pdo_fetchall('SELECT couponid  FROM ' . tablename('ewei_shop_cashier_user') . " WHERE 1 {$condition1}", $params);
            if(empty($list[0]['couponid'])){
                return array();
            }else{
                $condition .= " AND id IN ({$list[0]['couponid']})";
            }
        }else{
            $condition .= " AND id IN ({$_W['cashieruser']['couponid']})";
        }


        if (!empty($kwd)) {
            $condition.=" AND `couponname` LIKE :keyword";
            $params[':keyword'] = "%{$kwd}%";
        }

        $ds = pdo_fetchall('SELECT id,couponname as title , thumb FROM ' . tablename('ewei_shop_coupon') . " WHERE 1 {$condition} order by createtime desc", $params);
        $ds = set_medias($ds, 'thumb');

        return $ds;
    }

    public function seedCoupon($price = 0,$member)
    {
        global $_W;
        if (empty($member)){
            return false;
        }
        if(is_string($member)){
            $member = m('member')->getMember($member);
        }
        $set = $this->getUserSet('',$_W['cashierid']);
        $price = floatval($price);

        if (empty($price) || empty($set['coupon']) || (isset($set['coupontime']) && !$this->sale_time($set['coupontime']))){
            return false;
        }

        if (empty($set['coupon']['couponid'])){
            return false;
        }
        if (!empty($set['coupon']['minmoney']) && floatval($set['coupon']['minmoney']) > $price){
            return false;
        }
        $coupon = com('coupon');
        if ($coupon){
            $data = $coupon->getCoupon($set['coupon']['couponid']);
            if ($data['total']=='-1' || $data['total']>0){
                $coupon->poster($member,$set['coupon']['couponid'],1,9);
                return $set['coupon']['couponid'];
            }
        }

        return false;
    }

    function caculatecoupon($couponid, $totalprice,$openid=null)
    {
        global $_W;

        $openid = is_null($openid) ? $_W['openid'] : $openid;
        $uniacid = $_W['uniacid'];

        $sql = 'SELECT d.id,d.couponid,c.enough,c.backtype,c.deduct,c.discount,c.backmoney,c.backcredit,c.backredpack,c.merchid,c.limitgoodtype,c.limitgoodcatetype,c.limitgoodids,c.limitgoodcateids,c.limitdiscounttype  FROM ' . tablename('ewei_shop_coupon_data') . " d";
        $sql .= " left join " . tablename('ewei_shop_coupon') . " c on d.couponid = c.id";
        $sql .= ' where d.id=:id and d.uniacid=:uniacid and d.openid=:openid and d.used=0  limit 1';
        $data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $couponid, ':openid' => $openid));

        if (empty($data)) {
            return false;
        }

        $data['enough'] = floatval($data['enough']);
        if (!empty($data['enough']) && $data['enough']>$totalprice){
            return false;
        }

        $deduct = ((float)$data['deduct']);
        $discount = ((float)$data['discount']);
        $backtype = ((float)$data['backtype']);

        $deductprice = 0;

        if ($deduct > 0 && $backtype == 0 && $totalprice > 0) {
            if ($deduct > $totalprice) {
                $deduct = $totalprice;
            }
            if ($deduct <= 0) {
                $deduct = 0;
            }
            $deductprice = $deduct;

        } else if ($discount > 0 && $backtype == 1) {
            $deductprice = round($totalprice * (1 - $discount / 10),2);
            if ($deductprice > $totalprice) {
                $deductprice = $totalprice;
            }
            if ($deductprice <= 0) {
                $deductprice = 0;
            }
        }

        $new_price = $totalprice - $deductprice;

        return array(
            'old_price' => $totalprice,
            'new_price' => round($new_price,2),
            'discount' => $discount,
            'money' => $deductprice,
        );
    }

    public function sendCredit1($log,$userset=null)
    {

        $credit1_double = 1;
        $price = $log['money'] + $log['deduction'];

        if (empty($userset['credit1']) || empty($price)){
            return 0;
        }

        if (!empty($userset['credit1_double'])){
            $credit1_double = $userset['credit1_double'];
        }
        $price = $price * $credit1_double;

        $credit1 = com_run('sale::getCredit1',$log['openid'],$price,37,1,0,$log['title'].'收银订单号 : '.$log['logno'].'  收银台消费送积分');
        if ($credit1>0)
        {
            m('notice')->sendMemberPointChange($log['openid'],$credit1,0,1);
        }
        return $credit1;
    }

    public function upload_cert($fileinput) {
        global $_W;
        $filename = $_FILES[$fileinput]['name'];
        $tmp_name = $_FILES[$fileinput]['tmp_name'];
        if (!empty($filename) && !empty($tmp_name)) {
            $ext = strtolower(substr($filename, strrpos($filename, '.')));
            if ($ext != '.pem') {
                $errinput = "";
                if ($fileinput == 'cert_file') {
                    $errinput = "CERT文件格式错误";
                } else if ($fileinput == 'key_file') {
                    $errinput = 'KEY文件格式错误';
                } else if ($fileinput == 'root_file') {
                    $errinput = 'ROOT文件格式错误';
                }
                show_json(0, $errinput . ',请重新上传!');
            }
            return file_get_contents($tmp_name);
        }
        return "";
    }


    public function refundWechat($openid, $out_trade_no, $out_refund_no, $totalmoney, $refundmoney=0, $app=false,$refund_account = false)
    {

        global $_W, $_GPC;

        if (empty($openid)) {
            return error(-1, 'openid不能为空');
        }

        $wechatpay = json_decode($_W['cashieruser']['wechatpay'],true);
        if (!is_array($wechatpay)) {
            return error(1, '没有设定支付参数');
        }

        $certs = array(
            'cert' => $wechatpay['cert'],
            'key' => $wechatpay['key'],
            'root' => $wechatpay['root']
        );

        if (empty($wechatpay['appid']) && empty($wechatpay['mch_id']) && !empty($wechatpay['sub_appid'])){
            $wechatpay['appid'] = $wechatpay['sub_appid'];
            $wechatpay['mch_id'] = $wechatpay['sub_mch_id'];
            unset($wechatpay['sub_mch_id'],$wechatpay['sub_appid']);
        }

        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $pars = array();
        $pars['appid'] = $wechatpay['appid'];
        $pars['mch_id'] = $wechatpay['mch_id'];
        if (!empty($wechatpay['sub_mch_id'])){
            $pars['sub_mch_id'] = $wechatpay['sub_mch_id'];
        }
        $pars['nonce_str'] = random(8);
        $pars['out_trade_no'] = $out_trade_no;
        $pars['out_refund_no'] = $out_refund_no;
        $pars['total_fee'] = $totalmoney;
        $pars['refund_fee'] = $refundmoney;
        $pars['op_user_id'] = $wechatpay['mch_id'];

        if ($refund_account){
            $pars['refund_account'] = $refund_account;
        }

        if (!empty($wechatpay['sub_appid'] )){
            $pars['sub_appid'] = $wechatpay['sub_appid'];
        }

        ksort($pars, SORT_STRING);
        $string1 = '';
        foreach ($pars as $k => $v) {

            $string1 .= "{$k}={$v}&";
        }
        $string1 .= "key=" . $wechatpay['apikey'];
        $pars['sign'] = strtoupper(md5($string1));

        $xml = array2xml($pars);
        $extras = array();

        $errmsg = "未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!";
        if (is_array($certs)) {

            if (empty($certs['cert']) || empty($certs['key']) || empty($certs['root'])) {
                if ($_W['ispost']) {
                    show_json(0, array('message' => $errmsg));
                }
                show_message($errmsg, '', 'error');
            }

            $certfile = IA_ROOT . "/addons/ewei_shopv2/cert/" . random(128);
            file_put_contents($certfile, $certs['cert']);
            $keyfile = IA_ROOT . "/addons/ewei_shopv2/cert/" . random(128);
            file_put_contents($keyfile, $certs['key']);
            $rootfile = IA_ROOT . "/addons/ewei_shopv2/cert/" . random(128);
            file_put_contents($rootfile, $certs['root']);

            $extras['CURLOPT_SSLCERT'] = $certfile;
            $extras['CURLOPT_SSLKEY'] = $keyfile;
            $extras['CURLOPT_CAINFO'] = $rootfile;
        } else {
            if ($_W['ispost']) {
                show_json(0, array('message' => $errmsg));
            }
            show_message($errmsg, '', 'error');
        }

        load()->func('communication');
        $resp = ihttp_request($url, $xml, $extras);


        @unlink($certfile);
        @unlink($keyfile);
        @unlink($rootfile);
        if (is_error($resp)) {
            return error(-2, $resp['message']);
        }
        if (empty($resp['content'])) {
            return error(-2, '网络错误');
        } else {
            libxml_disable_entity_loader(true);
            $arr = json_decode(json_encode(simplexml_load_string($resp['content'], 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            if ($arr['return_code'] == 'SUCCESS' && $arr['result_code'] == 'SUCCESS') {
                return true;
            } elseif ($arr['return_code'] == 'SUCCESS' && $arr['result_code'] == 'FAIL'&& $arr['return_msg'] == 'OK' && !$refund_account){
                if ($arr['err_code'] == 'NOTENOUGH'){
                    return $this->refundWechat($openid, $out_trade_no, $out_refund_no, $totalmoney, $refundmoney, $app,'REFUND_SOURCE_RECHARGE_FUNDS');
                }
            }

            if ($arr['return_msg'] == $arr['err_code_des']) {
                $error = $arr['return_msg'];
            } else {
                $error = $arr['return_msg']. " | " . $arr['err_code_des'];
            }
            return error(-2, $error);
        }
    }

    protected function replaceTemplate($str, $datas = array()) {
        foreach ($datas as $k => $d) {
            $str = str_replace($k, $d, $str);
        }
        return $str;
    }
}