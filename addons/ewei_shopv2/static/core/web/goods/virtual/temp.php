<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Temp_EweiShopV2Page extends ComWebPage
{
	public function __construct($_com = 'virtual')
	{
		parent::__construct($_com);
	}

	public function main()
	{
		global $_W;
		global $_GPC;
		$page = ((empty($_GPC['page']) ? '' : $_GPC['page']));
		$pindex = max(1, intval($page));
		$psize = 12;
		$kw = ((empty($_GPC['keyword']) ? '' : $_GPC['keyword']));
		$items = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_virtual_type') . ' WHERE uniacid=:uniacid and merchid=0 and title like :name order by id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize, array(':name' => '%' . $kw . '%', ':uniacid' => $_W['uniacid']));
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_virtual_type') . ' WHERE uniacid=:uniacid and merchid=0 and title like :name order by id desc ', array(':uniacid' => $_W['uniacid'], ':name' => '%' . $kw . '%'));
		$pager = pagination($total, $pindex, $psize);
		$category = pdo_fetchall('select * from ' . tablename('ewei_shop_virtual_category') . ' where uniacid=:uniacid and merchid=0 order by id desc', array(':uniacid' => $_W['uniacid']), 'id');
		include $this->template();
	}

	public function add()
	{
		$this->post();
	}

	public function edit()
	{
		$this->post();
	}

	protected function post()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$datacount = 0;

		if (!empty($id)) {
			$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_virtual_type') . ' WHERE id=:id and uniacid=:uniacid and merchid=0', array(':id' => $id, ':uniacid' => $_W['uniacid']));
			$item['fields'] = iunserializer($item['fields']);
			$datacount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_virtual_data') . ' where typeid=:typeid and uniacid=:uniacid and merchid=0 and openid=\'\' limit 1', array(':typeid' => $id, ':uniacid' => $_W['uniacid']));
		}


		if ($_W['ispost']) {
			$keywords = $_GPC['tp_kw'];
			$names = $_GPC['tp_name'];

			if (!empty($keywords)) {
				$data = array();

				foreach ($keywords as $key => $val ) {
					$data[$keywords[$key]] = $names[$key];
				}
			}


			$insert = array('uniacid' => $_W['uniacid'], 'cate' => intval($_GPC['cate']), 'title' => trim($_GPC['tp_title']), 'fields' => iserializer($data));

			if (empty($id)) {
				pdo_insert('ewei_shop_virtual_type', $insert);
				$id = pdo_insertid();
				plog('virtual.temp.edit', '添加模板 ID: ' . $id);
			}
			 else {
				pdo_update('ewei_shop_virtual_type', $insert, array('id' => $id));
				plog('virtual.temp.edit', '编辑模板 ID: ' . $id);
			}

			show_json(1, array('url' => webUrl('goods/virtual/temp')));
		}


		$category = pdo_fetchall('select * from ' . tablename('ewei_shop_virtual_category') . ' where uniacid=:uniacid and merchid=0 order by id desc', array(':uniacid' => $_W['uniacid']), 'id');
		include $this->template();
	}

	public function tpl()
	{
		global $_W;
		global $_GPC;
		$kw = $_GPC['kw'];
		include $this->template('goods/virtual/temp/tpl');
		exit();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}


		$types = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_virtual_type') . ' WHERE id in( ' . $id . ' ) and merchid=0 AND uniacid=' . $_W['uniacid']);

		foreach ($types as $type ) {
			pdo_delete('ewei_shop_virtual_type', array('id' => $type['id']));
			pdo_delete('ewei_shop_virtual_data', array('typeid' => $type['id']));
			plog('virtual.temp.delete', '删除模板 ID: ' . $type['id']);
		}

		show_json(1, array('url' => webUrl('goods/virtual')));
	}
}


?>