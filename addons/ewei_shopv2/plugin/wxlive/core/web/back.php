<?php

if (!defined("IN_IA")) {
    exit("Access Denied");
}
class Back_EweiShopV2Page extends PluginWebPage
{
    public function main()
    {
        global $_W;
        global $_GPC;
        $live_id = $_GPC["id"];
        $room_id = $_GPC["room_id"];
        $uniacid = $_W["uniacid"];
        $item = pdo_get("ewei_shop_wxlive", compact("id", "room_id"));
        if (empty($item)) {
            $this->message("直播间不存在~", "", "error");
        }
        $page = intval($_GPC["page"]);
        $total = "";
        $list = pdo_getslice("ewei_shop_wxlive_back", compact("live_id", "room_id", "uniacid"), array($page * 20, 20), $total, $fields = array(), $keyfield = "id,room_id");
        foreach ($list as &$item) {
            $item["name"] = $this->model->getRoomId($item["id"], $item["room_id"]);
            $item["expire_time"] = date("Y-m-d H:i:s", $item["expire_time"]);
            $item["create_time"] = date("Y-m-d H:i:s", $item["create_time"]);
        }
        unset($item);
        $pager = pagination($total, $page, 20);
        include $this->template();
    }
    public function getBack()
    {
        global $_GPC;
        global $_W;
        $room_id = $_GPC["room_id"];
        $id = $_GPC["id"];
        $uniacid = $_W["uniacid"];
        $list = $this->model->getBackData($room_id);
        if ($list["errcode"] != 0 || $list["errmsg"] != "ok") {
            return show_json(0, $list["errmsg"]);
        }
        if (0 < $list["total"]) {
            $ret = pdo_delete("ewei_shop_wxlive_back", compact("room_id", "uniacid"));
            $result = $list["live_replay"];
            foreach ($result as $item) {
                $item["expire_time"] = strtotime($item["expire_time"]);
                $item["create_time"] = strtotime($item["create_time"]);
                $item["uniacid"] = $uniacid;
                $item["live_id"] = $id;
                $item["room_id"] = $room_id;
                pdo_insert("ewei_shop_wxlive_back", $item);
            }
        }
        return show_json(1, "获取回放成功");
    }
    public function delete()
    {
        global $_GPC;
        global $_W;
        $id = $_GPC["id"];
        pdo_delete("ewei_shop_wxlive_back", array("id" => $id));
        return show_json(1, "删除直播成功");
    }
}

?>