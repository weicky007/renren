<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<?php  if($operate == 'display') { ?>
	<?php  if(!in_array($type, array(STORE_TYPE_PACKAGE, STORE_TYPE_USER_PACKAGE, STORE_TYPE_ACCOUNT_PACKAGE))) { ?>
	<?php  if($type == STORE_TYPE_USER_RENEW) { ?>
		<div class="we7-page-title">账号有效期</div>
	<?php  } ?>
	<?php  if($type == 'module' || isset($module_types[$type])) { ?>
	<div class="we7-page-title"><?php  if($is_wish) { ?>预购应用<?php  } else { ?>应用模块<?php  } ?></div>
	<?php  } else if($type == 'account_num') { ?>
	<div class="we7-page-title">平台个数</div>
	<?php  } else if($type == 'renew') { ?>
	<div class="we7-page-title">平台续费</div>
	<?php  } else if($type == STORE_TYPE_API) { ?>
	<div class="we7-page-title">应用访问流量(API)</div>
	<?php  } ?>
	<?php  if($type == 'module' || isset($module_types[$type])) { ?>
	<!-- 搜索 start -->
	<div class="clearfix">
		<div class="search-box we7-margin-bottom">
			<select class="we7-margin-right">
				<option data-url="<?php  echo filter_url('type:');?>" <?php  if(empty($type) || $type == 'module') { ?>selected<?php  } ?>>模块类型</option>
				<?php  if(is_array($module_types)) { foreach($module_types as $item) { ?>
				<option data-url="<?php  echo filter_url('type:'. $item['type']);?>" <?php  if($type == $item['type']) { ?>selected<?php  } ?>><?php  echo $item['title'];?></option>
				<?php  } } ?>
			</select>

			<form action="" class="search-form " method="get">
				<input type="hidden" name="c" value="site">
				<input type="hidden" name="a" value="entry">
				<input type="hidden" name="do" value="goodsbuyer">
				<input type="hidden" name="m" value="store">
				<input type="hidden" name="direct" value="<?php  echo $_GPC['direct'];?>">
				<input type="hidden" name="type" value="<?php  echo $_GPC['type'];?>">
				<input type="hidden" name="is_wish" value="<?php  echo $_GPC['is_wish'];?>">
				<div class="input-group" style="width: 400px;">
					<input type="text" name="module_name" value="<?php  echo $_GPC['module_name'];?>" class="form-control" placeholder="输入要搜索的应用名称"/>
					<span class="input-group-btn"><button class="btn btn-default"><i class="wi wi-search"></i></button></span>
				</div>
			</form>
		</div>
	</div>
	<!-- 搜索 end -->
	<?php  } ?>
	<div class="wish-goods-list">
		<?php  if(is_array($store_goods)) { foreach($store_goods as $goods) { ?>
		<a href="<?php  echo $this->createWebUrl('goodsbuyer', array('operate' => 'goods_info', 'direct' => 1, 'goods' => $goods['id']))?>" class="wish-goods-item">
			<div class="wish-goods-box">
				<?php  if($goods['type'] == STORE_TYPE_API) { ?>
				<div class="icon icon-api logo"><span class="wi wi-api"></span></div>
				<?php  } else if(in_array($goods['type'], array(STORE_TYPE_PACKAGE, STORE_TYPE_ACCOUNT, STORE_TYPE_WXAPP,STORE_TYPE_WEBAPP,STORE_TYPE_PHONEAPP,STORE_TYPE_XZAPP,STORE_TYPE_ALIAPP,STORE_TYPE_BAIDUAPP,STORE_TYPE_TOUTIAOAPP, STORE_TYPE_ACCOUNT_RENEW, STORE_TYPE_WXAPP_RENEW, STORE_TYPE_WEBAPP_RENEW,STORE_TYPE_PHONEAPP_RENEW,STORE_TYPE_XZAPP_RENEW,STORE_TYPE_ALIAPP_RENEW,STORE_TYPE_BAIDUAPP_RENEW,STORE_TYPE_TOUTIAOAPP_RENEW, STORE_TYPE_USER_RENEW))) { ?>
				<div class="icon icon-wi logo"><span class="wi wi-appjurisdiction"></span></div>
				<?php  } else if($goods['type'] == STORE_TYPE_USER_PACKAGE) { ?>
				<div class="icon icon-wi logo"><span class="wi wi-userjurisdiction"></span></div>
				<?php  } else if(isset($module_types[$goods['type']])) { ?>
				<img src="<?php  echo $goods['module']['logo'];?>" alt="icon" class="logo" onerror="this.src='./resource/images/nopic-107.png'"/>
				<?php  } ?>
				<div class="info text-over">
					<div class="title text-over">
						<?php  if((in_array($goods['type'], array_keys($store_goods_types['renew'])))) { ?>
							<?php  echo $store_goods_types['renew'][$goods['type']]['title'];?>
						<?php  } else { ?>
							<?php  if((in_array($goods['type'], array_keys($store_goods_types['account_num'])))) { ?>
								<?php  echo $store_goods_types['account_num'][$goods['type']]['title'];?>
							<?php  } else { ?>
								<?php  echo $goods['title'];?>
							<?php  } ?>
							<?php  echo $goods['num'];?>
						<?php  } ?>
					</div>
					<div class="price">
						￥<?php  echo $goods['price'];?>元
						<?php  if(!in_array($goods['type'], array_keys($store_goods_types['account_num']))) { ?>
							/
							<?php  if($goods['type'] == STORE_TYPE_USER_RENEW) { ?>
								<?php  echo $goods['account_num'];?>
							<?php  } ?>
							<?php  if($goods['unit'] == 'month') { ?>
								<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
									<?php  echo $goods['account_num'];?>
								<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
									<?php  echo $goods['wxapp_num'];?>
								<?php  } ?>
								月
							<?php  } else if($goods['unit'] == 'ten_thousand') { ?>
								<?php  echo $goods['api_num'];?>万次
							<?php  } else if($goods['unit'] == 'day') { ?>
								<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
									<?php  echo $goods['account_num'];?>
								<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
									<?php  echo $goods['wxapp_num'];?>
								<?php  } ?>
								天
							<?php  } else if($goods['unit'] == 'year') { ?>
								<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
									<?php  echo $goods['account_num'];?>
								<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
									<?php  echo $goods['wxapp_num'];?>
								<?php  } ?>
								年
							<?php  } else if($goods['unit'] == 'week') { ?>
								<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
									<?php  echo $goods['account_num'];?>
								<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
									<?php  echo $goods['wxapp_num'];?>
								<?php  } ?>
								周
							<?php  } ?>
						<?php  } ?>
					</div>
					<?php  if(isset($module_types[$goods['type']])) { ?>
					<div class="support">
						适用
						<?php  if($module_types[$goods['type']]['sign'] == 'phoneapp') { ?>
						<i class="wi wi-app"></i>
						<?php  } else if($module_types[$goods['type']]['sign'] == 'webapp') { ?>
						<i class="wi wi-pc"></i>
						<?php  } else { ?>
						<i class="wi wi-<?php  echo $module_types[$goods['type']]['sign'];?>"></i>
						<?php  } ?>
					</div>
					<?php  } ?>
				</div>
				<span class="link">详情 ></span>
			</div>
		</a>
		<?php  } } ?>
	</div>
	<?php  } ?>

	<?php  if($_GPC['type'] == STORE_TYPE_PACKAGE) { ?>
	<div class="we7-page-title">应用权限组</div>
	<div class="wish-goods-list">
		<?php  if(is_array($store_goods)) { foreach($store_goods as $goods) { ?>
		<div class="wish-goods-item">
			<div class="wish-goods-box">
				<div class="icon icon-api logo"><span class="wi wi-appjurisdiction"></span></div>
				<div class="info text-over">
					<div class="title text-over"><?php  echo $module_groups[$goods['module_group']]['name'];?></div>
					<div class="price">¥ <?php  echo $goods['price'];?>元/<?php  if($goods['type'] != STORE_TYPE_API) { ?><?php  if($goods['unit'] == 'month') { ?>月<?php  } else if($goods['unit'] == 'day') { ?>日<?php  } else if($goods['unit'] == 'year') { ?>年<?php  } else if($goods['unit'] == 'week') { ?>周<?php  } ?><?php  } ?></div>
					<div class="support">
						<!-- 适用 <i class="wi wi-wxapp"></i> -->
					</div>
				</div>
				<a href="<?php  echo $this->createWebUrl('goodsbuyer', array('operate' => 'goods_info', 'direct' => 1, 'goods' => $goods['id']))?>" class="link">详情 ></a>
			</div>
		</div>
		<?php  } } ?>
	</div>
	<?php  } ?>

	<?php  if($_GPC['type'] == STORE_TYPE_USER_PACKAGE) { ?>
	<div class="we7-page-title">用户权限组</div>
	<div class="wish-goods-list">
		<?php  if(is_array($store_goods)) { foreach($store_goods as $goods) { ?>
		<div class="wish-goods-item">
			<div class="wish-goods-box">
				<div class="icon icon-api logo"><span class="wi wi-userjurisdiction"></span></div>
				<div class="info text-over">
					<div class="title text-over"><?php  echo $user_groups[$goods['user_group']]['name'];?></div>
					<div class="price">¥ <?php  echo $goods['price'];?>元/<?php  if($goods['type'] != STORE_TYPE_API) { ?><?php  if($goods['unit'] == 'month') { ?>月<?php  } else if($goods['unit'] == 'day') { ?>日<?php  } else if($goods['unit'] == 'year') { ?>年<?php  } else if($goods['unit'] == 'week') { ?>周<?php  } ?><?php  } ?></div>
					<div class="support">
						<!-- 适用 <i class="wi wi-wxapp"></i> -->
					</div>
				</div>
				<a href="<?php  echo $this->createWebUrl('goodsbuyer', array('operate' => 'goods_info', 'direct' => 1, 'goods' => $goods['id']))?>" class="link">详情 ></a>
			</div>
		</div>
		<?php  } } ?>
	</div>
	<?php  } ?>

	<?php  if($_GPC['type'] == STORE_TYPE_ACCOUNT_PACKAGE) { ?>
	<div class="we7-page-title">账号权限组</div>
	<div class="wish-goods-list">
		<?php  if(is_array($store_goods)) { foreach($store_goods as $goods) { ?>
		<div class="wish-goods-item">
			<div class="wish-goods-box">
				<div class="icon icon-api logo"><span class="wi wi-userjurisdiction"></span></div>
				<div class="info text-over">
					<div class="title text-over"><?php  echo $account_groups[$goods['account_group']]['group_name'];?></div>
					<div class="price">¥ <?php  echo $goods['price'];?>元</div>
					<div class="support">
						<!-- 适用 <i class="wi wi-wxapp"></i> -->
					</div>
				</div>
				<a href="<?php  echo $this->createWebUrl('goodsbuyer', array('operate' => 'goods_info', 'direct' => 1, 'goods' => $goods['id']))?>" class="link">详情 ></a>
			</div>
		</div>
		<?php  } } ?>
	</div>
	<?php  } ?>

	<div class="text-right we7-margin-top">
		<?php  echo $pager;?>
	</div>
	<script>
		$('select').niceSelect();
	</script>
<?php  } else if($operate == 'goods_info') { ?>
<div class="panel-body js-goods-buyer" ng-controller="goodsBuyerCtrl">
	<ol class="breadcrumb we7-breadcrumb">
		<a href="javascript:history.back()"><i class="wi wi-back-circle"></i> </a>
		<li>
			<a href="javascript:history.back()">商品列表</a>
		</li>
		<li>
			<?php  echo $goods['title'];?>
		</li>
	</ol>
	<div class="wish-goods-detail <?php  if($goods['type'] == STORE_TYPE_PACKAGE) { ?>jurisdiction-detail<?php  } else { ?>module-detail<?php  } ?>">
		<div class="wish-goods-info">
			<?php  if($goods['type'] == STORE_TYPE_API) { ?>
			<div class="logo"><span class="icon-box"><i class="wi wi-api"></i></span></div>
			<?php  } else if(in_array($goods['type'], array(STORE_TYPE_PACKAGE, STORE_TYPE_ACCOUNT, STORE_TYPE_WXAPP,STORE_TYPE_WEBAPP,STORE_TYPE_PHONEAPP,STORE_TYPE_XZAPP,STORE_TYPE_ALIAPP,STORE_TYPE_BAIDUAPP,STORE_TYPE_TOUTIAOAPP, STORE_TYPE_ACCOUNT_RENEW, STORE_TYPE_WXAPP_RENEW, STORE_TYPE_WEBAPP_RENEW,STORE_TYPE_PHONEAPP_RENEW,STORE_TYPE_XZAPP_RENEW,STORE_TYPE_ALIAPP_RENEW,STORE_TYPE_BAIDUAPP_RENEW,STORE_TYPE_TOUTIAOAPP_RENEW, STORE_TYPE_USER_RENEW))) { ?>
			<div class="logo"><span class="icon-box"><i class="wi wi-appjurisdiction"></i></span></div>
			<?php  } else if($goods['type'] == STORE_TYPE_USER_PACKAGE || $goods['type'] == STORE_TYPE_ACCOUNT_PACKAGE) { ?>
			<div class="logo"><span class="icon-box"><i class="wi wi-userjurisdiction"></i></span></div>
			<?php  } else { ?>
			<div class="logo"><img src="<?php  echo $goods['module']['logo'];?>" onerror="this.src='./resource/images/nopic-107.png'"/></div>
			<?php  } ?>
			<div class="info">
				<div class="title">
					<?php  if(in_array($all_type_info[$goods['type']]['group'], array('module', 'user_renew'))) { ?>
					<?php  echo $goods['title'];?>
					<?php  } else if($goods['type'] == STORE_TYPE_WXAPP) { ?>
					创建<?php  echo $goods['wxapp_num'];?>个小程序
					<?php  } else if($goods['type'] == STORE_TYPE_ACCOUNT) { ?>
					创建<?php  echo $goods['account_num'];?>个公众号
					<?php  } else if(in_array($goods['type'], array_keys($all_type_info['account_num']))) { ?>
					创建<?php  echo $goods['platform_num'];?>个<?php  echo $all_type_info['account_num'][$goods['type']]['title'];?>
					<?php  } else if($goods['type'] == STORE_TYPE_USER_PACKAGE) { ?>
					<?php  echo $group_info['name'];?> 套餐
					<?php  } else if($goods['type'] == STORE_TYPE_ACCOUNT_PACKAGE) { ?>
					<?php  echo $group_info['group_name'];?> 套餐
					<?php  } else { ?>
					<?php  echo $module_groups[$goods['module_group']]['name'];?> 套餐
					<?php  } ?>
				</div>
				<div class="support">
					<?php  if(in_array($all_type_info[$goods['type']]['group'], array('module', 'user_renew'))) { ?>
					<?php  echo $goods_type_info['title'];?>
					<?php  } else if(in_array($goods['type'], array_keys($all_type_info['account_num']))) { ?>
					增加创建<?php  echo $all_type_info['account_num'][$goods['type']]['title'];?>数量
					<?php  } else if($goods['type'] == STORE_TYPE_API) { ?>
					总计<span class="color-red"><?php  echo $goods['api_num'];?><?php  if($goods['unit'] == 'ten_thousand') { ?>万次</span><?php  } ?>浏览次数，不限时间
					<?php  } else if(in_array($goods['type'], array_keys($all_type_info['renew']))) { ?>
					延长<?php  echo $all_type_info['renew'][$goods['type']]['title'];?>到期时间
					<?php  } else if($goods['type'] == STORE_TYPE_ACCOUNT_PACKAGE) { ?>
					增加创建账号的数量
					<?php  } else { ?>
					增加公众号应用，小程序，模板数量
					<?php  } ?>
				</div>
			</div>
			<div class="price-box">
				<div class="price">￥
					<span class="fee">
						<?php  echo $goods['price'];?> 元
					</span>
					<?php  if($goods['type'] != STORE_TYPE_API && $goods['type'] != STORE_TYPE_ACCOUNT_PACKAGE && !in_array($goods['type'], array(STORE_TYPE_ACCOUNT, STORE_TYPE_WXAPP))) { ?>/<?php  if($goods['type'] == STORE_TYPE_USER_RENEW) { ?><?php  echo $goods['account_num'];?><?php  } ?>
					<?php  if($goods['unit'] == 'month') { ?>
						<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
							<?php  echo $goods['account_num'];?>
						<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
							<?php  echo $goods['wxapp_num'];?>
						<?php  } ?>月
					<?php  } else if($goods['unit'] == 'day') { ?>
						<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
							<?php  echo $goods['account_num'];?>
						<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
							<?php  echo $goods['wxapp_num'];?>
						<?php  } ?>日
					<?php  } else if($goods['unit'] == 'year') { ?>
						<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
							<?php  echo $goods['account_num'];?>
						<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
							<?php  echo $goods['wxapp_num'];?>
						<?php  } ?>年
					<?php  } else if($goods['unit'] == 'week') { ?>
						<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
							<?php  echo $goods['account_num'];?>
						<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
							<?php  echo $goods['wxapp_num'];?>
						<?php  } ?>周
					<?php  } ?>
					<?php  } ?>
				</div>
				<?php  if($all_type_info[$goods['type']]['group'] == 'module' || in_array($goods['type'], array(STORE_TYPE_PACKAGE, STORE_TYPE_ACCOUNT_RENEW, STORE_TYPE_WXAPP_RENEW, STORE_TYPE_USER_PACKAGE))) { ?>
				<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModalBuy">立即购买</button>
				<?php  } else if($goods['type'] == STORE_TYPE_API) { ?>
				<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#BuyApi">立即购买</button>
				<?php  } else { ?>
				<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#Buyaccount">立即购买</button>
				<?php  } ?>
			</div>
		</div>
		<?php  if($goods_type_info['group'] == 'module') { ?>
		<div class="wish-goods-more">
			<?php  if($goods['is_wish']) { ?>
			<div class="alert we7-page-alert"><i class="wi wi-info"></i> 预购应用，成功付款后，需要等待管理员正式安装后才能使用。</div>
			<?php  } ?>
			<div class="header">应用介绍</div>
			<div class="content">
				<?php  echo $goods['description'];?>
				<a href="javascript:;" class="more">展开更多</a>
			</div>
		</div>
		<div class="wish-goods-pic">
			<div class="header">应用预览图</div>
			<div class="pic-list">
				<?php  if(is_array($goods['slide'])) { foreach($goods['slide'] as $picture) { ?>
					<div class="pic-item">
						<img src="<?php  echo tomedia($picture)?>" alt="">
					</div>
				<?php  } } ?>
			</div>
		</div>
		<?php  } else if(in_array($goods['type'], array(STORE_TYPE_ACCOUNT, STORE_TYPE_WXAPP, STORE_TYPE_API))) { ?>
		<div class="wish-goods-more">
			<!-- 预购应用提示判断 -->
			<div class="header">介绍</div>
			<div class="content">
				<?php  if($goods['type'] == STORE_TYPE_API) { ?>
				购买API浏览次数，购买之后使用时间不限
				<?php  } else { ?>
				1.购买商品后您将多创建<?php  echo $goods['num'];?>个<?php  echo $goods['title'];?>, 不受已有用户组限制。<br/>
				2.购买的<?php  echo $goods['title'];?>是有时效的，到期需要继续购买方可使用。
				<?php  } ?>
			</div>
		</div>
		<?php  } else if(in_array($goods['type'], array(STORE_TYPE_ACCOUNT_RENEW, STORE_TYPE_WXAPP_RENEW))) { ?>
		<div class="wish-goods-more">
			<!-- 预购应用提示判断 -->
			<div class="header">介绍</div>
			<div class="content">
				<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
				续费公众号
				<?php  } else { ?>
				续费小程序
				<?php  } ?>
			</div>
		</div>
		<?php  } else if(in_array($goods['type'], array(STORE_TYPE_USER_PACKAGE, STORE_TYPE_ACCOUNT_PACKAGE))) { ?>
		<div class="wish-goods-more">
			<!-- 预购应用提示判断 -->
			<div class="header">介绍</div>
			<div class="content show">
				<div class="bg-gray">
					<div class="txt">
						<?php  if($goods['type'] == STORE_TYPE_USER_PACKAGE) { ?>
						<p>购买商品后您将拥有相应的公众号应用，小程序应用，模板的使用权限，不受已有用户组的限制。 </p>
						<?php  } else { ?>
						<p>购买商品后您将拥有创建相应类型账号的权限，不受已有用户组的限制。</p>
						<?php  } ?>
					</div>
					<div class="creat">
						<h1>可创建数量</h1>
						<ul class="clearfloat">
							<li>
								<div class="type"><div>公众号</div><i class="wi wi-account"></i></div>
								<div class="num">
									<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_PACKAGE) { ?>
									<?php  echo $goods['account_num'];?>
									<?php  } else { ?>
									<?php  echo $group_info['maxaccount'];?>
									<?php  } ?>
								</div>
							</li>
							<li>
								<div class="type"><div>小程序</div><span><i class="wi wi-wxapp"></i></span></div>
								<div class="num">
									<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_PACKAGE) { ?>
									<?php  echo $goods['wxapp_num'];?>
									<?php  } else { ?>
									<?php  echo $group_info['maxwxapp'];?>
									<?php  } ?>
								</div>
							</li>
							<li>
								<div class="type"><div>PC应用</div><i class="wi wi-pc"></i></div>
								<div class="num">
									<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_PACKAGE) { ?>
									<?php  echo $goods['webapp_num'];?>
									<?php  } else { ?>
									<?php  echo $group_info['maxwebapp'];?>
									<?php  } ?>
								</div>
							</li>
							<li>
								<div class="type"><div>APP</div><i class="wi wi-app"></i></div>
								<div class="num">
									<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_PACKAGE) { ?>
									<?php  echo $goods['phoneapp_num'];?>
									<?php  } else { ?>
									<?php  echo $group_info['maxphoneapp'];?>
									<?php  } ?>
								</div>
							</li>
							<li>
								<div class="type"><div>熊掌号</div><i class="wi wi-xzapp"></i></div>
								<div class="num">
									<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_PACKAGE) { ?>
									<?php  echo $goods['xzapp_num'];?>
									<?php  } else { ?>
									<?php  echo $group_info['maxxzapp'];?>
									<?php  } ?>
								</div>
							</li>
							<li>
								<div class="type"><div>支付宝小程序</div><i class="wi wi-aliapp"></i></div>
								<div class="num">
									<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_PACKAGE) { ?>
									<?php  echo $goods['aliapp_num'];?>
									<?php  } else { ?>
									<?php  echo $group_info['maxaliapp'];?>
									<?php  } ?>
								</div>
							</li>
							<li>
								<div class="type"><div>百度小程序</div><i class="wi wi-baiduapp"></i></div>
								<div class="num">
									<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_PACKAGE) { ?>
									<?php  echo $goods['baiduapp_num'];?>
									<?php  } else { ?>
									<?php  echo $group_info['maxbaiduapp'];?>
									<?php  } ?>
								</div>
							</li>
							<li>
								<div class="type"><div>头条小程序</div><i class="wi wi-toutiaoapp"></i></div>
								<div class="num">
									<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_PACKAGE) { ?>
									<?php  echo $goods['toutiaoapp_num'];?>
									<?php  } else { ?>
									<?php  echo $group_info['maxtoutiaoapp'];?>
									<?php  } ?>
								</div>
							</li>
						</ul>
					</div>
					<?php  if($goods['type'] == STORE_TYPE_USER_PACKAGE) { ?>
					<div class="about" >
						<h1>包含应用权限组</h1>
						<div class="box" >
							<div class="nav" ng-repeat="pack in packages">
								<div class="menu">
									<span class="pull-right color-default">
										<a href="javascript:;" class="open color-default"  data-toggle="collapse" data-target="#demo-{{pack.id}}">展开</a>
									</span>
									{{ pack.name}}
								</div>
								<ul class="submenu collapse" style="" id="demo-{{pack.id}}">
									<li ng-if="pack.account">
										<span class="tit">公众号应用</span>
										<ul>
											<li ng-repeat="account in pack.account">
												<img class="head" ng-src="{{account.logo}}" alt=""><span class="name" ng-bind="account.title"></span>
											</li>
										</ul>
									</li>
									<li ng-if="pack.wxapp">
										<span class="tit">小程序应用</span>
										<ul>
											<li ng-repeat="wxapp in pack.wxapp">
												<img class="head" ng-src="{{wxapp.logo}}"><span class="name" ng-bind="wxapp.title"></span>
											</li>
										</ul>
									</li>
									<li ng-if="pack.webapp">
										<span class="tit">PC应用</span>
										<ul >
											<li ng-repeat="webapp in pack.webapp">
												<img class="head" ng-src="{{webapp.logo}}"><span class="name" ng-bind="webapp.title"> </span>
											</li>
										</ul>
									</li>
									<li ng-if="pack.phoneapp">
										<span class="tit">APP应用</span>
										<ul >
											<li ng-repeat="phoneapp in pack.phoneapp">
												<img class="head" ng-src="{{phoneapp.logo}}"><span class="name" ng-bind="phoneapp.title"> </span>
											</li>
										</ul>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<?php  } ?>
				</div>
			</div>
		</div>
		<?php  } else if($goods['type'] == STORE_TYPE_USER_RENEW) { ?>
		<div class="wish-goods-more">
			<!-- 预购应用提示判断 -->
			<div class="header">介绍</div>
			<div class="content show">
				<div class="color-gray">购买商品后您的到期时间将会在原有基础上增加套餐所包含的时间。</div>
			</div>
		</div>
		<?php  } else { ?>
		<div class="wish-goods-more">
			<!-- 预购应用提示判断 -->
			<div class="header">介绍</div>
			<div class="content show">
				<div class="color-gray">购买商品后您将拥有相应的公众号应用，小程序应用，模板的使用权限，不受已有用户组的限制。</div>
				<?php  if($module_groups[$goods['module_group']]['account']) { ?>
				<div class="we7-margin-top  title">公众号应用</div>
				<div class="item-list clearfix">
					<?php  if(is_array($module_groups[$goods['module_group']]['account'])) { foreach($module_groups[$goods['module_group']]['account'] as $module) { ?>
					<div class="item">
						<img src="<?php  echo $module['logo'];?>" alt="" class="icon"/>
						<div class="text-over"><?php  echo $module['title'];?></div>
					</div>
					<?php  } } ?>
				</div>
				<?php  } ?>
				<?php  if($module_groups[$goods['module_group']]['wxapp']) { ?>
				<div class="we7-margin-top title">小程序应用</div>
				<div class="item-list clearfix">
					<?php  if(is_array($module_groups[$goods['module_group']]['wxapp'])) { foreach($module_groups[$goods['module_group']]['wxapp'] as $wxapp_module) { ?>
					<div class="item">
						<img src="<?php  echo $wxapp_module['logo'];?>" alt="" class="icon"/>
						<div class="text-over"><?php  echo $wxapp_module['title'];?></div>
					</div>
					<?php  } } ?>
				</div>
				<?php  } ?>
				<?php  if($module_groups[$goods['module_group']]['templates']) { ?>
				<div class="we7-margin-top title">模板</div>
				<div class="item-list clearfix">
					<?php  if(is_array($module_groups[$goods['module_group']]['templates'])) { foreach($module_groups[$goods['module_group']]['templates'] as $template) { ?>
					<div class="item">
						<div class="text-over"><?php  echo $template['title'];?></div>
					</div>
					<?php  } } ?>
				</div>
				<?php  } ?>
			</div>
		</div>
		<?php  } ?>
	</div>
	<div class="modal fade" id="myModalBuy">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<?php  if($goods['type'] == STORE_TYPE_PACKAGE) { ?>
						购买应用套餐
						<?php  } else if($goods['type'] == STORE_TYPE_USER_PACKAGE) { ?>
						购买用户组权限套餐
						<?php  } else if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
						续费公众号
						<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
						续费小程序
						<?php  } else { ?>
						购买<span><?php  echo $goods['title'];?></span>应用
						<?php  } ?>
					</h4>
				</div>
				<div class="modal-body">
					<form action="" method="get" class="we7-form">
						<div class="form-group">
							<label class="control-label col-sm-2">应用单价</label>
							<div class="col-sm-8 form-control-static">￥<?php  echo $goods['price'];?><?php  if($goods['type'] != STORE_TYPE_API) { ?>/
								<?php  if($goods['type'] == STORE_TYPE_USER_RENEW) { ?>
								<?php  echo $goods['account_num'];?>
								<?php  } ?>
								<?php  if($goods['unit'] == 'month') { ?>
									<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
										<?php  echo $goods['account_num'];?>
									<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
										<?php  echo $goods['wxapp_num'];?>
									<?php  } ?>月
								<?php  } else if($goods['unit'] == 'day') { ?>
									<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
										<?php  echo $goods['account_num'];?>
									<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
										<?php  echo $goods['wxapp_num'];?>
									<?php  } ?>日
								<?php  } else if($goods['unit'] == 'year') { ?>
									<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
										<?php  echo $goods['account_num'];?>
									<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
										<?php  echo $goods['wxapp_num'];?>
									<?php  } ?>年
								<?php  } else if($goods['unit'] == 'week') { ?>
									<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
										<?php  echo $goods['account_num'];?>
									<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
										<?php  echo $goods['wxapp_num'];?>
									<?php  } ?>周
								<?php  } ?>
								<?php  } ?></div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">购买时长</label>
							<div class="col-sm-8">
								<div class="clearfix we7-margin-bottom-sm" style="line-height: 34px;">
									<input type="number" class="form-control pull-left" style="width: 80px;" value="1" ng-model="duration"/>&nbsp;
									<?php  if($goods['type'] != STORE_TYPE_API) { ?>
										<?php  if($goods['unit'] == 'month') { ?>
											<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
												*<?php  echo $goods['account_num'];?>
											<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
												*<?php  echo $goods['wxapp_num'];?>
											<?php  } ?>月
										<?php  } else if($goods['unit'] == 'day') { ?>
											<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
												*<?php  echo $goods['account_num'];?>
											<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
												*<?php  echo $goods['wxapp_num'];?>
											<?php  } ?>日
										<?php  } else if($goods['unit'] == 'year') { ?>
											<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
												*<?php  echo $goods['account_num'];?>
											<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
												*<?php  echo $goods['wxapp_num'];?>
											<?php  } ?>年
										<?php  } else if($goods['unit'] == 'week') { ?>
											<?php  if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
												*<?php  echo $goods['account_num'];?>
											<?php  } else if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
												*<?php  echo $goods['wxapp_num'];?>
											<?php  } ?>周
										<?php  } ?>
									<?php  } ?>
									<?php  if($goods['type'] == STORE_TYPE_MODULE || $goods['type'] == STORE_TYPE_WXAPP_MODULE) { ?>
									,有效期至{{ expiretime }}
									<?php  } ?>
								</div>
								<div class="select-btn">
									<button type="button" class="btn" ng-class="duration == 1? 'btn-primary' : 'btn-default'" ng-click="changeDuration(1)">1</button>
									<button type="button" class="btn" ng-class="duration == 3? 'btn-primary' : 'btn-default'" ng-click="changeDuration(3)">3</button>
									<button type="button" class="btn" ng-class="duration == 6? 'btn-primary' : 'btn-default'" ng-click="changeDuration(6)">6</button>
									<button type="button" class="btn" ng-class="duration == 12? 'btn-primary' : 'btn-default'" ng-click="changeDuration(12)">12</button>
								</div>
							</div>
						</div>
						<?php  if($all_type_info[$goods['type']]['group'] == 'module') { ?>
						<div class="form-group">
							<label class="control-label col-sm-2"><?php  echo $all_type_info[$goods['type']]['title'];?></label>
							<div class="col-sm-10">
								<div class="clearfix" style="line-height: 34px;">
									<select class="" ng-model="uniacid">
										<option value="{{ uniacid }}" ng-repeat="(uniacid, account) in account_list track by uniacid">{{ account.name }}</option>
									</select>
								</div>
								<div class="help-block">
									<span class="text-error">注意!</span>请确认服务所需绑定的<?php  echo $all_type_info[$goods['type']]['title'];?>,购买后不可更换.
								</div>
							</div>
						</div>
						<?php  } ?>

						<?php  if(in_array($goods['type'], array(STORE_TYPE_WXAPP_RENEW, STORE_TYPE_ACCOUNT_RENEW)) || $goods['type'] == STORE_TYPE_PACKAGE && !empty($user_account)) { ?>
						<div class="form-group">
							<label class="control-label col-sm-2">
								<?php  if($goods['type'] == STORE_TYPE_WXAPP_RENEW) { ?>
									小程序
								<?php  } else if($goods['type'] == STORE_TYPE_ACCOUNT_RENEW) { ?>
									公众号
								<?php  } ?>
							</label>
							<div class="col-sm-10">
								<div class="clearfix" style="line-height: 34px;">
									<!-- <select class="we7-select" style="width:150px;" ng-model="<?php  if($goods['type'] == STORE_TYPE_WXAPP_MODULE) { ?>wxapp<?php  } else { ?>uniacid<?php  } ?>"> -->
									<select class="" style="width:150px;" ng-model="uniacid">
										<option value="{{ uniacid }}" ng-repeat="(uniacid, account) in account_list track by uniacid">{{ account.name }}</option>
									</select>
									<span class="text-error">注意!</span>请确认服务所需绑定的帐号,购买后不可更换.
								</div>
							</div>
						</div>
						<?php  } ?>
						<?php  if($goods['type'] == STORE_TYPE_PACKAGE && !empty($wxapp_account_list)) { ?>
						<div class="form-group">
							<label class="control-label col-sm-2">小程序</label>
							<div class="col-sm-10">
								<div class="clearfix" style="line-height: 34px;">
									<select class="" style="width:150px;" ng-model="wxapp">
										<option value="{{ account.uniacid }}" ng-repeat="account in wxapp_account_list">{{ account.name }}</option>
									</select>
									<span class="text-error">注意!</span>请确认服务所需绑定的小程序,购买后不可更换.
								</div>
							</div>
						</div>
						<?php  } ?>
						<div class="form-group">
							<label class="control-label col-sm-2">费用明细</label>
							<div class="col-sm-8 form-control-static">实付总计<span class="we7-margin-left">￥{{ price }}</span></div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">支付方式</label>
							<div class="select-btn">
								<button type="button" ng-repeat="(way, pay_way_info) in pay_way_list track by way" ng-class="pay_way == way? 'btn btn-primary' : 'btn btn-default'" ng-click="changePayWay(way)">{{ pay_way_info.title }}</button>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" ng-click="submit_order('order')">提交订单</button>
					<button type="button" class="btn btn-primary hidden" ng-click="submit_order('pay')">立即支付</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="Buyaccount">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">购买<span><?php  echo $goods['title'];?></span></h4>
				</div>
				<div class="modal-body">
					<form action="" method="get" class="we7-form">
						<div class="form-group">
							<label class="control-label col-sm-2">应用单价</label>
							<div class="col-sm-8 form-control-static">￥<?php  echo $goods['price'];?>
								<?php  if($goods['type'] != STORE_TYPE_API && $goods['type'] != STORE_TYPE_ACCOUNT_PACKAGE && $goods['type'] != STORE_TYPE_ACCOUNT && $goods['type'] != STORE_TYPE_WXAPP) { ?>/
								<?php  if($goods['type'] == STORE_TYPE_USER_RENEW) { ?>
								<?php  echo $goods['account_num'];?>
								<?php  } ?>
								<?php  if($goods['unit'] == 'month') { ?>
								月
								<?php  } else if($goods['unit'] == 'day') { ?>
								日
								<?php  } else if($goods['unit'] == 'year') { ?>
								年
								<?php  } else if($goods['unit'] == 'week') { ?>
								周
								<?php  } ?>
								<?php  } ?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">费用明细</label>
							<div class="col-sm-8 form-control-static">实付总计<span class="we7-margin-left">￥{{ price }}</span></div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">支付方式</label>
							<div class="select-btn">
								<button type="button" ng-repeat="(way, pay_way_info) in pay_way_list track by way" ng-class="pay_way == way? 'btn btn-primary' : 'btn btn-default'" ng-click="changePayWay(way)">{{ pay_way_info.title }}</button>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" ng-click="submit_order('order')">提交订单</button>
					<button type="button" class="btn btn-primary" ng-click="submit_order('pay')">立即支付</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="BuyApi">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">购买API浏览次数</h4>
				</div>
				<div class="modal-body">
					<form action="" method="get" class="we7-form">
						<div class="form-group">
							<label class="control-label col-sm-2">商品单价</label>
							<div class="col-sm-8 form-control-static color-red">￥<?php  echo $goods['price'];?> / <?php  echo $goods['api_num'];?><?php  if($goods['unit'] == 'ten_thousand') { ?>万次<?php  } ?></div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">购买份数</label>
							<div class="col-sm-8">
								<div class="clearfix we7-margin-bottom-sm" style="line-height: 34px;">
									<input type="number" class="form-control pull-left" style="width: 80px;" value="1" ng-model="duration"/>份，共购买<span class="color-red" ng-bind="goods.api_num * duration"></span>万次浏览量
								</div>
								<div class="select-btn">
									<button type="button" class="btn" ng-class="duration == 10? 'btn-primary' : 'btn-default'" ng-click="changeDuration(10)">10</button>
									<button type="button" class="btn" ng-class="duration == 20? 'btn-primary' : 'btn-default'" ng-click="changeDuration(20)">20</button>
									<button type="button" class="btn" ng-class="duration == 30? 'btn-primary' : 'btn-default'" ng-click="changeDuration(30)">30</button>
									<button type="button" class="btn" ng-class="duration == 50? 'btn-primary' : 'btn-default'" ng-click="changeDuration(50)">50</button>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">公众号</label>
							<div class="col-sm-10">
								<div class="clearfix" style="line-height: 34px;">
									<select class="" style="width:150px;" ng-model="uniacid">
										<option value="{{ uniacid }}" ng-repeat="(uniacid, account) in account_list track by uniacid">{{ account.name }}</option>
									</select>
									<span class="text-error">注意!</span>请确认服务所需绑定的公众号,购买后不可更换.
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">费用明细</label>
							<div class="col-sm-8 form-control-static">实付总计 ￥<span class="color-red" ng-bind="goods.price * duration"></span></div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">支付方式</label>
							<div class="select-btn">
								<button type="button" ng-repeat="(way, pay_way_info) in pay_way_list track by way" class="btn" ng-class="pay_way == way? 'btn-primary' : 'btn-default'" ng-click="changePayWay(way)">{{ pay_way_info.title }}</button>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" ng-click="submit_order('order')">提交订单</button>
					<button type="button" class="btn btn-primary" ng-click="submit_order('pay')">立即支付</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modal-img">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">查看图片</h4>
				</div>
				<div class="modal-body text-center">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
				</div>
			</div>
		</div>
	</div>
	<script>
	$(function() {
		if($('.wish-goods-more .content').outerHeight() < 400) {
			$('.wish-goods-more .content .more').hide()
		}
		$('.wish-goods-more .content .more').on('click', function() {
			$('.wish-goods-more .content').toggleClass('show');
			if($('.wish-goods-more .content').hasClass('show')) {
				$('.wish-goods-more .content .more').text('收起');
			} else {
				$('.wish-goods-more .content .more').text('展开更多');
			}
		})
		$('.wish-goods-pic .pic-item').on('click', function() {
			$('#modal-img .modal-body').html('<img src=' + $(this).children('img').attr('src') + '>');
			$('#modal-img').modal('show');
		})
	})
	</script>
</div>
<script>
	angular.module('storeApp').value('config', {
		'singlePrice' : <?php  echo $goods['price'];?>,
		'wxapp' : '<?php  echo $default_wxapp;?>',
		'unit' : '<?php  echo $goods['unit'];?>',
		account_list : <?php  echo json_encode($user_account)?>,
		wxapp_account_list : <?php  echo json_encode($wxapp_account_list)?>,
		pay_way : <?php  echo json_encode($pay_way)?>,
		expiretime : "<?php  echo date('Y-m-d', strtotime('+1 ' . $goods['unit'], time()))?>",
		first_uniacid : "<?php  echo $default_uniacid;?>",
		goods : <?php  echo json_encode($goods)?>,
		packages : <?php echo !empty($group_info['package_info']) ? json_encode($group_info['package_info']) : 'null'?>,
	});
	angular.bootstrap($('.js-goods-buyer'), ['storeApp']);
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>