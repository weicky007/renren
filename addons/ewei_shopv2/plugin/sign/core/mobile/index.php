<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Index_EweiShopV2Page extends PluginMobileLoginPage {

    public function main(){
        global $_W, $_GPC;
        session_start();
        $set = $this->model->getSet();
        if(!empty($set['sign_rule'])){
            $set['sign_rule'] = iunserializer($set['sign_rule']);
            $set['sign_rule'] =htmlspecialchars_decode($set['sign_rule']);
        }

        // 判断是否关闭签到
        if(empty($set['isopen'])){
            $this->message($set['textsign']. "未开启!", mobileUrl());
        }

        $month = $this->model->getMonth();

        //小程序用户标识
        $_SESSION['sign_xcx_isminiprogram'] = false;
        if(!empty($_GPC['uid'])){
            //  获取用户信息
            $member = m('member')->getMember($_GPC['uid']);
        }else{
            $member = m('member')->getMember($_W['openid']);
        }

        //判断是否是小程序用户
        if(strexists($member['openid'], 'sns_wa_')){
            $isminiprogram = true;
            $_SESSION['sign_xcx_openid'] = $member['openid'];
            $_SESSION['sign_xcx_isminiprogram'] = true;
            $_SESSION['sign_uid_'.$_SESSION['sign_xcx_openid']] = $_GPC['uid'];
        }else{
            $_SESSION['sign_xcx_openid'] = null;
            $_SESSION['sign_uid_'.$_SESSION['sign_xcx_openid']] = null;
            $_SESSION['sign_xcx_isminiprogram'] = false;
        }

        if(empty($member) || empty($_W['openid'])){
            $this->message("获取用户信息失败!", mobileUrl());
        }

        //  获取日历
        $calendar = $this->model->getCalendar();

        //  获取 签到信息
        $signinfo = $this->model->getSign();

        //  获取 高级奖励信息
        $advaward = $this->model->getAdvAward();

        $json_arr = array(
            'calendar'=>$calendar,
            'signinfo'=>$signinfo,
            'advaward'=>$advaward,
            'year'=>date('Y', time()),
            'month'=>date('m', time()),
            'today'=>date('d', time()),
            'signed'=>$signinfo['signed'],
            'signold'=>$set['signold'],
            'signoldprice'=>$set['signold_price'],
            'signoldtype'=>empty($set['signold_type']) ? $set['textmoney'] : $set['textcredit'],

            'textsign'=>$set['textsign'],
            'textsigned'=>$set['textsigned'],
            'textsignold'=>$set['textsignold'],
            'textsignforget'=>$set['textsignforget'],
        );
        $json = json_encode($json_arr);

        //  设置分享信息
        $this->model->setShare($set);

        $texts = array(
            'sign'=>$set['textsign'],
            'signed'=>$set['textsigned'],
            'signold'=>$set['textsignold'],
            'credit'=>$set['textcredit'],
            'color'=>$set['maincolor']
        );

        include $this->template();
    }

    public function getCalendar(){
        global $_W, $_GPC;
        session_start();
        if(!empty($_SESSION['sign_xcx_openid'])){
            $_W['openid'] = $_SESSION['sign_xcx_openid'];
        }
        $date = trim($_GPC['date']);
        $date = explode('-', $date);
        $calendar = $this->model->getCalendar($date[0], $date[1]);
        include $this->template('sign/calendar');
    }

    public function getAdvAward()
    {
        $set = $this->model->getSet();
        //  获取 高级奖励信息
        $advaward = $this->model->getAdvAward();
        include $this->template('sign/advaward');
    }




    public function dosign() {
        global $_W, $_GPC;
        session_start();
        if(!empty($_SESSION['sign_xcx_openid'])){
            $_W['openid'] = $_SESSION['sign_xcx_openid'];
        }

        if(!$_W['ispost'] || empty($_W['openid'])){
            show_json(0, "错误的请求!");
        }

        if (!is_error(redis())) {
            //redis 解决高并发签到问题
            $redis_key = "{$_W['uniacid']}_sign_refund_{$_W['openid']}";
            $redis = redis();
            if ($redis->setnx($redis_key, time())) {
                $redis->expireAt($redis_key, time() + 2);
            } else {
                if ($redis->get($redis_key)+2< time()) {
                    $redis -> del($redis_key);
                } else {
                    return false;
                }
            }
        }

        $set = $this->model->getSet();

        if(empty($set['isopen'])){
            show_json(0, $set['textcredit'].$set['textsign'] . "未开启!");
        }

        //  补签需传值 日期 格式2016-07-07
        $date = trim($_GPC['date']);
        $date = $date == 'null' ? '' : $date;

        // 严格验证传入日期
        if(!empty($date)) {
            $dates = date('Y-m-d', strtotime($date));
            $date_verify = date('Y-m-d', strtotime($date));
            if($date_verify != $dates) {
                show_json(0, "日期传入错误");
            }
        }
        
        //  获取 签到信息
        $signinfo = $this->model->getSign($date);

        if(!empty($date)){
            $datemonth = date('m', strtotime($date));
            $thismonth = date('m', time());
            if ($datemonth<$thismonth){
                show_json(0, $set['textsign'] . "月份小于当前月份!");
            }
        }

        // 判断今天是否 签到
        if(!empty($signinfo['signed'])){
            show_json(2, "已经" . $set['textsign'] . "，不要重复" . $set['textsign'] . "哦~");
        }

        if(!empty($date) && strtotime($date)>time()){
            show_json(0, $set['textsign'] . "日期大于当前日期!");
        }

        $member = m('member')->getMember($_W['openid']);

        $reword_special = iunserializer($set['reword_special']);
        $credit = 0;
        if(!empty($set['reward_default_day']) && $set['reward_default_day']>0){
            $credit = $set['reward_default_day'];
            $message = empty($date)?"日常" . $set['textsign'] . "+" : $set['textsignold'] . "+";
            $message .= $set['reward_default_day'].$set['textcredit'];
        }

        // 判断是否 首次签到
        if(!empty($set['reward_default_first']) && $set['reward_default_first']>0 && empty($signinfo['sum']) && empty($date)){
            $credit = $set['reward_default_first'];
            $message = "首次" . $set['textsign'] . "+".$set['reward_default_first'].$set['textcredit'];
        }

        // 判断特殊日期
        if(!empty($reword_special) && empty($date)){
            foreach ($reword_special as $item){
                $day = date('Y-m-d', $item['date']);
                $today = date('Y-m-d', time());
                if($day===$today && !empty($item['credit'])){
                    $credit = $credit + $item['credit'];
                    if(!empty($message)){
                        $message .= "\r\n";
                    }
                    $message .= empty($item['title'])?$today:$item['title'];
                    $message .= $set['textsign']."+" . $item['credit'] . $set['textcredit'];
                    break;
                }
            }
        }

        // 判断补签 扣除相应余额
        if(!empty($date) && !empty($set['signold']) && $set['signold_price']>0){
            if(empty($set['signold_type'])){
                if($member['credit2']<$set['signold_price']){
                    show_json(0, $set['textsignold'] . "失败! 您的" . $set['textmoney'] ."不足, 无法". $set['textsignold']);
                }
                //  执行扣除余额
                m('member')->setCredit($_W['openid'], 'credit2', -$set['signold_price'], $set['textcredit'] . $set['textsign'] . ": " . $set['textsignold'] . "扣除".$set['signold_price']. $set['textmoney']);
            }else{
                if($member['credit1']<$set['signold_price']){
                    show_json(0, $set['textsignold'] . "失败! 您的" . $set['textcredit'] ."不足, 无法". $set['textsignold']);
                }
                //  执行扣除积分
                m('member')->setCredit($_W['openid'], 'credit1', -$set['signold_price'], $set['textcredit'] . $set['textsign'] .": " . $set['textsignold'] . "扣除".$set['signold_price']. $set['textcredit']);
            }
        }

        // 判断是否有奖励
        if(!empty($credit) && $credit>0){
            // 执行增加积分
            m('member')->setCredit($_W['openid'], 'credit1', +$credit, $set['textcredit'] . $set['textsign'] . ": ".$message);
        }

        // 插入记录
        $arr = array(
            'uniacid'=>$_W['uniacid'],
            'time'=> empty($date) ? time() : strtotime($date),
            'openid'=>$_W['openid'],
            'credit'=>$credit,
            'log'=>$message
        );
        pdo_insert('ewei_shop_sign_records', $arr);
        $id = pdo_insertid();
        if($_SESSION['sign_xcx_isminiprogram']){
            $log_text = '小程序';
        }else{
            $log_text = '公众号';
        }
        //插入签到记录
        plog('sign', '签到ID:'.$id.'通过'.$log_text.'积分签到');
        //  获取 签到信息
        $signinfo = $this->model->getSign();
        $member = m('member')->getMember($_W['openid']);

        $result = array(
            'message'=>$set['textsign']. '成功!' . $message,
            'signorder'=>$signinfo['orderday'],
            'signsum'=>$signinfo['sum'],
            'addcredit'=>$credit,
            'credit'=>intval($member['credit1'])
        );

        //  更新
        $this->model->updateSign($signinfo);

        //抽奖模块
        if(p('lottery')){
            //type 1:消费 2:签到 3:任务 4:其他

            $res = p('lottery')->getLottery($member['openid'],2,array('day'=>$signinfo['orderday']));
            if($res){
                //发送模版消息
                p('lottery')->getLotteryList($member['openid'],array('lottery_id'=>$res));
            }
            //检测是否获得抽奖机会 【也可用 array('is_changes'=>1,'lottery'=>array('lottery_id'=>$res))】
            $result['lottery'] = p('lottery')->check_isreward();
            if($_SESSION['sign_xcx_isminiprogram']){
                $result['lottery']['is_changes'] = 0;
            }
        }else{
            $result['lottery']['is_changes'] = 0;
        }

        show_json(1,$result);
    }

    public function doreward() {
        global $_W, $_GPC;
        session_start();
        if(!empty($_SESSION['sign_xcx_openid'])){
            $_W['openid'] = $_SESSION['sign_xcx_openid'];
        }
        if(!$_W['ispost'] || empty($_W['openid'])){
            show_json(0, "错误的请求!");
        }

        $type = intval($_GPC['type']);
        $day = intval($_GPC['day']);

        if(empty($type) || empty($day)){
            show_json(0, "请求参数错误!");
        }

        $set = $this->model->getSet();
        if(empty($set['isopen'])){
            show_json(0, $set['textcredit'] . $set['textsign'] . "未开启!");
        }

        $reword_sum = iunserializer($set['reword_sum']);
        $reword_order = iunserializer($set['reword_order']);

        $condition = "";
        if(!empty($set['cycle'])){
            $month_start=mktime(0,0,0,date('m'),1,date('Y'));
            $month_end=mktime(23,59,59,date('m'),date('t'),date('Y'));
            $condition .= " and `time` between {$month_start} and {$month_end} ";
        }
        //  根据系统设置 指定时间段查询
        $record = pdo_fetch("select * from " . tablename('ewei_shop_sign_records') . ' where openid=:openid and `type`='.$type.' and `day`='.$day.' and uniacid=:uniacid '. $condition . ' limit 1 ', array(':uniacid'=>$_W['uniacid'], ':openid'=>$_W['openid']));

        //  判断是否领取
        if(!empty($record)){
            show_json(0, "此奖励已经领取, 请不要重复领取!");
        }

        $credit = 0;
        //  查询奖品内容
        if($type==1 && !empty($reword_order)){
            foreach ($reword_order as $item){
                if($item['day']==$day && !empty($item['credit'])){
                    $credit = $item['credit'];
                }
            }
            $message = "连续" . $set['textsign'];
        }
        elseif ($type==2 && !empty($reword_sum)){
            foreach ($reword_sum as $item){
                if($item['day']==$day && !empty($item['credit'])){
                    $credit = $item['credit'];
                }
            }
            $message = "总" . $set['textsign'];
        }

        $message .= $day . "天获得奖励" . $credit . $set['textcredit'];

        //  判断是否有奖励
        if(!empty($credit) && $credit>0){
            // 执行增加积分
            m('member')->setCredit($_W['openid'], 'credit1', +$credit, $set['textcredit'].$set['textsign'].": ".$message);
        }

        // 插入领取记录
        $arr = array(
            'uniacid'=>$_W['uniacid'],
            'time'=> time(),
            'openid'=>$_W['openid'],
            'credit'=>$credit,
            'log'=>$message,
            'type'=>$type,
            'day'=>$day
        );
        pdo_insert('ewei_shop_sign_records', $arr);

        $member = m('member')->getMember($_W['openid']);

        $result = array(
            'message'=>'领取成功!'.$message,
            'addcredit'=>$credit,
            'credit'=>intval($member['credit1'])
        );
        show_json(1, $result);

    }


    public function records()
    {
        global $_W;
        session_start();
        if(!empty($_SESSION['sign_xcx_openid'])){
            $_W['openid'] = $_SESSION['sign_xcx_openid'];
        }
        $set = $this->model->getSet();

        $texts = array(
            'sign'=>$set['textsign'],
            'signed'=>$set['textsigned'],
            'signold'=>$set['textsignold'],
            'credit'=>$set['textcredit'],
            'color'=>$set['maincolor']
        );

        include $this->template();
    }

    public function getRecords()
    {
        global $_W, $_GPC;
        session_start();
        if(!empty($_SESSION['sign_xcx_openid'])){
            $_W['openid'] = $_SESSION['sign_xcx_openid'];
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition = ' and openid=:openid and uniacid = :uniacid ';
        $params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
        $sql = 'SELECT COUNT(*) FROM ' . tablename('ewei_shop_sign_records') . " log where 1 {$condition}";
        $total = pdo_fetchcolumn($sql, $params);
        $list = array();
        if (!empty($total)) {
            $sql = 'SELECT * FROM ' . tablename('ewei_shop_sign_records') . ' where 1 ' . $condition . ' ORDER BY `time` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $list = pdo_fetchall($sql, $params);
            if(!empty($list)){
                foreach ($list as &$item){
                    $item['date'] = date("Y-m-d H:i:s", $item['time']);
                }
                unset($item);
            }
        }
        show_json(1, array('total' => $total, 'list' => $list, 'pagesize' => $psize));
    }

    public function rank() {
        global $_W, $_GPC;
        session_start();
        if(!empty($_SESSION['sign_xcx_openid'])){
            $_W['openid'] = $_SESSION['sign_xcx_openid'];
        }
        $set = $this->model->getSet();
        $texts = array(
            'sign'=>$set['textsign'],
            'signed'=>$set['textsigned'],
            'signold'=>$set['textsignold'],
            'credit'=>$set['textcredit'],
            'color'=>$set['maincolor']
        );

        include $this->template();
    }

    public function getRank() {
        global $_W, $_GPC;
        session_start();
        if(!empty($_SESSION['sign_xcx_openid'])){
            $_W['openid'] = $_SESSION['sign_xcx_openid'];
        }
        $type = trim($_GPC['type']);

        $set = $this->getSet();

        $total = 0;
        $list = array();

        $psize = 10;

        if(!empty($type)){
            $pindex = max(1, intval($_GPC['page']));
            $condition = ' and su.uniacid=:uniacid and sm.uniacid=:uniacid ';
            $conditioncol = ' and uniacid=:uniacid ';

            if(!empty($set['cycle'])){
                $condition .= ' and su.signdate="'.date('Y-m', time()) .'"';
                $conditioncol .= ' and signdate="'.date('Y-m', time()) .'"';
            }

            if($_SESSION['sign_xcx_isminiprogram']){
                $condition .= ' and su.isminiprogram = 1 ';
                $conditioncol .= ' and isminiprogram = 1 ';
            }

            $params = array(':uniacid' => $_W['uniacid']);
            $sql = 'SELECT COUNT(*) FROM ' . tablename('ewei_shop_sign_user') . " where 1 {$conditioncol}";
            $total = pdo_fetchcolumn($sql, $params);
            $list = array();
            if (!empty($total)) {
                $type  = 'su.'.$type;
                $sql = 'SELECT su.*, sm.nickname, sm.avatar FROM ' . tablename('ewei_shop_sign_user') . ' su left join ' . tablename('ewei_shop_member') . ' sm on sm.openid=su.openid where 1 ' . $condition . ' ORDER BY '.$type.' DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
                $list = pdo_fetchall($sql, $params);
                if(!empty($list)){
                    foreach ($list as &$item){
                        $item['type'] = str_replace('su.', '', $type);
                    }
                    unset($item);
                }
            }
        }
        show_json(1, array('total' => $total, 'list' => $list, 'pagesize' => $psize));
    }

}
