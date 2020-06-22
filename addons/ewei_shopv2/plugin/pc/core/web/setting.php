<?php

if (!defined("IN_IA")) {
    exit("Access Denied");
}
class SettingController extends PluginWebPage
{
    public function main()
    {
        global $_W;
        global $_GPC;
        $this->createDoaminTable();
        if (!is_dir(IA_ROOT . "/pc")) {
            @mkdirs(IA_ROOT . "/pc");
            $this->newSymlink();
            @copy(EWEI_SHOPV2_PLUGIN . $this->pluginname . "/core/tmp/index.php", IA_ROOT . "/pc/index.php");
        }
        if ($_W["ispost"]) {
            if (!empty($_GPC["qq_nick"]) && !empty($_GPC["qq_num"])) {
                $qq = array();
                foreach ($_GPC["qq_nick"] as $key => $val) {
                    $qq[$key]["nickname"] = $val;
                    $qq[$key]["qqnum"] = $_GPC["qq_num"][$key];
                }
            }
            if (!empty($_GPC["wx_nick"]) && !empty($_GPC["wx_img"])) {
                $wx = array();
                foreach ($_GPC["wx_nick"] as $key => $val) {
                    $wx[$key]["wxnickname"] = $val;
                    $wx[$key]["wximg"] = $_GPC["wx_img"][$key];
                }
                $wx_nick = $_GPC["wx_nick"];
                $wx_img = $_GPC["wx_img"];
            }
            $data = array();
            $data["search"] = $_GPC["search"];
            $data["search"] = str_replace("，", ",", $data["search"]);
            $data["copyright"] = $_GPC["copyright"];
            $data["qq"] = $qq;
            $data["wx"] = $wx;
            $data["wx_nick"] = $wx_nick;
            $data["wx_img"] = $wx_img;
            $data["domain"] = $_GPC["domain"];
            $data["mobile_domain"] = $_GPC["mobile_domain"];
            pdo_delete("ewei_shop_domain_bindings", array("uniacid" => $_W["uniacid"], "plugin" => "pc"));
            $res = pdo_get("ewei_shop_domain_bindings", array("domain" => $_GPC["domain"]));
            if (empty($res)) {
                pdo_delete("ewei_shop_domain_bindings", array("domain" => $_GPC["domain"]));
                pdo_insert("ewei_shop_domain_bindings", array("uniacid" => $_W["uniacid"], "plugin" => "pc", "domain" => $data["domain"], "mobile_domain" => $data["mobile_domain"]));
            }
            if ($res && $res["uniacid"] != $_W["uniacid"]) {
                show_json(0, "域名已经被绑定");
            }
            m("common")->updatePluginset(array("pc" => $data));
            show_json(1);
        }
        $data = m("common")->getPluginset("pc");
        if (0 < mb_strlen($data["domain"])) {
            $data["url"] = $data["domain"];
        } else {
            $data["url"] = pcUrl("pc", NULL, true);
        }
        $domain = $data["domain"];
        include $this->template();
    }
    public function pcUrl($do, $query, $full)
    {
        global $_W;
        global $_GPC;
        $result = m("common")->getPluginSet("pc");
        if (isset($result["domain"]) && mb_strlen($result["domain"])) {
            return $siteroot = ($full === true ? $_W["siteroot"] : "./") . "?r=" . $do . "&" . http_build_query($query);
        }
        return pcUrl($do, $query, $full);
    }
    public function newSymlink()
    {
        $manual = ATTACHMENT_ROOT;
        $manualLink = IA_ROOT . "/pc/attachment";
        $isExistFile = true;
        if (is_dir($manual) && !is_file($manualLink)) {
            return symlink($manual, $manualLink);
        }
        return true;
    }
    public function createDoaminTable()
    {
        if (!pdo_tableexists("ewei_shop_domain_bindings")) {
            return pdo_query(" CREATE TABLE " . tablename("ewei_shop_domain_bindings") . " (\r\n                              `id` int NOT NULL AUTO_INCREMENT,\r\n                              `uniacid` int DEFAULT NULL,\r\n                              `domain` varchar(255) DEFAULT NULL,\r\n                              `plugin` varchar(255) DEFAULT NULL,\r\n                              `mobile_domain` varchar(255) DEFAULT NULL,\r\n                              PRIMARY KEY (`id`)\r\n                            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
        }
    }
}

?>