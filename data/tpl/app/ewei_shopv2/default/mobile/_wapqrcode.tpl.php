<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!empty($currenturl) && !is_mobile()) { ?>
<div class="wap-qrcode-container">
    <p class="example1"><?php  echo $shopname;?></p>
    <div class="wap-qrcode-image" id="wap-qrcode"></div>
    <p class="example1">微信“扫一扫”浏览</p>
</div>
<script language="javascript">
    $(function(){
     setTimeout(function(){
         require(['jquery.qrcode'],function(q){
             $('#wap-qrcode').html('');
             $('#wap-qrcode').qrcode("<?php  echo $currenturl;?>");
         });
     },500);

    })
</script>
<?php  } ?>
