<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
require_once EWEI_SHOPV2_CORE . 'socket/pdo.php';
class LiveSocket 
{
	public function onMessage($server, $data, $fd) 
	{
		$data = $this->special($data);
		if (empty($data['type']) || empty($data['roomid']) || empty($data['uid'])) 
		{
			return false;
		}
		if ($data['type'] == 'login') 
		{
			$this->addUser($data['uid'], array('fd' => $fd, 'uid' => $data['uid'], 'nickname' => $data['nickname'], 'uniacid' => $data['uniacid'], 'roomid' => $data['roomid'], 'role' => $data['role']));
			$banned = $this->getBanned($data['roomid'], $data['uid']);
			$settings = $this->getRoomSetting($data['roomid'], $data['uid']);
			$this->sendAll($server, array('type' => 'userEnter', 'fromUser' => $data['uid'], 'toUser' => 'all', 'nickname' => $data['nickname'], 'role' => $data['role'], 'banned' => $banned['self'], 'roomid' => $data['roomid']), $fd);
			$sendArr = array('type' => 'connected', 'fromUser' => 'system', 'toUser' => $fd, 'banned' => $banned, 'online' => $this->getUserTotal($data['roomid']), 'settings' => $settings);
			if ($data['role'] == 'manage') 
			{
				if ($this->isManage($data['uid'])) 
				{
					$sendArr['userList'] = $this->getAllUser($data['roomid']);
					$bannedList = $this->getBannedUser($data['roomid']);
					$sendArr['bannedList'] = $bannedList;
					$sendArr['bannedNum'] = count($bannedList);
				}
			}
			$this->send($server, $fd, $sendArr);
			if (isset($settings['virtualadd']) && (1 < intval($settings['virtualadd']))) 
			{
				$table = $this->getTable('settings', $data['roomid']);
				redis()->hIncrBy($table, 'virtual', $settings['virtualadd']);
			}
			return true;
		}
		if ($data['type'] == 'update') 
		{
			$this->updateUser($data['uid'], array());
		}
		else 
		{
			if (($data['type'] == 'text') || ($data['type'] == 'image')) 
			{
				if (!($this->isManage($data['uid']))) 
				{
					$banned = $this->getBanned($data['roomid'], $data['uid']);
					if ($banned['all'] || $banned['self']) 
					{
						return false;
					}
				}
				if ($data['toUser'] == 'all') 
				{
					$at = ((isset($data['at']) ? $data['at'] : array()));
					$msgid = $this->getMsgid($data['uid']);
					$table_records = $this->getTable('chat_records', $data['roomid']);
					redis()->rPush($table_records, json_encode(array('id' => $msgid, 'mid' => $data['uid'], 'nickname' => $data['nickname'], 'type' => $data['type'], 'text' => $data['text'], 'sendtime' => time(), 'at' => (!(empty($at)) ? iserializer($at) : ''))));
					redis()->lTrim($table_records, 0, 299);
					$this->sendAll($server, array('type' => $data['type'], 'fromUser' => $data['uid'], 'toUser' => $data['toUser'], 'nickname' => $data['nickname'], 'text' => $data['text'], 'msgid' => $msgid, 'roomid' => $data['roomid']), 0, $at);
				}
				else 
				{
					return false;
				}
			}
			else if ($data['type'] == 'repeal') 
			{
				$message = $this->getSingleMsg($data['msgid'], $data['roomid']);
				if ($message['mid'] != $data['uid']) 
				{
					return false;
				}
				$this->sendAll($server, array('type' => 'repeal', 'fromUser' => $data['uid'], 'toUser' => 'all', 'nickname' => $data['nickname'], 'msgid' => $data['msgid'], 'roomid' => $data['roomid']));
				$this->updateMsg($data['msgid'], $data['roomid'], 1);
			}
			else if ($data['type'] == 'delete') 
			{
				if (!($this->isManage($data['uid']))) 
				{
					return false;
				}
				$this->sendAll($server, array('type' => 'delete', 'fromUser' => $data['uid'], 'toUser' => 'all', 'nickname' => $data['nickname'], 'msgid' => $data['msgid'], 'deleteNick' => $data['deleteNick'], 'deleteUid' => $data['deleteUid'], 'roomid' => $data['roomid']));
				$this->updateMsg($data['msgid'], $data['roomid'], 2, array('mid_manage' => $data['uid'], 'nickname_manage' => $data['nickname']));
			}
			else if ($data['type'] == 'banned') 
			{
				if (!($this->isManage($data['uid']))) 
				{
					return false;
				}
				$table = $this->getTable('banned', $data['roomid']);
				if ($data['banned'] == 1) 
				{
					redis()->hSet($table, $data['bannedUid'], json_encode(array('nickname' => $data['bannedNick'])));
				}
				else 
				{
					redis()->hDel($table, $data['bannedUid']);
				}
				$user = $this->getUser($data['roomid'], $data['bannedUid']);
				$this->sendAll($server, array('type' => 'banned', 'fromUser' => $data['uid'], 'toUser' => 'manage', 'nickname' => $data['nickname'], 'banned' => intval($data['banned']), 'bannedUid' => $data['bannedUid'], 'bannedNick' => $data['bannedNick'], 'roomid' => $data['roomid']), (!(empty($user)) ? $user['fd'] : 0));
			}
			else if ($data['type'] == 'bannedAll') 
			{
				if (!($this->isManage($data['uid']))) 
				{
					return false;
				}
				$table = $this->getTable('banned', $data['roomid']);
				redis()->hSet($table, 'bannedAll', intval($data['banned']));
				$this->sendAll($server, array('type' => 'bannedAll', 'fromUser' => $data['uid'], 'toUser' => 'all', 'nickname' => $data['nickname'], 'banned' => intval($data['banned']), 'roomid' => $data['roomid']), $fd);
			}
			else if ($data['type'] == 'setting') 
			{
				if (!($this->isManage($data['uid']))) 
				{
					return false;
				}
				$table = $this->getTable('settings', $data['roomid']);
				$settings = redis()->hGetAll($table);
				$settings = ((empty($settings) ? array() : $settings));
				$settings['canat'] = intval($data['canAt']);
				$settings['canrepeal'] = intval($data['canRepeal']);
				$settings['virtual'] = intval($data['virtualNum']);
				$settings['virtualadd'] = intval($data['virtualAddNum']);
				$settings[$data['uid']] = $data['manageNick'];
				redis()->hMset($table, $settings);
				unset($settings[$data['uid']]);
				if ($data['manageNick'] != $data['nickname']) 
				{
					$settings['nickname_old'] = $data['nickname'];
					$settings['nickname'] = $data['manageNick'];
					$this->updateUser($data['uid'], array('nickname' => $data['manageNick']));
				}
				$this->sendAll($server, array('type' => 'setting', 'fromUser' => $data['uid'], 'toUser' => 'all', 'settings' => $settings, 'roomid' => $data['roomid']));
			}
			else if ($data['type'] == 'setstatus') 
			{
				if (!($this->isManage($data['uid']))) 
				{
					return false;
				}
				$status = intval($data['status']);
				$table = $this->getTable('settings', $data['roomid']);
				redis()->hSet($table, 'status', $status);
				$update = array('living' => ($status != 1 ? 0 : 1));
				if ($status == 0) 
				{
					$update['lastlivetime'] = time();
				}
				pdo_update2('ewei_shop_live', $update, array('uniacid' => $data['uniacid'], 'id' => $data['roomid']));
				$this->sendAll($server, array('type' => 'setting', 'fromUser' => $data['uid'], 'toUser' => 'all', 'settings' => $this->getRoomSetting($data['roomid']), 'roomid' => $data['roomid']));
			}
			else if ($data['type'] == 'clicklike') 
			{
				$this->sendAll($server, array('type' => 'clicklike', 'fromUser' => $data['uid'], 'toUser' => 'all', 'roomid' => $data['roomid']), $fd);
			}
			else if ($data['type'] == 'redpack') 
			{
				if (!($this->isManage($data['uid']))) 
				{
					return false;
				}
				$redpacktitle = trim($data['redPackTitle']);
				$redpacktype = intval($data['redPackType']);
				$redpackmoney = trim($data['redPackMoney']);
				$redpacktotal = intval($data['redPackTotal']);
				if (empty($redpackmoney) || empty($redpacktotal)) 
				{
					return false;
				}
				if (empty($redpacktitle)) 
				{
					$redpacktitle = '红包来袭，手慢无！';
				}
				if (empty($redpacktype)) 
				{
					$redpacklist = array();
					$i = 0;
					while ($i < $redpacktotal) 
					{
						$redpacklist[] = $redpackmoney;
						++$i;
					}
				}
				else 
				{
					$redpacklist = createRedPack($redpackmoney, $redpacktotal);
				}
				if (empty($redpacklist)) 
				{
					return false;
				}
				$redpackid = time();
				$table_record = $this->getTable('push_records', $data['roomid']);
				$table_redpack = $this->getTable('redpack_' . $redpackid, $data['roomid']);
				redis()->lPush($table_record, json_encode(array('title' => $redpacktitle, 'type' => $redpacktype, 'time' => $redpackid, 'total' => $redpacktotal, 'total_remain' => $redpacktotal, 'money' => $redpackmoney, 'money_remain' => $redpackmoney)));
				print_r('id: ' . $redpackid);
				foreach ($redpacklist as $money ) 
				{
					redis()->rPush($table_redpack, json_encode(array('money' => $money, 'used' => 0)));
				}
				$this->sendAll($server, array( 'type' => 'redpack', 'fromUser' => $data['uid'], 'toUser' => 'all', 'nickname' => $data['nickname'], 'redpack' => array('title' => $redpacktitle, 'id' => $redpackid), 'msgid' => pdo_insertid(), 'roomid' => $data['roomid'] ));
			}
			else if ($data['type'] == 'redpackget') 
			{
				$redpackid = intval($data['pushid']);
				$sendArr = array('type' => 'redpackget', 'fromUser' => 'system', 'toUser' => $fd, 'prestatus' => 0, 'redpackid' => $redpackid);
				if (!(empty($redpackid))) 
				{
					$table_redpack = $this->getTable('redpack_' . $redpackid, $data['roomid']);
					$table_redpack_list = $this->getTable('redpack_list_' . $redpackid, $data['roomid']);
					if (redis()->exists($table_redpack)) 
					{
						$selfdata = redis()->hGet($table_redpack_list, $data['openid']);
						if (!(empty($selfdata))) 
						{
							$selfdata = json_decode($selfdata, true);
						}
						if (-1 < $selfdata['redpackindex']) 
						{
							$sendArr['prestatus'] = 1;
						}
						dump($selfdata);
						if ((redis()->hLen($table_redpack_list) < redis()->lLen($table_redpack)) || ($selfdata['redpackindex'] < 0)) 
						{
							$sendArr['prestatus'] = 3;
						}
						else 
						{
							$sendArr['prestatus'] = 2;
						}
					}
				}
				$this->send($server, $fd, $sendArr);
			}
			else if ($data['type'] == 'redpackdraw') 
			{
				$redpackid = intval($data['pushid']);
				$sendArr = array('type' => 'redpackdraw', 'fromUser' => 'system', 'toUser' => $fd, 'status' => 0, 'redpackid' => $redpackid);
				if (!(empty($redpackid))) 
				{
					$table_redpack = $this->getTable('redpack_' . $redpackid, $data['roomid']);
					$table_redpack_list = $this->getTable('redpack_list_' . $redpackid, $data['roomid']);
					$table_redpack_order = $this->getTable('redpack_order_' . $redpackid, $data['roomid']);
					if (redis()->exists($table_redpack)) 
					{
						$redpacktotal = redis()->lLen($table_redpack);
						if (0 < $redpacktotal) 
						{
							$selfdata = redis()->hGet($table_redpack_list, $data['openid']);
							if (!(empty($selfdata))) 
							{
								$selfdata = json_decode($selfdata, true);
								if (-1 < $selfdata['redpackindex']) 
								{
									$sendArr['status'] = 1;
									$sendArr['redpack'] = array();
									$sendArr['list'] = array();
								}
							}
							else if (redis()->hLen($table_redpack_list) < $redpacktotal) 
							{
								redis()->rPush($table_redpack_order, $data['openid']);
								redis()->hSet($table_redpack_list, $data['openid'], json_encode(array('redpackindex' => -1, 'money' => 0, 'time' => 0)));
							}
							$prelist = redis()->lRange($table_redpack_order, 0, $redpacktotal);
							$prelist = array_unique($prelist);
							$selfsent = false;
							foreach ($prelist as $index => $openid ) 
							{
								$userdata = redis()->hGet($table_redpack_list, $openid);
								if (empty($userdata)) 
								{
									continue;
								}
								$userdata = json_decode($userdata, true);
								if (-1 < $userdata['redpackindex']) 
								{
									continue;
								}
								$preredpack = redis()->lIndex($table_redpack, $index);
								$preredpack = json_decode($preredpack, true);
								if (!(empty($preredpack['used'])) || empty($preredpack['money'])) 
								{
									continue;
								}
								$preredpack['used'] = $openid;
								$preredpack['time'] = time();
								redis()->lSet($table_redpack, $index, json_encode($preredpack));
								$userdata['redpackindex'] = $index;
								$userdata['money'] = $preredpack['money'];
								$userdata['time'] = time();
								redis()->hSet($table_redpack_list, $openid, json_encode($userdata));
								if ($openid == $data['openid']) 
								{
									$selfsent = true;
								}
							}
							if ($selfsent) 
							{
								$sendArr['status'] = 3;
								$sendArr['redpack'] = array();
								$sendArr['list'] = array();
							}
						}
					}
				}
				$this->send($server, $fd, $sendArr);
			}
		}
		return false;
	}
	public function onClose($server, $fd = 0, $data = array()) 
	{
		if (empty($data) || empty($data['roomid'])) 
		{
			return false;
		}
		$isFd = true;
		if (!(empty($data['uid']))) 
		{
			$fd = $data['uid'];
			$isFd = false;
		}
		$this->delUser($server, $data['roomid'], $fd, $isFd);
		return true;
	}
	protected function send($server = NULL, $fd = 0, $data = array()) 
	{
		if (empty($server) || empty($fd) || empty($data)) 
		{
			return false;
		}
		if (!($server->exist($fd))) 
		{
			$this->delUser($server, $data['roomid'], $fd, true, false);
			return true;
		}
		if (is_array($data)) 
		{
			unset($data['roomid']);
			if (!(isset($data['time']))) 
			{
				$data['time'] = time();
			}
			$data = json_encode($data);
		}
		$result = $server->push($fd, $data);
		return $result;
	}
	protected function sendAll($server = NULL, $data = array(), $fd = 0, $at = array(), $isUid = false) 
	{
		if (empty($server) || empty($data) || empty($data['roomid'])) 
		{
			return false;
		}
		$allUser = $this->getAllUser($data['roomid']);
		if (empty($allUser)) 
		{
			return true;
		}
		$atArr = array();
		if (!(empty($at))) 
		{
			$at = array_filter($at);
			$atArr = array_keys($at);
			$data['atUsers'] = $at;
		}
		foreach ($allUser as $uid => $user ) 
		{
			if (empty($user)) 
			{
				continue;
			}
			$user = json_decode($user, true);
			if ($data['toUser'] == 'manage') 
			{
				if ($user['role'] != 'manage') 
				{
					if ($isUid) 
					{
						if ($uid != $fd) 
						{
							continue;
							if ($user['fd'] != $fd) 
							{
								continue;
								if ($isUid) 
								{
									if ($uid == $fd) 
									{
										continue;
										if (!(empty($fd)) && ($fd == $user['fd'])) 
										{
											continue;
										}
									}
								}
								else 
								{
									continue;
								}
							}
						}
					}
					else 
					{
						continue;
						continue;
						continue;
					}
				}
			}
			else 
			{
				continue;
				continue;
			}
			if (!(empty($atArr)) && in_array($uid, $atArr)) 
			{
				$data['at'] = 1;
			}
			if ($data['fromUser'] == $uid) 
			{
				$data['self'] = 1;
			}
			else 
			{
				unset($data['self']);
			}
			$this->send($server, $user['fd'], $data);
		}
		return true;
	}
	protected function getTable($table, $roomid) 
	{
		return 'ewei_shop_live_' . $table . '_' . $roomid;
	}
	protected function addUser($uid, $data) 
	{
		$table = $this->getTable('room', $data['roomid']);
		redis()->hSet($table, $uid, json_encode($data));
		return true;
	}
	protected function updateUser($uid, $data) 
	{
	}
	public function getUser($roomid = 0, $uid = 0, $isFd = false) 
	{
		if (empty($roomid) && empty($uid)) 
		{
			return false;
		}
		$table = $this->getTable('room', $roomid);
		if ($isFd) 
		{
			$user = false;
			$allUser = $this->getAllUser($roomid);
			if (!(empty($allUser))) 
			{
				foreach ($allUser as $key => $value ) 
				{
					if (empty($value)) 
					{
						continue;
					}
					$value = json_decode($value, true);
					if ($value['fd'] == $uid) 
					{
						$user = $value;
						break;
					}
				}
			}
		}
		else 
		{
			$user = redis()->hGet($table, $uid);
		}
		if (!(empty($user)) && !(is_array($user))) 
		{
			$user = json_decode($user, true);
		}
		return $user;
	}
	public function getAllUser($roomid = 0) 
	{
		$table = $this->getTable('room', $roomid);
		$list = redis()->hGetAll($table);
		return $list;
	}
	public function getUserTotal($roomid = 0) 
	{
		$table = $this->getTable('room', $roomid);
		$total = redis()->hLen($table);
		return intval($total);
	}
	public function getBanned($roomid = 0, $uid = 0) 
	{
		$return = array();
		$table = $this->getTable('banned', $roomid);
		$return['all'] = redis()->hGet($table, 'bannedAll');
		if (empty($uid)) 
		{
			$return['self'] = 1;
		}
		else 
		{
			$return['self'] = redis()->hGet($table, $uid);
		}
		return $return;
	}
	public function getBannedUser($roomid = 0) 
	{
		$table = $this->getTable('banned', $roomid);
		$list = redis()->hGetAll($table);
		unset($list['bannedAll']);
		return $list;
	}
	public function getBannedTotal($roomid = 0) 
	{
		$table = $this->getTable('banned', $roomid);
		return redis()->hLen($table);
	}
	protected function delUser($server, $roomid = 0, $uid = 0, $isFd = false, $notice = true) 
	{
		if (empty($roomid) || empty($uid)) 
		{
			return;
		}
		$table = $this->getTable('room', $roomid);
		if ($isFd) 
		{
			$user = $this->getUser($roomid, $uid, true);
		}
		else 
		{
			$user = redis()->hGet($table, $uid);
			$user = ((!(empty($user)) ? json_decode($user, true) : false));
		}
		if (!(empty($user))) 
		{
			if ($notice) 
			{
				$this->sendAll($server, array('type' => 'userLeave', 'fromUser' => $user['uid'], 'toUser' => 'all', 'nickname' => $user['nickname'], 'roomid' => $roomid), $user['fd']);
			}
			redis()->hDel($table, $user['uid']);
		}
	}
	protected function isManage($uid) 
	{
		global $_W;
		if (strexists($uid, 'console')) 
		{
			$user = explode('_', $uid);
			if (is_array($user)) 
			{
				if ($user[2] == 'founder') 
				{
					$founders = $_W['config']['setting']['founder'];
					if (!(empty($founders))) 
					{
						$founders = explode(',', $founders);
						if (in_array($user[1], $founders)) 
						{
							return true;
						}
					}
				}
				$account = pdo_fetch2('SELECT * FROM ' . tablename('uni_account_users') . ' WHERE uid=:uid AND uniacid=:uniacid', array(':uid' => $user[1], ':uniacid' => $user[3]));
				if (!(empty($account))) 
				{
					return true;
				}
			}
		}
		return false;
	}
	public function getMsgid($uid) 
	{
		$rand = rand(11111111, 99999999);
		$time = time();
		$id = $time . '' . $rand . '' . $uid;
		return md5($id);
	}
	public function getRoomSetting($roomid, $uid = 0) 
	{
		$settings = array('canat' => 0, 'canrepeal' => 0, 'virtual' => 0, 'virtualadd' => 1, 'status' => 0);
		if (empty($roomid)) 
		{
			return $settings;
		}
		$table = $this->getTable('settings', $roomid);
		$settingsArr = redis()->hGetAll($table);
		if (!(empty($settingsArr))) 
		{
			$settings['canat'] = intval($settingsArr['canat']);
			$settings['canrepeal'] = intval($settingsArr['canrepeal']);
			$settings['virtual'] = intval($settingsArr['virtual']);
			$settings['virtualadd'] = intval($settingsArr['virtualadd']);
			$settings['status'] = intval($settingsArr['status']);
			if (!(empty($uid)) && isset($settingsArr[$uid])) 
			{
				$settings['nickname'] = $settingsArr[$uid];
			}
		}
		return $settings;
	}
	public function getSingleMsg($msgid = 0, $roomid = 0) 
	{
		$message = array();
		if (!(empty($msgid)) && !(empty($roomid))) 
		{
			$table = $this->getTable('chat_records', $roomid);
			$records = redis()->lRange($table, 0, -1);
			if (!(empty($records))) 
			{
				foreach ($records as $index => $record ) 
				{
					$record = json_decode($record, true);
					if (empty($record)) 
					{
						continue;
					}
					if ($record['id'] == $msgid) 
					{
						$message = $record;
						break;
					}
				}
			}
		}
		return $message;
	}
	public function updateMsg($msgid = 0, $roomid = 0, $status = 0, $other = array()) 
	{
		$result = false;
		if (!(empty($msgid)) && !(empty($roomid)) && !(empty($status))) 
		{
			$table = $this->getTable('chat_records', $roomid);
			$records = redis()->lRange($table, 0, -1);
			if (!(empty($records))) 
			{
				foreach ($records as $index => $record ) 
				{
					$record = json_decode($record, true);
					if (empty($record)) 
					{
						continue;
					}
					if ($record['id'] == $msgid) 
					{
						$record['status'] = $status;
						$record = array_merge($record, $other);
						redis()->lSet($table, $index, json_encode($record));
						$result = true;
						break;
					}
				}
			}
		}
		return $result;
	}
	public function log($name, $text) 
	{
		$filename = dirname(__FILE__) . '/log_' . $name . '.log';
		$text = '[' . date('Y-m-d H:i:s', time()) . '] ' . $text;
		file_put_contents($filename, $text . "\r\n", FILE_APPEND);
	}
	public function special($obj) 
	{
		if (!(is_array($obj))) 
		{
			$obj = istripslashes($obj);
			$obj = ihtmlspecialchars($obj);
		}
		else 
		{
			foreach ($obj as $k => &$v ) 
			{
				$v = istripslashes($v);
				$v = ihtmlspecialchars($v);
			}
		}
		return $obj;
	}
}
?>