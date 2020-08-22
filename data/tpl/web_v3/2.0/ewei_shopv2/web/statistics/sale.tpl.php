<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-header">当前位置：<span class="text-primary">销售统计</span></div>
<div class="alert alert-primary">
    <p><b>数据说明</b></p>
    <p>本模块计算的数值是只有平台或本商户的数据。如果是总平台，那么会显示本平台的+本平台所有商户的数据。如果是商户的，那么只会展示本商户的。<br>
        销售统计-成交额：
        成交额=主商城成交额+多商户成交额之和。
        本模块计算的数值是只有平台或本商户的数据。如果是总平台，那么会显示本平台的+本平台所有商户的数据。如果是商户的，那么只会展示本商户的。
    </p>
</div>
<div class="page-content">
    <form action="./index.php" method="get" class="form-horizontal table-search">
            <input type="hidden" name="c" value="site" />
            <input type="hidden" name="a" value="entry" />
            <input type="hidden" name="m" value="ewei_shopv2" />
            <input type="hidden" name="do" value="web" />
            <input type="hidden" name="r"  value="statistics.sale" />

        <div class="page-toolbar">
            <div class="input-group">
                <span></span>
                <span class="input-group-select">
                    <select name="year" class='form-control'>
                        <?php  if(is_array($years)) { foreach($years as $y) { ?>
                        <option value="<?php  echo $y['data'];?>"  <?php  if($y['selected']) { ?>selected="selected"<?php  } ?>><?php  echo $y['data'];?>年</option>
                        <?php  } } ?>
                    </select>
                </span>
                <span class="input-group-select">
                    <select name="month" class='form-control'>
                        <option value=''>月份</option>
                        <?php  if(is_array($months)) { foreach($months as $m) { ?>
                        <option value="<?php  echo $m['data'];?>"  <?php  if($m['selected']) { ?>selected="selected"<?php  } ?>><?php  echo $m['data'];?>月</option>
                        <?php  } } ?>
                    </select>
                </span>
                <span class="input-group-select">
                     <select name="day" class='form-control'>
                         <option value=''>日期</option>
                     </select>
                </span>
                <span class="input-group-select">
                    <select name="type" class='form-control'>
                        <option value='0' <?php  if($_GPC['type']==0) { ?>selected="selected"<?php  } ?>>成交额</option>
                        <option value='1' <?php  if($_GPC['type']==1) { ?>selected="selected"<?php  } ?>>交易量</option>

                    </select>
                </span>
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit"> 搜索</button>
                     <?php if(cv('statistics.sale.export')) { ?>
                    <button type="submit" name="export" value='1' class="btn btn-success">导出</button>
                    <?php  } ?>
                </span>
            </div>

        </div>

 </form>


<div class="panel panel-default">
    <div class='panel-heading'>

        <?php  if(empty($type)) { ?>成交额<?php  } else { ?>交易量<?php  } ?>：<span style="color:red; "><?php  echo $totalcount;?></span>，
        最高<?php  if(empty($type)) { ?>成交额<?php  } else { ?>交易量<?php  } ?>：<span style="color:red; "><?php  echo $maxcount;?></span> <?php  if(!empty($maxcount_date)) { ?><span>(<?php  echo $maxcount_date;?></span>)<?php  } ?>

    </div>
    <div class="panel-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style='width:100px;'>
                        <?php  if(empty($_GPC['month'])) { ?>月份<?php  } else { ?>日期<?php  } ?>
                    </th>
                    <th style='width:200px;'><?php  if(empty($type)) { ?>成交额<?php  } else { ?>交易量<?php  } ?></th>
                    <th style="width: 65px;">所占比例</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($list)) { foreach($list as $row) { ?>
                <tr>
                    <td><?php  echo $row['data'];?></td>
                    <td><?php  echo $row['count'];?></td>
                    <td><span class="process-num" style="color:#000"><?php echo empty($row['percent'])?'':$row['percent'].'%'?></span></td>
                    <td>
                       <div class="progress">
                           <div style="width: <?php  echo $row['percent'];?>%;" class="progress-bar progress-bar-info" ></div>
                       </div>
                    </td>
                </tr>
                <?php  } } ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<script language='javascript'>
    function get_days(){
          
        var year = $('select[name=year]').val();
        var month =$('select[name=month]').val();
        var day  = $('select[name=day]');
       day.get(0).options.length = 0 ;
        if(month==''){
	   day.append("<option value=''>日期</option");
            return;
        }
       
        day.get(0).options.length = 0 ;
        day.append("<option value=''>...</option").attr('disabled',true);
        $.post("<?php  echo webUrl('util/days')?>",{year:year,month:month},function(days){
             day.get(0).options.length = 0 ;
             day.removeAttr('disabled');
             days =parseInt(days);
             day.append("<option value=''>日期</option");
             for(var i=1;i<=days;i++){
                 day.append("<option value='" + i +"'>" + i + "日</option");
             }
          
             <?php  if(!empty($day)) { ?>
                day.val( <?php  echo $day;?>);
             <?php  } ?>
        })
    }
    $('select[name=month]').change(function(){
           get_days();
    })
    
    get_days();
 </script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
