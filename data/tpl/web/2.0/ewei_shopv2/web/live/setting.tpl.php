<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-heading">
    <h2>基本设置</h2>
</div>
<div class="tabs-container">
    <form id="setform" action="" method="post" class="form-horizontal form-validate">
        <input type="hidden" id="tab" name="tab" value="<?php  echo $_GPC['tab'];?>" />
        <ul class="nav nav-tabs" id="myTab">
            <li  <?php  if(empty($_GPC['tab']) || $_GPC['tab']=='basic') { ?>class="active"<?php  } ?>><a href="#tab_basic">关注及分享</a></li>
            <!--<li  <?php  if($_GPC['tab']=='notice') { ?>class="active"<?php  } ?> ><a href="#tab_notice">通知设置</a></li>-->
        </ul>
        <div class="tab-content ">
            <div class="tab-pane <?php  if(empty($_GPC['tab']) || $_GPC['tab']=='basic') { ?>active<?php  } ?>" id="tab_basic">
                <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('live/setting/basic', TEMPLATE_INCLUDEPATH)) : (include template('live/setting/basic', TEMPLATE_INCLUDEPATH));?>
            </div>
            <!--<div class="tab-pane <?php  if($_GPC['tab']=='notice') { ?>active<?php  } ?>" id="tab_notice">
                <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('live/setting/notice', TEMPLATE_INCLUDEPATH)) : (include template('live/setting/notice', TEMPLATE_INCLUDEPATH));?>
            </div>-->
        </div>
        <?php if(cv('live.setting.edit')) { ?>
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
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
</script>


<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
