<?php
//dezend by http://www.yunlu99.com/
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require_once EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Base_EweiShopV2Page extends AppMobilePage
{
	public $wxliveModel;

	public function __construct()
	{
		parent::__construct();
		$this->wxliveModel = p('wxlive');

		if (!$this->wxliveModel) {
			exit(app_error(-1, '系统为安装小程序直播'));
		}
	}
}

?>
