<?php
//dezend by haha解密更新  维护群：468773368 
?>
<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Goods_EweiShopV2Page extends AppMobilePage
{
	/**
     * 商品列表
     */
	public function get_list()
	{
		global $_GPC;
		global $_W;
		$couponid = intval($_GPC['couponid']);

		if (!(empty($couponid))) {
			$this->getlistbyCoupon($couponid);
			return;
		}


		$args = array('pagesize' => intval($_GPC['pagesize']), 'page' => intval($_GPC['page']), 'isnew' => trim($_GPC['isnew']), 'ishot' => trim($_GPC['ishot']), 'isrecommand' => trim($_GPC['isrecommand']), 'isdiscount' => trim($_GPC['isdiscount']), 'istime' => trim($_GPC['istime']), 'keywords' => trim($_GPC['keywords']), 'cate' => intval($_GPC['cate']), 'order' => trim($_GPC['order']), 'by' => trim($_GPC['by']));
		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
		if ($merch_plugin && $merch_data['is_openmerch']) {
			$args['merchid'] = intval($_GPC['merchid']);
		}


		if (isset($_GPC['nocommission'])) {
			$args['nocommission'] = intval($_GPC['nocommission']);
		}


		$goods = m('goods')->getList($args);
		$saleout = ((!(empty($_W['shopset']['shop']['saleout'])) ? tomedia($_W['shopset']['shop']['saleout']) : ''));
		$goods_list = array();

		if (0 < $goods['total']) {
			$goods_list = $goods['list'];

			foreach ($goods_list as $index => $item ) {
				if ($goods_list[$index]['isdiscount']) {
					if (time() < $goods_list[$index]['isdiscount_time']) {
						$goods_list[$index]['isdiscount'] = 0;
					}

				}


				$goods_list[$index]['minprice'] = (double) $goods_list[$index]['minprice'];
				unset($goods_list[$index]['marketprice']);
				unset($goods_list[$index]['maxprice']);
				unset($goods_list[$index]['isdiscount_discounts']);
				unset($goods_list[$index]['description']);
				unset($goods_list[$index]['discount_time']);

				if ($item['total'] < 1) {
					$goods_list[$index]['saleout'] = $saleout;
				}

			}
		}


		app_json(array('list' => $goods_list, 'total' => $goods['total'], 'pagesize' => $args['pagesize']));
	}

	/**
     * 获取商品列表
     */
	public function getlistbyCoupon()
	{
		global $_GPC;
		global $_W;
		$args = array('pagesize' => 10, 'page' => intval($_GPC['page']), 'isnew' => trim($_GPC['isnew']), 'ishot' => trim($_GPC['ishot']), 'isrecommand' => trim($_GPC['isrecommand']), 'isdiscount' => trim($_GPC['isdiscount']), 'istime' => trim($_GPC['istime']), 'issendfree' => trim($_GPC['issendfree']), 'keywords' => trim($_GPC['keywords']), 'cate' => trim($_GPC['cate']), 'order' => trim($_GPC['order']), 'by' => trim($_GPC['by']), 'couponid' => trim($_GPC['couponid']), 'merchid' => intval($_GPC['merchid']));
		$plugin_commission = p('commission');
		if ($plugin_commission && (0 < intval($_W['shopset']['commission']['level'])) && empty($_W['shopset']['commission']['closemyshop']) && !(empty($_W['shopset']['commission']['select_goods']))) {
			$mid = intval($_GPC['mid']);

			if (!(empty($mid))) {
				$shop = p('commission')->getShop($mid);

				if (!(empty($shop['selectgoods']))) {
					$args['ids'] = $shop['goodsids'];
				}

			}

		}


		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
		if ($merch_plugin && $merch_data['is_openmerch']) {
			$args['merchid'] = intval($_GPC['merchid']);
		}


		if (isset($_GPC['nocommission'])) {
			$args['nocommission'] = intval($_GPC['nocommission']);
		}


		$goods = m('goods')->getListbyCoupon($args);
		app_json(array('list' => $goods['list'], 'total' => $goods['total'], 'pagesize' => $args['pagesize']));
	}

	/**
     * 商品详情
     */
	public function get_detail()
	{
		global $_W;
		global $_GPC;
		$result = array();
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$id = intval($_GPC['id']);

		if (empty($id)) {
			app_error(AppError::$ParamsError);
		}


		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
		if ($merch_plugin && $merch_data['is_openmerch']) {
			$is_openmerch = 1;
		}
		 else {
			$is_openmerch = 0;
		}

		$goods = pdo_fetch('select * from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		$member = m('member')->getMember($openid);
		$showlevels = (($goods['showlevels'] != '' ? explode(',', $goods['showlevels']) : array()));
		$showgroups = (($goods['showgroups'] != '' ? explode(',', $goods['showgroups']) : array()));
		$showgoods = 0;

		if (!(empty($member))) {
			if ((!(empty($showlevels)) && in_array($member['level'], $showlevels)) || (!(empty($showgroups)) && in_array($member['groupid'], $showgroups)) || (empty($showlevels) && empty($showgroups))) {
				$showgoods = 1;
			}

		}
		 else if (empty($showlevels) && empty($showgroups)) {
			$showgoods = 1;
		}


		$goods['seckillinfo'] = false;
		$seckill = p('seckill');

		if ($seckill) {
			$time = time();
			$goods['seckillinfo'] = $seckill->getSeckill($goods['id'], 0, false);

			if (!(empty($goods['seckillinfo']))) {
				if (($goods['seckillinfo']['starttime'] <= $time) && ($time < $goods['seckillinfo']['endtime'])) {
					$goods['seckillinfo']['status'] = 0;
				}
				 else if ($time < $goods['seckillinfo']['starttime']) {
					$goods['seckillinfo']['status'] = 1;
				}
				 else {
					$goods['seckillinfo']['status'] = -1;
				}
			}

		}


		if (empty($goods) || empty($showgoods)) {
			app_error(AppError::$GoodsNotFound);
		}


		$merchid = $goods['merchid'];

		if (!(empty($is_openmerch))) {
			if ((0 < $merchid) && ($goods['checked'] == 1)) {
				app_error(AppError::$GoodsNotChecked);
			}

		}


		$goods['sales'] = $goods['sales'] + $goods['salesreal'];
		$goods['buycontentshow'] = 0;

		if ($goods['buyshow'] == 1) {
			$sql = 'select o.id from ' . tablename('ewei_shop_order') . ' o left join ' . tablename('ewei_shop_order_goods') . ' g on o.id = g.orderid';
			$sql .= ' where o.openid=:openid and g.goodsid=:id and o.status>0 and o.uniacid=:uniacid limit 1';
			$buy_goods = pdo_fetch($sql, array(':openid' => $openid, ':id' => $id, ':uniacid' => $_W['uniacid']));

			if (!(empty($buy_goods))) {
				$goods['buycontentshow'] = 1;
				$goods['buycontent'] = m('ui')->lazy($goods['buycontent']);
			}

		}


		$goods['unit'] = ((empty($goods['unit']) ? '件' : $goods['unit']));
		$citys = m('dispatch')->getNoDispatchAreas($goods);

		if (!(empty($citys)) && is_array($citys)) {
			$has_city = 1;
		}
		 else {
			$has_city = 0;
		}

		$goods['citys'] = $citys;
		$goods['has_city'] = $has_city;
		$goods['dispatchprice'] = $this->getGoodsDispatchPrice($goods);
		$thumbs = iunserializer($goods['thumb_url']);

		if (empty($thumbs)) {
			$thumbs = array($goods['thumb']);
		}


		if (!(empty($goods['thumb_first'])) && !(empty($goods['thumb']))) {
			$thumbs = array_merge(array($goods['thumb']), $thumbs);
		}


		$goods['thumbs'] = set_medias($thumbs);
		$goods['video'] = tomedia($goods['video']);

		if (strexists($goods['video'], 'v.qq.com/iframe/player.html')) {
			$videourl = $this->model->getQVideo($goods['video']);

			if (!(is_error($videourl))) {
				$goods['video'] = $videourl;
			}

		}


		if (!(empty($goods['thumbs'])) && is_array($goods['thumbs'])) {
			$new_thumbs = array();

			foreach ($goods['thumbs'] as $i => $thumb ) {
				$new_thumbs[] = $thumb;
			}

			$goods['thumbs'] = $new_thumbs;
		}


		$specs = pdo_fetchall('select * from ' . tablename('ewei_shop_goods_spec') . ' where goodsid=:goodsid and  uniacid=:uniacid order by displayorder asc', array(':goodsid' => $id, ':uniacid' => $_W['uniacid']));
		$spec_titles = array();

		foreach ($specs as $key => $spec ) {
			if (2 <= $key) {
				break;
			}


			$spec_titles[] = $spec['title'];
		}

		if (0 < $goods['hasoption']) {
			$goods['spec_titles'] = implode('、', $spec_titles);
		}
		 else {
			$goods['spec_titles'] = '';
		}

		$goods['params'] = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_goods_param') . ' WHERE uniacid=:uniacid and goodsid=:goodsid order by displayorder asc', array(':uniacid' => $uniacid, ':goodsid' => $goods['id']));
		$goods = set_medias($goods, 'thumb');

		$goods['canbuy'] = ((!(empty($goods['status'])) && empty($goods['deleted']) ? 1 : 0));
		$goods['cannotbuy'] = '';

		if ($goods['total'] <= 0) {
			$goods['canbuy'] = 0;
			$goods['cannotbuy'] = '商品库存不足';
		}


		$goods['timestate'] = '';
		$goods['userbuy'] = '1';

		if (0 < $goods['usermaxbuy']) {
			$order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id ' . ' where og.goodsid=:goodsid and  o.status>=1 and o.openid=:openid  and og.uniacid=:uniacid ', array(':goodsid' => $goods['id'], ':uniacid' => $uniacid, ':openid' => $openid));

			if ($goods['usermaxbuy'] <= $order_goodscount) {
				$goods['userbuy'] = 0;
				$goods['canbuy'] = 0;
				$goods['cannotbuy'] = '超出最大购买数量';
			}

		}


		$levelid = $member['level'];
		$groupid = $member['groupid'];
		$goods['levelbuy'] = '1';

		if ($goods['buylevels'] != '') {
			$buylevels = explode(',', $goods['buylevels']);

			if (!(in_array($levelid, $buylevels))) {
				$goods['levelbuy'] = 0;
				$goods['canbuy'] = 0;
				$goods['cannotbuy'] = '购买级别不够';
			}

		}


		$goods['groupbuy'] = '1';

		if ($goods['buygroups'] != '') {
			$buygroups = explode(',', $goods['buygroups']);

			if (!(in_array($groupid, $buygroups))) {
				$goods['groupbuy'] = 0;
				$goods['canbuy'] = 0;
				$goods['cannotbuy'] = '所在会员组无法购买';
			}

		}


		$goods['timebuy'] = '0';

		if ($goods['istime'] == 1) {
			if (time() < $goods['timestart']) {
				$goods['timebuy'] = '-1';
				$goods['canbuy'] = 0;
				$goods['cannotbuy'] = '限时购未开始';
			}
			 else if ($goods['timeend'] < time()) {
				$goods['timebuy'] = '1';
				$goods['canbuy'] = 0;
				$goods['cannotbuy'] = '限时购已结束';
			}

		}


		$goods['canAddCart'] = 1;
		if (($goods['isverify'] == 2) || ($goods['type'] == 2) || ($goods['type'] == 3)) {
			$goods['canAddCart'] = 0;
		}


		$enoughs = com_run('sale::getEnoughs');
		$enoughfree = com_run('sale::getEnoughFree');
		if ($enoughfree && ($enoughfree < $goods['minprice'])) {
			$goods['dispatchprice'] = 0;
		}


		$goods['hasSales'] = 0;
		if ((0 < $goods['ednum']) || (0 < $goods['edmoney'])) {
			$goods['hasSales'] = 1;
		}


		if ($enoughfree || ($enoughs && (0 < count($enoughs)))) {
			$goods['hasSales'] = 1;
		}


		$goods['enoughfree'] = $enoughfree;
		$goods['enoughs'] = $enoughs;
		$minprice = $goods['minprice'];
		$maxprice = $goods['maxprice'];
		$level = m('member')->getLevel($openid);
		$memberprice = m('goods')->getMemberPrice($goods, $level);
		if ($goods['isdiscount'] && (time() <= $goods['isdiscount_time'])) {
			$goods['oldmaxprice'] = $maxprice;
			$isdiscount_discounts = json_decode($goods['isdiscount_discounts'], true);
			$prices = array();
			if (!(isset($isdiscount_discounts['type'])) || empty($isdiscount_discounts['type'])) {
				$level = m('member')->getLevel($openid);
				$prices_array = m('order')->getGoodsDiscountPrice($goods, $level, 1);
				$prices[] = $prices_array['price'];
			}
			 else {
				$goods_discounts = m('order')->getGoodsDiscounts($goods, $isdiscount_discounts, $levelid);
				$prices = $goods_discounts['prices'];
			}

			$minprice = min($prices);
			$maxprice = max($prices);
		}


		$goods['minprice'] = (double) $minprice;
		$goods['maxprice'] = (double) $maxprice;
		$goods['getComments'] = empty($_W['shopset']['trade']['closecommentshow']);
		$goods['hasServices'] = $goods['cash'] || $goods['seven'] || $goods['repair'] || $goods['invoice'] || $goods['quality'];
		$goods['services'] = array();

		if ($goods['cash']) {
			$goods['services'][] = '货到付款';
		}


		if ($goods['quality']) {
			$goods['services'][] = '正品保证';
		}


		if ($goods['seven']) {
			$goods['services'][] = '7天无理由退换';
		}


		if ($goods['invoice']) {
			$goods['services'][] = '发票';
		}


		if ($goods['repair']) {
			$goods['services'][] = '保修';
		}


		$labelstyle = pdo_fetch('SELECT id,uniacid,style FROM ' . tablename('ewei_shop_goods_labelstyle') . ' WHERE uniacid=:uniacid LIMIT 1', array(':uniacid' => $uniacid));

		if (json_decode($goods['labelname'], true)) {
			$labelname = json_decode($goods['labelname'], true);
		}
		 else {
			$labelname = unserialize($goods['labelname']);
		}

		$goods['labelname'] = $labelname;
		$goods['labelstyle'] = $labelstyle;
		$labellist = $goods['services'];

		if (is_array($labelname)) {
			$labellist = array_merge($labellist, $labelname);
		}


		$goods['labels'] = array('style' => (is_array($labelstyle) ? intval($labelstyle['style']) : 0), 'list' => $labellist);
		$goods['isfavorite'] = m('goods')->isFavorite($id);
		$goods['cartcount'] = m('goods')->getCartCount();
		m('goods')->addHistory($id);
		$shop = set_medias(m('common')->getSysset('shop'), 'logo');
		$shop['url'] = mobileUrl('', NULL);
		$mid = intval($_GPC['mid']);
		$opencommission = false;

		if (p('commission')) {
			if (empty($member['agentblack'])) {
				$cset = p('commission')->getSet();
				$opencommission = 0 < intval($cset['level']);

				if ($opencommission) {
					if (empty($mid)) {
						if (($member['isagent'] == 1) && ($member['status'] == 1)) {
							$mid = $member['id'];
						}

					}


					if (!(empty($mid))) {
						if (empty($cset['closemyshop'])) {
							$shop = set_medias(p('commission')->getShop($mid), 'logo');
							$shop['url'] = mobileUrl('commission/myshop', array('mid' => $mid), true);
						}

					}

				}

			}

		}


		if (empty($this->merch_user)) {
			$merch_flag = 0;

			if (($is_openmerch == 1) && (0 < $goods['merchid'])) {
				$merch_user = pdo_fetch('select * from ' . tablename('ewei_shop_merch_user') . ' where id=:id limit 1', array(':id' => intval($goods['merchid'])));

				if (!(empty($merch_user))) {
					$shop = $merch_user;
					$merch_flag = 1;
				}

			}


			if ($merch_flag == 1) {
				$shopdetail = array('logo' => (!(empty($goods['detail_logo'])) ? tomedia($goods['detail_logo']) : tomedia($shop['logo'])), 'shopname' => (!(empty($goods['detail_shopname'])) ? $goods['detail_shopname'] : $shop['merchname']), 'description' => (!(empty($goods['detail_totaltitle'])) ? $goods['detail_totaltitle'] : $shop['desc']), 'btntext1' => trim($goods['detail_btntext1']), 'btnurl1' => (!(empty($goods['detail_btnurl1'])) ? $goods['detail_btnurl1'] : mobileUrl('goods')), 'btntext2' => trim($goods['detail_btntext2']), 'btnurl2' => (!(empty($goods['detail_btnurl2'])) ? $goods['detail_btnurl2'] : mobileUrl('merch', array('merchid' => $goods['merchid']))));
			}
			 else {
				$shopdetail = array('logo' => (!(empty($goods['detail_logo'])) ? tomedia($goods['detail_logo']) : $shop['logo']), 'shopname' => (!(empty($goods['detail_shopname'])) ? $goods['detail_shopname'] : $shop['name']), 'description' => (!(empty($goods['detail_totaltitle'])) ? $goods['detail_totaltitle'] : $shop['description']), 'btntext1' => trim($goods['detail_btntext1']), 'btnurl1' => (!(empty($goods['detail_btnurl1'])) ? $goods['detail_btnurl1'] : mobileUrl('goods')), 'btntext2' => trim($goods['detail_btntext2']), 'btnurl2' => (!(empty($goods['detail_btnurl2'])) ? $goods['detail_btnurl2'] : $shop['url']));
			}

			$param = array(':uniacid' => $_W['uniacid']);

			if ($merch_flag == 1) {
				$sqlcon = ' and merchid=:merchid';
				$param[':merchid'] = $goods['merchid'];
			}


			if (empty($shop['selectgoods'])) {
				$statics = array('all' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid ' . $sqlcon . ' and status=1 and deleted=0', $param), 'new' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid ' . $sqlcon . ' and isnew=1 and status=1 and deleted=0', $param), 'discount' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid ' . $sqlcon . ' and isdiscount=1 and status=1 and deleted=0', $param));
			}
			 else {
				$goodsids = explode(',', $shop['goodsids']);
				$statics = array('all' => count($goodsids), 'new' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid ' . $sqlcon . ' and id in( ' . $shop['goodsids'] . ' ) and isnew=1 and status=1 and deleted=0', $param), 'discount' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid ' . $sqlcon . ' and id in( ' . $shop['goodsids'] . ' ) and isdiscount=1 and status=1 and deleted=0', $param));
			}
		}
		 else {
			if ($goods['checked'] == 1) {
				app_error(AppError::$GoodsNotChecked);
			}


			$shop = $this->merch_user;
			$shopdetail = array('logo' => (!(empty($goods['detail_logo'])) ? tomedia($goods['detail_logo']) : tomedia($shop['logo'])), 'shopname' => (!(empty($goods['detail_shopname'])) ? $goods['detail_shopname'] : $shop['merchname']), 'description' => (!(empty($goods['detail_totaltitle'])) ? $goods['detail_totaltitle'] : $shop['desc']), 'btntext1' => trim($goods['detail_btntext1']), 'btnurl1' => (!(empty($goods['detail_btnurl1'])) ? $goods['detail_btnurl1'] : mobileUrl('goods')), 'btntext2' => trim($goods['detail_btntext2']), 'btnurl2' => (!(empty($goods['detail_btnurl2'])) ? $goods['detail_btnurl2'] : mobileUrl('merch', array('merchid' => $goods['merchid']))));

			if (empty($shop['selectgoods'])) {
				$statics = array('all' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid and merchid=:merchid and status=1 and deleted=0', array(':uniacid' => $_W['uniacid'], ':merchid' => $goods['merchid'])), 'new' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid and merchid=:merchid and isnew=1 and status=1 and deleted=0', array(':uniacid' => $_W['uniacid'], ':merchid' => $goods['merchid'])), 'discount' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid and merchid=:merchid and isdiscount=1 and status=1 and deleted=0', array(':uniacid' => $_W['uniacid'], ':merchid' => $goods['merchid'])));
			}
			 else {
				$goodsids = explode(',', $shop['goodsids']);
				$statics = array('all' => count($goodsids), 'new' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid and merchid=:merchid and id in( ' . $shop['goodsids'] . ' ) and isnew=1 and status=1 and deleted=0', array(':uniacid' => $_W['uniacid'], ':merchid' => $goods['merchid'])), 'discount' => pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid and merchid=:merchid and id in( ' . $shop['goodsids'] . ' ) and isdiscount=1 and status=1 and deleted=0', array(':uniacid' => $_W['uniacid'], ':merchid' => $goods['merchid'])));
			}
		}

		$goodsdesc = ((!(empty($goods['description'])) ? $goods['description'] : $goods['subtitle']));
		$_W['shopshare'] = array('title' => (!(empty($goods['share_title'])) ? $goods['share_title'] : $goods['title']), 'imgUrl' => (!(empty($goods['share_icon'])) ? tomedia($goods['share_icon']) : tomedia($goods['thumb'])), 'desc' => (!(empty($goodsdesc)) ? $goodsdesc : $_W['shopset']['shop']['name']), 'link' => mobileUrl('app/share', array('type' => 'goods', 'id' => $goods['id']), true));
		$com = p('commission');

		if ($com) {
			$cset = $_W['shopset']['commission'];

			if (!(empty($cset))) {
				if (($member['isagent'] == 1) && ($member['status'] == 1)) {
					$_W['shopshare']['link'] = mobileUrl('app/share', array('type' => 'goods', 'id' => $goods['id'], 'mid' => $member['id']), true);
				}
				 else if (!(empty($_GPC['mid']))) {
					$_W['shopshare']['link'] = mobileUrl('app/share', array('type' => 'goods', 'id' => $goods['id'], 'mid' => $_GPC['mid']), true);
				}

			}

		}


		$stores = array();

		if ($goods['isverify'] == 2) {
			$storeids = array();

			if (!(empty($goods['storeids']))) {
				$storeids = array_merge(explode(',', $goods['storeids']), $storeids);
			}


			if (empty($storeids)) {
				if (0 < $merchid) {
					$stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 ', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
				}
				 else {
					$stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1', array(':uniacid' => $_W['uniacid']));
				}
			}
			 else if (0 < $merchid) {
				$stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and merchid=:merchid and status=1', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
			}
			 else {
				$stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1', array(':uniacid' => $_W['uniacid']));
			}
		}


		unset($goods['pcate'], $goods['ccate'], $goods['tcate'], $goods['costprice'], $goods['originalprice'], $goods['totalcnf'], $goods['salesreal'], $goods['score'], $goods['taobaoid'], $goods['taotaoid'], $goods['taobaourl'], $goods['updatetime'], $goods['noticeopenid'], $goods['noticetype'], $goods['ccates'], $goods['pcates'], $goods['tcates'], $goods['cates'], $goods['artid'], $goods['allcates'], $goods['hascommission'], $goods['commission1_rate'], $goods['commission1_pay'], $goods['commission2_rate'], $goods['commission2_pay'], $goods['commission3_rate'], $goods['commission3_pay'], $goods['commission_thumb'], $goods['commission'], $goods['needfollow'], $goods['followurl'], $goods['followtip'], $goods['sharebtn'], $goods['keywords'], $goods['timestate'], $goods['nocommission'], $goods['hidecommission'], $goods['diysave'], $goods['diysaveid'], $goods['deduct2'], $goods['shopid'], $goods['shorttitle'], $goods['diyformtype'], $goods['diyformid'], $goods['diymode'], $goods['discounts'], $goods['verifytype'], $goods['diyfields'], $goods['groupstype'], $goods['merchsale'], $goods['manydeduct'], $goods['checked'], $goods['goodssn'], $goods['productsn'], $goods['isdiscount_discounts'], $goods['isrecommand'], $goods['dispatchtype'], $goods['dispatchid'], $goods['buycontent'], $goods['storeids'], $goods['thumb_url'], $goods['share_icon'], $goods['share_title']);

		if (!(empty($goods['thumb_url']))) {
			$goods['thumb_url'] = iunserializer($goods['thumb_url']);
		}


		$goods['stores'] = $stores;

		if (!(empty($shopdetail))) {
			$shopdetail['btntext1'] = ((!(empty($shopdetail['btntext1'])) ? $shopdetail['btntext1'] : '全部商品'));
			$shopdetail['btntext2'] = ((!(empty($shopdetail['btntext2'])) ? $shopdetail['btntext2'] : '进店逛逛'));
			$shopdetail['btnurl1'] = $this->model->getUrl($shopdetail['btnurl1']);
			$shopdetail['btnurl2'] = $this->model->getUrl($shopdetail['btnurl2']);
			$shopdetail['static_all'] = $statics['all'];
			$shopdetail['static_new'] = $statics['new'];
			$shopdetail['static_discount'] = $statics['discount'];
		}


		$shopdetail = set_medias($shopdetail, 'logo');
		$goods['shopdetail'] = $shopdetail;
		$goods['share'] = $_W['shopshare'];
		$goods['memberprice'] = '';
		if (empty($goods['isdiscount']) || (!(empty($goods['isdiscount'])) && ($goods['isdiscount_time'] < time()))) {
			if (!(empty($memberprice)) && ($memberprice != $goods['minprice']) && !(empty($level))) {
				$goods['memberprice'] = array('levelname' => $level['levelname'], 'price' => $memberprice);
			}

		}


		if (com('coupon')) {
			$goods['coupons'] = $this->getCouponsbygood($goods['id']);
		}


		if ($goods['type'] == 3) {
		}


		$goods['presellsendstatrttime'] = date('m月d日', $goods['presellsendstatrttime']);
		$goods['endtime'] = date('Y-m-d H:i:s', $goods['endtime']);
		$goods['isdiscount_date'] = date('Y-m-d H:i:s', $goods['isdiscount_time']);
		$goods['productprice'] = (double) $goods['productprice'];
		$goods['credittext'] = $_W['shopset']['trade']['credittext'];
		$goods['moneytext'] = $_W['shopset']['trade']['moneytext'];
		$goods['content'] = m('common')->html_to_images($goods['content']);
		$goods['navbar'] = intval($_W['shopset']['app']['navbar']);
		$goods['customer'] = intval($_W['shopset']['app']['customer']);

		if (!(empty($goods['customer']))) {
			$goods['customercolor'] = ((empty($_W['shopset']['app']['customercolor']) ? '#ff5555' : $_W['shopset']['app']['customercolor']));
		}


		if (!(empty($goods['ispresell']))) {
			$goods['preselldatestart'] = ((empty($goods['preselltimestart']) ? 0 : date('Y-m-d H:i:s', $goods['preselltimestart'])));
			$goods['preselldateend'] = ((empty($goods['preselltimeend']) ? 0 : date('Y-m-d H:i:s', $goods['preselltimeend'])));
		}


		app_json(array('goods' => $goods));
	}

	public function get_comments()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$percent = 100;
		$params = array(':goodsid' => $id, ':uniacid' => $_W['uniacid']);
		$count = array('all' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order_comment') . ' where goodsid=:goodsid and level>=0 and deleted=0 and checked=0 and uniacid=:uniacid', $params), 'good' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order_comment') . ' where goodsid=:goodsid and level>=5 and deleted=0 and checked=0 and uniacid=:uniacid', $params), 'normal' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order_comment') . ' where goodsid=:goodsid and level>=2 and level<=4 and deleted=0 and checked=0 and uniacid=:uniacid', $params), 'bad' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order_comment') . ' where goodsid=:goodsid and level<=1 and deleted=0 and checked=0 and uniacid=:uniacid', $params), 'pic' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order_comment') . ' where goodsid=:goodsid and ifnull(images,\'a:0:{}\')<>\'a:0:{}\' and deleted=0 and checked=0 and uniacid=:uniacid', $params));
		$list = array();

		if (0 < $count['all']) {
			$percent = intval(($count['good'] / ((empty($count['all']) ? 1 : $count['all']))) * 100);
			$list = pdo_fetchall('select nickname,level,content,images,createtime from ' . tablename('ewei_shop_order_comment') . ' where goodsid=:goodsid and deleted=0 and checked=0 and uniacid=:uniacid order by istop desc, createtime desc, id desc limit 2', array(':goodsid' => $id, ':uniacid' => $_W['uniacid']));

			foreach ($list as &$row ) {
				$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
				$row['images'] = set_medias(iunserializer($row['images']));
				$row['nickname'] = cut_str($row['nickname'], 1, 0) . '**' . cut_str($row['nickname'], 1, -1);
			}

			unset($row);
		}


		app_json(array('count' => $count, 'percent' => $percent, 'list' => $list));
	}

	public function get_comment_list()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$level = trim($_GPC['level']);
		$params = array(':goodsid' => $id, ':uniacid' => $_W['uniacid']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$condition = '';

		if ($level == 'good') {
			$condition = ' and level=5';
		}
		 else if ($level == 'normal') {
			$condition = ' and level>=2 and level<=4';
		}
		 else if ($level == 'bad') {
			$condition = ' and level<=1';
		}
		 else if ($level == 'pic') {
			$condition = ' and ifnull(images,\'a:0:{}\')<>\'a:0:{}\'';
		}


		$list = pdo_fetchall('select * from ' . tablename('ewei_shop_order_comment') . ' ' . '  where goodsid=:goodsid and uniacid=:uniacid and deleted=0 and checked=0 ' . $condition . ' order by istop desc, createtime desc, id desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);

		foreach ($list as &$row ) {
			$row['headimgurl'] = tomedia($row['headimgurl']);
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
			$row['images'] = set_medias(iunserializer($row['images']));
			$row['reply_images'] = set_medias(iunserializer($row['reply_images']));
			$row['append_images'] = set_medias(iunserializer($row['append_images']));
			$row['append_reply_images'] = set_medias(iunserializer($row['append_reply_images']));
			$row['nickname'] = cut_str($row['nickname'], 1, 0) . '**' . cut_str($row['nickname'], 1, -1);
		}

		unset($row);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order_comment') . ' where goodsid=:goodsid  and uniacid=:uniacid and deleted=0 and checked=0 ' . $condition, $params);
		app_json(array('list' => $list, 'total' => $total, 'pagesize' => $psize));
	}

	/**
     * 商品简介
     */
	public function get_content()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = intval($_GPC['id']);
		$goods = pdo_fetch('select content from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		app_json(array('content' => base64_encode($goods['content'])));
	}

	/**
     * 获取规格选择
     */
	public function get_picker()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$openid = $_W['openid'];

		if (empty($id)) {
			app_error(AppError::$ParamsError);
		}


		$goods = pdo_fetch('select id,thumb,title,marketprice,total,maxbuy,minbuy,unit,hasoption,showtotal,diyformid,diyformtype,diyfields, `type`, isverify, maxprice, minprice, merchsale from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if (empty($goods)) {
			app_error(AppError::$GoodsNotFound);
		}


		$goods = set_medias($goods, 'thumb');
		$specs = array();
		$options = array();

		if (!(empty($goods)) && $goods['hasoption']) {
			$specs = pdo_fetchall('select id, goodsid, title from ' . tablename('ewei_shop_goods_spec') . ' where goodsid=:goodsid and uniacid=:uniacid order by displayorder asc', array(':goodsid' => $id, ':uniacid' => $_W['uniacid']));

			foreach ($specs as &$spec ) {
				$spec['items'] = pdo_fetchall('select id, specid, title, thumb, `virtual` from ' . tablename('ewei_shop_goods_spec_item') . ' where specid=:specid and `show`=1 order by displayorder asc', array(':specid' => $spec['id']));
			}

			unset($spec);
			$options = pdo_fetchall('select id, goodsid, title, thumb, productprice, marketprice, stock, weight, specs, goodssn, productsn, `virtual`  from ' . tablename('ewei_shop_goods_option') . ' where goodsid=:goodsid and uniacid=:uniacid order by displayorder asc', array(':goodsid' => $id, ':uniacid' => $_W['uniacid']));
		}


		$minprice = $goods['minprice'];
		$maxprice = $goods['maxprice'];
		if ($goods['isdiscount'] && (time() <= $goods['isdiscount_time'])) {
			$goods['oldmaxprice'] = $maxprice;
			$isdiscount_discounts = json_decode($goods['isdiscount_discounts'], true);
			$prices = array();
			if (!(isset($isdiscount_discounts['type'])) || empty($isdiscount_discounts['type'])) {
				$level = m('member')->getLevel($openid);
				$prices_array = m('order')->getGoodsDiscountPrice($goods, $level, 1);
				$prices[] = $prices_array['price'];
			}
			 else {
				$goods_discounts = m('order')->getGoodsDiscounts($goods, $isdiscount_discounts, $levelid, $options);
				$prices = $goods_discounts['prices'];
				$options = $goods_discounts['options'];
			}

			$minprice = min($prices);
			$maxprice = max($prices);
		}


		$goods['minprice'] = number_format($minprice, 2);
		$goods['maxprice'] = number_format($maxprice, 2);
		$diyform_plugin = p('diyform');

		if ($diyform_plugin) {
			$fields = false;

			if ($goods['diyformtype'] == 1) {
				if (!(empty($goods['diyformid']))) {
					$diyformid = $goods['diyformid'];
					$formInfo = $diyform_plugin->getDiyformInfo($diyformid);
					$fields = $formInfo['fields'];
				}

			}
			 else if ($goods['diyformtype'] == 2) {
				$diyformid = 0;
				$fields = iunserializer($goods['diyfields']);

				if (empty($fields)) {
					$fields = false;
				}

			}


			if (!(empty($fields))) {
				$inPicker = true;
				$openid = $_W['openid'];
				$member = m('member')->getMember($openid, true);
				$f_data = $diyform_plugin->getLastData(3, 0, $diyformid, $id, $fields, $member);
				$flag = 0;

				if (!(empty($f_data)) && is_array($f_data)) {
					foreach ($f_data as $k => $v ) {
						while (!(empty($v))) {
							$flag = 1;
							break;
						}
					}
				}


				if (empty($flag)) {
					$f_data = $diyform_plugin->getLastCartData($id);
				}

			}

		}


		if (!(empty($specs))) {
			foreach ($specs as $key => $value ) {
				foreach ($specs[$key]['items'] as $k => &$v ) {
					$v['thumb'] = tomedia($v['thumb']);
				}

				unset($v);
			}
		}


		$goods['canAddCart'] = 1;
		if (($goods['isverify'] == 2) || ($goods['type'] == 2) || ($goods['type'] == 3)) {
			$goods['canAddCart'] = 0;
		}


		unset($goods['diyformid'], $goods['diyformtype'], $goods['diyfields']);

		if (!(empty($options)) && is_array($options)) {
			foreach ($options as $index => &$option ) {
				$option_specs = $option['specs'];

				if (!(empty($option_specs))) {
					$option_specs_arr = explode('_', $option_specs);
					array_multisort($option_specs_arr, SORT_ASC);
					$option['specs'] = implode('_', $option_specs_arr);
				}

			}
		}


		unset($option);
		$appDatas = array(
			'fields' => array(),
			'f_data' => array()
			);

		if ($diyform_plugin) {
			$appDatas = $diyform_plugin->wxApp($fields, $f_data, $this->member);
		}


		app_json(array(
	'goods'   => $goods,
	'specs'   => $specs,
	'options' => $options,
	'diyform' => array('fields' => $appDatas['fields'], 'lastdata' => $appDatas['f_data'])
	));
	}

	/**
     * @param $goods
     * @return array|int
     */
	protected function getGoodsDispatchPrice($goods)
	{
		if (!(empty($goods['issendfree']))) {
			return 0;
		}


		if (($goods['type'] == 2) || ($goods['type'] == 3)) {
			return 0;
		}


		if ($goods['dispatchtype'] == 1) {
			return $goods['dispatchprice'];
		}


		if (empty($goods['dispatchid'])) {
			$dispatch = m('dispatch')->getDefaultDispatch($goods['merchid']);
		}
		 else {
			$dispatch = m('dispatch')->getOneDispatch($goods['dispatchid']);
		}

		if (empty($dispatch)) {
			$dispatch = m('dispatch')->getNewDispatch($goods['merchid']);
		}


		$areas = iunserializer($dispatch['areas']);

		if (!(empty($areas)) && is_array($areas)) {
			$firstprice = array();

			foreach ($areas as $val ) {
				$firstprice[] = $val['firstprice'];
			}

			array_push($firstprice, m('dispatch')->getDispatchPrice(1, $dispatch));
			$ret = array('min' => round(min($firstprice), 2), 'max' => round(max($firstprice), 2));
		}
		 else {
			$ret = m('dispatch')->getDispatchPrice(1, $dispatch);
		}

		return $ret;
	}

	/**
     * 获取分类
     */
	public function get_category()
	{
		global $_W;
		global $_GPC;
		$allcategory = m('shop')->getCategory();
		$catlevel = intval($_W['shopset']['category']['level']);
		$opencategory = true;
		$plugin_commission = p('commission');
		if ($plugin_commission && (0 < intval($_W['shopset']['commission']['level']))) {
			$mid = intval($_GPC['mid']);

			if (!(empty($mid))) {
				$shop = p('commission')->getShop($mid);

				if (empty($shop['selectcategory'])) {
					$opencategory = false;
				}

			}

		}


		app_json(array('allcategory' => $allcategory, 'catlevel' => $catlevel, 'opencategory' => $opencategory));
	}

	/**
     * 获取当前商品及当前用户组可领取的免费优惠券
     * @param $goodid
     * @return array
     */
	protected function getCouponsbygood($goodid)
	{
		global $_W;
		global $_GPC;
		$merchdata = $this->merchData();
		extract($merchdata);
		$time = time();
		$param = array();
		$param[':uniacid'] = $_W['uniacid'];
		$sql = 'select id,timelimit,coupontype,timedays,timestart,timeend,thumb,couponname,enough,backtype,deduct,discount,backmoney,backcredit,backredpack,bgcolor,thumb,credit,money,getmax,merchid,total as t,islimitlevel,limitmemberlevels,limitagentlevels,limitpartnerlevels,limitaagentlevels,limitgoodcatetype,limitgoodcateids,limitgoodtype,limitgoodids,tagtitle,settitlecolor,titlecolor from ' . tablename('ewei_shop_coupon') . ' c ';
		$sql .= ' where uniacid=:uniacid and money=0 and credit = 0 and coupontype=0';

		if ($is_openmerch == 0) {
			$sql .= ' and merchid=0';
		}
		 else if (!(empty($_GPC['merchid']))) {
			$sql .= ' and merchid=:merchid';
			$param[':merchid'] = intval($_GPC['merchid']);
		}
		 else {
			$sql .= ' and merchid=0';
		}

		$hascommission = false;
		$plugin_com = p('commission');

		if ($plugin_com) {
			$plugin_com_set = $plugin_com->getSet();
			$hascommission = !(empty($plugin_com_set['level']));

			if (empty($plugin_com_set['level'])) {
				$sql .= ' and ( limitagentlevels = "" or  limitagentlevels is null )';
			}

		}
		 else {
			$sql .= ' and ( limitagentlevels = "" or  limitagentlevels is null )';
		}

		$hasglobonus = false;
		$plugin_globonus = p('globonus');

		if ($plugin_globonus) {
			$plugin_globonus_set = $plugin_globonus->getSet();
			$hasglobonus = !(empty($plugin_globonus_set['open']));

			if (empty($plugin_globonus_set['open'])) {
				$sql .= ' and ( limitpartnerlevels = ""  or  limitpartnerlevels is null )';
			}

		}
		 else {
			$sql .= ' and ( limitpartnerlevels = ""  or  limitpartnerlevels is null )';
		}

		$hasabonus = false;
		$plugin_abonus = p('abonus');

		if ($plugin_abonus) {
			$plugin_abonus_set = $plugin_abonus->getSet();
			$hasabonus = !(empty($plugin_abonus_set['open']));

			if (empty($plugin_abonus_set['open'])) {
				$sql .= ' and ( limitaagentlevels = "" or  limitaagentlevels is null )';
			}

		}
		 else {
			$sql .= ' and ( limitaagentlevels = "" or  limitaagentlevels is null )';
		}

		$sql .= ' and gettype=1 and (total=-1 or total>0) and ( timelimit = 0 or  (timelimit=1 and timeend>unix_timestamp()))';
		$sql .= ' order by displayorder desc, id desc  ';
		$list = set_medias(pdo_fetchall($sql, $param), 'thumb');

		if (empty($list)) {
			$list = array();
		}


		if (!(empty($goodid))) {
			$goodparam[':uniacid'] = $_W['uniacid'];
			$goodparam[':id'] = $goodid;
			$sql = 'select id,cates,marketprice,merchid   from ' . tablename('ewei_shop_goods');
			$sql .= ' where uniacid=:uniacid and id =:id order by id desc LIMIT 1 ';
			$good = pdo_fetch($sql, $goodparam);
		}


		$cates = explode(',', $good['cates']);

		if (!(empty($list))) {
			foreach ($list as $key => &$row ) {
				$row = com('coupon')->setCoupon($row, time());
				$row['thumb'] = tomedia($row['thumb']);
				$row['timestr'] = '永久有效';

				if (empty($row['timelimit'])) {
					if (!(empty($row['timedays']))) {
						$row['timestr'] = '自领取日后' . $row['timedays'] . '天有效';
					}

				}
				 else if ($time <= $row['timestart']) {
					$row['timestr'] = '有效期至:' . date('Y-m-d', $row['timestart']) . '-' . date('Y-m-d', $row['timeend']);
				}
				 else {
					$row['timestr'] = '有效期至:' . date('Y-m-d', $row['timeend']);
				}

				if ($row['backtype'] == 0) {
					$row['backstr'] = '立减';
					$row['backmoney'] = (double) $row['deduct'];
					$row['backpre'] = true;

					if ($row['enough'] == '0') {
						$row['color'] = 'org ';
					}
					 else {
						$row['color'] = 'blue';
					}
				}
				 else if ($row['backtype'] == 1) {
					$row['backstr'] = '折';
					$row['backmoney'] = (double) $row['discount'];
					$row['color'] = 'red ';
				}
				 else if ($row['backtype'] == 2) {
					if ($row['coupontype'] == '0') {
						$row['color'] = 'red ';
					}
					 else {
						$row['color'] = 'pink ';
					}

					if (0 < $row['backredpack']) {
						$row['backstr'] = '返现';
						$row['backmoney'] = (double) $row['backredpack'];
						$row['backpre'] = true;
					}
					 else if (0 < $row['backmoney']) {
						$row['backstr'] = '返利';
						$row['backmoney'] = (double) $row['backmoney'];
						$row['backpre'] = true;
					}
					 else if (!(empty($row['backcredit']))) {
						$row['backstr'] = '返积分';
						$row['backmoney'] = (double) $row['backcredit'];
					}

				}


				$limitmemberlevels = explode(',', $row['limitmemberlevels']);
				$limitagentlevels = explode(',', $row['limitagentlevels']);
				$limitpartnerlevels = explode(',', $row['limitpartnerlevels']);
				$limitaagentlevels = explode(',', $row['limitaagentlevels']);
				$p = 0;

				if ($row['islimitlevel'] == 1) {
					$openid = trim($_W['openid']);
					$member = m('member')->getMember($openid);
					if (!(empty($row['limitmemberlevels'])) || ($row['limitmemberlevels'] == '0')) {
						$level1 = pdo_fetchall('select * from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and  id in (' . $row['limitmemberlevels'] . ') ', array(':uniacid' => $_W['uniacid']));

						if (in_array($member['level'], $limitmemberlevels)) {
							$p = 1;
						}

					}


					if ((!(empty($row['limitagentlevels'])) || ($row['limitagentlevels'] == '0')) && $hascommission) {
						$level2 = pdo_fetchall('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid and id  in (' . $row['limitagentlevels'] . ') ', array(':uniacid' => $_W['uniacid']));

						if (($member['isagent'] == '1') && ($member['status'] == '1')) {
							if (in_array($member['agentlevel'], $limitagentlevels)) {
								$p = 1;
							}

						}

					}


					if ((!(empty($row['limitpartnerlevels'])) || ($row['limitpartnerlevels'] == '0')) && $hasglobonus) {
						$level3 = pdo_fetchall('select * from ' . tablename('ewei_shop_globonus_level') . ' where uniacid=:uniacid and  id in(' . $row['limitpartnerlevels'] . ') ', array(':uniacid' => $_W['uniacid']));

						if (($member['ispartner'] == '1') && ($member['partnerstatus'] == '1')) {
							if (in_array($member['partnerlevel'], $limitpartnerlevels)) {
								$p = 1;
							}

						}

					}


					if ((!(empty($row['limitaagentlevels'])) || ($row['limitaagentlevels'] == '0')) && $hasabonus) {
						$level4 = pdo_fetchall('select * from ' . tablename('ewei_shop_abonus_level') . ' where uniacid=:uniacid and  id in (' . $row['limitaagentlevels'] . ') ', array(':uniacid' => $_W['uniacid']));

						if (($member['isaagent'] == '1') && ($member['aagentstatus'] == '1')) {
							if (in_array($member['aagentlevel'], $limitaagentlevels)) {
								$p = 1;
							}

						}

					}

				}
				 else {
					$p = 1;
				}

				if ($p == 1) {
					$p = 0;
					$limitcateids = explode(',', $row['limitgoodcateids']);
					$limitgoodids = explode(',', $row['limitgoodids']);

					if (($row['limitgoodcatetype'] == 0) && ($row['limitgoodtype'] == 0)) {
						$p = 1;
					}


					if ($row['limitgoodcatetype'] == 1) {
						$result = array_intersect($cates, $limitcateids);

						if (0 < count($result)) {
							$p = 1;
						}

					}


					if ($row['limitgoodtype'] == 1) {
						$isin = in_array($good['id'], $limitgoodids);

						if ($isin) {
							$p = 1;
						}

					}


					if ($p == 0) {
						unset($list[$key]);
					}

				}
				 else {
					unset($list[$key]);
				}
			}

			unset($row);
		}


		return array_values($list);
	}

	public function pay_coupon()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$id = intval($_GPC['id']);
		$coupon = pdo_fetch('select * from ' . tablename('ewei_shop_coupon') . ' where id=:id and uniacid=:uniacid  limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		$coupon = com('coupon')->setCoupon($coupon, time());

		if (empty($coupon['gettype'])) {
			app_error(AppError::$CouponBuyError, '无法' . $coupon['gettypestr']);
		}


		if ($coupon['total'] != -1) {
			if ($coupon['total'] <= 0) {
				app_error(AppError::$CouponBuyError, '优惠券数量不足');
			}

		}


		if (!($coupon['canget'])) {
			app_error(AppError::$CouponBuyError, '您已超出' . $coupon['gettypestr'] . '次数限制');
		}


		if ((0 < $coupon['money']) || (0 < $coupon['credit'])) {
			app_error(AppError::$CouponBuyError, '此优惠券需要前往领卷中心兑换');
		}


		$logno = m('common')->createNO('coupon_log', 'logno', 'CC');
		$log = array('uniacid' => $_W['uniacid'], 'merchid' => $coupon['merchid'], 'openid' => $openid, 'logno' => $logno, 'couponid' => $id, 'status' => 0, 'paystatus' => -1, 'creditstatus' => -1, 'createtime' => time(), 'getfrom' => 1);
		pdo_insert('ewei_shop_coupon_log', $log);
		$result = com('coupon')->payResult($log['logno']);

		if (is_error($result)) {
			app_error(AppError::$CouponBuyError, '领取失败(' . $result['errno'] . ') ' . $result['message']);
		}


		app_json(array('dataid' => $result['dataid'], 'coupontype' => $result['coupontype']));
	}

	protected function merchData()
	{
		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
		if ($merch_plugin && $merch_data['is_openmerch']) {
			$is_openmerch = 1;
		}
		 else {
			$is_openmerch = 0;
		}

		return array('is_openmerch' => $is_openmerch, 'merch_plugin' => $merch_plugin, 'merch_data' => $merch_data);
	}
}


?>