<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Plugin_EweiShopV2Model 
{
	public function exists($pluginName = '') 
	{
		$dbplugin = pdo_fetchall('select * from ' . tablename('ewei_shop_plugin') . ' where identity=:identyty limit  1', array(':identity' => $pluginName));
		if (empty($dbplugin)) 
		{
			return false;
		}
		return true;
	}
	public function getAll($iscom = false, $status = '') 
	{
		global $_W;
		$plugins = '';
		if ($status !== '') 
		{
			$status = 'and status = ' . intval($status);
		}
		if ($iscom) 
		{
			$plugins = m('cache')->getArray('coms2', 'global');
			if (empty($plugins)) 
			{
				$plugins = pdo_fetchall('select * from ' . tablename('ewei_shop_plugin') . ' where iscom=1 and deprecated=0 ' . $status . ' order by displayorder asc');
				m('cache')->set('coms2', $plugins, 'global');
			}
		}
		else 
		{
			$plugins = m('cache')->getArray('plugins2', 'global');
			if (empty($plugins)) 
			{
				$plugins = pdo_fetchall('select * from ' . tablename('ewei_shop_plugin') . ' where iscom=0 and deprecated=0 ' . $status . ' order by displayorder asc');
				m('cache')->set('plugins2', $plugins, 'global');
			}
		}
		return $plugins;
	}
	public function refreshCache($status = '', $iscom = false) 
	{
		if ($status !== '') 
		{
			$status = 'and status = ' . intval($status);
		}
		$com = pdo_fetchall('select * from ' . tablename('ewei_shop_plugin') . ' where iscom=1 and deprecated=0 ' . $status . ' order by displayorder asc');
		m('cache')->set('coms2', $com, 'global');
		$plugins = pdo_fetchall('select * from ' . tablename('ewei_shop_plugin') . ' where iscom=0 and deprecated=0 ' . $status . ' order by displayorder asc');
		m('cache')->set('plugins2', $plugins, 'global');
		if ($iscom) 
		{
			return $com;
		}
		return $plugins;
	}
	public function getList($status = '') 
	{
		global $_W;
		$list = $this->getCategory();
		$plugins = $this->getAll(false, $status);
		$filename = '../addons/ewei_shopv2/core/model/grant.php';
		if (file_exists($filename)) 
		{
			$item = pdo_fetch('select  plugins from ' . tablename('ewei_shop_perm_plugin') . ' where acid=:acid limit 1', array(':acid' => $_W['uniacid']));
			$setting = pdo_fetch('select * from ' . tablename('ewei_shop_system_grant_setting') . ' where id = 1 limit 1 ');
			foreach ($plugins as $key => $value ) 
			{
				if (!(strstr($item['plugins'], $value['identity'])) && !(strstr($setting['plugin'], $value['identity'])) && !(strstr($setting['com'], $value['identity']))) 
				{
					$plugin = pdo_fetch('SELECT max(permendtime) as permendtime FROM ' . tablename('ewei_shop_system_grant_log') . ' ' . "\n" . '                    WHERE `identity` = \'' . $value['identity'] . '\' and uniacid = ' . $_W['uniacid'] . ' and isperm = 1 ');
					$plugins[$key]['isgrant'] = 1;
					$plugins[$key]['permendtime'] = $plugin['permendtime'];
				}
			}
		}
		else if (p('grant')) 
		{
			$item = pdo_fetch('select plugins from ' . tablename('ewei_shop_perm_plugin') . ' where acid=:acid limit 1', array(':acid' => $_W['uniacid']));
			$setting = pdo_fetch('select * from ' . tablename('ewei_shop_system_plugingrant_setting') . ' where 1 = 1 limit 1 ');
			foreach ($plugins as $key => $value ) 
			{
				if (!(strstr($item['plugins'], $value['identity'])) && !(strstr($setting['plugin'], $value['identity'])) && !(strstr($setting['com'], $value['identity']))) 
				{
					$plugin = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('ewei_shop_system_plugingrant_log') . "\n" . '                    WHERE `identity` = \'' . $value['identity'] . '\' and uniacid = ' . $_W['uniacid'] . ' and isperm = 1 and `month` = 0 ');
					if (!($plugin)) 
					{
						$plugin = pdo_fetch('SELECT max(permendtime) as permendtime,`month`,isperm FROM ' . tablename('ewei_shop_system_plugingrant_log') . ' ' . "\n" . '                        WHERE `identity` = \'' . $value['identity'] . '\' and uniacid = ' . $_W['uniacid'] . ' and isperm = 1 ');
					}
					else 
					{
						$plugin = pdo_fetch('SELECT max(permendtime) as permendtime,`month`,isperm FROM ' . tablename('ewei_shop_system_plugingrant_log') . "\n" . '                        WHERE `identity` = \'' . $value['identity'] . '\' and uniacid = ' . $_W['uniacid'] . ' and isperm = 1 and `month` = 0 ');
					}
					$plugins[$key]['isplugingrant'] = 1;
					$plugins[$key]['month'] = $plugin['month'];
					$plugins[$key]['isperm'] = $plugin['isperm'];
					$plugins[$key]['permendtime'] = $plugin['permendtime'];
				}
			}
		}
		foreach ($list as $ck => &$cv ) 
		{
			$ps = array();
			foreach ($plugins as $p ) 
			{
				if ($p['category'] == $ck) 
				{
					$ps[] = $p;
				}
			}
			$cv['plugins'] = $ps;
		}
		unset($cv);
		return $list;
	}
	public function getName($identity = '') 
	{
		$plugins = $this->getAll();
		foreach ($plugins as $p ) 
		{
			if ($p['identity'] == $identity) 
			{
				return $p['name'];
			}
		}
		return '';
	}
	public function loadModel($pluginname = '') 
	{
		static $_model;
		if (!($_model)) 
		{
			$modelfile = IA_ROOT . '/addons/ewei_shopv2/plugin/' . $pluginname . '/core/model.php';
			if (is_file($modelfile)) 
			{
				$classname = ucfirst($pluginname) . 'Model';
				require_once EWEI_SHOPV2_CORE . 'inc/plugin_model.php';
				require_once $modelfile;
				$_model = new $classname($pluginname);
			}
		}
		return $_model;
	}
	public function getCategory() 
	{
		return array( 'biz' => array('name' => '业务类'), 'sale' => array('name' => '营销类'), 'tool' => array('name' => '工具类'), 'help' => array('name' => '辅助类') );
	}
}
?>