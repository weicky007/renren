<?php
//dezend by http://www.yunlu99.com/
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require __DIR__ . '/base.php';
class Room_EweiShopV2Page extends Base_EweiShopV2Page
{
	/**
     * 获取列表
     * @return false|mixed|string
     * @author likexin
     */
	public function get_list()
	{
		global $_GPC;
		global $_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 5;
		$this->wxliveModel->flushLiveStatus($_W['uniacid']);
		$condition = ' status = 1 AND uniacid = :uniacid ';
		$params = array(':uniacid' => $_W['uniacid']);
		$condition .= ' ORDER BY `is_top` DESC, `local_live_status` DESC, `start_time` ASC';
		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_wxlive') . (' WHERE 1 AND ' . $condition) . ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);

		if (!empty($list)) {
			foreach ($list as &$row) {
				$row['goods_list'] = json_decode($row['goods_json'], true);
				unset($row['goods_json']);

				if ($row['local_live_status'] == 0) {
					$row['date_text'] = date('m月d日H:i', $row['start_time']);
				}
			}
		}

		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_wxlive') . (' WHERE 1 and ' . $condition), $params);
		return app_json(array('list' => $list, 'pagesize' => $psize, 'total' => $total, 'page' => $pindex));
	}

	/**
     * 查询列表
     * @author likexin
     */
	public function get_list3()
	{
		global $_GPC;
		global $_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 5;
		$topOffset = intval($_GPC['top_offset']);
		$topEnd = false;
		$time = time();
		$list = array();

		if (!$topEnd) {
			$condition = ' is_top = 1 AND status = 1 AND end_time > :end_time AND uniacid = :uniacid ';
			$params = array(':uniacid' => $_W['uniacid'], ':end_time' => $time);
			$condition .= ' ORDER BY `start_time` ASC, `is_recommend` DESC';
			$condition .= ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
			$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_wxlive') . (' WHERE 1 and ' . $condition), $params);

			if (count($list) <= $psize) {
				$topEnd = false;
			}
		}

		if (!empty($list)) {
			foreach ($list as &$row) {
				$row['goods_list'] = json_decode($row['goods_json'], true);
				unset($row['goods_json']);
			}

			$list = $this->wxliveModel->handleStatus($list);
		}

		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_wxlive') . (' WHERE 1 and ' . $condition), $params);
		return app_json(array('list' => $list, 'pagesize' => $psize, 'total' => $total, 'page' => $pindex, 'top_end' => $topEnd));
	}

	public function get_list2()
	{
		global $_W;
		global $_GPC;
		$list = array();
		$topEnd = intval($_GPC['top_end']);
		$topOffset = intval($_GPC['top_offset']);
		$topPage = intval($_GPC['top_page']);
		$page = max(1, intval($_GPC['page']));
		$pageSize = 5;
		$time = time();

		if (empty($topEnd)) {
			$condition = ' is_top = 1 AND status = 1 AND end_time > :end_time AND uniacid = :uniacid ORDER BY `start_time` ASC, `is_recommend` DESC LIMIT ' . ($page - 1) * $pageSize . ',' . $pageSize;
			$params = array(':uniacid' => $_W['uniacid'], ':end_time' => $time);
			$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_wxlive') . (' WHERE 1 and ' . $condition), $params);

			if (count($list) < $pageSize) {
				$topOffset = $pageSize - count($list);
				$topPage = $page;
			}
		}

		if (count($list) < $pageSize) {
			$page2 = $page - $topPage;
			$pageSize2 = $pageSize - $topOffset;
			$condition = ' (is_top = 0 OR end_time < :end_time) AND status = 1 AND uniacid = :uniacid ORDER BY `is_recommend` DESC LIMIT ' . ($page2 - 1) * $pageSize2 . ',' . $pageSize2;
			$params = array(':uniacid' => $_W['uniacid'], ':end_time' => $time);
			$list2 = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_wxlive') . (' WHERE 1 and ' . $condition), $params);
			$list = array_merge($list, $list2);
		}

		if (!empty($list)) {
			foreach ($list as &$row) {
				$row['goods_list'] = json_decode($row['goods_json'], true);
				unset($row['goods_json']);
			}

			$list = $this->wxliveModel->handleStatus($list);
		}

		$condition = ' AND status = 1 AND uniacid = :uniacid';
		$params = array(':uniacid' => $_W['uniacid']);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_wxlive') . (' WHERE 1 and ' . $condition), $params);
		return app_json(array('list' => $list, 'pagesize' => $pageSize, 'total' => $total, 'page' => $page, 'top_end' => $topEnd));
	}
}

?>
