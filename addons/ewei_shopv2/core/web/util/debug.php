<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Debug_EweiShopV2Page extends WebPage
{

    function main()
    {
        phpinfo();
    }


}