<?php
/*
 * 人人商城V2
 *
 * @author ewei 狸小狐 
 */
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
class Upgrade_EweiShopV2Page extends SystemPage {
	function main() {
		global $_W;
		
		$versionfile = IA_ROOT . '/addons/ewei_shopv2/version.php';
		$updatedate = date('Y-m-d H:i', filemtime($versionfile));
		$version = EWEI_SHOPV2_VERSION;
		$release = EWEI_SHOPV2_RELEASE;

		$domain = trim( preg_replace( "/http(s)?:\/\//", "", rtrim($_W['siteroot'],"/") )  );
		$ip = gethostbyname($_SERVER['HTTP_HOST']);
		$setting = setting_load('site');
		$id = isset($setting['site']['key']) ? $setting['site']['key'] : '0';
		load()->func('communication');
		include $this->template();
	}
	function check() {
		global $_W, $_GPC;
		$plugins = pdo_fetchall('select `identity` from '.tablename('ewei_shop_plugin'),array(),'identity');
		load()->func('db');
        load()->func('communication');
		set_time_limit(0);
		$auth = get_auth();
		$version = defined('EWEI_SHOPV2_VERSION') ? EWEI_SHOPV2_VERSION : '2.0.0';
		$release = defined('EWEI_SHOPV2_RELEASE') ? EWEI_SHOPV2_RELEASE : '201605010000';
		$templatefiles = "";
        $upgrade = auth_check($auth,$version);
		if (is_array($upgrade)) {
			$templatefiles = "";
			if ($upgrade['result'] == 1) {

				$files = array();

				if (!empty($upgrade['files'])) {
					foreach ($upgrade['files'] as $file) {
						$entry = EWEI_SHOPV2_PATH . $file['path'];
						if (!is_file($entry) || md5_file($entry) != $file['md5']) {

							$files[] = array('path' => $file['path'], 'download' => 0, 'entry'=>$entry);
                            $templatefiles.= "/" . $file['path'] . "<br/>";
							if (strexists($entry, 'template/mobile') && strexists($entry, '.html')) {
								$templatefiles.= "/" . $file['path'] . "<br/>";
							}
						}
					}
				}
				//数据表
				$database = array();
				if (!empty($upgrade['structs'])) {
					$upgrade['structs'] = unserialize($upgrade['structs']);

					foreach ($upgrade['structs'] as $remote) {

						$name = substr($remote['tablename'], 4);
						$local = $this->table_schema(pdo(), $name);
						if(empty($local)) {
							$database[] = $remote;
						} else {
							$sqls = db_table_fix_sql($local, $remote);
							if(!empty($sqls)) {
								$database[] = $remote;
							}
						}
					}

				}
				cache_write('cloud:modules:upgradev2', array('files' => $files, 'version' => $upgrade['version'], 'release' => $upgrade['release'], 'upgrades' => $upgrade['upgrades'], "database"=>$database));
				$log = $upgrade['message'];
				show_json(1, array(
					'result' => 1,
					'version' => $upgrade['version'],
					'release' => $upgrade['release'],
					'filecount' => count($files),
					'database' => !empty($database),
					'upgrades'=> !empty($upgrade['upgrades']),
					'log' => nl2br($log),
					'templatefiles'=>$templatefiles
				));
			}
			show_json(0, $upgrade['message']);

		}
		if (is_file(EWEI_SHOPV2_PATH . "tmp")){
			@unlink(EWEI_SHOPV2_PATH . "tmp");
		}
		show_json(0, $upgrade['message']);
	}
	function process() {

		global $_W, $_GPC;
		load()->func('communication');
		load()->func('file');
		load()->func('db');
		$upgrade = cache_load('cloud:modules:upgradev2');
		$files = $upgrade['files'];
		$version = $upgrade['version'];
		$database = $upgrade['database'];
		$upgrades = $upgrade['upgrades'];

		$auth = get_auth();
		$action = trim($_GPC['action']);
		empty($action) && $action = 'database';
		if ($action == 'database') {
			if( empty($database)){
				show_json(2, array('total' => 0,'action'=>'database'));
			}
			$remote = false;
			foreach ($database as $d) {
				if (empty($d['updated'])) {
					$remote = $d;
					break;
				}
			}
			if (!empty($remote)) {
                 $name = substr($remote['tablename'], 4);
				$local = $this->table_schema(pdo(), $name);
				$sqls = db_table_fix_sql($local, $remote);
				$error = false;
				foreach ($sqls as $sql) {
					if (pdo_query($sql) === false) {
						$error = true;
						break;
					}
				}
				$success = 0;
				foreach ($database as &$d) {
					if ($d['tablename'] == $remote['tablename'] && !$error) {
						$d['updated'] = 1;
					}
					if($d['updated']){
						$success++;
					}
				}
				unset($d);
				cache_write('cloud:modules:upgradev2', array('files' => $files, 'version' => $version, 'release' => $upgrade['release'], 'upgrades' => $upgrade['upgrades'], 'database' => $database));
				if($success >= count($database) ){
					show_json(2, array('total' => count($database),'action'=>'database'));
				}
				show_json(1, array('total' => count($database), 'success' => $success,'action'=>'database'));
			}
			show_json(2, array('total' => count($database),'action'=>'database'));
		} elseif ($action == 'file') {


			$path = "";
			foreach ($files as $f) {
				if (empty($f['download'])) {
					$path = $f['path'];
					break;
				}
			}
			if (!empty($path)) {
				$ret = auth_download($auth,$path);

				if (is_array($ret)) {
					$path = $ret['path'];
					if(strexists($path,'pcsite/')){
						$dirpath = dirname($path);
						if (!is_dir(IA_ROOT ."/". $dirpath)) {
							mkdirs(IA_ROOT."/" . $dirpath);
							@chmod(IA_ROOT ."/". $dirpath,0777);
						}
						$content = base64_decode($ret['content']);
						file_put_contents(IA_ROOT."/". $path, $content);

					}

					$dirpath = dirname($path);
					if (!is_dir(EWEI_SHOPV2_PATH . $dirpath)) {
						mkdirs(EWEI_SHOPV2_PATH . $dirpath);
						@chmod(EWEI_SHOPV2_PATH . $dirpath,0777);
					}
					$content = base64_decode($ret['content']);
					file_put_contents(EWEI_SHOPV2_PATH . $path, $content);
					if (isset($ret['path1'])) {
						$path1 = $ret['path1'];
						$dirpath1 = dirname($path1);
						if (!is_dir(EWEI_SHOPV2_PATH . $dirpath1)) {
							mkdirs(EWEI_SHOPV2_PATH . $dirpath1);
							@chmod(EWEI_SHOPV2_PATH . $dirpath1,0777);
						}
						$content1 = base64_decode($ret['content1']);
						file_put_contents(EWEI_SHOPV2_PATH . $path1, $content1);
					}
					if (isset($ret['path2'])) {
						$path2 = $ret['path2'];
						$dirpath2 = dirname($path2);
						if (!is_dir(EWEI_SHOPV2_PATH . $dirpath2)) {
							mkdirs(EWEI_SHOPV2_PATH . $dirpath2);
							@chmod(EWEI_SHOPV2_PATH . $dirpath2,0777);
						}
						$content2 = base64_decode($ret['content2']);
						file_put_contents(EWEI_SHOPV2_PATH . $path2, $content2);
					}
					$success = 0;
					foreach ($files as &$f) {

						if ($f['path'] == $ret['path']) {
							$f['download'] = 1;
						}
						if ($f['download']) {
							$success++;
						}
					}
					unset($f);
					$updatefile = IA_ROOT.'/addons/'.$_GPC['m'].'/upgrade.php';
					if (file_exists($updatefile)) {
						require $updatefile;
					}
					cache_write('cloud:modules:upgradev2', array('files' => $files, 'version' => $version, 'release' => $upgrade['release'], 'upgrades' => $upgrade['upgrades']));
					if($success >= count($files) ){
					         show_json(2, array('total' => count($files),'action'=>'file'));
					}
				}
				show_json(1, array('total' => count($files), 'success' => $success,'action'=>'file'));
			}
			show_json(2, array('total' => count($files),'action'=>'file'));
		} else if ($action == 'upgrade') {
			if(empty($upgrades)){
				$this->updateComplete($upgrade['version'],$upgrade['release']);
				show_json(2, array('total' => count($upgrades),'action'=>'upgrade'));
			}
			$update = false;
			foreach ($upgrades as $up) {
				if (empty($up['updated'])) {
				         $update = $up;
				         break;
				}
			}
			if (!empty($update)) {
				$updatepath =EWEI_SHOPV2_PATH . "tmp/";
				if(!is_dir($updatepath)){
					mkdirs($updatepath);
				}
				$updatefile = $updatepath ."upgrade-".$update['release'].".php";
				$content = base64_decode($update['upgrade']);
				if(!empty($content)){
					file_put_contents($updatefile, $content);
					require $updatefile;
					@unlink($updatefile);
				}

				$success = 0;
				foreach ($upgrades as &$up) {
					if ($up['release'] == $update['release']) {
						$up['updated'] = 1;
					}
					if ($up['updated']) {
						$success++;
					}
				}
				unset($up);
				cache_write('cloud:modules:upgradev2', array('files' => $files, 'version' => $version, 'release' => $upgrade['release'], 'upgrades' => $upgrades));

				if($success >= count($upgrades) ){
					$this->updateComplete($upgrade['version'],$upgrade['release']);
					show_json(2, array('total' => count($upgrades),'action'=>'upgrade'));
				}

				show_json(1, array('total' => count($upgrades), 'success' => $success,'action'=>'upgrade'));
			}
			else{
				$this->updateComplete($upgrade['version'],$upgrade['release']);
				show_json(2, array('total' => count($upgrades),'action'=>'upgrade'));
			}
		}
	}
	protected function table_schema($db, $tablename = '') {
		$result = $db->fetch("SHOW TABLE STATUS LIKE '" . trim($db->tablename($tablename), '`') . "'");
		if(empty($result)) {
			return array();
		}
		$ret['tablename'] = $result['Name'];
		$ret['charset'] = $result['Collation'];
		$ret['engine'] = $result['Engine'];
		$ret['increment'] = $result['Auto_increment'];
		$result = $db->fetchall("SHOW FULL COLUMNS FROM " . $db->tablename($tablename));
		foreach($result as $value) {
			$temp = array();
			$type = explode(" ", $value['Type'], 2);
			$temp['name'] = $value['Field'];
			$pieces = explode('(', $type[0], 2);
			$temp['type'] = $pieces[0];
			$temp['length'] = rtrim($pieces[1], ')');
			$temp['null'] = $value['Null'] != 'NO';
			$temp['signed'] = empty($type[1]);
			$temp['increment'] = $value['Extra'] == 'auto_increment';
			$temp['default'] = $value['Default'];
			$ret['fields'][$value['Field']] = $temp;
		}
		$result = $db->fetchall("SHOW INDEX FROM " . $db->tablename($tablename));
		foreach($result as $value) {
			$ret['indexes'][$value['Key_name']]['name'] = $value['Key_name'];
			$ret['indexes'][$value['Key_name']]['type'] = ($value['Key_name'] == 'PRIMARY') ? 'primary' : ($value['Non_unique'] == 0 ? 'unique' : 'index');
			$ret['indexes'][$value['Key_name']]['fields'][] = $value['Column_name'];
		}
		return $ret;
	}
	protected function updateComplete($version,$release){
		load()->func('file');
		file_put_contents(EWEI_SHOPV2_PATH . "version.php", "<?php if(!defined('IN_IA')) {exit('Access Denied');}if(!defined('EWEI_SHOPV2_VERSION')) {define('EWEI_SHOPV2_VERSION', '" . $version . "');}if(!defined('EWEI_SHOPV2_RELEASE')) {define('EWEI_SHOPV2_RELEASE', '" . $release . "');}");
		cache_delete('cloud:modules:upgradev2');
		$time = time();
		global $my_scenfiles;
		my_scandir(IA_ROOT . "/addons/ewei_shopv2");
		foreach ($my_scenfiles as $file) {
		          if (!strexists($file, '/ewei_shopv2/data/') && !strexists($file, 'version.php')) {
			     @touch($file, $time);
			}
		}
		rmdirs(IA_ROOT . "/addons/ewei_shopv2/tmp");
	}
	function checkversion() {
		file_put_contents(IA_ROOT . "/addons/ewei_shopv2/version.php", "<?php if(!defined('IN_IA')) {exit('Access Denied');}if(!defined('EWEI_SHOPV2_VERSION')) {define('EWEI_SHOPV2_VERSION', '2.0.0');}if(!defined('EWEI_SHOPV2_RELEASE')) {define('EWEI_SHOPV2_RELEASE', '201605010000');}");
		header('location: ' . webUrl('system/auth/upgrade'));
		exit;
	}
}