<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

define("SITE_ID",base64_encode($_SERVER["HTTP_HOST"]));
if (!(class_exists('AppModel'))) {
	class AppModel extends PluginModel
	{
		protected $member = array();
		private $staticurl = array('index', 'shop', 'goods', 'member', 'sale', 'account', 'commission');
		private $loginPage = array('member', 'order', 'commission', 'sale.coupon', 'groups.orders', 'groups.team');

		public function getAuth1()
		{
			return $this->getAuth('&ismanage=1');
		}
		public function getRelease1($authid)
		{
			return $this->getRelease($authid,'&ismanage=1');
		}
		/**
         * 小程序微信支付
         * @param $params
         * @param int $type
         * @return array
         */
		public function wxpay($params, $type = 0)
		{
			global $_W;
			$data = m('common')->getSysset('app');
			$openid = ((empty($params['openid']) ? $_W['openid'] : $params['openid']));

			if (isset($openid) && strexists($openid, 'sns_wa_')) {
				$openid = str_replace('sns_wa_', '', $openid);
			}


			$sec = m('common')->getSec();
			$sec = iunserializer($sec['sec']);
			$package = array();
			$package['appid'] = $data['appid'];
			$package['mch_id'] = $sec['wxapp']['mchid'];
			$package['nonce_str'] = random(32);
			$package['body'] = $params['title'];
			$package['device_info'] = 'ewei_shopv2';
			$package['attach'] = $_W['uniacid'] . ':' . $type;
			$package['out_trade_no'] = $params['tid'];
			$package['total_fee'] = $params['fee'] * 100;
			$package['spbill_create_ip'] = CLIENT_IP;

			if (!(empty($params['goods_tag']))) {
				$package['goods_tag'] = $params['goods_tag'];
			}


			$package['notify_url'] = $_W['siteroot'] . 'addons/ewei_shopv2/payment/wechat/notify.php';
			$package['trade_type'] = 'JSAPI';
			$package['openid'] = $openid;
			ksort($package, SORT_STRING);
			$string1 = '';

			foreach ($package as $key => $v ) {
				if (empty($v)) {
					continue;
				}


				$string1 .= $key . '=' . $v . '&';
			}

			$string1 .= 'key=' . $sec['wxapp']['apikey'];
			$package['sign'] = strtoupper(md5($string1));
			$dat = array2xml($package);
			load()->func('communication');
			$response = ihttp_request('https://api.mch.weixin.qq.com/pay/unifiedorder', $dat);

			if (is_error($response)) {
				return error(-1, $response['message']);
			}


			$xml = @simplexml_load_string($response['content'], 'SimpleXMLElement', LIBXML_NOCDATA);

			if (strval($xml->return_code) == 'FAIL') {
				return error(-2, strval($xml->return_msg));
			}


			if (strval($xml->result_code) == 'FAIL') {
				return error(-3, strval($xml->err_code) . ': ' . strval($xml->err_code_des));
			}


			$prepayid = $xml->prepay_id;
			$wOpt['appId'] = $data['appid'];
			$wOpt['timeStamp'] = TIMESTAMP . '';
			$wOpt['nonceStr'] = random(32);
			$wOpt['package'] = 'prepay_id=' . $prepayid;
			$wOpt['signType'] = 'MD5';
			ksort($wOpt, SORT_STRING);
			$string = '';

			foreach ($wOpt as $key => $v ) {
				$string .= $key . '=' . $v . '&';
			}

			$string .= 'key=' . $sec['wxapp']['apikey'];
			$wOpt['paySign'] = strtoupper(md5($string));
			unset($wOpt['appId']);
			return $wOpt;
		}

		/**
         * 小程序微信支付
         * @param $out_trade_no
         * @param int $money
         * @return array
         */
		public function isWeixinPay($out_trade_no, $money = 0)
		{
			global $_W;
			global $_GPC;
			$data = m('common')->getSysset('app');
			$sec = m('common')->getSec();
			$sec = iunserializer($sec['sec']);
			$url = 'https://api.mch.weixin.qq.com/pay/orderquery';
			$pars = array();
			$pars['appid'] = $data['appid'];
			$pars['mch_id'] = $sec['wxapp']['mchid'];
			$pars['nonce_str'] = random(32);
			$pars['out_trade_no'] = $out_trade_no;
			ksort($pars, SORT_STRING);
			$string1 = '';

			foreach ($pars as $k => $v ) {
				$string1 .= $k . '=' . $v . '&';
			}

			$string1 .= 'key=' . $sec['wxapp']['apikey'];
			$pars['sign'] = strtoupper(md5($string1));
			$xml = array2xml($pars);
			load()->func('communication');
			$resp = ihttp_post($url, $xml);

			if (is_error($resp)) {
				return error(-2, $resp['message']);
			}


			if (empty($resp['content'])) {
				return error(-2, '网络错误');
			}


			$xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
			$dom = new DOMDocument();

			if ($dom->loadXML($xml)) {
				$xpath = new DOMXPath($dom);
				$code = $xpath->evaluate('string(//xml/return_code)');
				$ret = $xpath->evaluate('string(//xml/result_code)');
				$trade_state = $xpath->evaluate('string(//xml/trade_state)');

				if ((strtolower($code) == 'success') && (strtolower($ret) == 'success') && (strtolower($trade_state) == 'success')) {
					$total_fee = intval($xpath->evaluate('string(//xml/total_fee)')) / 100;

					if ($total_fee != $money) {
						return error(-1, '金额出错');
					}


					return true;
				}


				if ($xpath->evaluate('string(//xml/return_msg)') == $xpath->evaluate('string(//xml/err_code_des)')) {
					$error = $xpath->evaluate('string(//xml/return_msg)');
				}
				 else {
					$error = $xpath->evaluate('string(//xml/return_msg)') . ' | ' . $xpath->evaluate('string(//xml/err_code_des)');
				}

				return error(-2, $error);
			}


			return error(-1, '未知错误');
		}

		/**
         * 获取微信小程序accessToken
         * @return string
         */
		public function getAccessToken()
		{
			global $_W;
			$appset = m('common')->getSysset('app');
			$cacheKey = 'eweishop:wxapp:accesstoken:' . $_W['uniacid'];
			$accessToken = m('cache')->get($cacheKey, $_W['uniacid']);

			if (!(empty($accessToken)) && !(empty($accessToken['token'])) && (TIMESTAMP < $accessToken['expire'])) {
				return $accessToken['token'];
			}


			if (empty($appset['appid']) || empty($appset['secret'])) {
				return error(-1, '未填写小程序的 appid 或 appsecret！');
			}


			load()->func('communication');
			$content = ihttp_get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appset['appid'] . '&secret=' . $appset['secret']);

			if (is_error($content)) {
				return error(-1, '获取微信公众号授权失败, 请稍后重试！错误详情: ' . $content['message']);
			}


			$result = @json_decode($content['content'], true);
			if (empty($result) || !(is_array($result)) || empty($result['access_token']) || empty($result['expires_in'])) {
				$errorinfo = substr($content['meta'], strpos($content['meta'], '{'));
				$errorinfo = @json_decode($errorinfo, true);
				return error(-1, '获取微信公众号授权失败, 请稍后重试！ 公众平台返回原始数据为: 错误代码-' . $errorinfo['errcode'] . '，错误信息-' . $errorinfo['errmsg']);
			}


			$record['token'] = $result['access_token'];
			$record['expire'] = (TIMESTAMP + $result['expires_in']) - 200;
			m('cache')->set($cacheKey, $record, $_W['uniacid']);
			return $result['access_token'];
		}

		/**
         * 发送模板消息
         * @param null $openid  发送人openid
         * @param array $datas   发送数据
         * @param string $type  发送类型
         * @return bool
         */
		public function sendNotice($openid = NULL, $datas = array(), $prepay_id = NULL, $orderid = 0, $type = 'pay')
		{
			global $_W;
			if (empty($openid) || empty($datas) || empty($prepay_id)) {
				return error(-1, 'openid或datas或prepay_id为空');
			}


			$openid = str_replace('sns_wa_', '', $openid);
			$appset = m('common')->getSysset('app');

			if (empty($appset)) {
				return error(-1, '未读取到小程序设置');
			}


			$tempateid = $appset['tmessage_' . $type];

			if (empty($tempateid)) {
				return error(-1, '未选择消息模板');
			}


			$tempate = $this->getTMessage($tempateid);
			if (empty($tempate) || empty($tempate['templateid']) || empty($tempate['datas'])) {
				return error(-1, '消息模板未开启或不存在');
			}


			$data = array();
			$emphasis_keyword = '';

			foreach ($tempate['datas'] as $index => $item ) {
				$key = str_replace(array('{{', '.DATA}}'), '', trim($item['key']));

				if (empty($key)) {
					continue;
				}


				$data[$key] = array('value' => $this->replaceTemplate($item['value'], $datas), 'color' => $item['color']);

				if ($index == $tempate['emphasis_keyword']) {
					$emphasis_keyword = $key;
				}

			}

			unset($index, $item);
			$page = 'pages/order/detail/index?id=' . $orderid;

			if (empty($orderid)) {
				$page = '';
			}


			$obj = json_encode(array('touser' => $openid, 'template_id' => $tempate['templateid'], 'page' => $page, 'form_id' => $prepay_id, 'data' => $data, 'emphasis_keyword' => $emphasis_keyword . '.DATA'));
			$accessToken = $this->getAccessToken();

			if (is_error($accessToken)) {
				return error(-1, 'accessToken获取失败');
			}


			load()->func('communication');
			$result = ihttp_post('https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $accessToken, $obj);
			return $result;
		}

		/**
         * 替换数据
         * @param $str
         * @param array $datas
         * @return mixed
         */
		protected function replaceTemplate($str, $datas = array())
		{
			foreach ($datas as $d ) {
				$str = str_replace('[' . $d['name'] . ']', $d['value'], $str);
			}

			return $str;
		}

		/**
         * 获取单个消息模板
         * @param $id
         * @return bool
         */
		public function getTMessage($id)
		{
			global $_W;
			$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_wxapp_tmessage') . ' WHERE id=:id AND uniacid=:uniacid AND status=1 LIMIT 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

			if (!(empty($item))) {
				$item['datas'] = iunserializer($item['datas']);
			}


			return $item;
		}

		/**
         * 获取全局设置
         */
		public function getGlobal()
		{
			$data = m('cache')->getArray('wxapp', 'global');
			$path = IA_ROOT . '/addons/ewei_shopv2/data/global';

			if (empty($data['upload']) && is_file($path . '/wxapp.cache')) {
				$data_authcode = authcode(file_get_contents($path . '/wxapp.cache'), 'DECODE', 'global');
				$data = json_decode($data_authcode, true);
			}


			return (is_array($data) ? $data : array());
		}

		/**
         * 设置全局
         * @param $data
         * @return bool
         */
		public function setGlobal($data)
		{
			if (empty($data)) {
				return false;
			}


			m('cache')->set('wxapp', $data, 'global');
			$path = IA_ROOT . '/addons/ewei_shopv2/data/global';
			$data_authcode = authcode(json_encode($data), 'ENCODE', 'global');
			file_put_contents($path . '/wxapp.cache', $data_authcode);
			return true;
		}

		/**
         * 店铺装修 获取页面
         * @param int $id
         * @param bool $mobile
         * @return bool
         */
		public function getPage($id = 0, $mobile = false)
		{
			global $_W;

			if (empty($id)) {
				return false;
			}
			if ($mobile) {
				$this->member = m('member')->getMember($_W['openid']);
			}


			$where = ' WHERE uniacid=:uniacid';
			$params = array(':uniacid' => $_W['uniacid']);

			if (is_numeric($id)) {
				$where .= ' AND id=:id';
				$params[':id'] = $id;
			}
			 else {
				$where .= ' AND isdefault=1';
			}

			$page = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_wxapp_page') . $where . ' limit 1 ', $params);

			if (!(empty($page))) {
				$page['data'] = base64_decode($page['data']);
				$page['data'] = json_decode($page['data'], true);
				if (is_array($page['data']['page']) && !(empty($page['data']['page']['icon']))) {
					$page['data']['page']['icon'] = tomedia($page['data']['page']['icon']);
				}


				if (!(empty($page['data']['items']))) {
					foreach ($page['data']['items'] as $itemid => &$item ) {
						$item = $this->goodsData($item, $mobile);

						if ($mobile) {
							$item = $this->rpx($item);
							$item = $this->mediaData($item);

							if ($item['id'] == 'richtext') {
								if (is_array($item['params']) && !(empty($item['params']['content']))) {
									$item['params']['content'] = base64_decode($item['params']['content']);
								}

							}
							 else if (($item['id'] == 'menu') && !(empty($item['style']['showtype']))) {
								if (!(empty($item['data']))) {
									$swiperpage = ((empty($item['style']['pagenum']) ? 8 : $item['style']['pagenum']));
									$data_temp = array();
									$k = 0;
									$i = 1;

									foreach ($item['data'] as $childid => $child ) {
										$data_temp[$k][] = $child;

										if ($i < $swiperpage) {
											++$i;
										}
										 else {
											$i = 1;
											++$k;
										}
									}

									$item['data_temp'] = $data_temp;
									$rownum = ((empty($item['style']['rownum']) ? 4 : $item['style']['rownum']));
									$item['data_line'] = ceil($swiperpage / $rownum);
									unset($swiperpage, $data_temp, $k, $i);
								}
								 else {
									unset($page['data']['items'][$itemid]);
								}
							}
							 else if (($item['id'] == 'picturew') && !(empty($item['params']['showtype']))) {
								if (!(empty($item['data']))) {
									$swiperpage = ((empty($item['style']['pagenum']) ? 2 : $item['style']['pagenum']));
									$data_temp = array();
									$k = 0;
									$i = 1;

									foreach ($item['data'] as $childid => $child ) {
										$data_temp[$k][] = $child;

										if ($i < $swiperpage) {
											++$i;
										}
										 else {
											$i = 1;
											++$k;
										}
									}

									$item['data_temp'] = $data_temp;
									unset($swiperpage, $data_temp, $k, $i);
								}
								 else {
									unset($page['data']['items'][$itemid]);
								}
							}
							 else if (($item['id'] == 'notice') && empty($item['params']['noticedata'])) {
								$limit = ((!(empty($item['params']['noticenum'])) ? $item['params']['noticenum'] : 5));
								$notices = pdo_fetchall('select id, title, link, thumb from ' . tablename('ewei_shop_notice') . ' where uniacid=:uniacid and status=1 order by displayorder desc limit ' . $limit, array(':uniacid' => $_W['uniacid']));
								$item['data'] = array();

								if (!(empty($notices)) && is_array($notices)) {
									foreach ($notices as $index => $notice ) {
										$childid = rand(1000000000, 9999999999);
										$childid = 'C' . $childid;
										$item['data'][$childid] = array('id' => $notice['id'], 'title' => $notice['title'], 'linkurl' => '/pages/shop/notice/detail/detail?id=' . $notice['id']);
									
}
								}

							}
							 else if ($item['id'] == 'video') {
								if (empty($item['params']['videourl'])) {
									unset($page['data']['items'][$itemid]);
								}
								 else {
									$item['params']['videourl'] = tomedia($item['params']['videourl']);
									$item['params']['poster'] = tomedia($item['params']['poster']);
								}
							}
							 else if ($item['id'] == 'goods') {
								if (!(empty($item['data']))) {
									if (!(empty($item['params']['goodsscroll']))) {
										$swiperpage = 1;

										if ($item['style']['liststyle'] == 'block') {
											$swiperpage = 2;
										}
										 else if ($item['style']['liststyle'] == 'block three') {
											$swiperpage = 3;
										}


										$data_temp = array();
										$k = 0;
										$i = 1;

										foreach ($item['data'] as $childid => $child ) {
											$data_temp[$k][] = $child;

											if ($i < $swiperpage) {
												++$i;
											}
											 else {
												$i = 1;
												++$k;
											}
										}

										$item['data_temp'] = $data_temp;
										unset($swiperpage, $data_temp, $k, $i);
									}


									if ($item['params']['showicon'] == 1) {
										if ($item['style']['goodsicon'] == 'recommand') {
											$item['style']['goodsicon'] = '推荐';
										}
										 else if ($item['style']['goodsicon'] == 'hotsale') {
											$item['style']['goodsicon'] = '热销';
										}
										 else if ($item['style']['goodsicon'] == 'isnew') {
											$item['style']['goodsicon'] = '新上';
										}
										 else if ($item['style']['goodsicon'] == 'sendfree') {
											$item['style']['goodsicon'] = '包邮';
										}
										 else if ($item['style']['goodsicon'] == 'istime') {
											$item['style']['goodsicon'] = '限时购';
										}
										 else if ($item['style']['goodsicon'] == 'bigsale') {
											$item['style']['goodsicon'] = '促销';
										}
										 else {
											$item['params']['showicon'] == 0;
										}
									}


									if (($item['params']['saleout'] == 0) && !(empty($_W['shopset']['shop']['saleout']))) {
										$item['params']['saleout'] = tomedia($_W['shopset']['shop']['saleout']);

										if (empty($item['params']['saleout'])) {
											$item['params']['saleout'] = tomedia('../addons/ewei_shopv2/plugin/diypage/static/images/default/saleout-2.png');
										}

									}
									 else if (($item['params']['saleout'] == 1) && empty($item['params']['saleout'])) {
										$item['params']['saleout'] = tomedia('../addons/ewei_shopv2/plugin/diypage/static/images/default/saleout-' . $item['style']['saleoutstyle'] . '.png');
									}

								}
								 else {
									unset($page['data']['items'][$itemid]);
								}
							}

						}
						 else {
							$item = $this->goodsCG($item);
						}
					}

					if (!($mobile)) {
					}

				}

			}


			return $page;
		}

		/**
         * 店铺装修 计算样式rpx
         * @param $item
         * @return mixed
         */
		public function rpx($item)
		{
			$needHandle = array('fontsize', 'padding', 'paddingtop', 'paddingleft', 'margintop', 'marginleft', 'height', 'leftright', 'bottom');
			if (is_array($item) && is_array($item['style'])) {
				foreach ($item['style'] as $key => &$val ) {
					if (in_array($key, $needHandle)) {
						$val = intval($val) * 2;
					}

				}

				unset($key, $val);
			}


			return $item;
		}

		/**
         * 店铺装修 处理商品信息
         * @param $item
         * @return mixed
         */
		public function goodsData($item, $mobile = false)
		{
			global $_W;

			if ($item['id'] == 'goods') {
				if (empty($item['params']['goodsdata']) && !(empty($item['data'])) && is_array($item['data'])) {
					$goodsids = array();

					foreach ($item['data'] as $index => $data ) {
						if (!(empty($data['gid']))) {
							$goodsids[] = $data['gid'];
						}

					}

					if (!(empty($goodsids)) && is_array($goodsids)) {
						$goodsids = array_filter($goodsids);
						$newgoodsids = implode(',', $goodsids);
						$goods = pdo_fetchall('select id, title, subtitle, thumb, minprice, sales, salesreal, total, showlevels, showgroups, bargain, productprice from ' . tablename('ewei_shop_goods') . ' where id in( ' . $newgoodsids . ' ) and status=1 and deleted=0 and checked=0 and uniacid=:uniacid order by displayorder desc ', array(':uniacid' => $_W['uniacid']));
					}

				}
				 else if (($item['params']['goodsdata'] == 1) && !(empty($item['params']['cateid'])) && $mobile) {
					$orderby = ' displayorder desc, createtime desc';

					if ($item['params']['goodssort'] == 1) {
						$orderby = ' sales+salesreal desc, displayorder desc';
					}
					 else if ($item['params']['goodssort'] == 2) {
						$orderby = ' minprice desc, displayorder desc';
					}
					 else if ($item['params']['goodssort'] == 3) {
						$orderby = ' minprice asc, displayorder desc';
					}


					$item['params']['goodsnum'] = ((!(empty($item['params']['goodsnum'])) ? $item['params']['goodsnum'] : 20));
					$goodslist = m('goods')->getList(array('cate' => $item['params']['cateid'], 'pagesize' => $item['params']['goodsnum'], 'page' => 1, 'order' => $orderby));
					$goods = $goodslist['list'];
				}
				 else if (($item['params']['goodsdata'] == 2) && !(empty($item['params']['groupid'])) && $mobile) {
					$group = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_goods_group') . ' WHERE id=:id and uniacid=:uniacid limit 1 ', array(':id' => $item['params']['groupid'], ':uniacid' => $_W['uniacid']));

					if (!(empty($group)) && !(empty($group['goodsids']))) {
						$orderby = ' order by displayorder desc';

						if ($item['params']['goodssort'] == 1) {
							$orderby = ' sales+salesreal desc, displayorder desc';
						}
						 else if ($item['params']['goodssort'] == 2) {
							$orderby = ' minprice desc, displayorder desc';
						}
						 else if ($item['params']['goodssort'] == 3) {
							$orderby = ' minprice asc, displayorder desc';
						}


						$item['params']['goodsnum'] = ((!(empty($item['params']['goodsnum'])) ? $item['params']['goodsnum'] : 20));
						$goods = pdo_fetchall('select id, title, subtitle, thumb, minprice, sales, salesreal, total, showlevels, showgroups, bargain, productprice from ' . tablename('ewei_shop_goods') . ' where id in( ' . $group['goodsids'] . ' ) and status=1 and `deleted`=0 and `status`=1 and uniacid=:uniacid ' . $orderby . ' limit ' . $item['params']['goodsnum'], array(':uniacid' => $_W['uniacid']));
					}

				}
				 else if ((2 < $item['params']['goodsdata']) && $mobile) {
					$args = array('pagesize' => $item['params']['goodsnum'], 'page' => 1, 'order' => ' displayorder desc, createtime desc');

					if ($item['params']['goodssort'] == 1) {
						$args['order'] = ' sales+salesreal desc, displayorder desc';
					}
					 else if ($item['params']['goodssort'] == 2) {
						$args['order'] = ' minprice desc, displayorder desc';
					}
					 else if ($item['params']['goodssort'] == 3) {
						$args['order'] = ' minprice asc, displayorder desc';
					}


					if ($item['params']['goodsdata'] == 3) {
						$args['isnew'] = 1;
					}
					 else if ($item['params']['goodsdata'] == 4) {
						$args['ishot'] = 1;
					}
					 else if ($item['params']['goodsdata'] == 5) {
						$args['isrecommand'] = 1;
					}
					 else if ($item['params']['goodsdata'] == 6) {
						$args['isdiscount'] = 1;
					}
					 else if ($item['params']['goodsdata'] == 7) {
						$args['issendfree'] = 1;
					}
					 else if ($item['params']['goodsdata'] == 8) {
						$args['istime'] = 1;
					}


					$goodslist = m('goods')->getList($args);
					$goods = $goodslist['list'];
				}


				if (!(empty($goods)) && is_array($goods)) {
					if (empty($item['params']['goodsdata'])) {
						foreach ($item['data'] as $childid => $childgoods ) {
							foreach ($goods as $index => $good ) {
								if ($good['id'] == $childgoods['gid']) {
									if ($mobile && !(m('goods')->visit($good, $this->member))) {
										continue;
									}


									$item['data'][$childid] = array('gid' => $good['id'], 'title' => $good['title'], 'subtitle' => $good['subtitle'], 'price' => $good['minprice'], 'thumb' => $good['thumb'], 'total' => $good['total'], 'productprice' => $good['productprice'], 'ctype' => $good['type'], 'sales' => $good['sales'] + intval($good['salesreal']));
								}

							}
						}
					}
					 else {
						$item['data'] = array();

						foreach ($goods as $index => $good ) {
							$childid = 'C' . rand(1000000000, 9999999999);
							$item['data'][$childid] = array('gid' => $good['id'], 'title' => $good['title'], 'subtitle' => $good['subtitle'], 'price' => $good['minprice'], 'thumb' => $good['thumb'], 'total' => $good['total'], 'productprice' => $good['productprice'], 'ctype' => $good['type'], 'sales' => $good['sales'] + intval($good['salesreal']));
						}
					}
				}

			}


			return $item;
		}

		/**
         * 店铺装修 处理商品分类/分组
         * @param $item
         * @return mixed
         */
		public function goodsCG($item)
		{
			global $_W;

			if ($item['id'] == 'goods') {
				if (($item['params']['goodsdata'] == 1) && !(empty($item['params']['cateid']))) {
					$category = pdo_fetch('select id,`name`, enabled from ' . tablename('ewei_shop_category') . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $item['params']['cateid'], ':uniacid' => $_W['uniacid']));
					$item['params']['catename'] = ((!(empty($category)) ? $category['name'] : ''));
					$item['params']['cateid'] = ((!(empty($category)) ? $category['id'] : ''));
				}
				 else if (($item['params']['goodsdata'] == 2) && !(empty($item['params']['groupid']))) {
					$group = pdo_fetch('select id, `name` from ' . tablename('ewei_shop_goods_group') . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $item['params']['groupid'], ':uniacid' => $_W['uniacid']));
					$item['params']['groupname'] = ((!(empty($group)) ? $group['name'] : ''));
					$item['params']['groupid'] = ((!(empty($group)) ? $group['id'] : ''));
				}

			}


			return $item;
		}

		/**
         * 店铺装修 图片强转长链接、data对象转数组
         * @param $item
         * @return mixed
         */
		public function mediaData($item)
		{
			$needHandle = array('iconurl', 'imgurl', 'leftnavimg', 'rightnavimg', 'thumb', 'goodsiconsrc');
			if (is_array($item) && is_array($item['params'])) {
				foreach ($item['params'] as $key => &$val ) {
					if (in_array($key, $needHandle)) {
						$val = tomedia($val);
					}

				}

				unset($key, $val);
			}


			if (is_array($item) && is_array($item['data']) && (0 < count($item['data']))) {
				$newDatas = array();

				foreach ($item['data'] as $childid => $child ) {
					if (is_array($child)) {
						foreach ($child as $key => &$val ) {
							if (in_array($key, $needHandle)) {
								$val = tomedia($val);
							}

						}

						unset($key, $val);
					}


					$newDatas[] = $child;
				}

				$item['data'] = $newDatas;
			}


			if ($item['id'] == 'richtext') {
				$item = $this->mediaRich($item);
			}


			return $item;
		}

		/**
         * 店铺装修 处理富文本的图片
         * @param $item
         * @return mixed
         */
		public function mediaRich($item)
		{
			return $item;
		}

		/**
         * 获取
         * @return mixed
         */
		public function getCodeUnlimit($params = array())
		{
			if (empty($params) || !(is_array($params))) {
				return error(-1, '参数错误(params)');
			}


			if (empty($params['scene']) || empty($params['page'])) {
				return error(-1, '参数错误(scenepage)');
			}


			$accessToken = $this->getAccessToken();

			if (is_error($accessToken)) {
				return error(-1, $accessToken['message']);
			}


			load()->func('communication');
			$request = ihttp_post('https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $accessToken, json_encode($params));
			$content = json_decode($request['content'], true);

			if (!(empty($content['errcode']))) {
				return error(-1, '(errcode: ' . $content['errcode'] . ') ' . $content['errmsg']);
			}


			return $request['content'];
		}

		/**
         * 获取授权状态
         * @return array
         */
		public function getAuth($uri = '')
		{
			global $_W;
			$siteid = intval($_W['setting']['site']['key']);

			global $_W;
			//$siteid = intval($_W['setting']['site']['key']);
			//cc_zhong 直接返回结果
			//return array('errno'=>0, 'id'=>$_W['uniacid']);
			load()->func('communication');
			$request = ihttp_get(EWEI_SHOPV2_AUTH_WXAPP . '&do=auth&site_id=' . SITE_ID . '&uniacid=' . $_W['uniacid'].'&title='.$_W['account']['name'].$uri);
			//print_r($request);exit;
			if ($request['code'] != 200) {
				return error(-1, '信息查询失败！稍后重试');
			}

			if (empty($request['content'])) {
				return error(-1, '信息查询失败！稍后重试(nodata)');
			}

			$content = json_decode($request['content'], true);

			if (!is_array($content)) {
				return error(-1, '信息查询失败！稍后重试(dataerror)');
			}

			if ($content['status'] != 1) {
				return error(-1, $content['errmsg']);
			}

			if (is_array($content['data'])) {
				return $content['data'];
			}

		}

		/**
         * 获取版本信息
         * @param $authid
         * @return array
         */
		public function getRelease($authid,$uri='')
		{
		global $_W;
			if (empty($authid)) {
				return error(-1, 'authid为空');
			}


			load()->func('communication');
			$request = ihttp_get(EWEI_SHOPV2_AUTH_WXAPP . 'xcxapi/auth-xcx-info/view?site_id=' . SITE_ID . '&uniacid=' . $_W['uniacid'].$uri);

			if ($request['code'] != 200) {
				return error(-1, '接口通信失败');
			}


			if (empty($request['content'])) {
				return error(-1, '接口未返回信息(1)');
			}


			$content = json_decode($request['content'], true);

			if (!(is_array($content))) {
				return error(-1, '接口未返回信息(2)');
			}


			if ($content['status'] != 1) {
				return error(-1, $content['errmsg']);
			}


			if (is_array($content['data'])) {
				return $content['data'];
			}

		}

		/**
         * 获取全部列表
         * @return mixed
         */
		public function getReleaseList()
		{
			global $_W;
			//cc_zhong 首页跳转到发布页
        	header("Location:".webUrl('app/ccrelease'));
        	exit;
			$siteid = intval($_W['setting']['site']['key']);

			if (empty($siteid)) {
				//return error(-1, '站点未注册');
			}


			load()->func('communication');
			$request = ihttp_get(EWEI_SHOPV2_AUTH_WXAPP . 'xcxapi/auth-xcx-info/list?site_id=' . SITE_ID . '&uniacid=' . $_W['uniacid']);

			if ($request['code'] != 200) {
				return error(-1, '接口通信失败');
			}


			if (empty($request['content'])) {
				return error(-1, '接口未返回信息(1)');
			}


			$content = json_decode($request['content'], true);

			if (!(is_array($content))) {
				return error(-1, '接口未返回信息(2)');
			}


			if ($content['status'] != 1) {
				return error(-1, $content['errmsg']);
			}


			if (is_array($content['data'])) {
				return $content['data'];
			}

		}

		/**
         * 获取更新日志
         * @param int $page
         * @return mixed
         */
		public function getReleaseLog($page = 1, $psize = 10)
		{
			load()->func('communication');
			$request = ihttp_get(EWEI_SHOPV2_AUTH_URL . 'wxapp_log?pindex=' . $page . '&psize=' . $psize);
			$content = json_decode($request['content'], true);
			return $content;
		}

		/**
         * 处理接口文件
         */
		public function apiFile()
		{
			$path1 = __DIR__ . '/mobile/ewei_shopv2_api.php';
			$path2 = IA_ROOT . '/app/ewei_shopv2_api.php';

			if (!(is_file($path1))) {
				return;
			}


			if (!(is_file($path2)) || (md5_file($path1) != md5_file($path2))) {
				@copy($path1, $path2);
			}

		}

		/**
         * 获取腾讯视频
         * @param null $url
         * @param bool $vid
         * @return array|string|void
         */
		public function getQVideo($url = NULL, $vid = false)
		{
			if (empty($url)) {
				return;
			}


			if (!($vid)) {
				$vid = $this->getQVideoVid($url);
			}


			load()->func('communication');
			$request = ihttp_get('https://h5vv.video.qq.com/getinfo?callback=renrenVideo&otype=json&platform=11001&host=v.qq.com&sphttps=1&vid=' . $vid);
			if (empty($request) || ($request['code'] != 200) || empty($request['content'])) {
				return error(-1, '获取失败-1');
			}


			$content = $request['content'];
			$content = ltrim($content, 'renrenVideo(');
			$content = rtrim($content, ')');
			$array = json_decode($content, true);
			if (!(is_array($array)) || !(isset($array['vl'])) || !(is_array($array['vl']['vi'])) || !(is_array($array['vl']['vi'][0]))) {
				return error(-1, '获取失败-2');
			}


			$fvideo = $array['vl']['vi'][0];
			if (empty($fvideo['fvkey']) || !(isset($fvideo['ul'])) || !(is_array($fvideo['ul']['ui'])) || !(is_array($fvideo['ul']['ui'][0])) || !(isset($fvideo['ul']['ui'][0]['url']))) {
				return error(-1, '获取失败-3');
			}


			$videopath = ((isset($fvideo['ul']['ui'][1]) && !(empty($fvideo['ul']['ui'][1]['url'])) ? $fvideo['ul']['ui'][1]['url'] : $fvideo['ul']['ui'][0]['url']));
			return $videopath . $fvideo['fn'] . '?vkey=' . $fvideo['fvkey'];
		}

		/**
         * 获取腾讯视频VID
         * @param $url
         * @return string|void
         */
		public function getQVideoVid($url)
		{
			if (empty($url) || !(strexists($url, 'v.qq.com/iframe/'))) {
				return;
			}


			$vid = '';
			$params = parse_url($url);
			parse_str($params['query']);
			return $vid;
		}
		
		public function createAuth()
		{
			global $_W;
			global $_GPC;
			$domain = trim(preg_replace('/http(s)?:\\/\\//', '', rtrim($_W['siteroot'], '/')));
			$ip = gethostbyname($_SERVER['HTTP_HOST']);
			$setting = setting_load('site');
			$id = ((isset($setting['site']['key']) ? $setting['site']['key'] : '0'));
			$auth = get_auth();
			load()->func('communication');
			$resp = ihttp_request(EWEI_SHOPV2_AUTH_URL . 'app', array('ip' => $ip, 'id' => $id, 'code' => $auth['code'], 'domain' => $domain, 'appid' => $_W['account']['key']), NULL, 1);
			$result = @json_decode($resp['content'], true);
			if ($result['status'] && !(empty($result['result']['ak'])) && !(empty($result['result']['sk']))) {
				return array('ak' => $result['result']['ak'], 'sk' => $result['result']['sk']);
			}


			return error(-1, $result['result']['message']);
		}

		/**
         * 原生APP自定义菜单
         * @param $menuid
         * @return array|string|void
         */
		public function diyMenu($menuid)
		{
			global $_W;
			global $_GPC;
			$set = m('common')->getPluginset('diypage');
			$menuset = $set['menu'];
			$id = intval($menuid);

			if ($id == 0) {
				$id = $menuset[$menuid];
			}


			if (empty($id)) {
				return $this->defaultMenu($menuid);
			}


			if (empty($data['params']['navstyle'])) {
				$iconfont = $this->getIconUnicode();
			}


			$menu = pdo_fetch('select * from ' . tablename('ewei_shop_diypage_menu') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

			if (empty($menu)) {
				return $this->defaultMenu($menuid);
			}


			$data = base64_decode($menu['data']);
			$data = json_decode($data, true);
			$items = $data['data'];
			if (empty($items) || !(is_array($items))) {
				return;
			}


			$loginPage = $this->loginPage;
			$newMenu = array();

			foreach ($items as $id => $item ) {
				if (empty($item['linkurl'])) {
					continue;
				}


				if (empty($data['params']['navstyle'])) {
					if (empty($item['text'])) {
						continue;

						if (empty($item['imgurl'])) {
							continue;
						}

					}

				}
				 else {
					continue;
				}

				$newUrl = $this->getUrl($item['linkurl']);
				$newItem = array('url' => $newUrl['url']);

				if (!(empty($newUrl['vars']))) {
					$newItem['url_vars'] = $newUrl['vars'];
				}


				if (!(empty($data['params']['navstyle']))) {
					$newItem['image'] = tomedia($item['imgurl']);
				}
				 else {
					$newItem['icon'] = $iconfont[$item['iconclass']];
					$newItem['text'] = $item['text'];
				}

				if (!(empty($loginPage))) {
					foreach ($loginPage as $lp ) {
						while (strexists($newUrl['url'], $lp)) {
							$newItem['needlogin'] = 1;
							break;
						}
					}
				}


				if (!(empty($item['child']))) {
					foreach ($item['child'] as $id => $child ) {
						if (empty($child['linkurl']) || empty($child['text'])) {
							continue;
						}


						$childNewUrl = $this->getUrl($child['linkurl']);
						$newChild = array('text' => $child['text'], 'url' => $childNewUrl['url']);

						if (!(empty($childNewUrl['vars']))) {
							$newChild['url_vars'] = $childNewUrl['vars'];
						}


						if (!(empty($loginPage))) {
							foreach ($loginPage as $lp ) {
								while (strexists($childNewUrl['url'], $lp)) {
									$newChild['needlogin'] = 1;
									break;
								}
							}
						}


						if (!(empty($newChild))) {
							$newItem['child'][] = $newChild;
						}

					}
				}


				$newMenu[] = $newItem;
			}

			return array('type' => $data['params']['navstyle'], 'navfloat' => $data['params']['navfloat'], 'style' => $data['style'], 'items' => $newMenu);
		}

		/**
         * 原生APP默认菜单
         * @param $page
         * @return array|string
         */
		public function defaultMenu($page)
		{
			global $_W;

			if ($page == 'shop') {
				$menu = array(
					array('url' => 'index', 'icon' => 'e647', 'text' => '首页'),
					array('url' => 'shop.category', 'icon' => 'e62c', 'text' => '全部分类'),
					array('url' => 'commission', 'icon' => 'e647', 'text' => '分销中心'),
					array('url' => 'member.cart', 'icon' => 'e642', 'text' => '购物车', 'needlogin' => 1),
					array('url' => 'member', 'icon' => 'e724', 'text' => '会员中心', 'needlogin' => 1)
					);
				if (p('commission') && (0 < $_W['shopset']['commission']['level'])) {
					$member = m('member')->getMember($_W['openid']);

					if (($member['isagent'] == 1) && ($member['status'] == 1)) {
						$menu[2]['text'] = ((empty($_W['shopset']['commission']['texts']['center']) ? '分销中心' : $_W['shopset']['commission']['texts']['center']));
					}
					 else {
						$menu[2]['text'] = ((empty($_W['shopset']['commission']['texts']['become']) ? '成为分销商' : $_W['shopset']['commission']['texts']['become']));
						$menu[2]['url'] = 'commission.register';
					}
				}
				 else {
					array_splice($menu, 2, 1);
				}
			}
			 else if ($page == 'commission') {
				$menu = array(
					array('url' => 'commission', 'icon' => 'e647', 'text' => '分销中心', 'needlogin' => 1),
					array('url' => 'commission.withdraw', 'icon' => 'e74c', 'text' => '分销佣金', 'needlogin' => 1),
					array('url' => 'commission.order', 'icon' => 'e62c', 'text' => '佣金明细', 'needlogin' => 1),
					array('url' => 'commission.down', 'icon' => 'e6c8', 'text' => '我的下线', 'needlogin' => 1),
					array('url' => 'commission.myshop', 'icon' => 'e627', 'text' => '我的小店', 'needlogin' => 1)
					);
			}
			 else {
				return '';
			}

			return array(
	'type'     => 0,
	'navfloat' => 'top',
	'style'    => array('pagebgcolor' => '#f9f9f9', 'bgcolor' => '#ffffff', 'bgcoloron' => '#ffffff', 'iconcolor' => '#999999', 'iconcoloron' => '#ff0000', 'textcolor' => '#666666', 'textcoloron' => '#ff0000', 'bordercolor' => '#ffffff', 'bordercoloron' => '#ffffff', 'childtextcolor' => '#666666', 'childbgcolor' => '#f4f4f4', 'childbordercolor' => '#eeeeee'),
	'items'    => $menu
	);
		}

		/**
         * 原生APP获取URL
         * @param $url
         * @return array
         */
		public function getUrl($url)
		{
			global $_W;

			if (empty($url)) {
				return array();
			}


			if (strexists($url, './index.php?') && strexists($url, 'ewei_shopv2') && strexists($url, 'mobile')) {
				$parse = parse_url($url);
				$parse_query = $parse['query'];

				if (empty($parse_query)) {
					return array();
				}


				$vars = explode('&', $parse_query);
				$newVars = array();

				foreach ($vars as $i => $var ) {
					$vararr = explode('=', $var);
					$newVars[$vararr[0]] = $vararr[1];
				}

				if (($newVars['m'] != 'ewei_shopv2') || ($newVars['do'] != 'mobile')) {
					return array('url' => $url);
				}


				$route = $newVars['r'] = ((!(empty($newVars['r'])) ? $newVars['r'] : 'index'));
				unset($newVars['i'], $newVars['c'], $newVars['m'], $newVars['do'], $newVars['r']);
				$newUrl = array('url' => $route, 'vars' => $newVars);
				$routes = explode('.', $route);

				if (!(in_array($routes[0], $this->staticurl))) {
					$newUrl['url'] = $_W['siteroot'] . 'app/' . str_replace('./', '', $url);
					unset($newUrl['vars']);
				}


				return $newUrl;
			}


			return array('url' => $url);
		}

		/**
         * 原生APP ICON转码
         * @param null $class
         * @return array|string
         */
		public function getIconUnicode($class = NULL)
		{
			$file = EWEI_SHOPV2_PLUGIN . 'app/static/iconfont.json';

			if (!(file_exists($file))) {
				return '';
			}


			$json = file_get_contents($file);

			if (empty($json)) {
				return '';
			}


			$arr = json_decode($json, true);
			if (empty($arr) || !(is_array($arr))) {
				return '';
			}


			$newArr = array();

			foreach ($arr as $i => $item ) {
				if (!(empty($item['code'])) && !(empty($item['class']))) {
					$newArr[$item['class']] = $item['code'];
				}

			}

			return $newArr;
		}

		/**
         * 原生APP支付宝支付
         * @param $params
         * @param array $config
         * @return array|bool|string
         */
		public function alipay_build($params, $config = array())
		{
			global $_W;
			$arr = array('app_id' => $config['appid'], 'method' => 'alipay.trade.app.pay', 'format' => 'JSON', 'charset' => 'utf-8', 'sign_type' => 'RSA2', 'timestamp' => date('Y-m-d H:i:s', time()), 'version' => '1.0', 'notify_url' => $_W['siteroot'] . 'addons/ewei_shopv2/payment/alipay/notify.php', 'biz_content' => json_encode(array('timeout_express' => '90m', 'product_code' => 'QUICK_MSECURITY_PAY', 'total_amount' => $params['total_amount'], 'subject' => $params['subject'], 'body' => $params['body'], 'out_trade_no' => $params['out_trade_no'])));
			ksort($arr);
			$string1 = '';

			foreach ($arr as $key => $v ) {
				if (empty($v)) {
					continue;
				}


				$string1 .= $key . '=' . $v . '&';
			}

			$string1 = rtrim($string1, '&');
			$pkeyid = openssl_pkey_get_private(m('common')->chackKey($config['private_key'], false));

			if ($pkeyid === false) {
				return error(-1, '提供的私钥格式不对');
			}


			$signature = '';
			openssl_sign($string1, $signature, $pkeyid, OPENSSL_ALGO_SHA256);
			openssl_free_key($pkeyid);
			$signature = base64_encode($signature);
			$arr['sign'] = $signature;
			return http_build_query($arr);
		}
	}

}


?>