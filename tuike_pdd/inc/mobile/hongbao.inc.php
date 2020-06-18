<?php
	//小程序 数据列表
	//http://cs.tiger-app.com/app/index.php?i=8&c=entry&a=wxapp&do=pddview&m=tiger_tkxcx&owner_name=13735760105&itemid=4837612
global $_W, $_GPC;
		$weid=$_W['uniacid'];//绑定公众号的ID
		$cfg = $this->cfg;

		
		$fans=$this->islogin();
        if(empty($fans['tkuid'])){
        	$fans = mc_oauth_userinfo();        
        }

		$openid=$fans['openid'];
        $share=pdo_fetch("select * from ".tablename('tiger_newhu_share')." where weid='{$_W['uniacid']}' and from_user='{$openid}'");  

        if($share['dltype']==1){//是代理
			if(empty($share['pddpid'])){//如果是代理，PID没填写就默认公众号PID
				$pddset=pdo_fetch("select * from ".tablename('tuike_pdd_set')." where weid='{$weid}'");
				$share['pddpid']=$pddset['pddpid'];
			}
		}else{//不是代理
			if(!empty($share['helpid'])){//查看有没有上级
				$shshare=pdo_fetch("select * from ".tablename('tiger_newhu_share')." where weid='{$weid}' and id='{$share['helpid']}'");
				//file_put_contents(IA_ROOT."/addons/tiger_tkxcx/v_log.txt","\n helpid1:".$share['helpid']."--------".json_encode($shshare),FILE_APPEND);	
				if(empty($shshare['id'])){//没有上级代理，就用默认的公众号PID
					$pddset=pdo_fetch("select * from ".tablename('tuike_pdd_set')." where weid='{$weid}'");
				    $share['pddpid']=$pddset['pddpid'];
				}else{//有上级代理
					if($shshare['dltype']==1){//如果上级是代理，就用代理的PID
						$share['pddpid']=$shshare['pddpid'];
					}else{//上级不是代理就用默认的PID
						$pddset=pdo_fetch("select * from ".tablename('tuike_pdd_set')." where weid='{$weid}'");
				   		$share['pddpid']=$pddset['pddpid'];
					}
				}
			}else{//没有上级就用默认公众号PID
				$pddset=pdo_fetch("select * from ".tablename('tuike_pdd_set')." where weid='{$weid}'");
				$share['pddpid']=$pddset['pddpid'];
			}			
		}
		$p_id=$share['pddpid'];		
		$pddset = pdo_fetch("SELECT * FROM " . tablename($this->modulename."_set") . " WHERE  weid='{$_W['uniacid']}'");
		$owner_name=$pddset['ddjbbuid'];
		$p_id='["'.$p_id.'"]'; 
		$arr=array (
			'p_id_list'=>$p_id,
			'generate_short_url'=>'true',
		);       
				
		$view=$this->createres($arr,'pdd.ddk.oauth.rp.prom.url.generate',$owner_name);
		
		$url=$view['rp_promotion_url_generate_response']['url_list'][0]['mobile_short_url'];
		header("location:".$url);
		echo "<pre>";
		print_r($view);
		exit;
		

?>