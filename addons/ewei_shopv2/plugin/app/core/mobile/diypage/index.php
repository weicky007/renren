<?php
//dezend by haha解密更新  维护群：468773368 
?>
<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Index_EweiShopV2Page extends AppMobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pageid = intval($_GPC['id']);

		if (empty($pageid)) {
			$pageid = trim($_GPC['type']);
		}


		if (empty($pageid)) {
			app_error(AppError::$PageNotFound);
		}


		$page = $this->model->getPage($pageid, true);
		if (empty($page) || empty($page['data'])) {
			app_error(AppError::$PageNotFound);
		}


		$startadv = array();
		if (is_array($page['data']['page']) && !(empty($page['data']['page']['diyadv']))) {
			$startadvitem = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_wxapp_startadv') . ' WHERE id=:id AND uniacid=:uniacid', array(':id' => intval($page['data']['page']['diyadv']), ':uniacid' => $_W['uniacid']));

			if (!(empty($startadvitem)) && !(empty($startadvitem['data']))) {
				$startadv = base64_decode($startadvitem['data']);
				$startadv = json_decode($startadv, true);

				if (!(empty($startadv['data']))) {
					foreach ($startadv['data'] as $itemid => &$item ) {
						$item['imgurl'] = tomedia($item['imgurl']);
					}

					unset($itemid, $item);
				}


				if (is_array($startadv['params'])) {
					$startadv['params']['style'] = 'small-bot';
				}


				if (is_array($startadv['style'])) {
					$startadv['style']['opacity'] = '0.6';
				}

			}

		}


		$result = array('diypage' => $page['data'], 'startadv' => $startadv, 'customer' => intval($_W['shopset']['app']['customer']));

		if (!(empty($result['customer']))) {
			$result['customercolor'] = ((empty($_W['shopset']['app']['customercolor']) ? '#ff5555' : $_W['shopset']['app']['customercolor']));
		}


		app_json($result);
	}

	public function main2()
	{
		global $_W;
		global $_GPC;
		$diypage = p('diypage');

		if (!($diypage)) {
			app_error(AppError::$PluginNotFound);
		}


		$pagetype = trim($_GPC['type']);

		if (!(empty($pagetype))) {
			$pageid = $this->type2Pageid($pagetype);
		}
		 else {
			$pageid = intval($_GPC['id']);
		}

		if (empty($pageid)) {
			app_error(AppError::$PageNotFound);
		}


		$page = $diypage->getPage($pageid, true);
		if (empty($page) || empty($page['data'])) {
			app_error(AppError::$PageNotFound);
		}


		app_json(array('diypage' => $page['data']));
	}

	/**
     * 根据type获取id
     * @param null $type
     * @return int
     */
	public function type2Pageid($type = NULL)
	{
		if (empty($type)) {
			return 0;
		}


		$set = m('common')->getPluginset('diypage');
		$pageset = $set['page'];
		$pageid = intval($pageset[$type . '_wxapp']);
		return $pageid;
	}
}


?>