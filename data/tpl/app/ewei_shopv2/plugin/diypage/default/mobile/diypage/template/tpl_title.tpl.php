<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!empty($diyitem['params']['title'])) { ?>
<div class="fui-title" style="background: <?php  echo $diyitem['style']['background'];?>; color: <?php  echo $diyitem['style']['color'];?>; font-size: <?php  echo $diyitem['style']['fontsize'];?>px; text-align: <?php  echo $diyitem['style']['textalign'];?>; padding: <?php  echo $diyitem['style']['paddingtop'];?>px <?php  echo $diyitem['style']['paddingleft'];?>px; margin: 0;">
    <a href="<?php  echo $diyitem['params']['link'];?>" style="color: <?php  echo $diyitem['style']['color'];?>" data-nocache="true">
        <?php  if(!empty($diyitem['params']['icon'])) { ?><i class="icon <?php  echo $diyitem['params']['icon'];?>"></i><?php  } ?>
        <?php  echo $diyitem['params']['title'];?>
    </a>
</div>
<?php  } ?>