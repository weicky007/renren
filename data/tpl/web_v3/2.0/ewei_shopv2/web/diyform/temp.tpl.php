<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>


<div class="page-header">
    当前位置：
    <span class="text-primary">模板管理 </span> </div>

<div class="page-content">
    <form action="./index.php" method="get" class="form-horizontal" role="form">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="ewei_shopv2" />
        <input type="hidden" name="do" value="web" />
        <input type="hidden" name="r" value="diyform.temp" />
        <div class="page-toolbar  m-b-sm m-t-sm">
            <div class="col-sm-4">
                <span class=''>
                    <?php if(cv('diyform.temp.add')) { ?>
                          <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('diyform/temp/add')?>"><i class='fa fa-plus'></i> 添加模板</a>
                    <?php  } ?>
                </span>
            </div>
            <div class="col-sm-6 pull-right">
                <div class="input-group">
                    <input type="text" class=" form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键词"> <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit"> 搜索</button> </span>
                </div>
            </div>
        </div>
    </form>

    <?php  if(count($items)>0) { ?>
    <div class="page-table-header">
        <input type="checkbox">
        <div class="btn-group">
            <?php if(cv('diyform.temp.delete')) { ?>
            <button class="btn btn-default btn-sm dropdown-toggle btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('diyform/temp/delete')?>">
                <i class='icow icow-shanchu1'></i> 删除
            </button>
            <?php  } ?>
        </div>
    </div>
    <table class="table table-responsive">
        <thead>
        <tr>
            <th style="width:25px;"></th>
            <th >模板名称</th>
            <th >使用情况(正在使用)</th>
            <th style="width: 75px;">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php  if(is_array($items)) { foreach($items as $item) { ?>
        <tr>

            <td><input type='checkbox'   value="<?php  echo $item['id'];?>"/></td>
            <td><?php  if(!empty($category[$item['cate']]['name'])) { ?><label class='label label-primary'><?php  echo $category[$item['cate']]['name']?></label><?php  } ?> <?php  echo $item['title'];?></td>
            <td>
                <?php  if($item['use_flag1']) { ?>
                会员资料
                <?php  } ?>
                <?php  if($item['use_flag2']) { ?>
                分销商申请资料
                <?php  } ?>
                <?php  if($item['datacount3']) { ?>
                <?php  echo $item['datacount3']?>种商品
                <?php  } ?>
            </td>
            <td>
                <?php if(cv('diyform.temp.edit|diyform.temp.view')) { ?>
                <a class='btn btn-default btn-op btn-operation' href="<?php  echo webUrl('diyform/temp/edit', array( 'id' => $item['id']))?>">
                    <span data-toggle="tooltip" data-placement="top" data-original-title=" <?php if(cv('diyform.temp.edit')) { ?>编辑<?php  } else { ?>查看<?php  } ?>">
                         <?php if(cv('diyform.temp.edit')) { ?>
                        <i class="icow icow-bianji2"></i>
                        <?php  } else { ?>
                        <i class="icow icow-chakan-copy"></i>
                        <?php  } ?>
                    </span>
                </a>
                <?php  } ?>
                <?php if(cv('diyform.data')) { ?><!--a class='btn btn-default' href="<?php  echo webUrl('diyform/data', array('typeid' => $item['id']))?>" title='查看已有数据'><i class='fa fa-list'></i></a--><?php  } ?>
                <?php if(cv('diyform.temp.delete')) { ?>
                <?php  if(!$item['err']) { ?>
                <a data-toggle='ajaxRemove' class='btn btn-default btn-sm btn-op btn-operation'  href="<?php  echo webUrl('diyform/temp/delete', array('id' => $item['id']))?>" data-confirm="确认删除此模板吗？">
                    <span data-toggle="tooltip" data-placement="top" data-original-title="删除">
                        <i class="icow icow-shanchu1"></i>
                    </span>
                </a>
                <?php  } ?>
                <?php  } ?>
            </td>
        </tr>
        <?php  } } ?>
        </tbody>
        <tfoot>
            <tr>
                <td><input type="checkbox"></td>
                <td>
                    <div class="btn-group">
                        <?php if(cv('diyform.temp.delete')) { ?>
                        <button class="btn btn-default btn-sm dropdown-toggle btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('diyform/temp/delete')?>">
                            <i class='icow icow-shanchu1'></i> 删除
                        </button>
                        <?php  } ?>
                    </div>
                </td>
                <td colspan="2" class="text-right"> <?php  echo $pager;?></td>
            </tr>
        </tfoot>
    </table>
    <?php  } else { ?>
    <div class='panel panel-default'>
        <div class='panel-body' style='text-align: center;padding:30px;'>
            暂时没有任何模板!
        </div>
    </div>
    <?php  } ?>
    </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

<!--青岛易联互动网络科技有限公司-->