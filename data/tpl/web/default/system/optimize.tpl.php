<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="optimize">
	<ul class="we7-page-tab"></ul>
	<div class="alert we7-page-alert">
		<p><i class="wi wi-info-sign"></i> 启用内存优化功能将会大幅度提升程序性能和服务器的负载能力，内存优化功能需要服务器系统以及PHP扩展模块支持</p>
		<p><i class="wi wi-info-sign"></i> 目前支持的内存优化接口有 Memcache、Redis、eAccelerator、opcache<br><p>
		<p><i class="wi wi-info-sign"></i> 其中eAccelerator在PHP5.5版本以下可以开启，opcache在PHP5.5版本以上可以开启<p>
		<p><i class="wi wi-info-sign"></i> 内存接口的主要设置位于 config.php 当中，您可以通过编辑 config.php 进行高级设置<p>
	</div>
	<div class="clearfix">
		<div class="panel we7-panel">
			<div class="panel-heading">当前内存工作状态</div>
				<table class="table we7-table table-hover site-list">
					<col width="120px"/>
					<col width=""/>
					<col width=""/>
					<col width=""/>
					<tr>
						<th>内存接口</th>
						<th>PHP 扩展环境</th>
						<th>Config 设置</th>
						<th></th>
					</tr>
					<?php  if(is_array($extensions)) { foreach($extensions as $key => $extension) { ?>
					<tr>
						<td><span class="label label-success"><?php  echo $key;?></span></td>
						<td>
							<?php  if($extension['support']) { ?>
								支持
							<?php  } else { ?>
								不支持
							<?php  } ?>
						</td>
						<td>
							<?php  if($extension['status']) { ?>
								已开启
							<?php  } else { ?>
								未开启
							<?php  } ?>
							<?php  if($key == 'opcache' && $extensions['eAccelerator']['status'] && $extensions['opcache']['status']) { ?>
							<span class="label label-danger">eAccelerator 不可与 opcache同时开启</span>
							<?php  } ?>
						</td>
						<td>
							<?php  if($extension['status'] && $extension['clear']) { ?>
							<a href="<?php  echo $extension['clear']['url'];?>"><?php  echo $extension['clear']['title'];?></a>
							<?php  } ?>
							<?php  echo $extension['extra'];?>
						</td>
					</tr>
					<?php  } } ?>
				</table>
		</div>
		<div class="panel we7-panel">
			<div class="panel-heading">数据库读写分离工作状态</div>
				<table class="table we7-table table-hover site-list">
					<tr>
						<td width="200">读写分离状态</td>
						<td class="text-left">
							<?php  if($slave['slave_status']) { ?>
							<span class="label label-success">已开启</span>
							<?php  } else { ?>
							<span class="label label-danger">未开启</span>
							<?php  } ?>
						</td>
					<tr>
						<td>session存储方式</td>
						<td class="text-left">
							<?php  if($extensions['memcache']['status'] && $setting['memcache']['session'] == 1) { ?>
							<span class="label label-danger">memcache</span>
							<?php  } else if($extensions['redis']['status'] && $setting['redis']['session'] == 1) { ?>
							<span class="label label-success">redis</span>
							<?php  } else { ?>
							<span class="label label-success">mysql</span>
							<?php  } ?>
						</td>
					</tr>
					<tr>
						<td>禁用从数据库的数据表</td>
						<td class="text-left">
							<?php  if(!empty($slave['common']['slave_except_table'])) { ?>
								<?php  if(is_array($slave['common']['slave_except_table'])) { foreach($slave['common']['slave_except_table'] as $row) { ?>
									<?php  echo $row;?>
								<?php  } } ?>
							<?php  } else { ?>
								暂无
							<?php  } ?>
						</td>
					</tr>
				</table>
		</div>
		<div class="panel we7-panel">
			<div class="panel-heading">远程访问代理设置</div>
				<table class="table we7-table table-hover site-list">
					<tr>
						<td width="200">状态</td>
						<td class="text-left">
							<?php  if(!empty($setting['proxy']['host'])) { ?>
								<span class="label label-success">已开启</span>
							<?php  } else { ?>
								<span class="label label-danger">未开启</span>
							<?php  } ?>
						</td>
					</tr>
					<?php  if(!empty($setting['proxy']['host'])) { ?>
					<tr>
						<td>远程地址</td>
						<td class="text-left"><?php  echo $setting['proxy']['host'];?> 因安全原因，密码不予显示</td>
					</tr>
					<?php  } ?>
				</table>
		</div>
		<br />
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
