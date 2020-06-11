<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-heading"> 
    <span class='pull-right'>
        <?php if(cv('live.banner.add')) { ?>
        	<a class='btn btn-primary btn-sm' href="<?php  echo webUrl('live/banner/add')?>"><i class='fa fa-plus'></i> 添加幻灯片</a>
        <?php  } ?>
    </span>
    <h2>幻灯片管理</h2> </div>

<form action="./index.php" method="get" class="form-horizontal form-search" role="form">
    <input type="hidden" name="c" value="site" />
    <input type="hidden" name="a" value="entry" />
    <input type="hidden" name="m" value="ewei_shopv2" />
    <input type="hidden" name="do" value="web" />
    <input type="hidden" name="r" value="live.banner" />
    <div class="page-toolbar row m-b-sm m-t-sm">
        <div class="col-sm-4">

            <div class="input-group-btn">
                <button class="btn btn-default btn-sm"  type="button" data-toggle='refresh'><i class='fa fa-refresh'></i></button>
                <?php if(cv('live.banner.edit')) { ?>
	                <button class="btn btn-default btn-sm" type="button" data-toggle='batch' data-href="<?php  echo webUrl('live/banner/enabled',array('enabled'=>1))?>"><i class='fa fa-circle'></i> 显示</button>
	                <button class="btn btn-default btn-sm" type="button" data-toggle='batch'  data-href="<?php  echo webUrl('live/banner/enabled',array('enabled'=>0))?>"><i class='fa fa-circle-o'></i> 隐藏</button>
                <?php  } ?>
                <?php if(cv('live.banner.delete')) { ?>
                	<button class="btn btn-default btn-sm" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('live/banner/delete')?>"><i class='fa fa-trash'></i> 删除</button>
                <?php  } ?>
            </div>
        </div>	


        <div class="col-sm-6 pull-right">

            <select name="enabled" class='form-control input-sm select-sm'>
                <option value="" <?php  if($_GPC['enabled'] == '') { ?> selected<?php  } ?>>状态</option>
                <option value="1" <?php  if($_GPC['enabled']== '1') { ?> selected<?php  } ?>>显示</option>
                <option value="0" <?php  if($_GPC['enabled'] == '0') { ?> selected<?php  } ?>>隐藏</option>
            </select>	
            <div class="input-group">				 
                <input type="text" class="input-sm form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键词"> <span class="input-group-btn">
                    		
                    <button class="btn btn-sm btn-primary" type="submit"> 搜索</button> </span>
            </div>

        </div>
    </div>
</form>

<form action="" method="post">
    <?php  if(count($list)>0) { ?>

    <table class="table table-responsive table-hover" >
        <thead class="navbar-inner">
            <tr>
                <th style="width:25px;"><input type='checkbox' /></th>
                <th style='width:50px'>顺序</th>					
                <th>标题</th>
                <th>链接</th> 
                <th style='width:60px'>显示</th>
                <th style="width: 150px;">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php  if(is_array($list)) { foreach($list as $row) { ?>
            <tr>

                <td>
                    <input type='checkbox'   value="<?php  echo $row['id'];?>"/>
                </td>
                <td>
                    <?php if(cv('live.banner.edit')) { ?>
                    	<a href='javascript:;' data-toggle='ajaxEdit' data-href="<?php  echo webUrl('live/banner/displayorder',array('id'=>$row['id']))?>" ><?php  echo $row['displayorder'];?></a>
                    <?php  } else { ?>
                    	<?php  echo $row['displayorder'];?> 
                    <?php  } ?>
                </td>

                <td><?php  echo $row['advname'];?></td>
                <td><?php  echo $row['link'];?></td>
                <td>

                    <span class='label <?php  if($row['enabled']==1) { ?>label-success<?php  } else { ?>label-default<?php  } ?>' 
                          <?php if(cv('live.banner.edit')) { ?>
	                          data-toggle='ajaxSwitch' 
	                          data-switch-value='<?php  echo $row['enabled'];?>'
	                          data-switch-value0='0|隐藏|label label-default|<?php  echo webUrl('live/banner/enabled',array('enabled'=>1,'id'=>$row['id']))?>'
	                          data-switch-value1='1|显示|label label-success|<?php  echo webUrl('live/banner/enabled',array('enabled'=>0,'id'=>$row['id']))?>'
                          <?php  } ?>
                          >
                          <?php  if($row['enabled']==1) { ?>显示<?php  } else { ?>隐藏<?php  } ?></span>


                    </td>
                    <td style="text-align:left;">
                        <?php if(cv('live.banner.view|live.banner.edit')) { ?>
	                        <a href="<?php  echo webUrl('live/banner/edit', array('id' => $row['id']))?>" class="btn btn-default btn-sm">
	                        	<i class='fa fa-edit'></i> <?php if(cv('live.banner.edit')) { ?>修改<?php  } else { ?>查看<?php  } ?>
	                        </a>
                        <?php  } ?>
                        <?php if(cv('live.banner.delete')) { ?>
                        	<a data-toggle='ajaxRemove' href="<?php  echo webUrl('live/banner/delete', array('id' => $row['id']))?>"class="btn btn-default btn-sm" data-confirm='确认要删除此幻灯片吗?'><i class="fa fa-trash"></i> 删除</a>
                        <?php  } ?>
                    </td>
                </tr>
                <?php  } } ?> 
                <tr>
                    <td colspan='6'>
                        <div class='pagers' style='float:right;'>
                            <?php  echo $pager;?>			
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php  echo $pager;?>
        <?php  } else { ?>
        <div class='panel panel-default'>
            <div class='panel-body' style='text-align: center;padding:30px;'>
                暂时没有任何幻灯片!
            </div>
        </div>
        <?php  } ?>

    </form>


    <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
