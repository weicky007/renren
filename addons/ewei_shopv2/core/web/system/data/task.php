<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Task_EweiShopV2Page extends SystemPage {

	function main() {
		global $_W, $_GPC;
        $task_data = m('common')->getSysset('task');
        $task_mode = $task_data['task_mode'];
        $receive_time = $task_data['receive_time'];
        $closeorder_time = $task_data['closeorder_time'];
        $willcloseorder_time = $task_data['willcloseorder_time'];
        $couponback_time = $task_data['couponback_time'];
        $groups_order_cancelorder_time = $task_data['groups_order_cancelorder_time'];
        $groups_team_refund_time = $task_data['groups_team_refund_time'];
        $groups_receive_time = $task_data['groups_receive_time'];
        $fullback_receive_time = $task_data['fullback_receive_time'];
        $status_receive_time = $task_data['status_receive_time'];
        $presell_status_time = $task_data['presell_status_time'];
        $liveroom_receive_time = $task_data['liveroom_receive_time'];


        if ($_W['ispost']) {
            $data = array();
            $data['task_mode'] = intval($_GPC['task_mode']);
            $data['receive_time'] = intval($_GPC['receive_time']);
            $data['closeorder_time'] = intval($_GPC['closeorder_time']);
            $data['willcloseorder_time'] = intval($_GPC['willcloseorder_time']);
            $data['couponback_time'] = intval($_GPC['couponback_time']);
            $data['groups_order_cancelorder_time'] = intval($_GPC['groups_order_cancelorder_time']);
            $data['groups_team_refund_time'] = intval($_GPC['groups_team_refund_time']);
            $data['groups_receive_time'] = intval($_GPC['groups_receive_time']);
            $data['fullback_receive_time'] = intval($_GPC['fullback_receive_time']);
            $data['status_receive_time'] = intval($_GPC['status_receive_time']);
            $data['presell_status_time'] = intval($_GPC['presell_status_time']);
            $data['liveroom_receive_time'] = intval($_GPC['liveroom_receive_time']);

            m('common')->updateSysset(array('task' => $data));
            show_json(1);
        }
		include $this->template();
	}

}
