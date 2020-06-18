<?php
	
global $_W, $_GPC;
		$weid=$_W['uniacid'];//绑定公众号的ID
		$cfg = $this->cfg;
		$op=$_GPC['op'];
		$page=$_GPC['page'];
		if(empty($page)){
			$page=1;
		}
		

		if($op=='1'){  
			
			
				$pddset = pdo_fetch("SELECT * FROM " . tablename($this->modulename."_set") . " WHERE  weid='{$_W['uniacid']}'");
				$owner_name=$pddset['ddjbbuid'];
				$arr=array (
					'page_size'=>20,
					'page'=>$page,
				);       
						
				$zhuti=$this->createres($arr,'pdd.ddk.theme.list.get',$owner_name);
				$list=$zhuti['theme_list_get_response']['theme_list'];
				$pages=ceil($zhuti['theme_list_get_response']['total']/20);
		// 		[image_url] => http://t00img.yangkeduo.com/t08img/images/2018-06-29/fe40e86c490f65ffdd4bcc20d81dd226.jpeg
		// 		[name] => 女装热销单品推荐
		// 		[goods_num] => 7
		// 		[id] => 1012
		// 		[type] => 1
		
			die(json_encode(array("error"=>0,'data'=>$list,'pages'=>$pages)));  
		
		}


		
		$dblist = pdo_fetchall("select * from ".tablename("tiger_newhu_cdtype")." where weid='{$_W['uniacid']}' and fftype=4  order by px desc");//底部菜单

		include $this->template ( 'pdd/zhuti' ); 
		
?>