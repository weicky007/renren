<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<div class="page-header">
	当前位置：<span class="text-primary">文章管理 </span>
</div>
<div class="page-content">
<form action="./index.php" method="get" class="form-horizontal" role="form">
	<input type="hidden" name="c" value="site">
	<input type="hidden" name="a" value="entry">
	<input type="hidden" name="m" value="ewei_shopv2">
	<input type="hidden" name="do" value="web">
	<input type="hidden" name="r" value="article">
	<div class="page-toolbar m-b-sm m-t-sm">
		<div class="col-sm-4">
			<span class="">
				 <?php if(cv('article.add')) { ?>
						 <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('article/add')?>"><i class="fa fa-plus"></i> 添加文章</a>
				 <?php  } ?>
			</span>
		</div>
		<div class="col-sm-6 pull-right">
			<div class="input-group">
				<div class="input-group-select">
					<select name="category" class='form-control select2' style="width:150px;">
						<option value="" <?php  if($_GPC['category'] == '') { ?> selected<?php  } ?>>分类</option>
						<?php  if(is_array($categorys)) { foreach($categorys as $category) { ?>
						<option value="<?php  echo $category['id'];?>" <?php  if($_GPC['category']==$category['id']) { ?>selected="selected"<?php  } ?>><?php  echo $category['category_name'];?></option>
						<?php  } } ?>
					</select>
				</div>
				<input type="text" class=" form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键词"> <span class="input-group-btn">
				<button class="btn btn-primary" type="submit"> 搜索</button> </span>
			</div>

		</div>
	</div>
</form>

<!-- 文章列表 -->
<?php  if(count($articles)>0) { ?>
	<div class="page-table-header">
		<input type="checkbox">
		<div class="btn-group ">
			<?php if(cv('article.edit')) { ?>
			<button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch' data-href="<?php  echo webUrl('article/state',array('state'=>1))?>">
				<i class='icow icow-qiyong'></i> 开启
			</button>
			<button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch' data-href="<?php  echo webUrl('article/state',array('state'=>0))?>">
				<i class='icow icow-jinyong'></i> 关闭
			</button>
			<?php  } ?>
			<?php if(cv('article.delete')) { ?>
			<button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('article/delete')?>">
				<i class='icow icow-shanchu1'></i> 删除
			</button>
			<?php  } ?>
		</div>
	</div>
	<table class="table table-hover table-responsive">
		<thead>
			<tr>
				<th style="width:25px;"></th>
				<th style="width:44px;">排序</th>
				<th>文章标题</th>
	
				<th>关键字</th>
				<th>创建时间</th>
				<th>阅读量</th>
				<th>点赞量</th>
	
				<th style="width:100px;">状态</th>
				<th style="width: 150px;">操作</th>
			</tr>
		</thead>
		<tbody>
	
			<?php  if(is_array($articles)) { foreach($articles as $article) { ?>
			<tr>
				<td>
					<input type='checkbox' value="<?php  echo $article['id'];?>" />
				</td>
				<td>
					<?php if(cv('article.edit')) { ?>
						<a href='javascript:;' data-toggle='ajaxEdit' data-href="<?php  echo webUrl('article/displayorder',array('id'=>$article['id']))?>"><?php  echo $article['displayorder'];?></a> 
					<?php  } else { ?> 
						<?php  echo $article['displayorder'];?> 
					<?php  } ?>
				</td>
				<td>
					<?php  if(!empty($article['category_name'])) { ?>
						<span class="text-primary">[<?php  echo $article['category_name'];?>]</span><br/>
					<?php  } ?>
					<a href="<?php  echo mobileUrl('article',array('aid'=>$article['id'], 'preview'=>1), true)?>" target="_blank" data-toggle="tooltip" title="点击预览"><?php  echo $article['article_title'];?></a>
				</td>
				<td><?php  echo $article['article_keyword2'];?></td>
				<td><?php  echo date('Y-m-d', strtotime($article['article_date']))?><br/><?php  echo date('H:i', strtotime($article['article_date']))?></td>
				<td data-toggle='tooltip' title='<?php  echo $article['article_readnum'];?>'><?php  echo $article['article_readnum'];?></td>
				<td data-toggle='tooltip' title='<?php  echo $article['article_likenum'];?>'><?php  echo $article['article_likenum'];?></td>
	
				<td>
					<span class='label 
						<?php  if($article['article_state']==1) { ?>label-primary<?php  } else { ?>label-default<?php  } ?>' 
						<?php if(cv('article.page.edit')) { ?> 
							data-toggle="ajaxSwitch" 
							data-confirm = "确认<?php  if($article['article_state']==1) { ?>关闭<?php  } else { ?>开启<?php  } ?>吗？"
							data-switch-value="{$article[" article_state "]}" 
							data-switch-value0="0|关闭|label label-default|<?php  echo webUrl('article/state',array('state'=>1,'id'=>$article['id']))?>" 
							data-switch-value1="1|开启|label label-primary|<?php  echo webUrl('article/state',array('state'=>0,'id'=>$article['id']))?>" 
						<?php  } ?>>
						
						<?php  if($article['article_state']==1) { ?>开启<?php  } else { ?>关闭<?php  } ?>
					</span>
	
				</td>
				<td>
	
					<?php if(cv('article.record')) { ?>
						<a class="btn btn-default btn-sm btn-op btn-operation" href="<?php  echo webUrl('article/record', array('aid'=>$article['id']))?>">
							  <span data-toggle="tooltip" data-placement="top" title="" data-original-title="统计">
                                <i class='icow icow-iconfontpaixingbang'></i>
                           </span>
						</a>
					<?php  } ?>
					<a href="javascript:;" data-url="<?php  echo mobileUrl('article',array('aid'=>$article['id']), true)?>" class="js-clip btn btn-default btn-sm btn-op btn-operation">
						<span data-toggle="tooltip" data-placement="top"  data-original-title="复制链接">
                               <i class='icow icow-lianjie2'></i>
                           </span>
					</a>
					<a href="javascript:void(0);" class="btn btn-default btn-sm btn-op btn-operation" data-toggle="popover" data-trigger="hover" data-html="true"
						  data-content="<img src='<?php  echo $article['qrcode'];?>' width='130' alt='链接二维码'>" data-placement="auto right">
						<span data-toggle="tooltip" data-placement="top"  data-original-title="复制链接">
                               <i class='icow icow-erweima3'></i>
                           </span>
					</a>
					<?php if(cv('article.edit')) { ?>
						<a class='btn btn-default btn-sm btn-op btn-operation' href="<?php  echo webUrl('article/edit',array('aid'=>$article['id']))?>">
							<span data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑">
                             <i class="icow icow-bianji2"></i>
                         </span>
						</a>
					<?php  } ?> 
					<?php if(cv('article.delete')) { ?>
						<a data-toggle="ajaxRemove" class='btn btn-default btn-sm btn-op btn-operation' href="<?php  echo webUrl('article/delete',array('id'=>$article['id']))?>" data-confirm="确认要删除此文章?">
							<span data-toggle="tooltip" data-placement="top" title="" data-original-title="删除">
                                <i class='icow icow-shanchu1'></i>
                           </span>
						</a>
					<?php  } ?>
				</td>
			</tr>
			<?php  } } ?>
		</tbody>
		<tfoot>
			<tr>
				<td>
					<input type="checkbox">
				</td>
				<td colspan="3">
					<div class="btn-group ">
						<?php if(cv('article.edit')) { ?>
						<button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch' data-href="<?php  echo webUrl('article/state',array('state'=>1))?>">
							<i class='icow icow-qiyong'></i> 开启
						</button>
						<button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch' data-href="<?php  echo webUrl('article/state',array('state'=>0))?>">
							<i class='icow icow-jinyong'></i> 关闭
						</button>
						<?php  } ?>
						<?php if(cv('article.delete')) { ?>
						<button class="btn btn-default btn-sm btn-operation" type="button" data-toggle='batch-remove' data-confirm="确认要删除?" data-href="<?php  echo webUrl('article/delete')?>">
							<i class='icow icow-shanchu1'></i> 删除
						</button>
						<?php  } ?>
					</div>
				</td>
				<td colspan="5" class="text-right">	<?php  echo $pager;?> </td>
			</tr>
		</tfoot>
	</table>

<?php  } else { ?>
	<div class='panel panel-default'>
		<div class='panel-body' style='text-align: center;padding:30px;'>
			暂时没有任何文章!
		</div>
	</div>
<?php  } ?>
</form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
<!--青岛易联互动网络科技有限公司-->