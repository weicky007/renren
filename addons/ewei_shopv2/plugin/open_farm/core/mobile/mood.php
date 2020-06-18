<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require_once 'chicken.php';
class Mood_EweiShopV2Page extends PluginMobilePage
{
	/**
	private $table = 'ewei_open_farm_mood';
	/**
	private $sonTable = 'ewei_open_farm_mood_image';
	/**
	private $field = array('id', 'uniacid', 'logo', 'create_time');
	/**
	private $sonField = array('id', 'uniacid', 'picture', 'create_time');
	/**
	private $message = array('logo' => '请选择心情logo', 'picture_list' => '请选择心情图');
	/**
	private $openid = '';

	/**
	public function __construct()
	{
		parent::__construct();
		global $_W;
		$_W['openid'] = $_W['openid'];
	}

	/**
	public function main()
	{
		require_once $this->template();
	}

	/**
	public function getInfo()
	{
		global $_W;
		$where = array('uniacid' => $_W['uniacid']);
		$moodInfo = pdo_get($this->table, $where);
		$moodInfo['show_logo'] = tomedia($moodInfo['logo']);
		$moodInfo['picture_list'] = $this->getSon();
		$this->model->returnJson($moodInfo);
	}

	/**
	public function getSon()
	{
		global $_W;
		$where = array('uniacid' => $_W['uniacid']);
		$list = pdo_getall($this->sonTable, $where);
		$list = $this->model->forTomedia($list, 'picture', 'show_picture');
		$data = array();

		foreach ($list as $item) {
			$data[] = $item;
		}

		return $data;
	}

	/**
	public function generateMood()
	{
		global $_W;
		global $_GPC;
		$data = array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']);
		$url = mobileUrl('open_farm', $data, true);
		load()->library('qrcode');
		$content = $url;
		$errorCorrectionLevel = 'L';
		$qrCodeFolder = __DIR__ . '/../../static/mobile/qr_code/';

		if (!file_exists($qrCodeFolder)) {
			mkdir($qrCodeFolder, 511, true);
		}

		$qrCodeName = $_W['uniacid'] . '-' . $_W['openid'] . '.png';
		$imagesPrefix = $_SERVER['DOCUMENT_ROOT'] . '/attachment/';
		$qrCodePath = $qrCodeFolder . $qrCodeName;
		QRcode::png($content, $qrCodePath, $errorCorrectionLevel, 5.21);
		$chicken = new Chicken_EweiShopV2Page();
		$userInfo = $chicken->getInfo(true);
		$backgroundPath = tomedia($_GPC['__input']['background']);
		$picturePath = tomedia($_GPC['__input']['picture']);
		$saveFolder = __DIR__ . '/../../static/mobile/portrait/';
		$portraitPath = $saveFolder . $_W['uniacid'] . '-' . $_W['openid'] . '.jpg';
		$nickname = $userInfo['name'];
		$autograph = $_GPC['__input']['autograph'];
		$save = true;
		$saveFolder = __DIR__ . '/../../static/mobile/mood/';

		if (!file_exists($saveFolder)) {
			mkdir($saveFolder, 511, true);
		}

		$saveName = $_W['uniacid'] . '-' . $_W['openid'] . '.png';
		$imageUrl = $_W['siteroot'] . 'addons/ewei_shopv2/plugin/open_farm/static/mobile/mood/' . $saveName;
		$savePath = $saveFolder . $saveName;
		require_once __DIR__ . '/mood_image.php';
		$info = MoodImage::main($backgroundPath, $picturePath, $portraitPath, $qrCodePath, $nickname, $autograph, $save, $savePath);
		unlink($qrCodePath);

		if ($save) {
			$this->model->returnJson($imageUrl, false, '生成成功!');
		}
	}
}

?>