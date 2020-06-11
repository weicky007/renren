<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Room_EweiShopV2Page extends PluginWebPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
		if ($merch_plugin && $merch_data['is_openmerch']) 
		{
			$is_openmerch = 1;
		}
		else 
		{
			$is_openmerch = 0;
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' l.uniacid = :uniacid ';
		$params = array(':uniacid' => $_W['uniacid']);
		if (!(empty($_GPC['keyword']))) 
		{
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' AND l.title LIKE :title';
			$params[':title'] = '%' . trim($_GPC['keyword']) . '%';
		}
		if ($_GPC['state'] != '') 
		{
			$condition .= ' AND l.status = :status';
			$params[':status'] = intval($_GPC['state']);
		}
		if ($_GPC['cate'] != '') 
		{
			$condition .= ' AND lc.name = :cate';
			$params[':cate'] = intval($_GPC['cate']);
		}
		$sql = 'SELECT l.*,lc.name FROM ' . tablename('ewei_shop_live') . ' as l' . "\n\t\t" . '        left join ' . tablename('ewei_shop_live_category') . ' as lc on lc.id = l.category' . "\n\t\t" . '        where  1 and ' . $condition . ' ORDER BY l.displayorder DESC,l.id DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('ewei_shop_live') . ' as l' . "\n\t\t" . '        left join ' . tablename('ewei_shop_live_category') . ' as lc on lc.id = l.category' . "\n\t\t" . '        where  1 and ' . $condition, $params);
		foreach ($list as $key => &$value ) 
		{
			if ($value['covertype'] == 1) 
			{
				$value['thumb'] = $value['cover'];
			}
			$url = mobileUrl('live/room', array('id' => $value['id']), true);
			$value['qrcode'] = m('qrcode')->createQrcode($url);
		}
		unset($value);
		if ($merch_plugin) 
		{
			$merch_user = $merch_plugin->getListUser($list, 'merch_user');
			if (!(empty($list)) && !(empty($merch_user))) 
			{
				foreach ($list as &$row ) 
				{
					$row['merchname'] = (($merch_user[$row['merchid']]['merchname'] ? $merch_user[$row['merchid']]['merchname'] : $_W['shopset']['shop']['name']));
				}
				unset($row);
			}
		}
		$pager = pagination($total, $pindex, $psize);
		$category = pdo_fetchall('select id,`name`,thumb from ' . tablename('ewei_shop_live_category') . ' where uniacid=:uniacid order by displayorder desc', array(':uniacid' => $_W['uniacid']), 'id');
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
		$uniacid = intval($_W['uniacid']);
		$id = intval($_GPC['id']);
		if (0 < $id) 
		{
			$item = pdo_fetch('select * from ' . tablename('ewei_shop_live') . ' where uniacid = ' . $uniacid . ' and id = ' . $id . ' ');
			$coupon = array();
			if (!(empty($item['couponid']))) 
			{
				$coupon = pdo_fetchall('select c.couponname,c.thumb,c.id,lc.coupontotal,lc.couponlimit from ' . tablename('ewei_shop_live_coupon') . ' as lc' . "\n" . '                        left join ' . tablename('ewei_shop_coupon') . ' as c on c.id = lc.couponid' . "\n" . '                        where lc.uniacid = ' . $uniacid . ' and lc.roomid = ' . $id . ' and lc.couponid in(' . $item['couponid'] . ')  ');
			}
			$couponid = $item['couponid'];
		}
		if ($_W['ispost']) 
		{
			$data = array('displayorder' => intval($_GPC['displayorder']), 'uniacid' => $uniacid, 'title' => trim($_GPC['title']), 'liveidentity' => trim($_GPC['liveidentity']), 'url' => trim($_GPC['url']), 'video' => trim($_GPC['video']), 'thumb' => save_media($_GPC['thumb']), 'cover' => save_media($_GPC['cover']), 'covertype' => intval($_GPC['covertype']), 'goodsid' => $_GPC['goodsid'], 'couponid' => $_GPC['couponid'], 'livetype' => intval($_GPC['livetype']), 'screen' => intval($_GPC['screen']), 'category' => intval($_GPC['category']), 'livetime' => strtotime($_GPC['livetime']), 'recommend' => intval($_GPC['recommend']), 'hot' => intval($_GPC['hot']), 'status' => intval($_GPC['status']), 'introduce' => m('common')->html_images($_GPC['introduce']), 'packetmoney' => floatval($_GPC['packetmoney']), 'packettotal' => intval($_GPC['packettotal']), 'packetprice' => floatval($_GPC['packetprice']), 'packetdes' => trim($_GPC['packetdes']), 'share_title' => trim($_GPC['share_title']), 'share_icon' => trim($_GPC['share_icon']), 'share_desc' => trim($_GPC['share_desc']), 'share_url' => trim($_GPC['share_url']));
			if ($data['packetmoney'] < 0) 
			{
				show_json(0, '请填写正确的红包总额！');
			}
			if ($data['packettotal'] < 0) 
			{
				show_json(0, '请填写正确的红包个数！');
			}
			if ($data['packetprice'] < 0) 
			{
				show_json(0, '请填写正确的红包金额！');
			}
			if ($data['livetype'] == 2) 
			{
				if (!(empty($data['video']))) 
				{
					if (!($this->check_url($data['video']))) 
					{
						show_json(0, '请输入合法的地址！');
					}
				}
				else 
				{
					show_json(0, '视频流地址不能为空！');
				}
			}
			else if (!(empty($data['url']))) 
			{
				if (!($this->check_url($data['url']))) 
				{
					show_json(0, '请输入合法的地址！');
				}
			}
			else 
			{
				show_json(0, '直播地址不能为空！');
			}
			if (!(empty($_GPC['goodsid']))) 
			{
				$goodsid = $data['goodsid'];
				$data['goodsid'] = ((is_array($_GPC['goodsid']) ? implode(',', $_GPC['goodsid']) : 0));
			}
			if (!(empty($data['couponid']))) 
			{
				foreach ($_GPC['couponid'] as $key => $value ) 
				{
					if (strpos($couponid, $value) !== false) 
					{
						pdo_update('ewei_shop_live_coupon', array('coupontotal' => $_GPC['coupontotal' . $value . ''], 'couponlimit' => $_GPC['couponlimit' . $value . '']), array('uniacid' => $uniacid, 'roomid' => $id, 'couponid' => $value));
						$couponid = str_replace($value, '', $couponid);
					}
					else 
					{
						$data_coupon = array('uniacid' => $uniacid, 'roomid' => $id, 'couponid' => intval($value), 'coupontotal' => intval($_GPC['coupontotal' . $value . '']), 'couponlimit' => intval($_GPC['couponlimit' . $value . '']));
						pdo_insert('ewei_shop_live_coupon', $data_coupon);
					}
				}
				$couponid = array_filter(explode(',', $couponid));
				if (!(empty($couponid))) 
				{
					foreach ($couponid as $value ) 
					{
						pdo_delete('ewei_shop_live_coupon', array('couponid' => $value, 'uniacid' => $uniacid, 'roomid' => $id));
					}
				}
			}
			$data['couponid'] = ((is_array($_GPC['couponid']) ? implode(',', $_GPC['couponid']) : 0));
			if (!(empty($id))) 
			{
				if ($item['livetime'] < $data['livetime']) 
				{
					$data['subscribenotice'] = 0;
				}
				pdo_update('ewei_shop_live', $data, array('id' => $id));
				plog('live.room.edit', '编辑直播间 ID: ' . $id . ' <br/>全返名称: ' . $data['title']);
			}
			else 
			{
				$data['createtime'] = time();
				pdo_insert('ewei_shop_live', $data);
				$id = pdo_insertid();
				plog('live.room.add', '添加直播间 ID: ' . $id . '  <br/>全返名称: ' . $data['title']);
			}
			$option = $_GPC['livegoods'];
			$goodsliveid = $item['goodsid'];
			if (!(empty($goodsid))) 
			{
				foreach ($goodsid as $key => $value ) 
				{
					$good_data = pdo_fetch('select title,thumb,marketprice,goodssn,productsn,hasoption' . "\n" . '                            from ' . tablename('ewei_shop_goods') . ' where id = ' . $value . ' and uniacid = ' . $uniacid . ' ');
					if (empty($data['thumb'])) 
					{
						$data['thumb'] = save_media($good_data['thumb']);
					}
					$good_data['option'] = (($option[$value] ? $option[$value] : ''));
					if ($good_data['hasoption'] && empty($good_data['option'])) 
					{
						show_json(0, '请选择商品规格！');
					}
					$liveprice = floatval($_GPC['liveprice' . $value . '']);
					if (!(empty($good_data['option']))) 
					{
						$liveOption = array_filter(explode(',', $good_data['option']));
						pdo_update('ewei_shop_goods_option', array('islive' => 0), array('uniacid' => $uniacid, 'goodsid' => $value));
						foreach ($liveOption as $val ) 
						{
							$livegoodsoption = $_GPC['livegoodsoption' . $val . ''];
							$liveprice = (($liveprice < $livegoodsoption ? $livegoodsoption : $liveprice));
							$optionData = array('liveprice' => floatval($livegoodsoption), 'islive' => $id);
							pdo_update('ewei_shop_goods_option', $optionData, array('uniacid' => $uniacid, 'id' => intval($val)));
						}
					}
					else 
					{
						$liveprice = floatval($_GPC['goods' . $value . '']);
					}
					if (strpos($goodsliveid, $value) !== false) 
					{
						$goodsliveid = str_replace($value, '', $goodsliveid);
					}
					pdo_update('ewei_shop_goods', array('liveprice' => $liveprice, 'islive' => $id), array('uniacid' => $uniacid, 'id' => intval($value)));
					unset($liveprice);
				}
				$goodsliveid = array_filter(explode(',', $goodsliveid));
				if (!(empty($goodsliveid))) 
				{
					foreach ($goodsliveid as $value ) 
					{
						pdo_update('ewei_shop_goods', array('islive' => 0), array('uniacid' => $uniacid, 'id' => intval($value)));
					}
				}
				unset($good_data, $goodsid);
			}
			show_json(1, array('url' => webUrl('live/room/edit', array('id' => $id, 'tab' => str_replace('#tab_', '', $_GPC['tab'])))));
		}
		$liveidentity = array( array('type' => 0, 'identity' => 'shuidi', 'name' => '水滴直播'), array('type' => 0, 'identity' => 'qlive', 'name' => '青果直播'), array('type' => 0, 'identity' => 'ys7', 'name' => '萤石直播'), array('type' => 1, 'identity' => 'panda', 'name' => '熊猫直播'), array('type' => 1, 'identity' => 'douyu', 'name' => '斗鱼直播'), array('type' => 1, 'identity' => 'huajiao', 'name' => '花椒直播'), array('type' => 1, 'identity' => 'yizhibo', 'name' => '一直播'), array('type' => 1, 'identity' => 'inke', 'name' => '映客直播'), array('type' => 2, 'identity' => 'tencentcloud', 'name' => '腾讯云直播'), array('type' => 2, 'identity' => 'alicloud', 'name' => '阿里云直播'), array('type' => 2, 'identity' => 'other', 'name' => '其他直播') );
		$category = pdo_fetchall('select * from ' . tablename('ewei_shop_live_category') . ' where uniacid = ' . $uniacid . ' and enabled = 1 ');
		if ((0 < $id) && !(empty($item['goodsid']))) 
		{
			$goods = pdo_fetchall('select g.id,g.title,g.marketprice,g.liveprice,g.thumb,g.hasoption' . "\n" . '                  from ' . tablename('ewei_shop_goods') . ' as g' . "\n" . '                  where g.id in(' . $item['goodsid'] . ') ');
			foreach ($goods as $key => $value ) 
			{
				if (0 < $value['hasoption']) 
				{
					$goods[$key]['option'] = pdo_fetchall('SELECT id,title,marketprice,specs,liveprice,islive' . "\n" . '                      FROM ' . tablename('ewei_shop_goods_option') . "\n" . '                      WHERE uniacid = :uniacid and goodsid = :goodsid and islive > 0  ORDER BY displayorder DESC,id DESC ', array(':uniacid' => $uniacid, 'goodsid' => $value['id']));
					$optionid = array();
					$goods[$key]['minliveprice'] = $goods[$key]['option'][0]['liveprice'];
					$goods[$key]['maxliveprice'] = $goods[$key]['option'][0]['liveprice'];
					foreach ($goods[$key]['option'] as $k => $val ) 
					{
						Array_push($optionid, $val['id']);
						$goods[$key]['minliveprice'] = (($val['liveprice'] < $goods[$key]['minliveprice'] ? $val['liveprice'] : $goods[$key]['minliveprice']));
						$goods[$key]['maxliveprice'] = (($goods[$key]['maxliveprice'] < $val['liveprice'] ? $val['liveprice'] : $goods[$key]['maxliveprice']));
					}
					$goods[$key]['optionid'] = implode(',', $optionid);
				}
			}
		}
		include $this->template();
	}
	public function check_url($url) 
	{
		if (!(preg_match('/\\b(?:(?:https?|http):\\/\\/|www\\.)[-a-z0-9+&@#\\/%?=~_|!:,.;]*[-a-z0-9+&@#\\/%=~_|]/i', $url))) 
		{
			return false;
		}
		return true;
	}
	public function hasoption() 
	{
		global $_W;
		global $_GPC;
		$uniacid = intval($_W['uniacid']);
		$goodsid = intval($_GPC['goodsid']);
		$id = intval($_GPC['id']);
		$hasoption = 0;
		$params = array(':uniacid' => $uniacid, ':goodsid' => $goodsid);
		$goods = pdo_fetch('select id,title,marketprice,hasoption,liveprice from ' . tablename('ewei_shop_goods') . ' where uniacid = :uniacid and id = :goodsid ', $params);
		if (!(empty($id))) 
		{
			$live = pdo_fetch('select id,title,marketprice,liveprice from ' . tablename('ewei_shop_goods') . "\n" . '                        where id = ' . $id . ' and uniacid = :uniacid and id = :goodsid ', $params);
			$live['isfullback'] = $goods['isfullback'];
		}
		else 
		{
			$live = array('titles' => $goods['title'], 'marketprice' => $goods['marketprice'], 'liveprice' => 0);
		}
		if ($goods['hasoption']) 
		{
			$hasoption = 1;
			$option = array();
			$option = pdo_fetchall('SELECT id,title,marketprice,specs,liveprice' . "\n" . '                FROM ' . tablename('ewei_shop_goods_option') . "\n" . '                WHERE uniacid = :uniacid and goodsid = :goodsid  ORDER BY displayorder DESC,id DESC ', $params);
		}
		else 
		{
			$goods['marketprice'] = $goods['marketprice'];
		}
		include $this->template();
	}
	public function option() 
	{
		global $_W;
		global $_GPC;
		$uniacid = intval($_W['uniacid']);
		$options = ((is_array($_GPC['option']) ? implode(',', array_filter($_GPC['option'])) : 0));
		$options = intval($options);
		$option = pdo_fetch('SELECT id,title FROM ' . tablename('ewei_shop_goods_option') . "\n" . '            WHERE uniacid = ' . $uniacid . ' and id = ' . $options . '  ORDER BY displayorder DESC,id DESC LIMIT 1');
		show_json(1, $option);
	}
	public function deleted() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) 
		{
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}
		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('ewei_shop_live') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		foreach ($items as $item ) 
		{
			pdo_delete('ewei_shop_live', array('id' => $item['id']));
			plog('live.room.deleted', '删除直播间<br/>ID: ' . $item['id'] . '<br/>直播间名称: ' . $item['title']);
			$favoritetotal = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_live_favorite') . ' where roomid = ' . $id . ' ');
			if (0 < $favoritetotal) 
			{
				pdo_delete('ewei_shop_live_favorite', array('roomid' => $item['id']));
			}
			$viewtotal = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_live_view') . ' where roomid = ' . $id . ' ');
			if (0 < $viewtotal) 
			{
				pdo_delete('ewei_shop_live_view', array('roomid' => $item['id']));
			}
			if (function_exists('redis') && !(is_error(redis()))) 
			{
				$this->model->deleteRedisTable($item['id']);
			}
		}
		show_json(1, array('url' => referer()));
	}
	public function query() 
	{
		global $_W;
		global $_GPC;
		$uniacid = intval($_W['uniacid']);
		$kwd = trim($_GPC['keyword']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 8;
		$params = array();
		$params[':uniacid'] = $uniacid;
		$condition = ' and deleted=0 and uniacid=:uniacid and status = 1 and merchid = 0 and status = 1 and type != 4 and ispresell = 0 and bargain = 0 ';
		if (!(empty($kwd))) 
		{
			$condition .= ' AND (`title` LIKE :keywords OR `keywords` LIKE :keywords)';
			$params[':keywords'] = '%' . $kwd . '%';
		}
		$goods = pdo_fetchall('SELECT id,title,thumb,marketprice,total,hasoption' . "\n" . '            FROM ' . tablename('ewei_shop_goods') . "\n" . '            WHERE 1 ' . $condition . ' ORDER BY displayorder DESC,id DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('ewei_shop_goods') . ' WHERE 1 ' . $condition . ' ', $params);
		$pager = pagination($total, $pindex, $psize, '', array('before' => 5, 'after' => 4, 'ajaxcallback' => 'select_page', 'callbackfuncname' => 'select_page'));
		$goods = set_medias($goods, array('thumb'));
		include $this->template();
	}
	public function console() 
	{
		global $_W;
		global $_GPC;
		if (!(function_exists('redis')) || is_error(redis())) 
		{
			$this->message('请联系管理员开启 redis 支持，才能使用直播应用', '', 'error');
			exit();
		}
		$id = intval($_GPC['id']);
		if (empty($id)) 
		{
			$this->message('直播间参数错误');
		}
		$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_live') . ' WHERE uniacid=:uniacid AND id=:id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		if (empty($item)) 
		{
			$this->message('直播间不存在');
		}
		if (empty($item['status'])) 
		{
			$this->message('请先启用直播间');
		}
		if ($item['livetype'] == 2) 
		{
			$item['url'] = $item['video'];
		}
		if ($item['livetype'] < 2) 
		{
			$getVideo = $this->model->getLiveInfo($item['url'], $item['liveidentity']);
			if (!(is_error($getVideo)) && !(empty($getVideo['hls_url']))) 
			{
				$url = $getVideo['hls_url'];
			}
		}
		else 
		{
			$url = $item['url'];
		}
		$url = webUrl('live/room/console_video', array('url' => urlencode($url)));
		$emojiList = $this->model->getEmoji();
		$uid = 'console' . '_' . $_W['uid'] . '_' . $_W['role'] . '_' . $_W['uniacid'];
		$nickname = '管理员' . chr($_W['uid'] + 65);
		$wsConfig = json_encode(array('address' => $this->model->getWsAddress(), 'scene' => 'live', 'roomid' => $id, 'uniacid' => $_W['uniacid'], 'uid' => $uid, 'nickname' => $nickname, 'attachurl' => $_W['attachurl']));
		$records = $this->model->handleRecords($id, true);
		$table_push_record = $this->model->getRedisTable('push_records', '1');
		$push_records = redis()->lRange($table_push_record, 0, -1);
		$push_count = count($push_records);
		if (!(empty($push_records))) 
		{
			foreach ($push_records as $time => &$record ) 
			{
				$record = json_decode($record, true);
			}
			unset($record);
		}
		include $this->template();
	}
	public function console_record() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$pushid = intval($_GPC['pushid']);
		$type = intval($_GPC['type']);
		if (!(empty($id)) && !(empty($pushid))) 
		{
			(($type == 2 ? 'coupon' : 'redpack'));
			$table_redpack = $this->model->getRedisTable('redpack_' . $pushid, $id);
			$list = redis()->lRange($table_redpack, 0, -1);
		}
		if (!(empty($list))) 
		{
			foreach ($list as &$item ) 
			{
				$item = json_decode($item, true);
				if (!(empty($item['used']))) 
				{
					$item['nickname'] = 'a';
				}
			}
			unset($item);
		}
		include $this->template();
	}
	public function property() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$type = trim($_GPC['type']);
		$value = intval($_GPC['value']);
		if (in_array($type, array('status', 'displayorder', 'hot', 'recommend'))) 
		{
			$statusstr = '';
			if ($type == 'status') 
			{
				$typestr = '状态';
				$statusstr = (($value == 1 ? '显示' : '关闭'));
			}
			else if ($type == 'displayorder') 
			{
				$typestr = '排序';
				$statusstr = '序号 ' . $value;
			}
			else if ($type == 'hot') 
			{
				$typestr = '是否热门';
				$statusstr = (($value == 1 ? '是' : '否'));
			}
			else if ($type == 'recommend') 
			{
				$typestr = '是否推荐';
				$statusstr = (($value == 1 ? '是' : '否'));
			}
			$property_update = pdo_update('ewei_shop_live', array($type => $value), array('id' => $id, 'uniacid' => $_W['uniacid']));
			if (!($property_update)) 
			{
				show_json(0, '' . $typestr . '修改失败');
			}
			plog('live.room.edit', '修改直播' . $typestr . '状态   ID: ' . $id . ' ' . $statusstr . ' ');
		}
		show_json(1);
	}
	public function console_video() 
	{
		global $_GPC;
		$url = trim($_GPC['url']);
		if (!(empty($url))) 
		{
			$url = urldecode($url);
		}
		include $this->template();
	}
}
?>