<?php defined('IN_IA') or exit('Access Denied');?><div class="region-goods-details row">
	<div class="region-goods-left col-sm-2">库存</div>
	<div class="region-goods-right col-sm-10">
		<div class="form-group">
			<label class="col-sm-1 control-label">编码</label>
			<div class="col-sm-5">
				<?php if( ce('goods' ,$item) ) { ?>
				<input type="text" name="goodssn" id="goodssn" class="form-control hasoption" value="<?php  echo $item['goodssn'];?>" <?php  if($item['hasoption']) { ?>readonly<?php  } ?>//>
				<?php  } else { ?>
				<div class='form-control-static'><?php  echo $item['goodssn'];?></div>
				<?php  } ?>
			</div>

			<label class=" col-sm-1 control-label">条码</label>
			<div class="col-sm-5">
				<?php if( ce('goods' ,$item) ) { ?>
				<input type="text" name="productsn" id="productsn" class="form-control hasoption" value="<?php  echo $item['productsn'];?>" <?php  if($item['hasoption']) { ?>readonly<?php  } ?>//>
				<?php  } else { ?>
				<div class='form-control-static'><?php  echo $item['productsn'];?></div>
				<?php  } ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-1 control-label">重量</label>
			<div class="col-sm-11">
				<?php if( ce('goods' ,$item) ) { ?>
				<div class="input-group fixsingle-input-group">
					<input type="text" name="weight" id="weight" class="form-control hasoption" value="<?php  echo $item['weight'];?>" <?php  if($item['hasoption'] || $item['type']==3) { ?>readonly<?php  } ?>/>
					<span class="input-group-addon">克</span>
				</div>
				<?php  } else { ?>
				<div class='form-control-static'><?php  echo $item['weight'];?> 克</div>
				<?php  } ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-1 control-label">库存</label>
			<div class="col-sm-11">
				<?php if( ce('goods' ,$item) ) { ?>
				<input type="text" name="total" id="total" class="form-control hasoption" value="<?php  echo $item['total'];?>"  style="width:150px;display: inline;margin-right: 20px;" <?php  if($item['hasoption'] || $item['type']==3) { ?>readonly<?php  } ?>/>
				<label class="checkbox-inline">
					<input type="checkbox" id="showtotal" value="1" name="showtotal" <?php  if($item['showtotal']==1) { ?>checked<?php  } ?> />显示库存
				</label>
				<span class="help-block">商品的剩余数量, 如启用多规格<?php  if(com('virtual')) { ?>或为虚拟卡密产品<?php  } ?>，则此处设置无效.</span>
				<?php  } else { ?>
				<div class='form-control-static'><?php  echo $item['total'];?> 件 <?php  if(empty($item['showtotal'])) { ?>隐藏库存<?php  } else { ?>显示库存<?php  } ?></div>
				<?php  } ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-1 control-label"></label>
			<div class="col-sm-11">
				<?php if( ce('goods' ,$item) ) { ?>
				<?php  if($item['type'] == 3) { ?>
				<label for="totalcnf1" class="radio-inline"><input type="radio" name="totalcnf" value="0" id="totalcnf1" <?php  if(empty($item) || $item['totalcnf'] == 0) { ?>checked="true"<?php  } ?> /> 拍下减库存</label>
				<?php  } else { ?>
				<label for="totalcnf1" class="radio-inline"><input type="radio" name="totalcnf" value="0" id="totalcnf1" <?php  if(empty($item) || $item['totalcnf'] == 0) { ?>checked="true"<?php  } ?> /> 拍下减库存</label>
				<label for="totalcnf2" class="radio-inline"><input type="radio" name="totalcnf" value="1" id="totalcnf2"  <?php  if(!empty($item) && $item['totalcnf'] == 1) { ?>checked="true"<?php  } ?> /> 付款减库存</label>
				<label for="totalcnf3" class="radio-inline"><input type="radio" name="totalcnf" value="2" id="totalcnf3"  <?php  if(!empty($item) && $item['totalcnf'] == 2) { ?>checked="true"<?php  } ?> /> 永不减库存</label>
				<?php  } ?>
				<?php  } else { ?>
				<?php  if($item['type'] == 3) { ?>
				<div class='form-control-static'>
					<?php  if(empty($item) || $item['totalcnf'] == 0) { ?>拍下减库存<?php  } ?>
				</div>
				<?php  } else { ?>
					<div class='form-control-static'>
						<?php  if(empty($item) || $item['totalcnf'] == 0) { ?>拍下减库存<?php  } ?>
						<?php  if(!empty($item) && $item['totalcnf'] == 1) { ?>付款减库存<?php  } ?>
						<?php  if(!empty($item) && $item['totalcnf'] == 2) { ?>永不减库存<?php  } ?>
					</div>
				<?php  } ?>

				<?php  } ?>
			</div>
		</div>
	</div>

</div>



<div class="region-goods-details row">
	<div class="region-goods-left  col-sm-2">
		规格
	</div>
	<div class="region-goods-right  col-sm-10">
		<div class="form-group">
			<div class="col-sm-11" style='padding-left:30px;'>
				<?php if( ce('goods' ,$item) ) { ?>
				<label class="checkbox-inline">
					<input type="checkbox" id="hasoption" value="1" name="hasoption" <?php  if($item['hasoption']==1) { ?>checked<?php  } ?> />启用商品规格
				</label>
				<span class="help-block">启用商品规格后，商品的价格及库存以商品规格为准,库存设置为0则会到”已售罄“中，手机也不会显示, -1为不限制</span>

				<?php  } else { ?>

				<div class='form-control-static'><?php  if($item['hasoption']==1) { ?>启用<?php  } else { ?>不启用<?php  } ?> </div>

				<?php  } ?>
			</div>
		</div>

		<div id='tboption' style="padding-left:15px;<?php  if($item['hasoption']!=1) { ?>display:none<?php  } ?>" >
			<div class="alert alert-info">
				1. 拖动规格可调整规格显示顺序, 更改规格及规格项后请点击下方的【刷新规格项目表】来更新数据。<br/>
				2. 每一种规格代表不同型号，例如颜色为一种规格，尺寸为一种规格，如果设置多规格，手机用户必须每一种规格都选择一个规格项，才能添加购物车或购买。
			</div>
			<div id='specs'>
				<?php  if(is_array($allspecs)) { foreach($allspecs as $spec) { ?>
				<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods/tpl/spec', TEMPLATE_INCLUDEPATH)) : (include template('goods/tpl/spec', TEMPLATE_INCLUDEPATH));?>
				<?php  } } ?>
			</div>
			<?php if( ce('goods' ,$item) ) { ?>
			<table class="table">
				<tr>
					<td>
						<h4><a href="javascript:;" class='btn btn-primary' id='add-spec' onclick="addSpec()" style="margin-top:10px;margin-bottom:10px;" title="添加规格"><i class='fa fa-plus'></i> 添加规格</a>
							<a href="javascript:;" onclick="refreshOptions();" title="刷新规格项目表" class="btn btn-primary"><i class="fa fa-refresh"></i> 刷新规格项目表</a></h4>
					</td>
				</tr>
				<tr style="display: none;" id="optiontip">
					<td>
						<div class="alert alert-danger">警告：规格数据有变动，请重新点击上方 [刷新规格项目表] 按钮！</div>
					</td>
				</tr>
			</table>
			<?php  } ?>
			<div class="alert alert-info wholesalewarning"  <?php  if($item['type']!=4) { ?>  style="display: none"<?php  } ?>>
			1. 批发商品设置多规格时,无需设置价格参数(现价,原价,成本价,预售价),当商品保存时会自动获取第一级批发价作为不同规格商品的统一价格!
		</div>
		<div id="options" style="padding:0;"><?php  echo $html;?></div>
	</div>

	<div id="modal-module-chooestemp" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:600px;margin:0px auto;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
					<h3>选择虚拟物品模板</h3>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label class="col-sm-2 control-label" style='width: 150px'>选择模板:</label>
						<div class="col-sm-9 col-xs-12" style='width: 380px'>
							<select class="form-control tpl-category-parent">
								<?php  if(is_array($virtual_types)) { foreach($virtual_types as $virtual_type) { ?>
								<option value="<?php  echo $virtual_type['id'];?>"><?php  echo $virtual_type['usedata'];?>/<?php  echo $virtual_type['alldata'];?> | <?php  echo $virtual_type['title'];?></option>
								<?php  } } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<span class="btn btn-primary span2" onclick="addtemp()">确认选择</span>
					<a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
<input type="hidden" name="optionArray" value=''>
<input type="hidden" name="isdiscountDiscountsArray" value=''>
<input type="hidden" name="discountArray" value=''>
<input type="hidden" name="commissionArray" value=''>
<script language="javascript">
	$(function(){
		$(document).on('input propertychange change', '#specs input', function () {
			// 改变规格锁定提交
			window.optionchanged = true;
			$('#optiontip').show();
		});


		$(".spec_item_thumb").find('i').click(function(){
			var group  =$(this).parent();
			group.find('img').attr('src',"<?php echo EWEI_SHOPV2_LOCAL;?>static/images/nopic100.jpg");
			group.find(':hidden').val('');
			$(this).hide();
			group.find('img').popover('destroy');
		});

			require(['jquery.ui'],function(){
			$('#specs').sortable({
				stop: function(){
					refreshOptions();
				}
			});
			$('.spec_item_items').sortable(
			{
						handle:'.fa-arrows',
						stop: function(){
							refreshOptions();
						}
					}
			);
	    });
		$("#hasoption").click(function(){
			var obj = $(this);
			if (obj.get(0).checked){
				$('#goodssn').attr('readonly',true);
				$('#productsn').attr('readonly',true);
				$('#weight').attr('readonly',true);
				$('#total').attr('readonly',true);

				$("#tboption").show();
				$("#tbdiscount").show();
				$("#isdiscount_discounts").show();
				$("#isdiscount_discounts_default").hide();
				$("#commission").show();
				$("#commission_default").hide();
				$("#discounts_type1").show().parent().show();
				refreshOptions();
			}else{
				$("#tboption").hide();
				refreshOptions();

				$("#isdiscount_discounts").hide();
				var isdiscount_discounts = $("#isdiscount_discounts").html();
				$("#isdiscount_discounts").html('');
				isdiscount_change();
				$("#isdiscount_discounts").html(isdiscount_discounts);

				<?php  if(p('commission') && !empty($com_set['level'])) { ?>
				$("#commission").hide();
				var commission = $("#commission").html();
				$("#commission").html('');
				commission_change();
				$("#commission").html(commission);
				<?php  } ?>

				$("#tbdiscount").hide();
				$("#isdiscount_discounts_default").show();

				$("#commission_default").show();

                $('#goodssn').removeAttr('readonly');
                $('#productsn').removeAttr('readonly');

                // 商品类型如果为虚拟卡密则不允许修改库存
                if(type !=3){
                    $('#weight').removeAttr('readonly');
                    $('#total').removeAttr('readonly');
				}
				$("#discounts_type1").hide().parent().hide();
				$("#discounts_type0").click();
			}
		});
	});
	function selectSpecItemImage(obj){
		util.image('',function(val){
			$(obj).attr('src',val.url).popover({
				trigger: 'hover',
				html: true,
				container: $(document.body),
				content: "<img src='" + val.url  + "' style='width:100px;height:100px;' />",
				placement: 'top'
			});

			var group  =$(obj).parent();

			group.find(':hidden').val(val.attachment), group.find('i').show().unbind('click').click(function(){
				$(obj).attr('src',"<?php echo EWEI_SHOPV2_LOCAL;?>static/images/nopic100.jpg");
				group.find(':hidden').val('');
				group.find('i').hide();
				$(obj).popover('destroy');
			});
		});
	}
	function addSpec(){
                    var len = $(".spec_item").length;

                    if(type==3 && virtual==0 && len>=1){
						tip.msgbox.err('您的商品类型为：虚拟物品(卡密)的多规格形式，只能添加一种规格！');
                        return;
                    }

					if(type==4 && virtual==0 && len>=2){
						tip.msgbox.err('您的商品类型为：批发商品的多规格形式，只能添加两种规格！');
						return;
					}

					if(type==10 && len>=1){
						tip.msgbox.err('您的商品类型为：话费流量充值，只能添加一种规格！')
						return;
					}

	         $("#add-spec").html("正在处理...").attr("disabled", "true").toggleClass("btn-primary");
		var url = "<?php  echo webUrl('goods/tpl',array('tpl'=>'spec'))?>";
		$.ajax({
			"url": url,
			success:function(data){
				$("#add-spec").html('<i class="fa fa-plus"></i> 添加规格').removeAttr("disabled").toggleClass("btn-primary"); ;
				$('#specs').append(data);
				var len = $(".add-specitem").length -1;
				$(".add-specitem:eq(" +len+ ")").focus();
                                        refreshOptions();
			}
		});
	}
	function removeSpec(specid){
		if (confirm('确认要删除此规格?')){
			$("#spec_" + specid).remove();
			refreshOptions();
		}
	}
	function addSpecItem(specid){
	         $("#add-specitem-" + specid).html("正在处理...").attr("disabled", "true");
		var url = "<?php  echo webUrl('goods/tpl',array('tpl'=>'specitem'))?>" + "&specid=" + specid;
		$.ajax({
			"url": url,
			success:function(data){
				$("#add-specitem-" + specid).html('<i class="fa fa-plus"></i> 添加规格项').removeAttr("disabled");
				$('#spec_item_' + specid).append(data);
				var len = $("#spec_" + specid + " .spec_item_title").length -1;
				$("#spec_" + specid + " .spec_item_title:eq(" +len+ ")").focus();
				refreshOptions();
				if(type==3 && virtual==0){
					$(".choosetemp").show();
				}
			}
		});
	}
	function removeSpecItem(obj){
		$(obj).closest('.spec_item_item').remove();
		refreshOptions();
	}

	function refreshOptions(){
		// 刷新后重置
		window.optionchanged = false;
		$('#optiontip').hide();


 var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
	var specs = [];
         if($('.spec_item').length<=0){
             $("#options").html('');
			 $("#discount").html('');
			 $("#isdiscount_discounts").html('');
			 $("#commission").html('');
			 <?php  if(p('commission') && !empty($com_set['level'])) { ?>
			 	commission_change();
			 <?php  } ?>
			 	isdiscount_change();
             return;
         }
	$(".spec_item").each(function(i){
		var _this = $(this);

		var spec = {
			id: _this.find(".spec_id").val(),
			title: _this.find(".spec_title").val()
		};

		var items = [];
		_this.find(".spec_item_item").each(function(){
			var __this = $(this);
			var item = {
				id: __this.find(".spec_item_id").val(),
				title: __this.find(".spec_item_title").val(),
                                                                        virtual: __this.find(".spec_item_virtual").val(),
				show:__this.find(".spec_item_show").get(0).checked?"1":"0"
			}
			items.push(item);
		});
		spec.items = items;
		specs.push(spec);
	});
	specs.sort(function(x,y){
		if (x.items.length > y.items.length){
			return 1;
		}
		if (x.items.length < y.items.length) {
			return -1;
		}
	});

	var len = specs.length;
	var newlen = 1;
	var h = new Array(len);
	var rowspans = new Array(len);
	for(var i=0;i<len;i++){
		html+="<th>" + specs[i].title + "</th>";
		var itemlen = specs[i].items.length;
		if(itemlen<=0) { itemlen = 1 };
		newlen*=itemlen;

		h[i] = new Array(newlen);
		for(var j=0;j<newlen;j++){
			h[i][j] = new Array();
		}
		var l = specs[i].items.length;
		rowspans[i] = 1;
		for(j=i+1;j<len;j++){
			rowspans[i]*= specs[j].items.length;
		}
	}

	/*商品类型如果为虚拟卡密则不允许修改库存*/
	if(type==3){
        html += '<th><div class=""><div style="padding-bottom:10px;text-align:center;">库存</div><div class="input-group"><input type="text" class="form-control  input-sm option_stock_all" readonly="readonly" VALUE=""/><span class="input-group-addon disabled"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置"></a></span></div></div></th>';
	}else{
        html += '<th><div class=""><div style="padding-bottom:10px;text-align:center;">库存</div><div class="input-group"><input type="text" class="form-control  input-sm option_stock_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';
	}

	html += '<th class="type-4"><div class=""><div style="padding-bottom:10px;text-align:center;">预售价</div><div class="input-group"><input type="text" class="form-control  input-sm option_presell_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_presell\');"></a></span></div></div></th>';
	html += '<th class="type-4"><div class=""><div style="padding-bottom:10px;text-align:center;">现价</div><div class="input-group"><input type="text" class="form-control  input-sm option_marketprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div></div></th>';
	html+='<th class="type-4"><div class=""><div style="padding-bottom:10px;text-align:center;">原价</div><div class="input-group"><input type="text" class="form-control  input-sm option_productprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></div></th>';
	html+='<th class="type-4"><div class=""><div style="padding-bottom:10px;text-align:center;">成本价</div><div class="input-group"><input type="text" class="form-control  input-sm option_costprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_costprice\');"></a></span></div></div></th>';
	html+='<th><div class=""><div style="padding-bottom:10px;text-align:center;">编码</div><div class="input-group"><input type="text" class="form-control  input-sm option_goodssn_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_goodssn\');"></a></span></div></div></th>';
	html+='<th><div class=""><div style="padding-bottom:10px;text-align:center;">条码</div><div class="input-group"><input type="text" class="form-control  input-sm option_productsn_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_productsn\');"></a></span></div></div></th>';
	html+='<th><div class=""><div style="padding-bottom:10px;text-align:center;">重量（克）</div><div class="input-group"><input type="text" class="form-control  input-sm option_weight_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></div></th>';
	html+='</tr></thead>';

	for(var m=0;m<len;m++){
		var k = 0,kid = 0,n=0;
		for(var j=0;j<newlen;j++){
			var rowspan = rowspans[m];
            var spec_item = specs[m].items[kid] || {};
            var spec_item_title = spec_item.title;
            if(!spec_item_title || spec_item_title == 'undefined'){
                spec_item_title = '';
            }
			if( j % rowspan==0){
			    h[m][j]={title: spec_item_title, virtual: spec_item.virtual,html: "<td class='full' rowspan='" +rowspan + "'>"+ spec_item_title+"</td>\r\n",id: spec_item.id};
			}
			else{
			    h[m][j]={title:spec_item_title,virtual: spec_item.virtual, html: "",id: spec_item.id};
			}
			n++;
			if(n==rowspan){
				kid++; if(kid>specs[m].items.length-1) { kid=0; }
				n=0;
			}
		}
	}
	var hh = "";
	for(var i=0;i<newlen;i++){
		if (i != 0) {
			hh+="<tr style='border-top: 1px solid #eee'>";
		} else {
			hh+="<tr>";
		}

		var ids = [];
		var titles = [];
		var virtuals = [];
		for(var j=0;j<len;j++){
			hh+=h[j][i].html;
			ids.push( h[j][i].id);
			titles.push( h[j][i].title);
		    virtuals.push( h[j][i].virtual);
		}

        var sortarr  = permute([],ids);
        titles= titles.join('+');
		ids = ids.join('_');
		var val ={ id : "",title:titles, stock : "",presell : "",costprice : "",productprice : "",marketprice : "",weight:"",productsn:"",goodssn:"",virtual:virtuals };
		for(var kkk=0;kkk<sortarr.length;kkk++) {
		    var sids = sortarr[kkk].join('_');
            if ($(".option_id_" + sids).length > 0) {
                val = {
                    id: $(".option_id_" + sids + ":eq(0)").val(),
                    title: titles,
                    stock: $(".option_stock_" + sids + ":eq(0)").val(),
                    presell: $(".option_presell_" + sids + ":eq(0)").val(),
                    costprice: $(".option_costprice_" + sids + ":eq(0)").val(),
                    productprice: $(".option_productprice_" + sids + ":eq(0)").val(),
                    marketprice: $(".option_marketprice_" + sids + ":eq(0)").val(),
                    goodssn: $(".option_goodssn_" + sids + ":eq(0)").val(),
                    productsn: $(".option_productsn_" + sids + ":eq(0)").val(),
                    weight: $(".option_weight_" + sids + ":eq(0)").val(),
                    virtual: virtuals
                }
                break;
            }
        }
		hh += '<td>'
        //  商品类型如果为虚拟卡密则不允许修改库存
        if(type==3){
            hh += '<input data-name="option_stock_' + ids +'" type="text" class="form-control option_stock option_stock_' + ids +'" readonly="readonly"  value=""/></td>';
        }else{
            hh += '<input data-name="option_stock_' + ids +'" type="text" class="form-control option_stock option_stock_' + ids +'" value="' +(val.stock=='undefined'?'':val.stock )+'"/></td>';
		}
		hh += '<input data-name="option_id_' + ids+'" type="hidden" class="form-control option_id option_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
		hh += '<input data-name="option_ids" type="hidden" class="form-control option_ids option_ids_' + ids +'" value="' + ids +'"/>';
		hh += '<input data-name="option_title_' + ids +'" type="hidden" class="form-control option_title option_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
        hh += '<input data-name="option_virtual_' + ids +'" type="hidden" class="form-control option_virtual option_virtual_' + ids +'" value="' +(val.virtual=='undefined'?'':val.virtual )+'"/></td>';
		hh += '</td>';
		hh += '<td class="type-4"><input data-name="option_presell_' + ids+'" type="text" class="form-control option_presell option_presell_' + ids +'" value="' +(val.presell=='undefined'?'':val.presell )+'"/></td>';
		hh += '<td class="type-4"><input data-name="option_marketprice_' + ids+'" type="text" class="form-control option_marketprice option_marketprice_' + ids +'" value="' +(val.marketprice=='undefined'?'':val.marketprice )+'"/></td>';
		hh += '<td class="type-4"><input data-name="option_productprice_' + ids+'" type="text" class="form-control option_productprice option_productprice_' + ids +'" " value="' +(val.productprice=='undefined'?'':val.productprice )+'"/></td>';
		hh += '<td class="type-4"><input data-name="option_costprice_' +ids+'" type="text" class="form-control option_costprice option_costprice_' + ids +'" " value="' +(val.costprice=='undefined'?'':val.costprice )+'"/></td>';
		hh += '<td><input data-name="option_goodssn_' +ids+'" type="text" class="form-control option_goodssn option_goodssn_' + ids +'" " value="' +(val.goodssn=='undefined'?'':val.goodssn )+'"/></td>';
		hh += '<td><input data-name="option_productsn_' +ids+'" type="text" class="form-control option_productsn option_productsn_' + ids +'" " value="' +(val.productsn=='undefined'?'':val.productsn )+'"/></td>';
		hh += '<td><input data-name="option_weight_' + ids +'" type="text" class="form-control option_weight option_weight_' + ids +'" " value="' +(val.weight=='undefined'?'':val.weight )+'"/></td>';
		hh += "</tr>";
	}
	html+=hh;
	html+="</table>";
	$("#options").html(html);
		refreshDiscount();
		refreshIsDiscount();
		<?php  if(p('commission') && !empty($com_set['level'])) { ?>
		refreshCommission();
		commission_change();
		<?php  } ?>
		isdiscount_change();

		if(window.type=='4'){
			$('.type-4').hide();
		}else{
			$('.type-4').show();
		}
}
             function permute(temArr,testArr){
                 var permuteArr=[];
                 var arr = testArr;
                 function innerPermute(temArr){
                     for(var i=0,len=arr.length; i<len; i++) {
                         if(temArr.length == len - 1) {
                             if(temArr.indexOf(arr[i]) < 0) {
                                 permuteArr.push(temArr.concat(arr[i]));
                             }
                             continue;
                         }
                         if(temArr.indexOf(arr[i]) < 0) {
                             innerPermute(temArr.concat(arr[i]));
                         }
                     }
                 }
                 innerPermute(temArr);
                 return permuteArr;
             }
	function refreshDiscount() {
		var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
		var specs = [];

		$(".spec_item").each(function (i) {
			var _this = $(this);

			var spec = {
				id: _this.find(".spec_id").val(),
				title: _this.find(".spec_title").val()
			};

			var items = [];
			_this.find(".spec_item_item").each(function () {
				var __this = $(this);
				var item = {
					id: __this.find(".spec_item_id").val(),
					title: __this.find(".spec_item_title").val(),
					virtual: __this.find(".spec_item_virtual").val(),
					show: __this.find(".spec_item_show").get(0).checked ? "1" : "0"
				};
				items.push(item);
			});
			spec.items = items;
			specs.push(spec);
		});
		specs.sort(function (x, y) {
			if (x.items.length > y.items.length) {
				return 1;
			}
			if (x.items.length < y.items.length) {
				return -1;
			}
		});

		var len = specs.length;
		var newlen = 1;
		var h = new Array(len);
		var rowspans = new Array(len);
		for (var i = 0; i < len; i++) {
			html += "<th>" + specs[i].title + "</th>";
			var itemlen = specs[i].items.length;
			if (itemlen <= 0) {
				itemlen = 1
			}
			;
			newlen *= itemlen;

			h[i] = new Array(newlen);
			for (var j = 0; j < newlen; j++) {
				h[i][j] = new Array();
			}
			var l = specs[i].items.length;
			rowspans[i] = 1;
			for (j = i + 1; j < len; j++) {
				rowspans[i] *= specs[j].items.length;
			}
		}

		<?php  if(is_array($levels)) { foreach($levels as $level) { ?>
		<?php  if($level['key']=='default') { ?>
		html += '<th><div class=""><div style="padding-bottom:10px;text-align:center;"><?php  echo $level['levelname'];?></div><div class="input-group"><input type="text" class="form-control  input-sm discount_<?php  echo $level["key"];?>_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'discount_<?php  echo $level["key"];?>\');"></a></span></div></div></th>';
		<?php  } else { ?>
		html += '<th><div class=""><div style="padding-bottom:10px;text-align:center;"><?php  echo $level['levelname'];?></div><div class="input-group"><input type="text" class="form-control  input-sm discount_level<?php  echo $level['id'];?>_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'discount_level<?php  echo $level['id'];?>\');"></a></span></div></div></th>';
		<?php  } ?>
		<?php  } } ?>
		html += '</tr></thead>';

		for (var m = 0; m < len; m++) {
			var k = 0, kid = 0, n = 0;
			for (var j = 0; j < newlen; j++) {
				var rowspan = rowspans[m];
                var spec_item = specs[m].items[kid] || {};
                var spec_item_title = spec_item.title;
                if(!spec_item_title || spec_item_title == 'undefined'){
                    spec_item_title = '';
                }
				if (j % rowspan == 0) {
				    h[m][j] = {
						title: spec_item_title,
						virtual: spec_item.virtual,
						html: "<td class='full' rowspan='" + rowspan + "'>" + spec_item_title + "</td>\r\n",
						id: spec_item.id
					};
				}
				else {
				    h[m][j] = {
						title: spec_item_title,
						virtual: spec_item.virtual,
						html: "",
						id: spec_item.id
					};
				}
				n++;
				if (n == rowspan) {
					kid++;
					if (kid > specs[m].items.length - 1) {
						kid = 0;
					}
					n = 0;
				}
			}
		}

		var hh = "";
		for (var i = 0; i < newlen; i++) {
			hh += "<tr>";
			var ids = [];
			var titles = [];
			var virtuals = [];
			for (var j = 0; j < len; j++) {
				hh += h[j][i].html;
				ids.push(h[j][i].id);
				titles.push(h[j][i].title);
				virtuals.push(h[j][i].virtual);
			}
			ids = ids.join('_');
			titles = titles.join('+');
			var val = {
				id: "",
				title: titles,
				<?php  if(is_array($levels)) { foreach($levels as $level) { ?>
				<?php  if($level['key']=='default') { ?>
				level<?php  echo $level['key'];?>: '',
				<?php  } else { ?>
				level<?php  echo $level['id'];?>: '',
				<?php  } ?>
				<?php  } } ?>
				costprice: "",
				presell: "",
				productprice: "",
				marketprice: "",
				weight: "",
				productsn: "",
				goodssn: "",
				virtual: virtuals
			};

			var val ={ id : "",title:titles,<?php  if(is_array($levels)) { foreach($levels as $level) { ?><?php  if($level['key']=='default') { ?> level<?php  echo $level['key'];?>: '',<?php  } else { ?> level<?php  echo $level['id'];?>: '',<?php  } ?><?php  } } ?>costprice : "",productprice : "",marketprice : "",weight:"",productsn:"",goodssn:"",virtual:virtuals };
			if ($(".discount_id_" + ids).length > 0) {
				val = {
					id: $(".discount_id_" + ids + ":eq(0)").val(),
					title: titles,
					<?php  if(is_array($levels)) { foreach($levels as $level) { ?>
				<?php  if($level['key']=='default') { ?>
					level<?php  echo $level['key'];?>: $(".discount_<?php  echo $level['key'];?>_" + ids + ":eq(0)").val(),
				<?php  } else { ?>
					level<?php  echo $level['id'];?>: $(".discount_level<?php  echo $level['id'];?>_" + ids + ":eq(0)").val(),
				<?php  } ?>
					<?php  } } ?>
					costprice: $(".discount_costprice_" + ids + ":eq(0)").val(),
					presell: $(".discount_presell_" + ids + ":eq(0)").val(),
					productprice: $(".discount_productprice_" + ids + ":eq(0)").val(),
					marketprice: $(".discount_marketprice_" + ids + ":eq(0)").val(),
					presell: $(".discount_presell_" + ids + ":eq(0)").val(),
					goodssn: $(".discount_goodssn_" + ids + ":eq(0)").val(),
					productsn: $(".discount_productsn_" + ids + ":eq(0)").val(),
					weight: $(".discount_weight_" + ids + ":eq(0)").val(),
					virtual: virtuals
				}
			}

			<?php  if(is_array($levels)) { foreach($levels as $level) { ?>
			hh += '<td>'
			<?php  if($level['key']=='default') { ?>
			hh += '<input data-name="discount_level_<?php  echo $level['key'];?>_' + ids +'"type="text" class="form-control discount_<?php  echo $level['key'];?> discount_<?php  echo $level['key'];?>_' + ids +'" value="' +(val.level<?php  echo $level['key'];?>=='undefined'?'':val.level<?php  echo $level['key'];?> )+'"/>';
			<?php  } else { ?>
			hh += '<input data-name="discount_level_<?php  echo $level['id'];?>_' + ids +'"type="text" class="form-control discount_level<?php  echo $level['id'];?> discount_level<?php  echo $level['id'];?>_' + ids +'" value="' +(val.level<?php  echo $level['id'];?>=='undefined'?'':val.level<?php  echo $level['id'];?> )+'"/>';
			<?php  } ?>
			hh += '</td>';
			<?php  } } ?>
			hh += '<input data-name="discount_id_' + ids+'"type="hidden" class="form-control discount_id discount_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
			hh += '<input data-name="discount_ids"type="hidden" class="form-control discount_ids discount_ids_' + ids +'" value="' + ids +'"/>';
			hh += '<input data-name="discount_title_' + ids +'"type="hidden" class="form-control discount_title discount_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
			hh += '<input data-name="discount_virtual_' + ids +'"type="hidden" class="form-control discount_virtual discount_virtual_' + ids +'" value="' +(val.virtual=='undefined'?'':val.virtual )+'"/></td>';
			hh += "</tr>";
		}
		html += hh;
		html += "</table>";
		$("#discount").html(html);
	}

	function refreshIsDiscount() {
		var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
		var specs = [];

		$(".spec_item").each(function (i) {
			var _this = $(this);

			var spec = {
				id: _this.find(".spec_id").val(),
				title: _this.find(".spec_title").val()
			};

			var items = [];
			_this.find(".spec_item_item").each(function () {
				var __this = $(this);
				var item = {
					id: __this.find(".spec_item_id").val(),
					title: __this.find(".spec_item_title").val(),
					virtual: __this.find(".spec_item_virtual").val(),
					show: __this.find(".spec_item_show").get(0).checked ? "1" : "0"
				}
				items.push(item);
			});
			spec.items = items;
			specs.push(spec);
		});
		specs.sort(function (x, y) {
			if (x.items.length > y.items.length) {
				return 1;
			}
			if (x.items.length < y.items.length) {
				return -1;
			}
		});

		var len = specs.length;
		var newlen = 1;
		var h = new Array(len);
		var rowspans = new Array(len);
		for (var i = 0; i < len; i++) {
			html += "<th>" + specs[i].title + "</th>";
			var itemlen = specs[i].items.length;
			if (itemlen <= 0) {
				itemlen = 1
			}
			;
			newlen *= itemlen;

			h[i] = new Array(newlen);
			for (var j = 0; j < newlen; j++) {
				h[i][j] = new Array();
			}
			var l = specs[i].items.length;
			rowspans[i] = 1;
			for (j = i + 1; j < len; j++) {
				rowspans[i] *= specs[j].items.length;
			}
		}

		<?php  if(is_array($levels)) { foreach($levels as $level) { ?>
		<?php  if($level['key']=='default') { ?>
		html += '<th><div class=""><div style="padding-bottom:10px;text-align:center;"><?php  echo $level['levelname'];?></div><div class="input-group"><input type="text" class="form-control  input-sm isdiscount_discounts_<?php  echo $level['key'];?>_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'isdiscount_discounts_<?php  echo $level['key'];?>\');"></a></span></div></div></th>';
		<?php  } else { ?>
		html += '<th><div class=""><div style="padding-bottom:10px;text-align:center;"><?php  echo $level['levelname'];?></div><div class="input-group"><input type="text" class="form-control  input-sm isdiscount_discounts_level<?php  echo $level['id'];?>_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'isdiscount_discounts_level<?php  echo $level['id'];?>\');"></a></span></div></div></th>';
		<?php  } ?>
		<?php  } } ?>
		html += '</tr></thead>';

		for (var m = 0; m < len; m++) {
			var k = 0, kid = 0, n = 0;
			for (var j = 0; j < newlen; j++) {
				var rowspan = rowspans[m];
                var spec_item = specs[m].items[kid] || {};
                var spec_item_title = spec_item.title;
                if(!spec_item_title || spec_item_title == 'undefined'){
                    spec_item_title = '';
                }
				if (j % rowspan == 0) {
					h[m][j] = {
						title: spec_item_title,
						virtual: spec_item.virtual,
						html: "<td class='full' rowspan='" + rowspan + "'>" + spec_item_title + "</td>\r\n",
						id: spec_item.id
					};
				}
				else {
					h[m][j] = {
						title: spec_item_title,
						virtual: spec_item.virtual,
						html: "",
						id: spec_item.id
					};
				}
				n++;
				if (n == rowspan) {
					kid++;
					if (kid > specs[m].items.length - 1) {
						kid = 0;
					}
					n = 0;
				}
			}
		}

		var hh = "";
		for (var i = 0; i < newlen; i++) {
			hh += "<tr>";
			var ids = [];
			var titles = [];
			var virtuals = [];
			for (var j = 0; j < len; j++) {
				hh += h[j][i].html;
				ids.push(h[j][i].id);
				titles.push(h[j][i].title);
				virtuals.push(h[j][i].virtual);
			}
			ids = ids.join('_');
			titles = titles.join('+');
			var val = {
				id: "",
				title: titles,
			<?php  if(is_array($levels)) { foreach($levels as $level) { ?>
			<?php  if($level['key']=='default') { ?>
			level<?php  echo $level['key'];?>: '',
			<?php  } else { ?>
			level<?php  echo $level['if'];?>: '',
			<?php  } ?>
			<?php  } } ?>
			costprice: "",
					presell: "",
					productprice: "",
					marketprice: "",
					weight: "",
					productsn: "",
					goodssn: "",
					virtual: virtuals
		};

			var val ={ id : "",title:titles,<?php  if(is_array($levels)) { foreach($levels as $level) { ?><?php  if($level['key']=='default') { ?> level<?php  echo $level['key'];?>: '',<?php  } else { ?> level<?php  echo $level['id'];?>: '',<?php  } ?><?php  } } ?>costprice : "",productprice : "",marketprice : "",weight:"",productsn:"",goodssn:"",virtual:virtuals };
			if ($(".isdiscount_discounts_id_" + ids).length > 0) {
				val = {
					id: $(".isdiscount_discounts_id_" + ids + ":eq(0)").val(),
					title: titles,
				<?php  if(is_array($levels)) { foreach($levels as $level) { ?>
				<?php  if($level['key']=='default') { ?>
				level<?php  echo $level['key'];?>: $(".isdiscount_discounts_<?php  echo $level['key'];?>_" + ids + ":eq(0)").val(),
				<?php  } else { ?>
				level<?php  echo $level['id'];?>: $(".isdiscount_discounts_level<?php  echo $level['id'];?>_" + ids + ":eq(0)").val(),
				<?php  } ?>
				<?php  } } ?>
				costprice: $(".isdiscount_discounts_costprice_" + ids + ":eq(0)").val(),
						productprice: $(".isdiscount_discounts_productprice_" + ids + ":eq(0)").val(),
						marketprice: $(".isdiscount_discounts_marketprice_" + ids + ":eq(0)").val(),
						presell: $(".isdiscount_discounts_presell_" + ids + ":eq(0)").val(),
						goodssn: $(".isdiscount_discounts_goodssn_" + ids + ":eq(0)").val(),
						productsn: $(".isdiscount_discounts_productsn_" + ids + ":eq(0)").val(),
						weight: $(".isdiscount_discounts_weight_" + ids + ":eq(0)").val(),
						virtual: virtuals
			}
			}

			<?php  if(is_array($levels)) { foreach($levels as $level) { ?>
			hh += '<td>'
			<?php  if($level['key']=='default') { ?>
			hh += '<input data-name="isdiscount_discounts_level_<?php  echo $level['key'];?>_' + ids +'"type="text" class="form-control isdiscount_discounts_<?php  echo $level['key'];?> isdiscount_discounts_<?php  echo $level['key'];?>_' + ids +'" value="' +(val.level<?php  echo $level['key'];?>=='undefined'?'':val.level<?php  echo $level['key'];?> )+'"/>';
			<?php  } else { ?>
			hh += '<input data-name="isdiscount_discounts_level_<?php  echo $level['id'];?>_' + ids +'"type="text" class="form-control isdiscount_discounts_level<?php  echo $level['id'];?> isdiscount_discounts_level<?php  echo $level['id'];?>_' + ids +'" value="' +(val.level<?php  echo $level['id'];?>=='undefined'?'':val.level<?php  echo $level['id'];?> )+'"/>';
			<?php  } ?>
			hh += '</td>';
			<?php  } } ?>
			hh += '<input data-name="isdiscount_discounts_id_' + ids+'"type="hidden" class="form-control isdiscount_discounts_id isdiscount_discounts_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
			hh += '<input data-name="isdiscount_discounts_ids"type="hidden" class="form-control isdiscount_discounts_ids isdiscount_discounts_ids_' + ids +'" value="' + ids +'"/>';
			hh += '<input data-name="isdiscount_discounts_title_' + ids +'"type="hidden" class="form-control isdiscount_discounts_title isdiscount_discounts_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
			hh += '<input data-name="isdiscount_discounts_virtual_' + ids +'"type="hidden" class="form-control isdiscount_discounts_virtual isdiscount_discounts_virtual_' + ids +'" value="' +(val.virtual=='undefined'?'':val.virtual )+'"/></td>';
			hh += "</tr>";
		}
		html += hh;
		html += "</table>";
		$("#isdiscount_discounts").html(html);
	}

	function refreshCommission() {
		var commission_level = <?php  echo json_encode($commission_level)?>;
		var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
		var specs = [];

		$(".spec_item").each(function (i) {
			var _this = $(this);

			var spec = {
				id: _this.find(".spec_id").val(),
				title: _this.find(".spec_title").val()
			};

			var items = [];
			_this.find(".spec_item_item").each(function () {
				var __this = $(this);
				var item = {
					id: __this.find(".spec_item_id").val(),
					title: __this.find(".spec_item_title").val(),
					virtual: __this.find(".spec_item_virtual").val(),
					show: __this.find(".spec_item_show").get(0).checked ? "1" : "0"
				}
				items.push(item);
			});
			spec.items = items;
			specs.push(spec);
		});
		specs.sort(function (x, y) {
			if (x.items.length > y.items.length) {
				return 1;
			}
			if (x.items.length < y.items.length) {
				return -1;
			}
		});

		var len = specs.length;
		var newlen = 1;
		var h = new Array(len);
		var rowspans = new Array(len);
		for (var i = 0; i < len; i++) {
			html += "<th>" + specs[i].title + "</th>";
			var itemlen = specs[i].items.length;
			if (itemlen <= 0) {
				itemlen = 1
			}
			;
			newlen *= itemlen;

			h[i] = new Array(newlen);
			for (var j = 0; j < newlen; j++) {
				h[i][j] = new Array();
			}
			var l = specs[i].items.length;
			rowspans[i] = 1;
			for (j = i + 1; j < len; j++) {
				rowspans[i] *= specs[j].items.length;
			}
		}

		$.each(commission_level,function (key,level) {
			html += '<th><div class=""><div style="padding-bottom:10px;text-align:center;">'+level.levelname+'</div></div></th>';
		})
		html += '</tr></thead>';

		for (var m = 0; m < len; m++) {
			var k = 0, kid = 0, n = 0;
			for (var j = 0; j < newlen; j++) {
				var rowspan = rowspans[m];
                var spec_item = specs[m].items[kid] || {};
                var spec_item_title = spec_item.title;
                if(!spec_item_title || spec_item_title == 'undefined'){
                    spec_item_title = '';
                }
				if (j % rowspan == 0) {
					h[m][j] = {
						title: spec_item_title,
						virtual: spec_item.virtual,
						html: "<td class='full' rowspan='" + rowspan + "'>" + spec_item_title + "</td>\r\n",
						id: spec_item.id
					};
				}
				else {
					h[m][j] = {
						title: spec_item_title,
						virtual: spec_item.virtual,
						html: "",
						id: spec_item.id
					};
				}
				n++;
				if (n == rowspan) {
					kid++;
					if (kid > specs[m].items.length - 1) {
						kid = 0;
					}
					n = 0;
				}
			}
		}
		var hh = "";
		for (var i = 0; i < newlen; i++) {
			hh += "<tr>";
			var ids = [];
			var titles = [];
			var virtuals = [];
			for (var j = 0; j < len; j++) {
				hh += h[j][i].html;
				ids.push(h[j][i].id);
				titles.push(h[j][i].title);
				virtuals.push(h[j][i].virtual);
			}
			ids = ids.join('_');
			titles = titles.join('+');

			var val = {
				id: "",
				title: titles,
			<?php  if(is_array($commission_level)) { foreach($commission_level as $level) { ?>
			<?php  if($level["key"] == "default") { ?>
			level<?php  echo $level['key'];?>: '',
			<?php  } else { ?>
			level<?php  echo $level['id'];?>: '',
			<?php  } ?>
			<?php  } } ?>
			costprice: "",
					presell: "",
					productprice: "",
					marketprice: "",
					weight: "",
					productsn: "",
					goodssn: "",
					virtual: virtuals
		};

			var val ={ id : "",title:titles,<?php  if(is_array($commission_level)) { foreach($commission_level as $level) { ?> <?php  if($level["key"] == "default") { ?>level<?php  echo $level['key'];?>: '',<?php  } else { ?>level<?php  echo $level['id'];?>: '',<?php  } ?><?php  } } ?>costprice : "",productprice : "",marketprice : "",weight:"",productsn:"",goodssn:"",virtual:virtuals };
			<?php  if(is_array($commission_level)) { foreach($commission_level as $level) { ?>
			<?php  if($level["key"] == "default") { ?>
			var level<?php  echo $level['key'];?> = new Array(3);
			$(".commission_<?php  echo $level['key'];?>_"+ ids).each(function(index,val){
				level<?php  echo $level['key'];?>[index] = val;
			})
			<?php  } else { ?>
			var level<?php  echo $level['id'];?> = new Array(3);
			$(".commission_level<?php  echo $level['id'];?>_"+ ids).each(function(index,val){
				level<?php  echo $level['id'];?>[index] = val;
			})
			<?php  } ?>
			<?php  } } ?>
			if ($(".commission_id_" + ids).length > 0) {
				val = {
					id: $(".commission_id_" + ids + ":eq(0)").val(),
					title: titles,
					costprice: $(".commission_costprice_" + ids + ":eq(0)").val(),
					presell: $(".commission_presell_" + ids + ":eq(0)").val(),
						productprice: $(".commission_productprice_" + ids + ":eq(0)").val(),
						marketprice: $(".commission_marketprice_" + ids + ":eq(0)").val(),
						goodssn: $(".commission_goodssn_" + ids + ":eq(0)").val(),
						productsn: $(".commission_productsn_" + ids + ":eq(0)").val(),
						weight: $(".commission_weight_" + ids + ":eq(0)").val(),
						virtual: virtuals
				}
			}
			<?php  if(is_array($commission_level)) { foreach($commission_level as $level) { ?>
			hh += '<td>';
			var level_temp = <?php  if($level['key'] == 'default') { ?>level<?php  echo $level['key'];?><?php  } else { ?>level<?php  echo $level['id'];?><?php  } ?>;
			if (len >= i && typeof (level_temp) != 'undefined')
			{
				if('<?php  echo $level['key'];?>' == 'default')
				{
					for (var li = 0; li<<?php  echo $shopset_level;?>;li++)
					{
						if (typeof (level_temp[li])!= "undefined")
						{
							hh += '<input data-name="commission_level_<?php  echo $level['key'];?>_' +ids+ '"  type="text" class="form-control commission_<?php  echo $level['key'];?> commission_<?php  echo $level['key'];?>_' +ids+ '" value="' +$(level_temp[li]).val()+ '" style="display:inline;width: '+96/parseInt(<?php  echo $shopset_level;?>)+'%;"/> ';
						}
						else
						{
							hh += '<input data-name="commission_level_<?php  echo $level['key'];?>_' +ids+ '"  type="text" class="form-control commission_<?php  echo $level['key'];?> commission_<?php  echo $level['key'];?>_' +ids+ '" value="" style="display:inline;width: '+96/parseInt(<?php  echo $shopset_level;?>)+'%;"/> ';
						}
					}
				}
				else
				{
					for (var li = 0; li<<?php  echo $shopset_level;?>;li++)
					{
						if (typeof (level_temp[li])!= "undefined")
						{
							hh += '<input data-name="commission_level_<?php  echo $level['id'];?>_' +ids+ '"  type="text" class="form-control commission_level<?php  echo $level['id'];?> commission_level<?php  echo $level['id'];?>_' +ids+ '" value="' +$(level_temp[li]).val()+ '" style="display:inline;width: '+96/parseInt(<?php  echo $shopset_level;?>)+'%;"/> ';
						}
						else
						{
							hh += '<input data-name="commission_level_<?php  echo $level['id'];?>_' +ids+ '"  type="text" class="form-control commission_level<?php  echo $level['id'];?> commission_level<?php  echo $level['id'];?>_' +ids+ '" value="" style="display:inline;width: '+96/parseInt(<?php  echo $shopset_level;?>)+'%;"/> ';
						}
					}
				}
			}
			else
			{
				if('<?php  echo $level['key'];?>' == 'default')
				{
					for (var li = 0; li<<?php  echo $shopset_level;?>;li++)
					{
						if (typeof (level_temp[li])!= "undefined")
						{
							hh += '<input data-name="commission_level_<?php  echo $level['key'];?>_' +ids+ '"  type="text" class="form-control commission_<?php  echo $level['key'];?> commission_<?php  echo $level['key'];?>_' +ids+ '" value="' +$(level_temp[li]).val()+ '" style="display:inline;width: '+96/parseInt(<?php  echo $shopset_level;?>)+'%;"/> ';
						}
						else
						{
							hh += '<input data-name="commission_level_<?php  echo $level['key'];?>_' +ids+ '"  type="text" class="form-control commission_<?php  echo $level['key'];?> commission_<?php  echo $level['key'];?>_' +ids+ '" value="" style="display:inline;width: '+96/parseInt(<?php  echo $shopset_level;?>)+'%;"/> ';
						}
					}
				}
				else
				{
					for (var li = 0; li<<?php  echo $shopset_level;?>;li++)
					{
						if (typeof (level_temp[li])!= "undefined")
						{
							hh += '<input data-name="commission_level_<?php  echo $level['id'];?>_' +ids+ '"  type="text" class="form-control commission_level<?php  echo $level['id'];?> commission_level<?php  echo $level['id'];?>_' +ids+ '" value="' +$(level_temp[li]).val()+ '" style="display:inline;width: '+96/parseInt(<?php  echo $shopset_level;?>)+'%;"/> ';
						}
						else
						{
							hh += '<input data-name="commission_level_<?php  echo $level['id'];?>_' +ids+ '"  type="text" class="form-control commission_level<?php  echo $level['id'];?> commission_level<?php  echo $level['id'];?>_' +ids+ '" value="" style="display:inline;width: '+96/parseInt(<?php  echo $shopset_level;?>)+'%;"/> ';
						}
					}
				}
			}
			hh += '</td>';
			<?php  } } ?>
			hh += '<input data-name="commission_id_' + ids+'"type="hidden" class="form-control commission_id commission_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
			hh += '<input data-name="commission_ids"type="hidden" class="form-control commission_ids commission_ids_' + ids +'" value="' + ids +'"/>';
			hh += '<input data-name="commission_title_' + ids +'"type="hidden" class="form-control commission_title commission_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
			hh += '<input data-name="commission_virtual_' + ids +'"type="hidden" class="form-control commission_virtual commission_virtual_' + ids +'" value="' +(val.virtual=='undefined'?'':val.virtual )+'"/></td>';
			hh += "</tr>";
		}
		html += hh;
		html += "</table>";
		$("#commission").html(html);
	}

function setCol(cls){
	$("."+cls).val( $("."+cls+"_all").val());
}
function showItem(obj){
	var show = $(obj).get(0).checked?"1":"0";
	$(obj).parents('.spec_item_item').find('.spec_item_show:eq(0)').val(show);
}
function nofind(){
	var img=event.srcElement;
	img.src="./resource/image/module-nopic-small.jpg";
	img.onerror=null;
}

    function choosetemp(id){
    $('#modal-module-chooestemp').modal();
    $('#modal-module-chooestemp').data("temp",id);
}
function addtemp(){
    var id = $('#modal-module-chooestemp').data("temp");
    var temp_id = $('#modal-module-chooestemp').find("select").val();
    var temp_name = $('#modal-module-chooestemp option[value='+temp_id+']').text();
    //alert(temp_id+":"+temp_name);
    $("#temp_name_"+id).val(temp_name);
    $("#temp_id_"+id).val(temp_id);
    $('#modal-module-chooestemp .close').click();
    refreshOptions()
}

function setinterval(type)
{
	var intervalfloor =$('#intervalfloor').val();
	if(intervalfloor=="")
	{
		intervalfloor=0;
	}
	intervalfloor = parseInt(intervalfloor);

	if(type=='plus')
	{
		if(intervalfloor==3)
		{
			tip.msgbox.err("最多添加三个区间价格");
			return;
		}
		intervalfloor=intervalfloor+1;
	}
	else if(type=='minus')
	{
		if(intervalfloor==0)
		{
			tip.msgbox.err("请最少添加一个区间价格");
			return;
		}
		intervalfloor=intervalfloor-1;
	}else
	{
		return;
	}

	if(intervalfloor<1)
	{

		$('#interval1').hide();
		$('#intervalnum1').val("");
		$('#intervalprice1').val("");
	}else
	{
		$('#interval1').show();
	}

	if(intervalfloor<2)
	{

		$('#interval2').hide();
		$('#intervalnum2').val("");
		$('#intervalprice2').val("");
	}else
	{
		$('#interval2').show();
	}

	if(intervalfloor<3)
	{

		$('#interval3').hide();
		$('#intervalnum3').val("");
		$('#intervalprice3').val("");
	}else
	{
		$('#interval3').show();
	}


	$('#intervalfloor').val(intervalfloor);

}


</script>