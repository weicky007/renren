<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Mood_EweiShopV2Page extends PluginMobilePage
{
	/**
	public function main()
	{
		require_once $this->template();
	}

	/**
	public function relation()
	{
		global $_W;
		global $_GPC;
		$this->model->dd(true, $_W, $_GPC);
	}
}

?>