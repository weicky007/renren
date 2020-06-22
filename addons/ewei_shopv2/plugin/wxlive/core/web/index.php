<?php

if (!defined("IN_IA")) {
    exit("Access Denied");
}
class Index_EweiShopV2Page extends PluginWebPage
{
    /**
     * @author likexin
     */
    public function main()
    {
        header("location: " . webUrl("wxlive/room"));
        exit;
    }
}

?>