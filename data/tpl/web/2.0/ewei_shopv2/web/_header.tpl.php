<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header_base', TEMPLATE_INCLUDEPATH)) : (include template('_header_base', TEMPLATE_INCLUDEPATH));?>
<style>
    .deg180{
        transform:rotate(180deg);
        -ms-transform:rotate(180deg); 	/* IE 9 */
        -moz-transform:rotate(180deg); 	/* Firefox */
        -webkit-transform:rotate(180deg); /* Safari 和 Chrome */
        -o-transform:rotate(180deg); 	/* Opera */
    }

    .nav.navbar-right {
        width:90px !important;
    }

</style>
<div class="navbar-collapse collapse" id="navbar">
    <?php  $routes = explode(".", $GLOBALS['_W']['routes']);?>
    <?php  if($routes['0'] != 'system' ) { ?>
    <ul class="nav navbar-nav gray-bg">

        <li <?php  if($_W['controller']=='shop') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl()?>">首页</a></li>
        <?php if(cv('goods')) { ?><li <?php  if($_W['controller']=='goods') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('goods')?>"> 商品</a></li><?php  } ?>
        <?php if(cv('member')) { ?><li <?php  if($_W['controller']=='member') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('member')?>"> 会员</a></li><?php  } ?>
        <?php if(cv('order')) { ?><li <?php  if($_W['controller']=='order') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('order')?>"> 订单</a></li><?php  } ?>

        <?php  if(p('newstore')) { ?>
        <?php if(cv('store')) { ?><li <?php  if($_W['controller']=='store') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('store/statistic')?>"> 门店</a></li><?php  } ?>
        <?php  } else { ?>
        <?php if(cv('store')) { ?><li <?php  if($_W['controller']=='store') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('store')?>"> 门店</a></li><?php  } ?>
        <?php  } ?>
        <?php  if(com('sale') || com('coupon')) { ?>
        <?php if(cv('sale')) { ?><li <?php  if($_W['controller']=='sale') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('sale')?>">营销</a></li><?php  } ?>
        <?php  } ?>
        <?php if(cv('finance')) { ?><li <?php  if($_W['controller']=='finance') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('finance')?>"> 财务</a></li><?php  } ?>

        <?php if(cv('statistics.sale')) { ?><li <?php  if($_W['controller']=='statistics') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('statistics')?>">数据</a></li><?php  } ?>
        <?php if(cv($this->isOpenPlugin())) { ?><li <?php  if($_W['controller']=='plugins' || !empty($_W['plugin'])) { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('plugins')?>">应用</a></li><?php  } ?>

        <?php  if(p('diypage') && !empty($_W['shopset']['diypage']['setmenu'])) { ?><li><a href="<?php  echo webUrl('diypage')?>"> 页面</a></li><?php  } ?>


        <?php if(cv('sysset')) { ?><li <?php  if($_W['controller']=='sysset') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('sysset')?>"> 设置</a></li><?php  } ?>

    </ul>
    <ul class="nav navbar-top-links navbar-right">
        <li class="dropdown">
            <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown" style="position:relative;" onclick="$(this).find('span').toggleClass('deg180')">
                <name style="width: 80px;white-space:nowrap;overflow:hidden;text-overflow: ellipsis;display: block;text-align: center"><?php  echo $_W['uniaccount']['name'];?></name>
                <span class="caret" style="position: absolute;top: 22px;right: 12px;"></span></a>
            <ul role="menu" class="dropdown-menu">
                <li><a href="<?php  echo webUrl('sysset/account')?>"><i class="icon icon-similar"></i>  切换公众号</a></li>
                <?php  if($_W['role'] == 'manager' || $_W['role'] == 'founder') { ?>
                <li><a href="./index.php?c=account&a=post&uniacid=<?php  echo $GLOBALS['_W']['uniacid'];?>&acid=<?php  echo $GLOBALS['_W']['acid'];?>" target="_blank"><i class="icon icon-wechat"></i>  编辑公众号</a></li>
                <li><a href="<?php  echo webUrl('sysset/payset')?>"><i class="icon icon-pay"></i>  支付方式</a></li>
                <li><a href="./index.php?c=utility&a=emulator&" target="_blank"><i class="icon icon-machinery"></i>  模拟测试</a></li>
                <?php  } ?>

                <?php if(cv('perm')) { ?>
                <li class="divider"></li>
                <li><a href="<?php  echo webUrl('perm')?>"><i class="icon icon-person2"></i> 权限管理</a></li>
                <?php  } ?>
                <?php  if($_W['role'] == 'founder') { ?>
                <li class="divider"></li>
               <!-- <li><a href="<?php  echo webUrl('system/plugin/apps')?>" style="color: red;"><b><i class="icon icon-app"></i> 应用中心</b></a></li>-->
                <?php  if(p("grant")) { ?>
                <!--<li><a href="<?php  echo webUrl('plugingrant')?>"><i class="icon icon-assessedbadge"></i> 应用授权</a></li>-->
                <?php  } ?>
                <li><a href="<?php  echo webUrl('system')?>"><i class="icon icon-settings"></i> 系统管理</a></li>
                <li><a href="<?php  echo webUrl('system/auth/upgrade')?>"><i class="icon icon-down"></i> 系统更新</a></li>
                <?php  } ?>
                <li><a href="./index.php?c=user&a=profile&" target="_blank"><i class="icon icon-lock"></i>  修改密码</a></li>
                <li><a href="./index.php?c=account&a=display&" target="_blank"><i class="icon icon-back"></i>  返回系统</a></li>
            </ul>
        </li>
    </ul>
    <?php  } else { ?>
    <ul class="nav navbar-nav    gray-bg">

        <!--<li <?php  if($_W['current_menu']=='') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('system')?>"> 首页</a></li>-->
        <li <?php  if($_W['current_menu']=='plugin') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('system/plugin')?>"> 应用</a></li>
        <li <?php  if($_W['current_menu']=='copyright') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('system/copyright')?>">版权</a></li>
        <li <?php  if($_W['current_menu']=='data') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('system/data')?>">数据</a></li>
        <li <?php  if($_W['current_menu']=='site') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('system/site')?>">网站</a></li>
        <li <?php  if($_W['current_menu']=='auth') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('system/auth')?>">授权</a></li>
    </ul>
    <ul class="nav navbar-top-links navbar-right" style="width: 120px !important;">
        <li>
            <a  href="<?php  echo webUrl()?>" > 返回商城</a>
        </li>
    </ul>
    <?php  } ?>
        <a class="switch-version" title="体验新版" href="<?php  echo webUrl('switchversion', array('route'=>$_GET['r'], 'id'=>$_GET['id']))?>">体验新版</a>
</div>
</nav>
</div>
<div class='wrapper main-wrapper wrapper-content '>
    <?php  if($no_left) { ?>
    <div class="page-content" style="width:1000px">
        <?php  } else { ?>
        <div class="page-menubar">
            <?php  echo $this->frame_menus()?>
        </div>
        <div class="page-content">
            <?php  } ?>
        
<!--OTEzNzAyMDIzNTAzMjQyOTE0-->