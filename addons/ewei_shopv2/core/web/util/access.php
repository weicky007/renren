<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Access_EweiShopV2Page extends WebPage
{

    function main()
    {
        global $_W, $_GPC;
        $account = m('common')->getAccount();
        $token = $account->getAccessToken();
        echo "<pre/>";
        print_r($token);
        echo "</br>";
        print_r("刷新成功,请关闭页面");
        die();
    }

}