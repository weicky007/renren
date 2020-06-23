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
foreach ($sets as $set ) 
{
	$_W['uniacid'] = $set['uniacid'];
	if (empty($_W['uniacid'])) 
	{
		continue;
	}
	$trade = m('common')->getSysset('trade', $_W['uniacid']);
	$goods = pdo_fetchall('select id,preselltimeend,presellover,ispresell from ' . tablename('ewei_shop_goods') . ' where uniacid = ' . $_W['uniacid'] . ' and ispresell > 0 and deleted = 0 ');
	foreach ($goods as $key => $value ) 
	{
		if (($value['ispresell'] == 1) && ($value['presellover'] == 0) && ($value['preselltimeend'] < time())) 
		{
			$value['status'] = 0;
			pdo_update('ewei_shop_goods', array('status' => $value['status']), array('id' => $value['id']));
		}
	}
}
?>