<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type='text/css'>
	.feed-activity-list {
		width: 100%;
		overflow: hidden;
	}
	
	.feed-element {
		float: left;
		width: 320px;
		height:100px;
		margin-left: 15px;
		margin-bottom: 20px;
		border:1px solid #efefef;
		padding: 20px;
	}
	
	.feed-element::after {
		display: none
	}
	
	.feed-element .title {
		font-size: 14px;
		height: 24px;
		line-height: 24px;
		vertical-align: bottom;
		color: #333;
		font-weight: bold;
		margin-left: 10px;
	}
	.feed-element img.img-circle,
	.dropdown-messages-box img.img-circle {
		float: left;
		width: 60px;
		height: 60px;
		border-radius: 4px;
	}
	
	.media-body {
		margin-top: 3px;
	}
	.text-muted{
		margin-left:10px;
		width:200px;
		display: block;
		overflow : hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
	}
</style>
<div class="page-header">
	当前位置：<span class="text-primary">我的应用</span>
</div>

<div class="page-content">
	<div class='panel panel-default' style='border:none;'>
		<?php  if(is_array($category)) { foreach($category as $ck => $cv) { ?>
		<?php  if(count($cv['plugins'])<=0) { ?><?php  continue;?><?php  } ?>
		<div class="panel-heading" style='background:none;border:none;'>
			<?php  echo $cv['name'];?>
		</div>
		<div class="feed-activity-list">
			<?php  if(is_array($cv['plugins'])) { foreach($cv['plugins'] as $plugin) { ?>
			<?php  if(com_run('perm::check_plugin',$plugin['identity'])) { ?>
			<?php if(cv($plugin['identity'])) { ?>
			<a class="feed-element" href="<?php  echo webUrl($plugin['identity'])?>">
						<span class="pull-left">
							<img src="<?php  echo tomedia($plugin['thumb'])?>" class="img-circle" alt="image" onerror="this.src='../addons/ewei_shopv2/static/images/yingyong.png'">
						</span>
				<div class="media-body ">
					<span class="title"><?php  echo $plugin['name'];?></span>
					<br>
					<small class="text-muted"><?php  echo $plugin['desc'];?></small>
				</div>
			</a>
			<?php  } ?>
			<?php  } ?>
			<?php  } } ?>
		</div>
		<?php  } } ?>
	</div>
</div>
<script>
	$(document).ready(function () {
		$('.feed-activity-list,.plugin_tabs').each(function(){
			if($(this).children().length<=0){
				$(this).prev().remove();
				$(this).remove();
			}
		});
	})
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--4000097827-->