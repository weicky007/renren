<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Index_EweiShopV2Page extends PluginWebPage {
    function main() {
        header('location: '.webUrl('polyapi/set'));
    }




}