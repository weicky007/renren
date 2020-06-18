<?php defined('IN_IA') or exit('Access Denied');?></div>
<div class="clearfix"></div>
<div class="container-fluid footer text-center" role="footer">	
	<div class="friend-link">
		<?php  if(empty($_W['setting']['copyright']['footerright'])) { ?>
			<a href="https://www.microeco.net">微信开发</a>
			<a href="https://www.microeco.net/module">微信应用</a>
			<a href="https://www.microeco.net/module/xch">小程序开发</a>
		<?php  } else { ?>
			<?php  echo $_W['setting']['copyright']['footerright'];?>
		<?php  } ?>
	</div>
	<div class="copyright"><?php  if(empty($_W['setting']['copyright']['footerleft'])) { ?>Powered by <a href="http://www.microeco.net"><b>百码云</b></a> v<?php echo IMS_VERSION;?> &copy; 2014-2018 <a href="http://www.microeco.net">www.microeco.net</a><?php  } else { ?><?php  echo $_W['setting']['copyright']['footerleft'];?><?php  } ?></div>
	
	<div>
		<?php  $icps = iunserializer($_W['setting']['copyright']['icps']);?>
		<?php  if(!empty($icps)) { ?>
		<?php  if(is_array($icps)) { foreach($icps as $icp) { ?>
		<?php  if($icp['domain'] == $_SERVER['HTTP_HOST']) { ?>
		备案号：<a href="http://beian.miit.gov.cn/" target="_blank"><?php  echo $icp['icp'];?></a>
		<?php  } ?>
		<?php  } } ?>
		<?php  } ?>
		<?php  if(!empty($_W['setting']['copyright']['policeicp']['policeicp_location']) && !empty($_W['setting']['copyright']['policeicp']['policeicp_code'])) { ?>
			<a target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=<?php  echo $_W['setting']['copyright']['policeicp']['policeicp_code']?>">
                &nbsp;&nbsp;<img src="./resource/images/icon-police.png" >
				<?php  echo $_W['setting']['copyright']['policeicp']['policeicp_location']?> <?php  echo $_W['setting']['copyright']['policeicp']['policeicp_code']?>号
			</a>
		<?php  } ?>
	</div>
</div>
</div>

</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-base', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-base', TEMPLATE_INCLUDEPATH));?>
</body>
</html>