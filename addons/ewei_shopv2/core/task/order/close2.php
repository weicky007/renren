<?php

error_reporting(0);
require '../../../../../framework/bootstrap.inc.php';
require '../../../../../addons/ewei_shopv2/defines.php';
require '../../../../../addons/ewei_shopv2/core/inc/functions.php';
global $_W;
global $_GPC;
ignore_user_abort();
set_time_limit(0);
$sets = pdo_fetchall('select uniacid from ' . tablename('ewei_shop_sysset'));

foreach ($sets as $set) {
	$_W['uniacid'] = $set['uniacid'];

	if (empty($_W['uniacid'])) {
		continue;
	}

	$set = m('cache')->getArray('sysset', $_W['uniacid']);
	$trade = $set['sets']['trade'];
	$days = intval($trade['closeorder']);

	if ($days <= 0) {
		continue;
	}

	$daytimes = 86400 * $days;
	$orders = pdo_fetchall('select id,openid,deductcredit2,ordersn,isparent,deductcredit,deductprice from ' . tablename('ewei_shop_order') . ' where  uniacid=' . $_W['uniacid'] . ' and status=0 and paytype<>3  and createtime + ' . $daytimes . ' <=unix_timestamp() ');
	$p = com('coupon');

	foreach ($orders as $o) {
		$isPeerpay = m('order')->checkpeerpay($o['id']);

		if (!empty($isPeerpay)) {
			$daytimes = 86400 * 15;
		}

		$onew = pdo_fetch('select status,isparent from ' . tablename('ewei_shop_order') . ' where id=:id and status=0 and paytype<>3  and createtime + ' . $daytimes . ' <=unix_timestamp()  limit 1', array(':id' => $o['id']));
		if (!empty($onew) && ($onew['status'] == 0)) {
			if ($o['isparent'] == 0) {
				if ($p) {
					if (!empty($o['couponid'])) {
						$p->returnConsumeCoupon($o['id']);
					}
				}

				m('order')->setStocksAndCredits($o['id'], 2);
				m('order')->setDeductCredit2($o);

				if (0 < $o['deductprice']) {
					m('member')->setCredit($o['openid'], 'credit1', $o['deductcredit'], array('0', $_W['shopset']['shop']['name'] . '自动关闭订单返还抵扣积分 积分: ' . $o['deductcredit'] . ' 抵扣金额: ' . $o['deductprice'] . ' 订单号: ' . $o['ordersn']));
				}
			}

			if (!empty($isPeerpay)) {
				$refundsql = 'SELECT * FROM ' . tablename('ewei_shop_order_peerpay_payinfo') . ' WHERE pid = :pid AND uniacid = :uniacid';
				$refundlist = pdo_fetchall($refundsql, array(':pid' => $isPeerpay['id'], ':uniacid' => $_W['unaicid']));

				foreach ($refundlist as $k => $v) {
					$openid = pdo_fetch('SELECT openid FROM ' . tablename('ewei_shop_memeber') . ' WHERE id = :id AND uniacid = :uniacid', array(':uniacid' => $_W['uniacid'], ':id' => $v['id']));
					$result = m('finance')->pay($openid, 1, $v['price'] * 100, $o['ordersn'], '退款: ' . $v['price'] . '元 订单号: ' . $o['ordersn']);

					if (is_error($result)) {
						m('member')->setCredit($openid, 'credit2', $v['price'], array(0, '退款: ' . $v['price'] . '元 订单号: ' . $o['ordersn']));
					}
				}
			}

			pdo_query('update ' . tablename('ewei_shop_order') . ' set status=-1,canceltime=' . time() . ' where id=' . $o['id']);
		}
	}
}

?>
