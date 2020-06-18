<?php
	
	//小程序 数据列表
	//wx.baokuanba.com/app/index.php?i=3&c=entry&do=pddgoodslist&m=tiger_newhu&owner_name=13735760105&page=1&keyword=%E8%A1%AC%E8%A1%AB&category_id=743&sort_type=0&with_coupon=true
global $_W, $_GPC;
//		include IA_ROOT . "/addons/tiger_newhu/inc/sdk/tbk/pdd.php"; 
		$weid=$_W['uniacid'];//绑定公众号的ID
		$cfg = $this->cfg;
		$op=$_GPC['op'];
		$sort_type=$_GPC['sort_type'];//1-实时热销榜；2-实时收益榜
		if(empty($sort_type)){
			$sort_type=1;
		}
			
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
		//结束


		if($op=='1'){  
			
			
			$goodslist=$this->baokuan($sort_type,400);
			$data=$goodslist['top_goods_list_get_response']['list'];
		
			$list=array();
			foreach($data as $k=>$v){
				
				$itemendprice=($v['min_group_price']-$v['coupon_discount'])/100;
				
				if($cfg['lbratetype']==3){
                	$ratea=$this->ptyjjl($itemendprice,$v['promotion_rate']/10,$cfg);
                }else{
                	$ratea=$this->sharejl($itemendprice,$v['promotion_rate']/10,$bl,$share,$cfg);
                }
				$list[$k]['itemid']=$v['goods_id'];
				$list[$k]['itemtitle']=$v['goods_name'];
				$list[$k]['itempic']=$v['goods_thumbnail_url'];//小图
				$list[$k]['itempic1']=$v['goods_image_url'];//大图
				$list[$k]['itemprice']=$v['min_group_price']/100;//原价
				$list[$k]['itemendprice']=$itemendprice;//券后拼购
				$list[$k]['couponmoney']=$v['coupon_discount']/100;//优惠券金额
	            $list[$k]['coupon_end_time']=$v['coupon_end_time'];//优惠券失效时间
	            $list[$k]['itemsale']=$v['sales_tip'];//销量
	            $list[$k]['rate']=$ratea;//佣金
			}
			die(json_encode(array("error"=>0,'data'=>$list)));  
		
		}


		
		$dblist = pdo_fetchall("select * from ".tablename("tiger_newhu_cdtype")." where weid='{$_W['uniacid']}' and fftype=4  order by px desc");//底部菜单

		include $this->template ( 'pdd/baokuan' ); 
		
?>