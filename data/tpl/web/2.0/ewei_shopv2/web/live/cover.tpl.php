<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class='page-heading'><h2><?php  if($_W['merchid']>0) { ?>多商户<?php  } ?>直播入口设置</h2></div>
<form id="setform"  action="" method="post" class="form-horizontal form-validate" >
    <div class="form-group">
        <label class="col-sm-2 control-label">直接链接</label>
        <div class="col-sm-9 col-xs-12">
            <p class='form-control-static'>
                <a href='javascript:;' class="js-clip" title="点击复制链接" data-url="<?php  echo mobileUrl('live',array('merchid'=>$_W['merchid']),true)?>" >
                    <?php  echo mobileUrl('live',array('merchid'=>$_W['merchid']),true)?>
                </a>
                <span style="cursor: pointer;" data-toggle="popover" data-trigger="hover" data-html="true"
                      data-content="<img src='<?php  echo $qrcode;?>' width='130' alt='链接二维码'>" data-placement="auto right">
                    <i class="glyphicon glyphicon-qrcode"></i>
                </span>
            </p>
            <p>
            </p>
        </div>
    </div>
    <?php  if($_W['merchid']==0) { ?>
    <div class="form-group">
        <label class="col-sm-2 control-label must">关键词</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('live.cover.edit')) { ?>
                <input type='text' class='form-control' name='cover[keyword]' value="<?php  echo $keyword['content'];?>" data-rule-required='true' />
             <?php  } else { ?>
                <div class='form-control-static'><?php  echo $keyword['content'];?></div>
             <?php  } ?>
        </div>
    </div>
    <?php  } ?>
    <?php  if($_W['merchid']==0) { ?>
    <div class="form-group">
        <label class="col-sm-2 control-label">封面标题</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('live.cover.edit')) { ?>
                <input type='text' class='form-control' name='cover[title]' value="<?php  echo $cover['title'];?>" />
             <?php  } else { ?>
                <div class='form-control-static'><?php  echo $cover['title'];?></div>
             <?php  } ?>
        </div>
    </div>
    <?php  } ?>
    <?php  if($_W['merchid']==0) { ?>
    <div class="form-group">
        <label class="col-sm-2 control-label">封面图片</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('live.cover.edit')) { ?>
                <?php  echo tpl_form_field_image('cover[thumb]',$cover['thumb'])?>
             <?php  } else { ?>
                <?php  if(!empty($cover['thumb'])) { ?>
                    <div class='form-control-static'>
                        <img src="<?php  echo tomedia($cover['thumb'])?>" style='width:100px;border:1px solid #ccc;padding:1px' />
                    </div>
                <?php  } ?>
             <?php  } ?>
        </div>
    </div>
    <?php  } ?>
    <?php  if($_W['merchid']==0) { ?>
    <div class="form-group">
        <label class="col-sm-2 control-label">封面描述</label>
        <div class="col-sm-9 col-xs-12">
            <?php if(cv('live.cover.edit')) { ?>
                <textarea name='cover[desc]' class='form-control'><?php  echo $cover['description'];?></textarea>
            <?php  } else { ?>
                <div class='form-control-static'><?php  echo $cover['description'];?></div>
            <?php  } ?>
        </div>
    </div>
    <?php  } ?>
    <?php  if($_W['merchid']==0) { ?>
    <div class="form-group">
        <label class="col-sm-2 control-label">关键词状态</label>
        <div class="col-sm-9">
            <?php if(cv('live.cover.edit')) { ?>
                <label class="radio-inline">
                    <input type="radio" name="cover[status]" value="0" <?php  if(empty($rule['status'])) { ?> checked="checked"<?php  } ?>/>禁用
                </label>
                <label class="radio-inline">
                    <input type="radio" name="cover[status]" value="1" <?php  if($rule['status']==1) { ?> checked="checked"<?php  } ?>/>启用
                </label>
            <?php  } else { ?>
                <div class='form-control-static'><?php  if($data['status']==1) { ?>启用<?php  } else { ?>禁用<?php  } ?></div>
            <?php  } ?>
        </div>
    </div>
    <?php  } ?>
    <?php  if($_W['merchid']==0) { ?>
    <div class="form-group">
        <label class="col-sm-2 control-label"></label>
        <div class="col-sm-9">
            <?php if(cv('live.cover.edit')) { ?>
                <input type="submit" value="提交" class="btn btn-primary" />
            <?php  } ?>
        </div>
    </div>
    <?php  } ?>
</form>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
