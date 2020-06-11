<?php defined('IN_IA') or exit('Access Denied');?><ul class="menu-head-top">
	<li <?php  if($_GPC['r']=='live') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('live')?>"><?php  echo $this->plugintitle?> <i class="fa fa-caret-right"></i></a></li>
</ul>

<div class='menu-header'>直播间</div>
<li <?php  if($_GPC['r']=='live.room' || $_GPC['r']=='live.room.add' || $_GPC['r']=='live.room.edit') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('live/room')?>">直播间管理</a></li>
<li <?php  if($_GPC['r']=='live.category' || $_GPC['r']=='live.category.add' || $_GPC['r']=='live.category.edit') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('live/category')?>">分类管理</a></li>
<li <?php  if($_GPC['r']=='live.banner' || $_GPC['r']=='live.banner.add' || $_GPC['r']=='live.banner.edit') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('live/banner')?>">幻灯片管理</a></li>

<div class='menu-header'>其他</div>
<!--<li <?php  if($_GPC['r']=='live.get') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('live/get')?>">测试视频抓取</a></li>-->
<li <?php  if($_GPC['r']=='live.service') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('live/service')?>">通信服务</a></li>

<div class='menu-header'>设置</div>
<li <?php  if($_GPC['r']=='live.cover') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('live/cover')?>">入口设置</a></li>
<li <?php  if($_GPC['r']=='live.setting') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('live/setting')?>">基础设置</a></li>