<?php
global $_W, $_GPC;


					    
       if($_GPC['op']=='post'){
           $data=array(
							'weid'=>$_W['uniacid'],
							'access_token'=>$_GPC['access_token'],
							'expires_in'=>$_GPC['expires_in'],
							'refresh_token'=>$_GPC['refresh_token'],
							'scope'=>$_GPC['code'],
							'owner_id'=>$_GPC['owner_id'],
							'owner_name'=>$_GPC['owner_name'],
							'endtime'=>$_GPC['endtime'],
							'client_id'=>$_GPC['client_id'],
							'createtime'=>time(),
           );
           echo "<pre>";
           print_r($data);	
           $go = pdo_fetch("SELECT id FROM " . tablename($this->modulename."_pddsign") . " WHERE  owner_name='{$_GPC['owner_name']}'");
            if(empty($go)){
                  $res=pdo_insert($this->modulename."_pddsign",$data);
                  if($res=== false){
                    echo '授权失败';
                  }else{
                    //echo '授权成功:'.$_GPC['sign'];
                    $url=$_W['siteroot']."web/index.php?c=site&a=entry&do=set&m=tuike_pdd";
                    //echo $url;
                    echo "<a href='".$url."' style='font-size:20px;width:60%;height:50px;text-height:50px;text-align: center'>授权成功！点击返回</a>";
                   // message('授权成功！',$url, 'success');
                  }
            }else{                          
                  $res=pdo_update($this->modulename."_pddsign", $data, array('owner_name' =>$_GPC['owner_name']));
                  if($res=== false){
                    echo '授权失败';
                  }else{
                    $url=$_W['siteroot']."web/index.php?c=site&a=entry&do=set&m=tuike_pdd";
                    echo "<a href='".$url."' style='font-size:20px;width:60%;height:50px;text-height:50px;text-align: center'>授权成功！点击返回</a>";
                    //message('授权成功！',$url, 'success');
                  }
            }
       }
?>