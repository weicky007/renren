<?php
/*
 * 人人商城V2
 * 
 * @author ewei 狸小狐 
 */
if (!defined('IN_IA')) {
	exit('Access Denied');
}
class Index_EweiShopV2Page extends SystemPage {
	function main() {
		global $_W,$_GPC;
		
		include $this->template();
	}
}