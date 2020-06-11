<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Room_EweiShopV2Page extends PluginMobileLoginPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$uniacid = intval($_W['uniacid']);
		$openid = trim($_W['openid']);
		$roomid = trim($_GPC['id']);
		if (empty($roomid)) 
		{
			$this->message('指定直播间不存在');
		}
		$room = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_live') . ' where uniacid = :uniacid and id = :roomid ', array(':uniacid' => $uniacid, ':roomid' => $roomid));
		if (empty($room)) 
		{
			$this->message('指定直播间不存在');
		}
		if ($room['covertype'] == 1) 
		{
			$room['thumb'] = tomedia($room['cover']);
		}
		if ($room['livetype'] == 2) 
		{
			$room['url'] = $room['video'];
		}
		$room_goods = array();
		if (!(empty($room['goodsid']))) 
		{
			$room_goods = pdo_fetchall('select id,thumb,title,liveprice,marketprice from ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid and id in (' . $room['goodsid'] . ') and islive > 0  ', array(':uniacid' => $_W['uniacid']));
		}
		$coupon = false;
		if (!(empty($room['couponid']))) 
		{
			$coupon = true;
			$room_coupon = pdo_fetchall('select lc.*, sc.couponname, sc.coupontype,sc.backtype ,sc.enough from ' . tablename('ewei_shop_live_coupon') . ' lc left join ' . tablename('ewei_shop_coupon') . ' sc on sc.id=lc.couponid where lc.uniacid=:uniacid and lc.roomid=:roomid ', array(':uniacid' => $_W['uniacid'], ':roomid' => $roomid));
		}
		$packet = false;
		if (0 < $room['packetmoney']) 
		{
			$packet = true;
		}
		$member = m('member')->getMember($_W['openid']);
		$favorite = $this->model->isFavorite($_W['openid'], $roomid);
		$emojiList = $this->model->getEmoji();
		$records = $this->model->handleRecords($roomid, false, $member['id']);
		$fullscreen = intval($room['screen']);
		$video = trim($room['url']);
		$poster = $room['thumb'];
		if (!(empty($video)) && ($room['livetype'] != 2)) 
		{
			$video_info = $this->model->getLiveInfo($video, $room['liveidentity']);
			if (!(is_error($video_info)) && !(empty($video_info['hls_url']))) 
			{
				$video = $video_info['hls_url'];
				pdo_update('ewei_shop_live', array('thumb' => $video_info['poster']), array('uniacid' => $uniacid, 'id' => $roomid));
			}
			else 
			{
				$video = '';
			}
		}
		$wsConfig = json_encode(array('address' => $this->model->getWsAddress(), 'roomid' => $roomid, 'uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'uid' => $member['id'], 'nickname' => $member['nickname'], 'attachurl' => $_W['attachurl'], 'isMobile' => is_mobile(), 'isIos' => is_ios(), 'fullscreen' => $fullscreen));
		$view = pdo_fetch('select * from ' . tablename('ewei_shop_live_view') . ' where uniacid = ' . $uniacid . ' and openid = \'' . $openid . '\' and roomid = ' . $roomid . ' ');
		$viewing = pdo_fetch('select max(viewing) as viewing from ' . tablename('ewei_shop_live_view') . ' where uniacid = ' . $uniacid . ' and openid = \'' . $openid . '\' ');
		if (!(empty($view))) 
		{
			pdo_update('ewei_shop_live_view', array('viewing' => $viewing['viewing'] + 1), array('uniacid' => $uniacid, 'openid' => $openid, 'roomid' => $roomid));
		}
		else 
		{
			$view_data = array('uniacid' => $uniacid, 'openid' => $openid, 'roomid' => $roomid);
			$view_data['viewing'] = ((!(empty($viewing)) ? $viewing['viewing'] + 1 : 1));
			pdo_insert('ewei_shop_live_view', $view_data);
		}
		$shop = set_medias(m('common')->getSysset('shop'), 'logo');
		$setting = pdo_fetch('select * from ' . tablename('ewei_shop_live_setting') . ' where uniacid = :uniacid  ', array(':uniacid' => $uniacid));
		$_W['shopshare'] = array('title' => (!(empty($room['share_title'])) ? $room['share_title'] : $shop['name']), 'imgUrl' => (!(empty($room['share_icon'])) ? tomedia($room['share_icon']) : tomedia($room['thumb'])), 'link' => (!(empty($room['share_url'])) ? $room['share_url'] : mobileUrl('live/room', array('id' => $roomid), true)), 'desc' => (!(empty($room['share_desc'])) ? $room['share_desc'] : $room['title']));
		include $this->template();
	}
	public function favorite() 
	{
		global $_W;
		global $_GPC;
		$roomid = intval($_GPC['roomid']);
		if (!(empty($roomid))) 
		{
			$favorite = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_live_favorite') . 'WHERE uniacid=:uniacid AND roomid=:roomid AND openid=:openid LIMIT 1', array(':uniacid' => $_W['uniacid'], ':roomid' => $roomid, ':openid' => $_W['openid']));
			if (empty($favorite)) 
			{
				pdo_insert('ewei_shop_live_favorite', array('uniacid' => $_W['uniacid'], 'roomid' => $roomid, 'openid' => $_W['openid'], 'createtime' => time()));
				pdo_query('update ' . tablename('ewei_shop_live') . ' set subscribe = subscribe+1 where id = ' . $roomid . ' and uniacid = ' . intval($_W['uniacid']) . ' ');
				show_json(1, array('favorite' => 1));
			}
			else 
			{
				pdo_update('ewei_shop_live_favorite', array('deleted' => (!(empty($favorite['deleted'])) ? 0 : 1), 'createtime' => time()), array('id' => $favorite['id']));
				if ($favorite['deleted'] == 0) 
				{
					pdo_query('update ' . tablename('ewei_shop_live') . ' set subscribe = subscribe+1 where id = ' . $roomid . ' and uniacid = ' . intval($_W['uniacid']) . ' ');
				}
				show_json(1, array('favorite' => (!(empty($favorite['deleted'])) ? 1 : 0)));
			}
		}
		show_json(0, '参数错误');
	}
	public function draw() 
	{
		global $_W;
		global $_GPC;
		$type = trim($_GPC['type']);
	}
}
?>