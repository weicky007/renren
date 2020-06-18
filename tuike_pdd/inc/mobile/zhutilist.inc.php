<?php
	
global $_W, $_GPC;
		$weid=$_W['uniacid'];//绑定公众号的ID
		$cfg = $this->cfg;
		$op=$_GPC['op'];
		$keyword=$_GPC['key'];//搜索关键词
		$pddset=pdo_fetch("select * from ".tablename('tuike_pdd_set')." where weid='{$weid}'");
		$owner_name=$pddset['ddjbbuid'];
		$theme_id=$_GPC['theme_id'];
		$pic=urldecode($_GPC['pic']);
		$title=urldecode($_GPC['title']);
		$sum=$_GPC['sum'];
		 
  


		//$op=1;
		if($op=='1'){  
			//die(json_encode(array("error"=>0,'data'=>$list,'theme_id'=>$theme_id))); 
			$pddset = pdo_fetch("SELECT * FROM " . tablename($this->modulename."_set") . " WHERE  weid='{$_W['uniacid']}'");
			$owner_name=$pddset['ddjbbuid'];
			$arr=array (
				'theme_id'=>$theme_id
			);       
					
			$zhuti=$this->createres($arr,'pdd.ddk.theme.goods.search',$owner_name);		
			$data=$zhuti['theme_list_get_response']['goods_list'];

			$pages=ceil($zhuti['theme_list_get_response']['total']/20);
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
			
			die(json_encode(array("error"=>0,'data'=>$list,'pages'=>$pages)));  
		
		}

		
		$dblist = pdo_fetchall("select * from ".tablename("tiger_newhu_cdtype")." where weid='{$_W['uniacid']}' and fftype=4  order by px desc");//底部菜单

		include $this->template ( 'pdd/zhutilist' ); 
		
?>