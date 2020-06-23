<?php

define("IN_MOBILE", true);
global $_W;
require __DIR__ . "/../framework/bootstrap.inc.php";
load()->web("common");
if (empty($uniacid)) {
    $res = pdo_get("ewei_shop_domain_bindings", array("domain" => $_W["siteroot"]));
    if (!empty($res)) {
        $_W["uniacid"] = $res["uniacid"];
        $uniacid = $_W["uniacid"];
    } else {
        echo "域名未绑定!";
        exit;
    }
}
$_W["attachurl_local"] = $_W["siteroot"] . $_W["config"]["upload"]["attachdir"] . "/";
$_W["attachurl"] = $_W["attachurl_local"];
if (!empty($_W["setting"]["remote"][$_W["uniacid"]]["type"])) {
    $_W["setting"]["remote"] = $_W["setting"]["remote"][$_W["uniacid"]];
}
$info = uni_setting_load("remote", $uniacid);
if (!empty($info["remote"]) && $info["remote"]["type"] != 0) {
    $_W["setting"]["remote"] = $info["remote"];
}
if (!empty($_W["setting"]["remote"]["type"])) {
    if ($_W["setting"]["remote"]["type"] == ATTACH_FTP) {
        $_W["attachurl_remote"] = $_W["setting"]["remote"]["ftp"]["url"] . "/";
        $_W["attachurl"] = $_W["attachurl_remote"];
    } else {
        if ($_W["setting"]["remote"]["type"] == ATTACH_OSS) {
            $_W["attachurl_remote"] = $_W["setting"]["remote"]["alioss"]["url"] . "/";
            $_W["attachurl"] = $_W["attachurl_remote"];
        } else {
            if ($_W["setting"]["remote"]["type"] == ATTACH_QINIU) {
                $_W["attachurl_remote"] = $_W["setting"]["remote"]["qiniu"]["url"] . "/";
                $_W["attachurl"] = $_W["attachurl_remote"];
            } else {
                if ($_W["setting"]["remote"]["type"] == ATTACH_COS) {
                    $_W["attachurl_remote"] = $_W["setting"]["remote"]["cos"]["url"] . "/";
                    $_W["attachurl"] = $_W["attachurl_remote"];
                }
            }
        }
    }
}
header("ACCESS-CONTROL-ALLOW-ORIGIN:*");
if (empty($uniacid)) {
    exit("Access Denied.");
}
$site = WeUtility::createModuleSite("ewei_shopv2");
$_GPC["c"] = "site";
$_GPC["a"] = "entry";
$_GPC["m"] = "ewei_shopv2";
$_GPC["do"] = "mobile";
$_W["uniacid"] = (int) $_GPC["i"];
$_W["account"] = uni_fetch($_W["uniacid"]);
$_W["acid"] = (int) $_W["account"]["acid"];
$_GPC["r"] = str_replace("/", ".", $_GPC["r"]);
if (strexists($_GPC["r"], "pc")) {
    $_GPC["r"] = str_replace("pc", "", $_GPC["r"]);
    $_GPC["r"] = str_replace("pc.", "", $_GPC["r"]);
}
if (!isset($_GPC["r"])) {
    $_GPC["r"] = "pc";
} else {
    $_GPC["r"] = "pc." . $_GPC["r"];
}
$_W["uniacid"] = $uniacid;
if (!is_error($site)) {
    $method = "doMobileMobile";
    $site->uniacid = $uniacid;
    $site->inMobile = true;
    if (method_exists($site, $method)) {
        $r = $site->{$method}();
        var_dump($r);
        if (!empty($r)) {
            echo $r;
            exit;
        }
        exit;
    }
}

?>