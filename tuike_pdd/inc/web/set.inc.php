<?php
global $_W, $_GPC;
        $weid=$_W['uniacid'];
        $set = pdo_fetch ( 'select * from ' . tablename ($this->modulename . "_set" ) . " where weid='{$weid}'" );
        
        $pddsign = pdo_fetch ( 'select * from ' . tablename ($this->modulename . "_pddsign" ) . " where client_id='{$set['client_id']}'" );
        
//      echo "<pre>";
//      print_r($pddsign);
//      exit;
        
        
        if(empty($set)){
           if (checksubmit('submit')){  
                $indata=array(
                    'weid'=>$_W['uniacid'],
                    'ddjbbuid'=>$_GPC['ddjbbuid'],
                    'pddpid'=>$_GPC['pddpid'],
                );
            //echo '<pre>';
            //print_r($indata);
            //exit;
                $result=pdo_insert($this->modulename."_set",$indata);
                if(empty($result)){
                  message('添加失败', referer(), 'error');
                }else{
                  message ( '添加成功!' );
                }    
           }
        }else{
         if (checksubmit('submit')){
           $id = intval($_GPC['id']);
           $updata=array(              
                    'ddjbbuid'=>$_GPC['ddjbbuid'],
                    'pddpid'=>$_GPC['pddpid'], 

                );
           if(pdo_update($this->modulename."_set",$updata,array('id'=>$id)) === false){
                  message ( '更新失败' );
                }else{
                  message ( '更新成功!' );
                }
          }
        }

		include $this->template ( 'set' );
?>