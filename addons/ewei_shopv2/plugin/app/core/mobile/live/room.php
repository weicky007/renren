<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
require __DIR__ . '/base.php';

class Room_EweiShopV2Page extends Base_EweiShopV2Page
{


    function get_list()
    {
        global $_GPC, $_W;

        $pindex = max(1, intval($_GPC['page']));
        $psize = 5;
        $this->wxliveModel->flushLiveStatus($_W['uniacid']);

        $condition = ' status = 1 AND uniacid = :uniacid ';
        $params = array(':uniacid' => $_W['uniacid']);

        $condition .= " ORDER BY `is_top` DESC, `local_live_status` DESC, `start_time` ASC";

        $list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_wxlive') . " WHERE 1 AND {$condition}" . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);

        if (!empty($list)) {
            foreach ($list as &$row) {
                $row['goods_list'] = json_decode($row['goods_json'], true);
                unset($row['goods_json']);

                foreach ($row['goods_list'] as &$goods) {
                    if ($goods['price'] <= 0) {
                        continue;
                    }

                    $goods['price'] = price_format($goods['price'] / 100);
                }
                unset($goods);

                if ($row['local_live_status'] == 0) {
                    $row['date_text'] = date('m月d日H:i', $row['start_time']);
                }
                if ($row['local_live_status'] == -1){
                    $replay = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_wxlive_back') . " WHERE room_id=" .$row['room_id']);
                    if (!empty($replay)){
                        $row['replay_status'] = 1;
                    }
                }
            }
        }

        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_wxlive') . " WHERE 1 and {$condition}", $params);

        return app_json(array('list' => $list, 'pagesize' => $psize, 'total' => $total, 'page' => $pindex));
    }

    function get_replay()
    {
        global $_GPC, $_W;

        $uniacid = $_W['uniacid'];

        $list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_wxlive_back') . " WHERE room_id= " .$_GPC['id']." AND uniacid=".$uniacid);

        return app_json(array('list' => $list));

    }


}