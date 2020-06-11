<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type='text/css'>
    .dd-handle { height: 40px; line-height: 30px}
    .dd-list { width:860px;}
</style>

<div class="page-header">
	当前位置：<span class="text-primary">魔方推荐</span>
</div>

<div class="page-content">
	<img src="<?php  echo EWEI_SHOPV2_STATIC . 'images/'?>cube.jpg">
	<div class="alert alert-primary" style="margin-top: 10px;">提示：拖动改变位置，最多添加4个，请参照上图</div>

	<form action="" method="post" class="form-validate">
		<table class="table">
			<thead>
				<th style="width:60px;"></th>
				<th>图片</th>
				<th>链接</th>
				<th style="width: 60px;">操作</th>
			</thead>
			<tbody id="tbody">
				<?php  if(is_array($cubes)) { foreach($cubes as $i => $cube) { ?>
					<tr class="cube-item">
						<td>
							<?php if(cv('shop.cube.edit')) { ?>
								<a href="javascript:;" class="btn btn-default btn-sm btn-move"><i class="fa fa-arrows"></i></a>
							<?php  } ?>
						</td>
						<td>
							<div class="input-group img-item">
								<div class="input-group-addon" style="padding: 0 5px;">
									<img src="<?php  echo tomedia($cube['img'])?>" style="height:20px;width:20px" onerror="this.src='../addons/ewei_shopv2/static/images/nopic.png'" />
								</div>

								<input type="text" class="form-control" name="cube_img[]" value="<?php  echo $cube['img'];?>" <?php if(cv('shop.cube.edit')) { ?> <?php  } else { ?> readonly="readonly"<?php  } ?> />
								<?php if(cv('shop.cube.edit')) { ?>
									<div class="input-group-btn">
										<button type="button" class="btn btn-default btn-select-pic">选择图片</button>
									</div>
								<?php  } ?>
							</div>
						</td>
						<td>
							<div class="input-group form-group" style="margin: 0;">
								<?php if(cv('shop.cube.edit')) { ?>
									<input type="text" value="<?php  echo $cube['url'];?>" class="form-control valid" name="cube_url[]" placeholder="" <?php if(cv('shop.cube.edit')) { ?>id="cubelink_<?php  echo $i+10000?>"<?php  } else { ?> readonly="readonly"<?php  } ?>>
									<span class="input-group-btn">
										<span data-input="#cubelink_<?php  echo $i+10000?>" data-toggle="selectUrl" class="btn btn-default">选择链接</span>
									</span>
								<?php  } else { ?>
									<input type="text" value="<?php  echo $cube['url'];?>" class="form-control valid"  readonly="readonly">
								<?php  } ?>
							</div>
						</td>
						<td>
							<?php if(cv('shop.shop.cube.edit')) { ?>
								<button type="button" class="btn btn-danger  btn-sm" onclick="removeCube(this)"><i class="fa fa-remove"></i></button>
							<?php  } ?>
						</td>
					</tr>
				<?php  } } ?>
			</tbody>
			<?php if(cv('shop.cube.edit')) { ?>
				<tfoot>
					<tr>
						<td colspan="4">
							<input type="submit" class="btn btn-primary" value="保存">
							<?php if(cv('shop.cube.edit')) { ?>
								<button type="button" class="btn btn-default" onclick="addCube()"><i class="fa fa-plus"></i> 添加魔方</button>
							<?php  } ?>
						</td>
					</tr>
				</tfoot>
			<?php  } ?>
		</table>
	</form>
</div>
<?php if(cv('shop.cube.edit')) { ?>
	<script language='javascript'>



		$(function () {
			bindEvents();
		})
		function removeCube(obj){
			$(obj).closest('.cube-item').remove();
		}
		function addCube(){
		    if($('.cube-item').length>=4){
			tip.msgbox.err('最多添加4个魔方');
			return;
		    }
		   	var timestamp=new Date().getTime();
			var html='<tr class="cube-item">';
			html+='<td><a href="javascript:;" class="btn btn-default btn-sm btn-move"><i class="fa fa-arrows"></i></a></td>';
			html+='<td>';
			html+='<div class="input-group img-item">';
			html+='<div class="input-group-addon">';
			html+='<img src="" style="height:20px;width:20px" onerror="this.src=\'../addons/ewei_shopv2/static/images/nopic.png\'"/>';
			html+='</div>';
			html+='<input type="text" class="form-control" name="cube_img[]" />';
			html+='<div class="input-group-btn">';
			html+='<button type="button" class="btn btn-default btn-select-pic">选择图片</button>';
			html+='</div>';
			html+='</div>';
			html+='</td><td><div class="input-group form-group" style="margin: 0;">';
			html+='<input type="text" value="" class="form-control valid" name="cube_url[]" placeholder="" id="cubelink_'+timestamp+'">';
			html+='<span data-input="#cubelink_'+timestamp+'" data-toggle="selectUrl" class="input-group-addon btn btn-default">选择链接</span></div>'
			html+='</td><td><button type="button" class="btn btn-danger  btn-sm" onclick="removeCube(this)"><i class="fa fa-remove"></i></button>';

			html+='</td>';
			html+='</tr>';
			$('#tbody').append(html);
			bindEvents();
		}
		function bindEvents() {
			require(['jquery', 'util'], function ($, util) {
				$('.btn-select-pic').unbind('click').click(function () {
					var imgitem = $(this).closest('.img-item');
					util.image('', function (data) {
						imgitem.find('img').attr('src', data['url']);
						imgitem.find('input').val(data['attachment']);
					});
				});
			});
			require(['jquery.ui'] ,function(){
				$("#tbody").sortable({handle: '.btn-move'});
			})
		}
		myrequire(['jquery.nestable'], function () {
			$('#div_nestable').nestable({maxDepth: 1});
			$('.dd-item').addClass('full');
			$(".dd-handle a,.dd-handle div").mousedown(function (e) {
				e.stopPropagation();
			});
			$("#save_sort").click(function () {
				var json = window.JSON.stringify($('#div_nestable').nestable("serialize"));
				$(':input[name=datas]').val(json);
			})
		})
	</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>


<!--OTEzNzAyMDIzNTAzMjQyOTE0-->