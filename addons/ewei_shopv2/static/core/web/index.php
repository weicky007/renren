<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
$auth = get_auth();
$result = auth_checkauth($auth);
if($result['status'] != 1 && $_GET['r']!='system.auth'){
    echo '<script>window.location.href="index.php?c=site&a=entry&m=ewei_shopv2&do=web&r=system.auth"</script>';
    exit;
}
class Index_EweiShopV2Page extends WebPage 
{
	public function main() 
	{
		header('location:' . webUrl('shop'));
		exit();
	}
}
?>