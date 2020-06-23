<?php

if (!defined("IN_IA")) {
    exit("Access Denied");
}
/**
 * 微信小程序直播模型
 * Class WxliveModel
 */
class WxliveModel extends PluginModel
{
    /**
     * @var array 状态映射
     */
    public $statusMap = array("直播未开始", "正在直播", "18446744073709551615" => "直播已结束");
    /**
     * 处理直播状态
     * @param $list
     * @return mixed
     * @author likexin
     */
    public function handleStatus($list)
    {
        foreach ($list as &$row) {
            $row["live_status_text"] = isset($this->statusMap[$row["local_live_status"]]) ? $this->statusMap[$row["local_live_status"]] : "-";
        }
        return $list;
    }
    /**
     * 同步直播间列表
     * @author likexin
     */
    public function syncRoomList($uniacid)
    {
        $plugin = p("app");
        if (!$plugin) {
            return error(-1, "系统未安装小程序");
        }
        $accessToken = $plugin->getAccessToken();
        if (is_error($accessToken)) {
            return $accessToken;
        }
        $pageSize = 30;
        $roomIds = array();
        $url = "http://api.weixin.qq.com/wxa/business/getliveinfo?access_token=" . $accessToken;
        while (true) {
            $response = ihttp_post($url, json_encode(array("start" => 0, "limit" => $pageSize)));
            $result = json_decode($response["content"], true);
            if ($result["errcode"] != 0) {
                if ($result["errcode"] == 1) {
                    return error($result["errcode"], "直播间列表为空");
                }
                if ($result["errcode"] == 48001) {
                    return error($result["errcode"], "小程序没有直播权限");
                }
                return error($result["errcode"], $result["errmsg"]);
            }
            foreach ($result["room_info"] as $room) {
                $roomId = (int) $room["roomid"];
                $roomIds[] = $roomId;
                $wxlive = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("ewei_shop_wxlive") . " WHERE `room_id`=:room_id AND `uniacid`=:uniacid", array(":uniacid" => $uniacid, ":room_id" => $roomId));
                $updateData = array("name" => (string) $room["name"], "cover_img" => (string) $room["cover_img"], "live_status" => (int) $room["live_status"], "start_time" => (int) $room["start_time"], "end_time" => (int) $room["end_time"], "anchor_name" => (string) $room["anchor_name"], "anchor_img" => (string) $room["share_img"], "goods_json" => json_encode($room["goods"]));
                if (empty($wxlive)) {
                    $insertData = array_merge($updateData, array("uniacid" => $uniacid, "room_id" => $roomId, "status" => 1));
                    pdo_insert("ewei_shop_wxlive", $insertData);
                    continue;
                }
                pdo_update("ewei_shop_wxlive", $updateData, array("room_id" => $room["roomid"], "uniacid" => $uniacid));
            }
            if ($result["total"] < $pageSize) {
                break;
            }
            unset($room);
        }
        unset($result);
        pdo_query("DELETE FROM " . tablename("ewei_shop_wxlive") . " where room_id not in ( " . implode(",", $roomIds) . ") AND uniacid=:uniacid", array(":uniacid" => $uniacid));
        $this->flushLiveStatus($uniacid);
    }
    /**
     * 刷新直播状态
     * @param $uniacid
     * @author likexin
     */
    public function flushLiveStatus($uniacid)
    {
        $time = time();
        $cacheKey = "wxlive_flush_live_status_" . $uniacid;
        $cache = m("cache")->get($cacheKey);
        if (!empty($cache) && $time < $cache + 30) {
            return NULL;
        }
        pdo_query("UPDATE " . tablename("ewei_shop_wxlive") . " SET `local_live_status`=1 WHERE `local_live_status` = 0 AND `uniacid` = :uniacid AND `start_time` < :time AND `end_time` > :time", array(":uniacid" => $uniacid, ":time" => $time));
        pdo_query("UPDATE " . tablename("ewei_shop_wxlive") . " SET `local_live_status`=-1 WHERE `uniacid` = :uniacid AND `end_time` < :time", array(":uniacid" => $uniacid, ":time" => $time));
        m("cache")->set($cacheKey, $time);
    }
    public function getRoomId($id, $room_id)
    {
        $ret = pdo_get("ewei_shop_wxlive", compact("id", "room_id"));
        return $ret["name"];
    }
    public function makeAccessToken()
    {
        global $_W;
        $plugin = p("app");
        if (!$plugin) {
            return error(-1, "系统未安装小程序");
        }
        $accessToken = $plugin->getAccessToken();
        return $accessToken;
    }
    public function getBackData($id, $begin = 0, $end = 1)
    {
        $access_toekn = $this->makeAccessToken();
        $data = array("action" => "get_replay", "room_id" => $id, "start" => $begin, "limit" => $end);
        $data = json_encode($data);
        $result = ihttp_request("http://api.weixin.qq.com/wxa/business/getliveinfo?access_token=" . $access_toekn, $data);
        return json_decode($result["content"], true);
    }
    public function saveBack()
    {
    }
}

?>