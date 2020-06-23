<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>
<?php  if($_W['template'] == 'black') { ?>
<div class="skin-black" data-skin="black">
	<div class="head">
		<nav class="navbar navbar-default" role="navigation">
			<div class="container ">
                <?php  if(!$_W['iscontroller']) { ?>
				<div class="header-info-common pull-left">
					<a class="header-info-common__logo" href="<?php  echo $_W['siteroot'] . 'web/home.php'?>">
						<img src="<?php  if(!empty($_W['setting']['copyright']['blogo'])) { ?><?php  echo to_global_media($_W['setting']['copyright']['blogo'])?><?php  } else { ?>./resource/images/logo/logo.png<?php  } ?>" class="logo" style="max-height: 40px;max-width: 100px;">
					</a>
					<?php  if($_W['breadcrumb']) { ?>
					<div class="header-info-common__breadcrumb">
						<a href="<?php  echo $_W['siteroot'];?>web/home.php" class="home">
							<i class="wi wi-home"></i>
						</a>
						<span class="separator"> <i class="wi wi-angle-right"></i> </span>
						<div class="item"><?php  echo $_W['breadcrumb'];?></div>
					</div>
					<?php  } ?>
				</div>
                <?php  } ?>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav navbar-left">
						<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header-topnav', TEMPLATE_INCLUDEPATH)) : (include template('common/header-topnav', TEMPLATE_INCLUDEPATH));?>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header-user', TEMPLATE_INCLUDEPATH)) : (include template('common/header-user', TEMPLATE_INCLUDEPATH));?>
					</ul>
				</div>
			</div>
		</nav>
	</div>

<div class="main">
	<div class="container">
		<div class="panel panel-content main-panel-content ">
			<div class="panel-body clearfix main-panel-body ">
				<div class="left-menu">
					<div class="left-menu-content">
						<div class="left-menu-top skin-black">
							<div class="account-info-name">
								<span class="account-name"><i class="wi wi-store"></i><a href="./index.php?c=home&a=welcome&do=account&">商城</a></span>
							</div>
						</div>
						<?php  if(is_array($this->left_menus)) { foreach($this->left_menus as $key => $menus) { ?>
						<?php  if(in_array($key, array('store_manage', 'store_payments', 'store_cash_manage')) && !$_W['isadmin']) { ?>
						<?php  continue;?>
						<?php  } ?>
						<div class="panel panel-menu">
							<div class="panel-heading">
								<span class="" data-toggle="collapse" data-target="#frame-<?php  echo $key;?>" onclick="util.cookie.set('menu_fold_tag:<?php  echo $key;?>', util.cookie.get('menu_fold_tag:<?php  echo $key;?>') == 1 ? 0 : 1)"><?php  echo $menus['title'];?><i class="wi wi-down-sign-s pull-right"></i></span>
							</div>
							<ul class="list-group collapse <?php  if($_GPC['menu_fold_tag:'.$key] == 0) { ?>in<?php  } ?>" id="frame-<?php  echo $key;?>">
								<?php  if(is_array($menus['menu'])) { foreach($menus['menu'] as $menu_key => $menu) { ?>
								<?php  if($key == 'store_goods' && !empty($_W['setting']['store'][$menu_key])) { ?>
									<?php  continue;?>
								<?php  } ?>
								<?php  if($menu_key == 'store_goods_users_package' && user_is_vice_founder()) { ?>
									<?php  continue;?>
								<?php  } ?>
								<?php  if($menu_key == 'store_check_cash' && !$this->store_setting['cash_status']) { ?>
									<?php  continue;?>
								<?php  } ?>
								<li class="list-group-item <?php  if(($_GPC['type'] == $menu['type'] && $_GPC['do'] == 'goodsbuyer') || ($_GPC['do'] == $menu['type'] && $_GPC['do'] != 'goodsbuyer') || ($_GPC['do'] == 'cash' && $_GPC['operate'] == $menu['type'])) { ?>active<?php  } ?>">
									<a href="<?php  echo $menu['url'];?>" class="text-over" >
										<i class="<?php  echo $menu['icon'];?>"></i> <?php  echo $menu['title'];?></a>
								</li>
								<?php  } } ?>
							</ul>
						</div>
						<?php  } } ?>
					</div>
				</div>
				<div class="right-content">
<?php  } else if($_W['template'] == 'classical') { ?>
<style>
	.tooltip.right .tooltip-arrow{border-right-color: #428bca;}
	.tooltip.bottom .tooltip-arrow{border-bottom-color: #428bca;}
	.tooltip-inner{background-color: #428bca; padding: 6px 12px;}
</style>
<div class="skin-classical" data-skin="classical">
<div class="first-sidebar">
		<div class="<?php  if(!empty($frames['section']['platform_module_menu']['plugin_menu'])) { ?>plugin-head<?php  } ?>">
			<a class="logo-wrap" href="<?php  echo $_W['siteroot'];?>">
				<img src="<?php  if(!empty($_W['setting']['copyright']['blogo'])) { ?><?php  echo to_global_media($_W['setting']['copyright']['blogo'])?><?php  } else { ?>./resource/images/logo/logo.png<?php  } ?>" class="logo" style="max-height: 40px;max-width: 100px;">
			</a>
			<?php  if(!empty($_W['uid'])) { ?>
			<div class="nav">
				<ul class="main-nav">
					<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header-topnav', TEMPLATE_INCLUDEPATH)) : (include template('common/header-topnav', TEMPLATE_INCLUDEPATH));?>
				</ul>
				<ul class="user-info">
                    <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header-user', TEMPLATE_INCLUDEPATH)) : (include template('common/header-user', TEMPLATE_INCLUDEPATH));?>
				</ul>
			</div>
			<?php  } else { ?>
			<ul class="user-info">
				<li class="dropdown"><a href="<?php  echo url('user/register');?>">注册</a></li>
				<li class="dropdown"><a href="<?php  echo url('user/login');?>">登录</a></li>
			</ul>
			<?php  } ?>
		</div>
</div>
<script>
	$(function(){
		var $dropdownLi = $('.msg.dropdown');
		$dropdownLi.mouseover(function() {
			$(this).addClass('open');
		}).mouseout(function() {
			$(this).removeClass('open');
		});
	});
</script>


<div class="main main-classical">
	<div class="right-fixed-top"></div>
	<div class="container">
		<div class="panel panel-content main-panel-content <?php  if(!empty($frames['section']['platform_module_menu']['plugin_menu'])) { ?>panel-content-plugin<?php  } ?>">
		<div class="panel-body clearfix main-panel-body <?php  if($_GPC['menu_fold_tag:classical'] == 1) { ?>folded<?php  } ?>">
			<div class="icon-unfold js-folded" onclick="util.cookie.set('menu_fold_tag:classical', util.cookie.get('menu_fold_tag:classical') == 1 ? 0 : 1)">
				<span class="wi wi-folded"></span>
			</div>
			<div class="left-menu-container">
				<div class="left-menu">
					<?php  if(is_array($this->left_menus)) { foreach($this->left_menus as $key => $menus) { ?>
					<?php  if(in_array($key, array('store_manage', 'store_payments', 'store_cash_manage')) && !$_W['isadmin']) { ?>
					<?php  continue;?>
					<?php  } ?>
					<div class="panel panel-menu">
						<div class="panel-heading">
							<span class="no-collapse"<?php  if($_GPC['menu_fold_tag:'.$key] == 1) { ?>collapsed<?php  } ?>" data-toggle="collapse" data-target="#frame-<?php  echo $key;?>" onclick="util.cookie.set('menu_fold_tag:<?php  echo $key;?>', util.cookie.get('menu_fold_tag:<?php  echo $key;?>') == 1 ? 0 : 1)"><?php  echo $menus['title'];?><i class="wi wi-appsetting pull-right setting"></i></span>
						</div>
						<ul class="list-group collapse <?php  if($_GPC['menu_fold_tag:'.$key] == 0) { ?>in<?php  } ?>" id="frame-<?php  echo $key;?>"">
							<?php  if(is_array($menus['menu'])) { foreach($menus['menu'] as $menu_key => $menu) { ?>
							<?php  if($key == 'store_goods' && !empty($_W['setting']['store'][$menu_key])) { ?>
								<?php  continue;?>
							<?php  } ?>
							<?php  if($menu_key == 'store_goods_users_package' && user_is_vice_founder()) { ?>
								<?php  continue;?>
							<?php  } ?>
							<?php  if($menu_key == 'store_check_cash' && !$this->store_setting['cash_status']) { ?>
								<?php  continue;?>
							<?php  } ?>
							<li class="list-group-item <?php  if(($_GPC['type'] == $menu['type'] && $_GPC['do'] == 'goodsbuyer') || ($_GPC['do'] == $menu['type'] && $_GPC['do'] != 'goodsbuyer') || ($_GPC['do'] == 'cash' && $_GPC['operate'] == $menu['type'])) { ?>active<?php  } ?>">
								<a href="<?php  echo $menu['url'];?>" class="text-over" >
									<span class="nav-icon" data-container="body" data-toggle="tooltip" data-placement="right" title="<?php  echo $menu['title'];?>"><i class="<?php  echo $menu['icon'];?>"></i></span> <span class="nav-title"><?php  echo $menu['title'];?></span>
								</a>
							</li>
							<?php  } } ?>
						</ul>
					</div>
					<?php  } } ?>
				</div>
			</div>
			<script>
				$(function(){
					$('.left-menu-container').slimScroll({
						width: '210px',
						height: 'calc(100vh - 51px)',
						opacity: .4,
						color: '#aaa',
					});
					$('.main-panel-content .icon-unfold').click(function(){
						$('.main-panel-content').find('.main-panel-body').toggleClass('folded');
					});
					$('.nav-icon').tooltip('hide');
				});
			</script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
			<div class="right-content">
				<div class="content">
<?php  } else if($_W['template'] == '2.0') { ?>
<div class="skin-2 <?php  if(!$_W['iscontroller']) { ?>skin-2--full<?php  } ?>" data-skin="2">
    <div class="skin-2__left <?php  if($frames['dimension'] == 3) { ?>skin-2__left--small<?php  } ?>">
        <!-- logo -->
        <a class="skin-2__logo" href="<?php  if($_W['role'] == ACCOUNT_MANAGE_NAME_CLERK) { ?><?php  echo url('module/display');?><?php  } else { ?><?php  echo url('account/display');?><?php  } ?>">
            <img src="<?php  if(!empty($_W['setting']['copyright']['blogo'])) { ?><?php  echo to_global_media($_W['setting']['copyright']['blogo'])?><?php  } else { ?>./resource/images/logo/logo.png<?php  } ?>" class="logo" width="100%" style="max-height: 24px;">
        </a>
        <!-- end logo-->
        <!-- 一级菜单 -->
		<ul class="main-nav">
			<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header-topnav', TEMPLATE_INCLUDEPATH)) : (include template('common/header-topnav', TEMPLATE_INCLUDEPATH));?>
        </ul>
        <!-- end一级菜单 -->
    </div>
    <div class="skin-2__right">
        <div class="skin-2__header">
			<?php  if(!$_W['iscontroller']) { ?>
			<div class="header-info-common pull-left">
				<a class="header-info-common__logo" href="<?php  echo $_W['siteroot'] . 'web/home.php'?>">
					<img src="<?php  if(!empty($_W['setting']['copyright']['blogo'])) { ?><?php  echo to_global_media($_W['setting']['copyright']['blogo'])?><?php  } else { ?>./resource/images/logo/logo.png<?php  } ?>" class="logo" style="max-height: 40px;max-width: 100px;">
				</a>
				<?php  if($_W['breadcrumb']) { ?>
				<div class="header-info-common__breadcrumb">
					<a href="<?php  echo $_W['siteroot'];?>web/home.php" class="home">
						<i class="wi wi-home"></i>
					</a>
					<span class="separator"> <i class="wi wi-angle-right"></i> </span>
					<div class="item"><?php  echo $_W['breadcrumb'];?></div>
				</div>
				<?php  } ?>
			</div>
			<?php  } ?>
			<div class="shortcut pull-left">
				<div class="shortcut-header" data-toggle="dropdown"><i class="wi wi-menu-setting"></i>菜单</div>
				<ul class="shortcut-list">
					<li class="shortcut-item">
						<?php  if(is_array($top_nav_shortcut)) { foreach($top_nav_shortcut as $nav) { ?>
						<div class="parent">
							<a href="<?php  if(empty($nav['url'])) { ?><?php  echo url('home/welcome/' . $nav['name']);?><?php  } else { ?><?php  echo $nav['url'];?><?php  } ?>" <?php  if(!empty($nav['blank'])) { ?>target="_blank"<?php  } ?>>
							<i class="<?php  echo $nav['icon'];?> icon"></i><?php  echo $nav['title'];?>
							</a>
						</div>
						<?php  } } ?>
					</li>
					<?php  $shortcut_menu = system_shortcut_menu()?>
					<?php  if(is_array($shortcut_menu)) { foreach($shortcut_menu as $menu) { ?>
					<?php  if(!empty($menu['section'])) { ?>
					<li class="shortcut-item">
						<div class="parent">
							<a href="<?php  echo $menu['url'];?>">
								<i class="<?php  echo $menu['icon'];?> icon"></i><?php  echo $menu['title'];?>
							</a>
						</div>
						<div class="child">
							<?php  if(is_array($menu['section'])) { foreach($menu['section'] as $section) { ?>
							<?php  if(!isset($section['is_display']) || !empty($section['is_display'])) { ?>
							<?php  if(is_array($section['menu'])) { foreach($section['menu'] as $item) { ?>
							<?php  if(!empty($item['is_display'])) { ?>
							<div class="item text-over">
								<a href="<?php  echo $item['url'];?>">
									<i class="<?php  echo $item['icon'];?> icon"></i><?php  echo $item['title'];?>
								</a>
							</div>
							<?php  } ?>
							<?php  } } ?>
							<?php  } ?>
							<?php  } } ?>
						</div>
					</li>
					<?php  } ?>
					<?php  } } ?>
				</ul>
				<div class=""></div>
			</div>
            <?php  if(!empty($_W['uid'])) { ?>
            <ul class="user-info">
                <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header-user', TEMPLATE_INCLUDEPATH)) : (include template('common/header-user', TEMPLATE_INCLUDEPATH));?>
            </ul>
            <?php  } else { ?>
            <ul class="user-info">
                <li class="dropdown"><a href="<?php  echo url('user/register');?>">注册</a></li>
                <li class="dropdown"><a href="<?php  echo url('user/login');?>">登录</a></li>
            </ul>
            <?php  } ?>
        </div>
        <div class="skin-2__content main">
            <?php  if(!defined('IN_MESSAGE')) { ?>
                <?php  if($frames['dimension'] == 3 && in_array(FRAME, array('account', 'system', 'advertisement', 'wxapp', 'site', 'webapp', 'phoneapp', 'xzapp')) && !in_array($_GPC['a'], array('news-show', 'notice-show'))) { ?>
                <div class="skin-2__sub">
                    <div class="sub-top">
                        <div class="sub-module-info">
                            <div class="module-info-name">
								<i class="wi wi-store color-default" style="font-size: 60px;margin-bottom: 10px;"></i>
                                <div class="name text-over">站内商城</div>
                            </div>
                        </div>
                    </div>

                    <!-- 二级菜单-->
                    <div class="js-menu" id="js-menu-<?php echo FRAME;?><?php  echo $_W['account']['uniacid'];?>">
                        <?php  if(is_array($this->left_menus)) { foreach($this->left_menus as $key => $menus) { ?>
                        <?php  if(in_array($key, array('store_manage', 'store_payments', 'store_cash_manage')) && !$_W['isadmin']) { ?>
                        <?php  continue;?>
                        <?php  } ?>
                        <?php  if($key == 'store_wish_goods' && $this->store_setting['wish_module_status'] == 0) { ?>
                        <?php  continue;?>
                        <?php  } ?>
                        <div class="panel panel-menu">
                            <div class="panel-heading">
                                <span class="no-collapse"<?php  if($_GPC['menu_fold_tag:'.$key] == 1) { ?>collapsed<?php  } ?>" data-toggle="collapse" data-target="#frame-<?php  echo $key;?>" onclick="util.cookie.set('menu_fold_tag:<?php  echo $key;?>', util.cookie.get('menu_fold_tag:<?php  echo $key;?>') == 1 ? 0 : 1)"><?php  echo $menus['title'];?></span>
                            </div>
                            <ul class="list-group collapse <?php  if($_GPC['menu_fold_tag:'.$key] == 0) { ?>in<?php  } ?>" id="frame-<?php  echo $key;?>"">
                                <?php  if(is_array($menus['menu'])) { foreach($menus['menu'] as $menu_key => $menu) { ?>
                                <?php  if($key == 'store_goods' && !empty($_W['setting']['store'][$menu_key])) { ?>
                                    <?php  continue;?>
                                <?php  } ?>
                                <?php  if($menu_key == 'store_goods_users_package' && user_is_vice_founder()) { ?>
                                    <?php  continue;?>
                                <?php  } ?>
                                <?php  if($menu_key == 'store_check_cash' && !$this->store_setting['cash_status']) { ?>
                                    <?php  continue;?>
                                <?php  } ?>
                                <li class="list-group-item <?php  if(($_GPC['type'] == $menu['type'] && $_GPC['do'] == 'goodsbuyer') || ($_GPC['do'] == $menu['type'] && $_GPC['do'] != 'goodsbuyer') || ($_GPC['do'] == 'cash' && $_GPC['operate'] == $menu['type'])) { ?>active<?php  } ?>">
                                    <a href="<?php  echo $menu['url'];?>" class="text-over" >
                                        <span class="nav-icon" data-container="body" data-toggle="tooltip" data-placement="right" title="<?php  echo $menu['title'];?>"><i class="<?php  echo $menu['icon'];?>"></i></span> <span class="nav-title"><?php  echo $menu['title'];?></span>
                                    </a>
                                </li>
                                <?php  } } ?>
                            </ul>
                        </div>
                        <?php  } } ?>
                    </div>
                    <!-- end二级菜单-->
                </div>
                <?php  } ?>
                <div class="skin-2__container container">
                        <div class="content">
            <?php  } ?>
<?php  } else { ?>

<div data-skin="default" class="skin-default <?php  if($_GPC['main-lg']) { ?> main-lg-body <?php  } ?>">
<div class="head">
<nav class="navbar navbar-default" role="navigation">
<div class="container ">
	<div class="navbar-header">
		<a class="navbar-brand" href="<?php  echo $_W['siteroot'];?>">
			<img src="<?php  if(!empty($_W['setting']['copyright']['blogo'])) { ?><?php  echo to_global_media($_W['setting']['copyright']['blogo'])?><?php  } else { ?>./resource/images/logo/logo.png<?php  } ?>" class="pull-left" wstyle="max-height: 40px;max-width: 100px;">
		</a>
	</div>
	<div class="collapse navbar-collapse">
		<style>
			.nav > li:hover .dropdown-menu {display: block;}
		</style>
		<?php  global $top_nav?>
		<?php  $nav_top_fold=array()?>
		<?php  $platform_url=url('account/display')?>
		<?php  $nav_top_fold[] = array('name' => 'all', 'title'=>'全部', 'type' => 'all', 'url' => $platform_url)?>
		<?php  if(is_array($top_nav)) { foreach($top_nav as $nav) { ?>
			<?php  if(in_array($nav['name'], array(ACCOUNT_TYPE_SIGN, WXAPP_TYPE_SIGN, WEBAPP_TYPE_SIGN, PHONEAPP_TYPE_SIGN, XZAPP_TYPE_SIGN, ALIAPP_TYPE_SIGN, BAIDUAPP_TYPE_SIGN, TOUTIAOAPP_TYPE_SIGN))) { ?>
				<?php  $nav_top_fold[]=$nav?>
			<?php  } else if(in_array($nav['name'], array('store', 'help', 'workorder', 'custom_help'))) { ?>
				<?php  $nav_top_tiled_other[] = $nav?>
			<?php  } else if($nav['name'] =='message') { ?>
				<?php  $nav_top_message = $nav?>
			<?php  } else { ?>
				<?php  $nav_top_tiled_system[] = $nav?>
			<?php  } ?>
			<?php  if('store' == $nav['name'] && $_W['isadmin']) { ?><?php  $nav_top_tiled_system[] = $nav?><?php  } ?>
		<?php  } } ?>
		<ul class="nav navbar-nav  navbar-left">
			<?php  if(is_array($nav_top_tiled_system)) { foreach($nav_top_tiled_system as $nav) { ?>
			<li <?php  if(FRAME == $nav['name'] && !defined('IN_MODULE')) { ?> class="active" <?php  } ?>>
			<a href="<?php  if(empty($nav['url'])) { ?><?php  echo url('home/welcome/' . $nav['name']);?><?php  } else { ?><?php  echo $nav['url'];?><?php  } ?>" <?php  if(!empty($nav['blank'])) { ?>target="_blank"<?php  } ?>><?php  echo $nav['title'];?></a>
			</li>
			<?php  } } ?>
		</ul>
		<ul class="nav navbar-nav navbar-left hidden">
			<?php  global $top_nav?>
			<?php  if(is_array($top_nav)) { foreach($top_nav as $nav) { ?>
			<li <?php  if(FRAME == $nav['name'] && !defined('IN_MODULE')) { ?> class="active"<?php  } ?>><a href="<?php  if(empty($nav['url'])) { ?><?php  echo url('home/welcome/' . $nav['name']);?><?php  } else { ?><?php  echo $nav['url'];?><?php  } ?>" <?php  if(!empty($nav['blank'])) { ?>target="_blank"<?php  } ?>><?php  echo $nav['title'];?></a></li>
			<?php  } } ?>
		</ul>
		<ul class="nav navbar-nav navbar-right">
            <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header-user', TEMPLATE_INCLUDEPATH)) : (include template('common/header-user', TEMPLATE_INCLUDEPATH));?>
		</ul>
	</div>
</div>
</nav>
</div>

<div class="main">
<div class="container">
<a href="javascript:;" class="js-big-main button-to-big color-gray" title="加宽">宽屏</a>
<div class="panel panel-content main-panel-content">
	<div class="content-head panel-heading main-panel-heading">
		<span class="font-lg"><i class="wi wi-store"></i> 商城</span></div>
	<div class="panel-body clearfix main-panel-body">
		<div class="left-menu">
			<div class="left-menu-content">
				<?php  if(is_array($this->left_menus)) { foreach($this->left_menus as $key => $menus) { ?>
				<?php  if(in_array($key, array('store_manage', 'store_payments', 'store_cash_manage')) && !$_W['isadmin']) { ?>
					<?php  continue;?>
				<?php  } ?>
				<div class="panel panel-menu">
					<div class="panel-heading">
						<span class="no-collapse"><?php  echo $menus['title'];?><i class="wi wi-appsetting pull-right setting"></i></span>
					</div>
					<ul class="list-group">
						<?php  if(is_array($menus['menu'])) { foreach($menus['menu'] as $menu_key => $menu) { ?>
						<?php  if($key == 'store_goods' && !empty($_W['setting']['store'][$menu_key])) { ?>
							<?php  continue;?>
						<?php  } ?>
						<?php  if($menu_key == 'store_goods_users_package' && user_is_vice_founder()) { ?>
							<?php  continue;?>
						<?php  } ?>
						<?php  if($menu_key == 'store_check_cash' && !$this->store_setting['cash_status']) { ?>
							<?php  continue;?>
						<?php  } ?>
						<li class="list-group-item <?php  if(($_GPC['type'] == $menu['type'] && $_GPC['do'] == 'goodsbuyer') || ($_GPC['do'] == $menu['type'] && $_GPC['do'] != 'goodsbuyer') || ($_GPC['do'] == 'cash' && $_GPC['operate'] == $menu['type'])) { ?>active<?php  } ?>">
							<a href="<?php  echo $menu['url'];?>" class="text-over" >
								<i class="<?php  echo $menu['icon'];?>"></i> <?php  echo $menu['title'];?></a>
						</li>
						<?php  } } ?>
					</ul>
				</div>
				<?php  } } ?>
			</div>
		</div>
		<div class="right-content" style="overflow: hidden;">
<?php  } ?>