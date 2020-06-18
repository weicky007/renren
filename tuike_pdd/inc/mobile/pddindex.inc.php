<?php

global $_W, $_GPC;
		include IA_ROOT . "/addons/tiger_newhu/inc/sdk/tbk/pdd.php"; 
		$weid=$_W['uniacid'];//绑定公众号的ID
		$cfg =$this->cfg;
		$op=$_GPC['op'];
		$keyword=$_GPC['key'];//搜索关键词
		$pddset=pdo_fetch("select * from ".tablename('tuike_pdd_set')." where weid='{$weid}'");
		$owner_name=$pddset['ddjbbuid'];
		$hd=$_GPC['hd'];
		$pdaaa=pddtype($owner_name);
		$pddtype=$pdaaa['goods_opt_get_response']['goods_opt_list'];//拼多多分类
//		echo "<pre>";
//		print_r($pddtype);
//		exit;

		$category_id=$_GPC['category_id'];//商品分类
		$sort_type=$_GPC['sort_type'];//0-综合排序3-按价格升序 6-按销量降序 2-按佣金比率升序
		$with_coupon=$_GPC['with_coupon'];//false返回所有商品，true只返回有优惠券的商品     
		
		//PID绑定
		$fans=$this->islogin();
		if(empty($fans['tkuid'])){
			$fans = mc_oauth_userinfo();        
		}
		if(!empty($dluid)){
		  $share=pdo_fetch("select * from ".tablename('tiger_newhu_share')." where weid='{$_W['uniacid']}' and id='{$dluid}'");
		}else{
		  //$fans=mc_oauth_userinfo();
		  $openid=$fans['openid'];
		  if(empty($openid)){
		  	$openid=$_W['openid'];
		  }
		  $zxshare=pdo_fetch("select * from ".tablename('tiger_newhu_share')." where weid='{$_W['uniacid']}' and from_user='{$openid}'");
		}
		if($zxshare['dltype']==1){
		    if(!empty($zxshare['dlptpid'])){
		       $cfg['ptpid']=$zxshare['dlptpid'];
		       $cfg['qqpid']=$zxshare['dlqqpid'];
		    }
		}else{
		   if(!empty($zxshare['helpid'])){//查询有没有上级
		         $sjshare=pdo_fetch("select * from ".tablename('tiger_newhu_share')." where weid='{$_W['uniacid']}' and dltype=1 and id='{$zxshare['helpid']}'");           
		    }
		}
		
		
		if(!empty($sjshare['dlptpid'])){
		    if(!empty($sjshare['dlptpid'])){
		      $cfg['ptpid']=$sjshare['dlptpid'];
		      $cfg['qqpid']=$sjshare['dlqqpid'];
		    }   
		}else{
		   if($share['dlptpid']){
		       if(!empty($share['dlptpid'])){
		         $cfg['ptpid']=$share['dlptpid'];
		         $cfg['qqpid']=$share['dlqqpid'];
		       }       
		    }
		}
				$p_id=$share['pddpid'];
		//结束

		
		$lbad = pdo_fetchall("SELECT * FROM " . tablename("tiger_newhu_ad") . " WHERE weid = '{$_W['uniacid']}' and type=2 order by id desc");//轮播图
		$ad4 = pdo_fetchall("SELECT * FROM " . tablename("tiger_newhu_ad") . " WHERE weid = '{$_W['uniacid']}' and type=3 order by id desc");//菜单下4张图

    //echo $p_id;
    $baokuan=$this->baokuan(1,10);
    $baokuan=$baokuan['top_goods_list_get_response']['list'];
				
//echo "<pre>";
//				echo 1;
//				print_r($baokuan);
//				exit;

		
		$dblist = pdo_fetchall("select * from ".tablename("tiger_newhu_cdtype")." where weid='{$_W['uniacid']}' and fftype=4  order by px desc");//底部菜单
		$cdlist = pdo_fetchall("select * from ".tablename("tiger_newhu_cdtype")." where weid='{$_W['uniacid']}' and fftype=3  order by px desc");//首页轮播图下面菜单
//		
//		echo "<pre>";
//		print_r($list);
//		exit;
////		
		include $this->template ( 'pdd/index' ); 
		
?>