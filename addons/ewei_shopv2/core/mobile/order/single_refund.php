<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Single_Refund_EweiShopV2Page extends MobileLoginPage
{
	protected function globalData()
	{
		global $_W;
		global $_GPC;
		$order_goodsid = intval($_GPC['id']);
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$order = pdo_fetch('select o.id,o.price,o.couponprice,o.iscycelbuy,o.status,o.virtual,o.isverify,o.refundstate,o.finishtime,o.deductprice,o.deductcredit2,o.dispatchprice,o.deductenough,o.merchdeductenough,g.cannotrefund,g.refund,g.returngoods,g.exchange,g.type,og.single_refundid,og.single_refundstate,og.single_refundtime,og.realprice as og_realprice,o.grprice,og.consume,o.ispackage,og.sendtime from ' . tablename('ewei_shop_order') . ' o ' . ' left join ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid' . ' where og.id=:ogid and o.openid=:openid and o.uniacid=:uniacid', array(':ogid' => $order_goodsid, ':openid' => $openid, ':uniacid' => $uniacid));

		if (empty($order)) {
			if (!$_W['isajax']) {
				header('location: ' . mobileUrl('order'));
				exit();
			}
			else {
				show_json(0, '订单未找到');
			}
		}

		$_err = '';

		if ($order['iscycelbuy'] == 1) {
			$order_goods = pdo_fetch('select * from ' . tablename('ewei_shop_cycelbuy_periods') . ('where orderid = ' . $order['id'] . ' and status != 0'));

			if (!empty($order_goods)) {
				$_err = '订单已经开始，无法进行单商品退款';
			}
		}

		if (!empty($order['ispackage'])) {
			$_err = '套餐订单,无法进行单品维权!';
		}

		$ispeerpay = m('order')->checkpeerpay($order['id']);

		if (!empty($ispeerpay)) {
			$_err = '代付订单,无法进行单品维权!';
		}

		$fullback_goods_count = pdo_fetchcolumn('select count(og.id) from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid' . ' where og.orderid=:orderid and g.isfullback>0 and og.uniacid=:uniacid', array(':orderid' => $order['id'], ':uniacid' => $uniacid));

		if (!empty($fullback_goods_count)) {
			$_err = '全返订单,无法进行单商品退款';
		}

		$refundgoods = array('refund' => $order['refund'] ? true : false, 'returngoods' => $order['returngoods'] ? true : false, 'exchange' => $order['exchange'] ? true : false);
		if (3 <= $order['status'] && $order['type'] == 2) {
			$refundgoods['returngoods'] = false;
			$refundgoods['exchange'] = false;
		}

		if ($order['status'] <= 0) {
			$_err = '订单未付款或已关闭，不能申请退款!';
		}
		else {
			if ($order['status'] == 2 && !empty($order['cannotrefund']) && empty($order['refund']) && empty($order['returngoods']) && empty($order['exchange'])) {
				$_err = '此商品不可退换货!';
			}
			else {
				if ($order['status'] == 3) {
					if (!empty($order['virtual']) || $order['isverify'] == 1) {
						$_err = '此订单不允许退款!';
					}
					else {
						$tradeset = m('common')->getSysset('trade');
						$refunddays = intval($tradeset['refunddays']);

						if (0 < $refunddays) {
							$days = intval((time() - $order['finishtime']) / 3600 / 24);

							if ($refunddays < $days) {
								$_err = '订单完成已超过 ' . $refunddays . ' 天, 无法发起退款申请!';
							}
						}
						else {
							$_err = '订单完成, 无法申请退款!';
						}
					}
				}
			}
		}

		$refund = pdo_fetch('select id.orderid from ' . tablename('ewei_shop_order_refund') . ' where uniacid = :uniacid and orderid = :orderid and :goodsid in (ordergoodsids) and status > 0 limit 1 ', array(':uniacid' => $uniacid, ':orderid' => $order['id'], ':goodsid' => $order_goodsid));

		if (!empty($refund)) {
			$_err = '此订单已完成维权，不能申请退款!';
		}

		if (!empty($_err)) {
			if ($_W['isajax']) {
				show_json(0, $_err);
			}
			else {
				$this->message($_err, '', 'error');
			}
		}

		$order_goods = pdo_fetchall('select og.id,og.single_refundid,og.single_refundstate,r.applyprice from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order_single_refund') . ' r on r.id=og.single_refundid' . ' where og.orderid=:orderid and og.uniacid=:uniacid', array(':orderid' => $order['id'], ':uniacid' => $uniacid));
		$is_last = true;
		$refund_price = 0;

		foreach ($order_goods as $og) {
			if (intval($og['id']) != $order_goodsid) {
				if (empty($og['single_refundid'])) {
					$is_last = false;
					break;
				}

				$refund_price += $og['applyprice'];
			}
		}

		if ($is_last) {
			$order['single_refundprice'] = $order['price'] - $refund_price;

			if (2 < $order['status']) {
				$order['single_refundprice'] -= $order['dispatchprice'];
			}
		}
		else {
			$order_discount = $order['deductprice'] + $order['deductcredit2'] + $order['deductenough'] + $order['merchdeductenough'] + $order['couponprice'];
			$goods_discount = round($order_discount * ($order['og_realprice'] / $order['grprice']), 2);
			$order['single_refundprice'] = $order['og_realprice'] - $goods_discount;
		}

		if ($order['single_refundprice'] <= 0) {
			$order['single_refundprice'] = 0;
		}

		return array('uniacid' => $uniacid, 'refundgoods' => $refundgoods, 'openid' => $_W['openid'], 'order_goodsid' => $order_goodsid, 'order' => $order, 'single_refundid' => $order['single_refundid']);
	}

	public function main()
	{
		global $_W;
		global $_GPC;
		extract($this->globalData());
		if ($order['status'] == 2 && $order['price'] == $order['dispatchprice']) {
			$canreturns = 1;
		}

		$refund = false;

		if (!empty($single_refundid)) {
			$refund = pdo_fetch('select * from ' . tablename('ewei_shop_order_single_refund') . ' where id=:id and ordergoodsid=:ordergoodsid and uniacid=:uniacid limit 1', array(':id' => $single_refundid, ':ordergoodsid' => $order_goodsid, ':uniacid' => $uniacid));

			if (!empty($refund['refundaddress'])) {
				$refund['refundaddress'] = iunserializer($refund['refundaddress']);
			}
		}

		if (!empty($refund['imgs'])) {
			$refund['imgs'] = iunserializer($refund['imgs']);
		}

		$express_list = m('express')->getExpressList();
		$trade = m('common')->getSysset('trade', $_W['uniacid']);
		include $this->template();
	}

	public function submit()
	{
		global $_W;
		global $_GPC;
		extract($this->globalData());

		if ($order['single_refundstate'] == '9') {
			show_json(0, '商品维权已经处理完毕!');
		}

		$price = floatval($_GPC['price']);
		$rtype = intval($_GPC['rtype']);

		if ($rtype != 2) {
			if (empty($price) && $order['deductprice'] == 0) {
				show_json(0, '退款金额不能为0元');
			}

			if (round($order['single_refundprice'], 2) < round($price, 2)) {
				show_json(0, '退款金额不能超过' . $order['single_refundprice'] . '元');
			}
		}

		$refund = array('uniacid' => $uniacid, 'merchid' => $order['merchid'], 'applyprice' => $price, 'rtype' => $rtype, 'reason' => trim($_GPC['reason']), 'content' => trim($_GPC['content']), 'imgs' => iserializer($_GPC['images']));

		if ($refund['rtype'] == 2) {
			$refundstate = 2;
		}
		else {
			$refundstate = 1;
		}

		if ($order['single_refundstate'] == 0) {
			$refund['createtime'] = time();
			$refund['orderid'] = $order['id'];
			$refund['ordergoodsid'] = $order_goodsid;
			$refund['ordergoodsrealprice'] = $order['og_realprice'];
			$refund['refundno'] = m('common')->createNO('order_refund', 'refundno', 'SR');
			pdo_insert('ewei_shop_order_single_refund', $refund);
			$single_refundid = pdo_insertid();
			pdo_update('ewei_shop_order_goods', array('single_refundid' => $single_refundid, 'single_refundstate' => $refundstate), array('id' => $order_goodsid, 'uniacid' => $uniacid));
		}
		else {
			$refund['status'] = 0;
			pdo_update('ewei_shop_order_goods', array('single_refundstate' => $refundstate), array('id' => $order_goodsid, 'uniacid' => $uniacid));
			pdo_update('ewei_shop_order_single_refund', $refund, array('id' => $single_refundid, 'uniacid' => $uniacid));
		}

		pdo_update('ewei_shop_order', array('refundstate' => 3, 'refundtime' => 0), array('id' => $order['id'], 'uniacid' => $uniacid));
		m('notice')->sendOrderMessage($order['id'], true);
		show_json(1);
	}

	public function cancel()
	{
		global $_W;
		global $_GPC;
		extract($this->globalData());
		$change_refund = array();
		$change_refund['status'] = -2;
		$change_refund['refundtime'] = time();
		pdo_update('ewei_shop_order_single_refund', $change_refund, array('id' => $single_refundid, 'uniacid' => $uniacid));
		pdo_update('ewei_shop_order_goods', array('single_refundstate' => 0), array('id' => $order_goodsid, 'uniacid' => $uniacid));
		$order_goods = pdo_fetchall('select single_refundid,single_refundstate,single_refundtime from ' . tablename('ewei_shop_order_goods') . ' where orderid=:orderid', array(':orderid' => $order['id']));
		$refund_num = 0;
		$apply_refund_num = 0;

		foreach ($order_goods as $og) {
			if (0 < $og['single_refundtime']) {
				++$refund_num;
			}

			if ($og['single_refundstate'] == 1 || $og['single_refundstate'] == 2) {
				++$apply_refund_num;
			}
		}

		if (empty($apply_refund_num) && !empty($refund_num)) {
			pdo_update('ewei_shop_order', array('refundtime' => time()), array('id' => $order['id'], 'uniacid' => $uniacid));
		}

		if (empty($apply_refund_num) && empty($refund_num)) {
			pdo_update('ewei_shop_order', array('refundstate' => 0, 'refundtime' => 0), array('id' => $order['id'], 'uniacid' => $uniacid));
		}

		show_json(1);
	}

	public function express()
	{
		global $_W;
		global $_GPC;
		extract($this->globalData());

		if (empty($single_refundid)) {
			show_json(0, '参数错误!');
		}

		if (empty($_GPC['expresssn'])) {
			show_json(0, '请填写快递单号');
		}

		$refund = array('status' => 4, 'express' => trim($_GPC['express']), 'expresscom' => trim($_GPC['expresscom']), 'expresssn' => trim($_GPC['expresssn']), 'sendtime' => time());
		pdo_update('ewei_shop_order_single_refund', $refund, array('id' => $single_refundid, 'uniacid' => $uniacid));
		show_json(1);
	}

	public function receive()
	{
		global $_W;
		global $_GPC;
		extract($this->globalData());
		$single_refundid = intval($_GPC['single_refundid']);
		$refund = pdo_fetch('select * from ' . tablename('ewei_shop_order_single_refund') . ' where id=:id and ordergoodsid=:ordergoodsid and uniacid=:uniacid limit 1', array(':id' => $single_refundid, ':ordergoodsid' => $order_goodsid, ':uniacid' => $uniacid));

		if (empty($refund)) {
			show_json(0, '换货申请未找到!');
		}

		$time = time();
		$refund_data = array();
		$refund_data['status'] = 1;
		$refund_data['refundtime'] = $time;
		pdo_update('ewei_shop_order_single_refund', $refund_data, array('id' => $single_refundid, 'uniacid' => $uniacid));
		$order_data = array();
		$order_data['single_refundstate'] = 9;
		pdo_update('ewei_shop_order_goods', $order_data, array('id' => $order_goodsid, 'uniacid' => $uniacid));
		$is_single_refund = pdo_fetchcolumn('select count(id) from ' . tablename('ewei_shop_order_goods') . 'where orderid=:orderid and (single_refundstate=1 or single_refundstate=2)', array(':orderid' => $order['id']));

		if (empty($is_single_refund)) {
			pdo_update('ewei_shop_order', array('refundtime' => $time), array('id' => $order['id'], 'uniacid' => $uniacid));
		}

		show_json(1);
	}

	public function refundexpress()
	{
		global $_W;
		global $_GPC;
		extract($this->globalData());
		$express = trim($_GPC['express']);
		$expresssn = trim($_GPC['expresssn']);
		$expresscom = trim($_GPC['expresscom']);
		$expresslist = m('util')->getExpressList($express, $expresssn);
		include $this->template('order/refundexpress');
	}
}

?>
