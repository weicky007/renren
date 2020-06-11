<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Index_EweiShopV2Page extends PluginWebPage 
{
	public function main() 
	{
		global $_W;
		$wsConfig = json_encode(array('address' => $this->model->getWsAddress()));
		$plugin = pdo_fetch('select `desc` from ' . tablename('ewei_shop_plugin') . ' where `identity`=:identyty limit  1', array(':identyty' => 'live'));
		$livenum = pdo_fetchcolumn('SELECT count(0) FROM ' . tablename('ewei_shop_live') . 'WHERE uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));
		$livingnum = pdo_fetchcolumn('SELECT count(0) FROM ' . tablename('ewei_shop_live') . 'WHERE uniacid=:uniacid AND living=1 ', array(':uniacid' => $_W['uniacid']));
		$liveprice = array();
		$liveprice[0] = $this->selectOrderPrice(0);
		$liveprice[7] = $this->selectOrderPrice(7);
		$liveprice[30] = $this->selectOrderPrice(30);
		include $this->template();
	}
	public function selectOrderPrice($day = 0) 
	{
		global $_W;
		if (!(empty($day))) 
		{
			$createtime1 = strtotime(date('Y-m-d', time() - ($day * 3600 * 24)));
			$createtime2 = strtotime(date('Y-m-d', time()));
		}
		else 
		{
			$createtime1 = strtotime(date('Y-m-d', time()));
			$createtime2 = strtotime(date('Y-m-d', time() + (3600 * 24)));
		}
		$sql = 'select id,price,createtime from ' . tablename('ewei_shop_order') . ' where uniacid = :uniacid and liveid>0 and ismr=0 and isparent=0 and (status > 0 or ( status=0 and paytype=3)) and deleted=0 and createtime between :createtime1 and :createtime2';
		$param = array(':uniacid' => $_W['uniacid'], ':createtime1' => $createtime1, ':createtime2' => $createtime2);
		$pdo_res = pdo_fetchall($sql, $param);
		$price = 0;
		foreach ($pdo_res as $arr ) 
		{
			$price += $arr['price'];
		}
		return round($price, 1);
	}
	public function get() 
	{
		global $_W;
		global $_GPC;
		if ($_W['ispost']) 
		{
			$url = trim($_GPC['url']);
			$type = 'auto';
			if (empty($url)) 
			{
				show_json(0, '请输入PC端直播地址');
			}
			if (!(strexists($url, 'http://')) && !(strexists($url, 'https://'))) 
			{
				show_json(0, '直播地址请以http://或https://开头');
			}
			$result = $this->model->getLiveInfo($url, $type);
			if (is_error($result)) 
			{
				show_json(0, $result['message']);
			}
			show_json(1, $result);
		}
		$list = $this->model->getLiveList();
		include $this->template();
	}
	public function service() 
	{
		global $_W;
		$wsConfig = json_encode(array('address' => $this->model->getWsAddress()));
		include $this->template();
	}
}
?>