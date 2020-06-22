<?php
/*WEMECMS  http://shop258163088.taobao.com*/
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require EWEI_SHOPV2_PLUGIN . 'merch/core/inc/page_merch.php';
class Index_EweiShopV2Page extends MerchWebPage
{
	public function main($goodsfrom = 'sale')
	{
		global $_W;
		global $_GPC;



		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$sqlcondition = $groupcondition = '';
		$condition = ' WHERE g.`uniacid` = :uniacid and g.`merchid`=:merchid';
		$params = array(':uniacid' => $_W['uniacid'], ':merchid' => $_W['merchid']);
		$not_add = 0;
		$merch_user = $_W['merch_user'];
		$maxgoods = intval($merch_user['maxgoods']);

		if (0 < $maxgoods) {
			$sql = 'SELECT COUNT(1) FROM ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid and merchid=:merchid';
			$goodstotal = pdo_fetchcolumn($sql, $params);

			if ($maxgoods <= $goodstotal) {
				$not_add = 1;
			}
		}

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$sqlcondition = ' left join ' . tablename('ewei_shop_goods_option') . ' op on g.id = op.goodsid';
			$groupcondition = ' group by g.`id`';
			$condition .= ' AND (g.`id` = :id or g.`title` LIKE :keyword or g.`goodssn` LIKE :keyword or g.`productsn` LIKE :keyword or op.`title` LIKE :keyword or op.`goodssn` LIKE :keyword or op.`productsn` LIKE :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
			$params[':id'] = $_GPC['keyword'];
		}

		if (!empty($_GPC['cate'])) {
			$_GPC['cate'] = intval($_GPC['cate']);
			$condition .= ' AND FIND_IN_SET(' . $_GPC['cate'] . ',cates)<>0 ';
		}

		if ($goodsfrom == 'sale') {
			$condition .= ' AND  g.`total`>0 and g.`deleted`=0  AND g.`checked`=0';
			$status = 1;
		}


		$sql = 'SELECT COUNT(g.`id`) FROM ' . tablename('ewei_shop_goods') . 'g' . $sqlcondition . $condition . $groupcondition;
		$total = pdo_fetchcolumn($sql, $params);
		$list = array();

		if (!empty($total)) {
			$sql = 'SELECT g.* FROM ' . tablename('ewei_shop_goods') . 'g' . $sqlcondition . $condition . $groupcondition . ' ORDER BY g.`status` DESC, g.`merchdisplayorder` DESC,
                g.`id` DESC  ';
			if (empty($_GPC['export'])) {
				$sql .= 'LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
			}
			$list = pdo_fetchall($sql, $params);

			foreach ($list as $key => &$value) {
				$url = mobileUrl('goods/detail', array('id' => $value['id']), true);
				$value['qrcode'] = m('qrcode')->createQrcode($url);
			}
			if ($_GPC['export'] == 1) {
				$this->model = p('leading');
				$this->model->export($list);
			}
			$pager = pagination2($total, $pindex, $psize);
		}


		$categorys = m('shop')->getFullCategory(true);
		$category = array();

		foreach ($categorys as $cate) {
			$category[$cate['id']] = $cate;
		}

		include $this->template();
	}


public function export()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
		}

		$items = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_goods') . (' WHERE id in( ' . $id . ' ) AND uniacid=') . $_W['uniacid'] . 'AND merchid=' . $_W['merchid']);
		$this->model->export($items);
		//show_json(1, array('url' => referer()));
	}


}

?>
