<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">
    当前位置：<span class="text-primary">基本设置</span>
</div>

<div class="page-content">
<form id="setform"  <?php if(cv('mmanage.setting.save')) { ?>action="" method="post"<?php  } ?> class="form-horizontal form-validate" >

    <div class="form-group">
        <label class="col-lg control-label">直接链接</label>
        <div class="col-sm-9 col-xs-12">
            <div class="form-control-static">
                <a href='javascript:;' class="js-clip" title="点击复制链接" data-url="<?php  echo mobileUrl('mmanage', array(), true)?>" ><?php  echo mobileUrl('mmanage',array(),true)?></a>
                <span style="cursor: pointer;" data-toggle="popover" data-trigger="hover" data-html="true" data-content="<img src='<?php  echo $qrcode;?>' width='130' alt='链接二维码'>" data-placement="auto right">
                    <i class="glyphicon glyphicon-qrcode"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg control-label">入口关键字</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('mmanage.setting.save')) { ?>
                <input type="text" class="form-control valid" name="keyword" value="<?php  echo $data['keyword'];?>" placeholder="请输入入口关键字，不填则不设置" />
            <?php  } else { ?>
                <div class="form-control-static"><?php  echo $data['keyword'];?></div>
            <?php  } ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg control-label">入口标题</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('mmanage.setting.save')) { ?>
                <input type="text" class="form-control valid" name="title" value="<?php  echo $data['title'];?>" placeholder="请输入入口标题" />
            <?php  } else { ?>
                <div class="form-control-static"><?php  echo $data['title'];?></div>
            <?php  } ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg control-label">入口图片</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('mmanage.setting.save')) { ?>
                <?php  echo tpl_form_field_image2('thumb', $data['thumb'])?>
            <?php  } else { ?>
                <img width="150" class="img-responsive img-thumbnail" onerror="this.src='./resource/images/nopic.jpg'; this.title='图片未找到.'" src="<?php  echo tomedia($data['thumb'])?>" />
            <?php  } ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg control-label">入口介绍</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('mmanage.setting.save')) { ?>
                <textarea class="form-control" name="desc" placeholder="请输入入口介绍" rows="5"><?php  echo $data['desc'];?></textarea>
            <?php  } else { ?>
                <div class="form-control-static"><?php  echo $data['desc'];?></div>
            <?php  } ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-lg control-label">关键字状态</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('mmanage.setting.save')) { ?>
                <label class="radio-inline"><input type="radio" value="1" <?php  if(!empty($data['status'])) { ?>checked<?php  } ?> name="status"> 启用</label>
                <label class="radio-inline"><input type="radio" value="0" <?php  if(empty($data['status'])) { ?>checked<?php  } ?> name="status"> 禁用</label>
            <?php  } else { ?>
                <div class="form-control-static"><?php echo empty($data['status'])?"禁用":"启用"?></div>
            <?php  } ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg control-label">手机端后台开关</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('mmanage.setting.save')) { ?>
                <label class="radio-inline"><input type="radio" value="1" <?php  if(!empty($data['open'])) { ?>checked<?php  } ?> name="open"> 开启</label>
                <label class="radio-inline"><input type="radio" value="0" <?php  if(empty($data['open'])) { ?>checked<?php  } ?> name="open"> 关闭</label>
            <?php  } else { ?>
                <div class="form-control-static"><?php echo empty($data['open'])?"关闭":"开启"?></div>
            <?php  } ?>
        </div>
    </div>

    <?php if(cv('mmanage.setting.save')) { ?>
        <div class="form-group">
            <label class="col-lg control-label"></label>
            <div class="col-sm-9 col-xs-12">
                <input type="submit" class="btn btn-primary" value="保存" />
            </div>
        </div>
    <?php  } ?>

</form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--6Z2S5bKb5piT6IGU5LqS5Yqo572R57uc56eR5oqA5pyJ6ZmQ5YWs5Y+4-->