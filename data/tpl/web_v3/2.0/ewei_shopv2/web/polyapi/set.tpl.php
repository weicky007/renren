<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">当前位置：<span class="text-primary">基础设置</span> </div>
<div class="page-content">
<div class="alert alert-primary">
	提示：
	<br/>请确定您的网店管家帐号能在'网店管家云端版'和'esAPI(云端版)'两款软件使用,再联系官方(VIP人工服务)对接
	<a class="text-danger" target="_blank" href="http://huoban.wdgj.com/partner/spread.html?pd=2491">点击申请网店管家</a>
</div>



<form id="setform"  <?php if(cv('polyapi.set.edit')) { ?>action="" method="post"<?php  } ?> class="form-horizontal form-validate">

<div class="form-group">
	<label class="col-lg control-label">接口访问地址</label>
	<div class="col-sm-9 col-xs-12">
		<p class='form-control-static'>
			<a href='javascript:;' class="js-clip" title='点击复制链接' data-url="<?php  echo $_W['siteroot'];?>addons/ewei_shopv2/plugin/polyapi/api.php" >
				<?php  echo $_W['siteroot'];?>addons/ewei_shopv2/plugin/polyapi/api.php
			</a>
		</p>
	</div>
</div>

<div class="form-group">
	<label class="col-lg control-label must">AppKey</label>
	<div class="col-sm-8">
		<?php if( ce('polyapi.set' ,$item) ) { ?>
		<input type="text" class="form-control" name="appkey" value="<?php  echo $item['appkey'];?>" data-rule-required="true"/>
		<?php  } else { ?>
		<div class="form-control-static"><?php  echo $item['appkey'];?></div>
		<?php  } ?>
	</div>
</div>

<div class="form-group">
	<label class="col-lg control-label must">AppSecret</label>
	<div class="col-sm-8">
		<?php if( ce('polyapi.set' ,$item) ) { ?>
		<input type="text" class="form-control" name="appsecret" value="<?php  echo $item['appsecret'];?>" data-rule-required="true"/>
		<?php  } else { ?>
		<div class="form-control-static"><?php  echo $item['appsecret'];?></div>
		<?php  } ?>
	</div>
</div>

<div class="form-group">
	<label class="col-lg control-label must">Token</label>
	<div class="col-sm-8">
		<?php if( ce('polyapi.set' ,$item) ) { ?>
		<input type="text" class="form-control" name="token" value="<?php  echo $item['token'];?>" data-rule-required="true"/>
		<?php  } else { ?>
		<div class="form-control-static"><?php  echo $item['token'];?></div>
		<?php  } ?>
	</div>
</div>


<div class="form-group">
	<label class="col-lg control-label">是否开启接口</label>
	<div class="col-sm-8">
		<?php if(cv('polyapi.set.edit')) { ?>
		<label class="radio-inline"><input type="radio"  name="status" value="1" <?php  if($item['status'] ==1) { ?> checked="checked"<?php  } ?> /> 开启</label>
		<label class="radio-inline"><input type="radio"  name="status" value="0" <?php  if($item['status'] ==0) { ?> checked="checked"<?php  } ?> /> 关闭</label>
		<div class='help-block'></div>
		<?php  } else { ?>
		<?php  if($item['status'] ==0) { ?>关闭<?php  } else { ?>开启<?php  } ?>
		<?php  } ?>
	</div>
</div>


<?php if(cv('polyapi.set.edit')) { ?>
    <div class="form-group">
		<label class="col-lg control-label"></label>
		<div class="col-sm-9 col-xs-12">
			<input type="submit"  value="提交" class="btn btn-primary" />
		</div>
    </div>
<?php  } ?>

</form>
</div>
<script language='javascript'>
        require(['bootstrap'], function () {
            $('#myTab a').click(function (e) {
                $('#tab').val($(this).attr('href'));
                e.preventDefault();
                $(this).tab('show');
            })
        });

        $(function () {
            $('.open_apply').click(function () {
                var type = $(".open_apply:checked").val();
                if (type == '1') {
                    $('.protocol-group').show();
                } else {
                    $('.protocol-group').hide();
                }
            })
        });

</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--NDAwMDA5NzgyNw==-->