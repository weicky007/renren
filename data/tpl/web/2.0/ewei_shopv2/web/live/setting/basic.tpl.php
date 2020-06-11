<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
	<label class="col-sm-2 control-label">会员中心显示</label>
	<div class="col-sm-9 col-xs-12">
		<?php if(cv('live.setting.edit')) { ?>
		<label class='radio radio-inline'>
			<input type='radio' value='1' name='data[ismember]'  <?php  if($data['ismember']==1) { ?>checked<?php  } ?> /> 是
		</label>
		<label class='radio radio-inline'>
			<input type='radio' value='0' name='data[ismember]' <?php  if($data['ismember']==0) { ?>checked<?php  } ?> /> 否
		</label>
		<?php  } else { ?>
		<div class='form-control-static'><?php  if($data['ismember']==1) { ?>是<?php  } else { ?>否<?php  } ?></div>
		<?php  } ?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">分享标题</label>
	<div class="col-sm-9 col-xs-12">
		<?php if(cv('live.setting.edit')) { ?>
		<input type="text" name="data[share_title]" class="form-control" value="<?php  echo $data['share_title'];?>" />
		<span class="help-block">不填写默认商城名称</span>
		<?php  } else { ?>
		<input type="hidden" name="data[share_title]" value="<?php  echo $data['share_title'];?>" />
		<div class='form-control-static'><?php  echo $data['share_title'];?></div>
		<?php  } ?>

	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">分享图标</label>
	<div class="col-sm-9 col-xs-12">
		<?php if(cv('live.setting.edit')) { ?>

		<?php  echo tpl_form_field_image('data[share_icon]', $data['share_icon']);?>
		<span class="help-block">不选择默认商城LOGO</span>
		<?php  } else { ?>
		<input type="hidden" name="data[share_icon]" value="<?php  echo $data['share_icon'];?>" />
		<?php  if(!empty($data['share_icon'])) { ?>
		<a href='<?php  echo tomedia($data['share_icon'])?>' target='_blank'>
		<img src="<?php  echo tomedia($data['share_icon'])?>" style='width:100px;border:1px solid #ccc;padding:1px' />
		</a>
		<?php  } ?>
		<?php  } ?>

	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">分享描述</label>
	<div class="col-sm-9 col-xs-12">
		<?php if(cv('live.setting.edit')) { ?>
		<textarea style="height:100px;" name="data[share_desc]" class="form-control" cols="60"><?php  echo $data['share_desc'];?></textarea>
		<?php  } else { ?>
		<textarea style="height:100px;display: none" name="data[share_desc]" class="form-control" cols="60"><?php  echo $data['share_desc'];?></textarea>
		<div class='form-control-static'><?php  echo $data['share_desc'];?></div>
		<?php  } ?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">分享链接</label>
	<div class="col-sm-9 col-xs-12">
		<?php if(cv('live.setting.edit')) { ?>

		<div class="input-group form-group" style="margin: 0;">
			<input type="text" name="data[share_url]" class="form-control" value="<?php  echo $data['share_url'];?>" id="shareurl" />
			<span data-input="#shareurl" data-toggle="selectUrl" data-full="true" class="input-group-addon btn btn-default">选择链接</span>
		</div>

		<span class='help-block'>用户分享出去的链接，默认为首页</span>
		<?php  } else { ?>
		<input type="hidden" name="data[share_url]" value="<?php  echo $data['share_url'];?>" />
		<div class='form-control-static'><?php  echo $data['share_url'];?></div>
		<?php  } ?>
	</div>
</div>
