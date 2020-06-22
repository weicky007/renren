<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

return array(
	'version' => '1.0',
	'id'      => 'wxlive',
	'name'    => '小程序直播',
	'v3'      => true,
	'menu'    => array(
		'title'     => '直播间管理',
		'plugincom' => 1,
		'icon'      => 'page',
		'items'     => array(
			array('title' => '直播间管理', 'route' => 'room')
		)
	)
);

?>
