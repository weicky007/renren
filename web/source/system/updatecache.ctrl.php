<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('cache');
load()->model('setting');
load()->object('cloudapi');

$dos = array('updatecache');
$do = in_array($do, $dos) ? $do : '';

if ('updatecache' == $do) {
	cache_updatecache();
	if ($_W['isajax']) {
	iajax(0, '更新缓存成功！', '');
}
	itoast('更新缓存成功', '', 'success');
}
