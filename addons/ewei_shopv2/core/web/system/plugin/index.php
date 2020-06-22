<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends SystemPage
{
	public function main()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			if (!empty($_GPC['displayorder'])) {
				foreach ($_GPC['displayorder'] as $id => $displayorder) {
					pdo_update('ewei_shop_plugin', array('status' => $_GPC['status'][$id], 'displayorder' => $displayorder, 'name' => $_GPC['name'][$id], 'thumb' => $_GPC['thumb'][$id], 'desc' => $_GPC['desc'][$id]), array('id' => $id));
				}

				m('plugin')->refreshCache(1);
				show_json(1);
			}
		}

		$condition = ' and iscom=0 and deprecated=0';

		if (!empty($_GPC['keyword'])) {
			$condition .= ' and identity like :keyword or name like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'];
		}

		$list = pdo_fetchall('select * from ' . tablename('ewei_shop_plugin') . (' where 1 ' . $condition . ' order by displayorder asc'), $params);
		$bucket = array();

		foreach ($list as $k => $v) {
			if (is_array($v) && in_array($v['name'], $bucket)) {
				unset($list[$k]);
			}
			else {
				array_push($bucket, $v['name']);
			}
		}

		$list = array_values($list);
		$total = count($list);
		include $this->template();
		exit();
	}

	public function apps()
	{
		header('location:https://www.we7shop.com');
	}

	public function arrayToHashTable($arr, $key)
	{
		$retArr = array();

		foreach ($arr as $k => $val) {
			if (is_array($val) && !empty($val[$key])) {
				$retArr[$val[$key]] = $val;
			}
		}

		return $retArr;
	}
}

?>
