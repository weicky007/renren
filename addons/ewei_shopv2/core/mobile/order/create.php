<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Create_EweiShopV2Page extends MobileLoginPage
{
    static $bargain_id;

   
    protected function merchData()
    {
        $merch_plugin = p('merch');
        $merch_data = m('common')->getPluginset('merch');
        if ($merch_plugin && $merch_data['is_openmerch']) {
            $is_openmerch = 1;
        } else {
            $is_openmerch = 0;
        }

        return array(
            'is_openmerch' => $is_openmerch,
            'merch_plugin' => $merch_plugin,
            'merch_data' => $merch_data
        );
    }

    protected function diyformData($member, $goods_fields = false, $diyformid = false)
    {
        global $_W, $_GPC;

        $diyform_plugin = p('diyform');
        $order_formInfo = false;
        $diyform_set = false;
        $orderdiyformid = 0;
        $fields = array();
        $f_data = array();

        if ($diyform_plugin) {
            $diyform_set = $_W['shopset']['diyform'];
            if (!empty($diyform_set['order_diyform_open'])) {
                $orderdiyformid = intval($diyform_set['order_diyform']);
                if (!empty($orderdiyformid)) {
                    $order_formInfo = $diyform_plugin->getDiyformInfo($orderdiyformid);
                    $fields = $order_formInfo['fields'];
                    $f_data = $diyform_plugin->getLastOrderData($orderdiyformid, $member);
                }
            }

            if (!empty($diyformid)) {
                $order_formInfo = $diyform_plugin->getDiyformInfo($diyformid);
                $fields = $order_formInfo['fields'];
            } else if (!empty($goods_fields)) {

                $order_formInfo = $goods_fields;
                $fields = $goods_fields;
            }


        }

        return array(
            'diyform_plugin' => $diyform_plugin,
            'order_formInfo' => $order_formInfo,
            'diyform_set' => $diyform_set,
            'orderdiyformid' => $orderdiyformid,
            'has_fields' => !empty($fields),
            'fields' => $fields,
            'f_data' => $f_data
        );
    }


    function main()
    {

        global $_W, $_GPC;

        $trade = m('common')->getSysset('trade');
        $shop = m('common')->getSysset('shop');
        $area_set = m('util')->get_area_config_set();
        $new_area = intval($area_set['new_area']);
        $address_street = intval($area_set['address_street']);

        $member = m('member')->getMember($_W['openid']);


        $show_card = true; 
        if (p('exchange')) {
            $exchangeOrder = trim($_GPC['exchange']);
          
            $exchange_diyform = array();
            if (!empty($exchangeOrder)) {
                $show_card = false;
                $_SESSION['exchange'] = 1;
                $exchangepostage = $_SESSION['exchangepostage'];
                $exchangeprice = $_SESSION['exchangeprice'];
                if ($_GPC['dflag'] == '1') {
                    $exchangeprice = 0;
                }
                $exchangerealprice = $exchangeprice + $exchangepostage;
                if (!empty($_SESSION['diyform'])) {
                    $exchange_diyform = $_SESSION['diyform'];
                }
            } else {
                unset($_SESSION['exchange']);
                unset($_SESSION['exchangeprice']);
                unset($_SESSION['exchangepostage']);
            }
        }
        if (p('threen')) {
            $threenvip = p('threen')->getMember($_W['openid']);
            if (!empty($threenvip)) {
                $threenprice = true;
            }
        }
        if (p('quick')) {
            $quickid = intval($_GPC['fromquick']);
            if (!empty($quickid)) {
                $quickinfo = p("quick")->getQuick($quickid);
                if (empty($quickinfo)) {
                    $this->message("快速购买页面不存在");
                    exit();
                }
            }
        }


        $liveid = intval($_GPC['liveid']);
        $card_live_id = intval($_GPC['liveid']);

        if (p('live') && !empty($liveid)) {
            $isliving = p('live')->isLiving($liveid);
            if (!$isliving || $this->getdefaultMembercardId()) {
                $liveid = 0;
            }
        }

        $open_redis = function_exists('redis') && !is_error(redis());

        $seckillinfo = false;

        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];

        $goodsid = intval($_GPC['id']);

        $commission = m('common')->getPluginset('commission');

        $offic_register = false;
        if ($commission['become_goodsid'] == $goodsid) {
            $offic_register = true;
        }

        $giftid = intval($_GPC['giftid']);
        $giftGood = array();
        $sysset = m('common')->getSysset('trade');

        $allow_sale = true;

        $area_set = m('util')->get_area_config_set();
        $new_area = intval($area_set['new_area']);
        $address_street = intval($area_set['address_street']);

        $packageid = intval($_GPC['packageid']);
        if (!$packageid) {
            $merchdata = $this->merchData();
            extract($merchdata);

            $merch_array = array();
            $merchs = array();
            $merch_id = 0;
            $total_array = array();

            $member = m('member')->getMember($openid, true);
            $member['carrier_mobile'] = empty($member['carrier_mobile']) ? $member['mobile'] : $member['carrier_mobile'];

            $share = m('common')->getSysset('share');


            $level = m('member')->getLevel($openid);


            $id = intval($_GPC['id']);
            $iswholesale = intval($_GPC['iswholesale']);
            $bargain_id = intval($_GPC['bargainid']);
            $_SESSION['bargain_id'] = null;
            if (p('bargain') && !empty($bargain_id)) {//??
                $show_card = false;
                cache_write($_W['openid'] . '_bargain_id', $bargain_id);
                self::$bargain_id = $bargain_id;
                $bargain_act = pdo_fetch("SELECT *  FROM " . tablename('ewei_shop_bargain_actor') . " WHERE id = :id AND openid = :openid AND status = '0'", array(':id' => $bargain_id, ':openid' => $_W['openid']));
                if (empty($bargain_act)) {
                    die('没有这个商品!');
                }
                $bargain_act_id = pdo_fetch("SELECT *  FROM " . tablename('ewei_shop_bargain_goods') . " WHERE id = '{$bargain_act['goods_id']}'");
                if (empty($bargain_act_id)) {
                    die('没有这个商品!');
                }
                $if_bargain = pdo_fetch("SELECT bargain FROM " . tablename('ewei_shop_goods') . " WHERE id = :id AND uniacid = :uniacid ", array(':id' => $bargain_act_id['goods_id'], ':uniacid' => $_W['uniacid']));
                if (empty($if_bargain['bargain'])) {
                    die('没有这个商品!');
                }
                $id = $bargain_act_id['goods_id'];
            }


            $optionid = intval($_GPC['optionid']);

            $total = intval($_GPC['total']);
            if ($total < 1) {
                $total = 1;
            }
            $buytotal = $total; 
            $errcode = 0;

            $isverify = false;
            $isforceverifystore = false;
            $isvirtual = false;
            $isvirtualsend = false;
            $isonlyverifygoods = true;
            $changenum = false;
            $fromcart = 0;
            $hasinvoice = false;
            $invoicename = "";
            $buyagain_sale = true;

            $buyagainprice = 0;

            //所有商品
            $goods = array();

            if (empty($id)) {
                //购物车
                if (!empty($quickid)) {
                    $sql = 'SELECT c.goodsid,c.total,g.maxbuy,g.type,g.intervalfloor,g.intervalprice,g.issendfree,g.isnodiscount,g.ispresell,g.presellprice as gpprice,o.presellprice,g.preselltimeend,g.presellsendstatrttime,g.presellsendtime,g.presellsendtype'
                        . ',g.weight,o.weight as optionweight,g.title,g.thumb,ifnull(o.marketprice, g.marketprice) as marketprice,o.title as optiontitle,c.optionid,'
                        . ' g.storeids,g.isverify,g.isforceverifystore,g.deduct,g.manydeduct,g.manydeduct2,g.virtual,o.virtual as optionvirtual,discounts,'
                        . ' g.deduct2,g.ednum,g.edmoney,g.edareas,g.edareas_code,g.diyformtype,g.diyformid,diymode,g.dispatchtype,g.dispatchid,g.dispatchprice,g.minbuy '
                        . ' ,g.isdiscount,g.isdiscount_time,g.isdiscount_time_start,g.isdiscount_discounts,g.cates,g.isfullback, '
                        . ' g.virtualsend,invoice,o.specs,g.merchid,g.checked,g.merchsale,g.unite_total,'
                        . ' g.buyagain,g.buyagain_islong,g.buyagain_condition, g.buyagain_sale, g.hasoption, g.threen'
                        . ' FROM ' . tablename('ewei_shop_quick_cart') . ' c '
                        . ' left join ' . tablename('ewei_shop_goods') . ' g on c.goodsid = g.id '
                        . ' left join ' . tablename('ewei_shop_goods_option') . ' o on c.optionid = o.id '
                        . " where c.openid=:openid and c.selected=1 and  c.deleted=0 and c.uniacid=:uniacid and c.quickid={$quickid}  order by c.id desc";
                    $goods = pdo_fetchall($sql, array(':uniacid' => $uniacid, ':openid' => $openid));
                } elseif (empty($exchangeOrder)) {
                    $sql = 'SELECT c.goodsid,c.total,g.maxbuy,g.type,g.intervalfloor,g.intervalprice,g.issendfree,g.isnodiscount,g.ispresell,g.presellprice as gpprice,o.presellprice,g.preselltimeend,g.presellsendstatrttime,g.presellsendtime,g.presellsendtype'
                        . ',g.weight,o.weight as optionweight,g.title,g.thumb,ifnull(o.marketprice, g.marketprice) as marketprice,o.title as optiontitle,c.optionid,'
                        . ' g.storeids,g.isverify,g.isforceverifystore,g.deduct,g.manydeduct,g.manydeduct2,g.virtual,o.virtual as optionvirtual,discounts,'
                        . ' g.deduct2,g.ednum,g.edmoney,g.edareas,g.edareas_code,g.diyformtype,g.diyformid,diymode,g.dispatchtype,g.dispatchid,g.dispatchprice,g.minbuy '
                        . ' ,g.isdiscount,g.isdiscount_time,g.isdiscount_time_start,g.isdiscount_discounts,g.cates, '
                        . ' g.virtualsend,invoice,o.specs,g.merchid,g.checked,g.merchsale,'
                        . ' g.buyagain,g.buyagain_islong,g.buyagain_condition, g.buyagain_sale, g.hasoption'
                        . ' FROM ' . tablename('ewei_shop_member_cart') . ' c '
                        . ' left join ' . tablename('ewei_shop_goods') . ' g on c.goodsid = g.id '
                        . ' left join ' . tablename('ewei_shop_goods_option') . ' o on c.optionid = o.id '
                        . " where c.openid=:openid and c.selected=1 and  c.deleted=0 and c.uniacid=:uniacid  order by c.id desc";
                    $goods = pdo_fetchall($sql, array(':uniacid' => $uniacid, ':openid' => $openid));
                } elseif (p('exchange')) {
                    $sql = 'SELECT c.goodsid,c.total,g.maxbuy,g.type,g.intervalfloor,g.intervalprice,g.issendfree,g.isnodiscount,g.ispresell,g.presellprice as gpprice,o.presellprice,g.preselltimeend,g.presellsendstatrttime,g.presellsendtime,g.presellsendtype'
                        . ',g.weight,o.weight as optionweight,g.title,g.thumb,ifnull(o.marketprice, g.marketprice) as marketprice,o.title as optiontitle,c.optionid,'
                        . ' g.storeids,g.isverify,g.isforceverifystore,g.deduct,g.manydeduct,g.manydeduct2,g.virtual,o.virtual as optionvirtual,discounts,'
                        . ' g.deduct2,g.ednum,g.edmoney,g.edareas,g.edareas_code,g.diyformtype,g.diyformid,diymode,g.dispatchtype,g.dispatchid,g.dispatchprice,g.minbuy '
                        . ' ,g.isdiscount,g.isdiscount_time,g.isdiscount_time_start,g.isdiscount_discounts,g.cates,g.isfullback, '
                        . ' g.virtualsend,invoice,o.specs,g.merchid,g.checked,g.merchsale,g.unite_total,'
                        . ' g.buyagain,g.buyagain_islong,g.buyagain_condition, g.buyagain_sale, g.hasoption'
                        . ' FROM ' . tablename('ewei_shop_exchange_cart') . ' c '
                        . ' left join ' . tablename('ewei_shop_goods') . ' g on c.goodsid = g.id '
                        . ' left join ' . tablename('ewei_shop_goods_option') . ' o on c.optionid = o.id '
                        . " where c.openid=:openid and c.selected=1 and  c.deleted=0 and c.uniacid=:uniacid  order by c.id desc";
                    $goods = pdo_fetchall($sql, array(':uniacid' => $uniacid, ':openid' => $openid));
                }

                if (empty($goods)) {
                    header("location: " . mobileUrl('member/cart'));
                    exit;
                    $errcode = 1;
                    include $this->template();
                    exit;
                } else {
                    $merch_dif = array();
                    $diyformdata = $this->diyformData($member);
                    extract($diyformdata);
                    foreach ($goods as $k => $v) {
                        $merch_dif[] = $v['merchid'];
                        if ($v['type'] == 4) {
                            $intervalprice = iunserializer($v['intervalprice']);


                            if ($v['intervalfloor'] > 0) {
                                $goods[$k]['intervalprice1'] = $intervalprice[0]['intervalprice'];
                                $goods[$k]['intervalnum1'] = $intervalprice[0]['intervalnum'];
                            }
                            if ($v['intervalfloor'] > 1) {
                                $goods[$k]['intervalprice2'] = $intervalprice[1]['intervalprice'];
                                $goods[$k]['intervalnum2'] = $intervalprice[1]['intervalnum'];
                            }
                            if ($v['intervalfloor'] > 2) {
                                $goods[$k]['intervalprice3'] = $intervalprice[2]['intervalprice'];
                                $goods[$k]['intervalnum3'] = $intervalprice[2]['intervalnum'];
                            }
                        }

                        $opdata = array();
                        if ($v['hasoption'] > 0) {
                            $opdata = m('goods')->getOption($v['goodsid'], $v['optionid']);
                            if (empty($opdata) || empty($v['optionid'])) {
                                $this->message('商品' . $v['title'] . '的规格不存在,请到购物车删除该商品重新选择规格!', '', 'error');
                            }

                            if (!empty($v['unite_total'])) {
                                $total_array[$v['goodsid']]['total'] += $v['total'];
                            }
                        }
                        if (!empty($opdata)) {
                            $goods[$k]['marketprice'] = $v['marketprice'];
                        }
                        if ($v['ispresell'] > 0 && ($v['preselltimeend'] == 0 || $v['preselltimeend'] > time())) {
                            $goods[$k]['marketprice'] = intval($v['hasoption']) > 0 ? $v['presellprice'] : $v['gpprice'];
                        }
                        //全返商品---咖啡2017-03-17
                        $fullbackgoods = array();
                        if ($v['isfullback']) {
                            $fullbackgoods = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_fullback_goods') . " WHERE goodsid = :goodsid and uniacid = :uniacid and status = 1 limit 1 ", array(':goodsid' => $v['goodsid'], ':uniacid' => $uniacid));
                        }

                        if ($is_openmerch == 0) {
                            //未开启多商户的情况下,购物车中是否有多商户的商品
                            if ($v['merchid'] > 0) {
                                $err = true;
                                include $this->template('goods/detail');
                                exit;
                            }
                        } else {
                            //判断多商户商品是否通过审核
                            if ($v['merchid'] > 0 && $v['checked'] == 1) {
                                $err = true;
                                include $this->template('goods/detail');
                                exit;
                            }
                        }

                        //读取规格的图片
                        if (!empty($v['specs'])) {
                            $thumb = m('goods')->getSpecThumb($v['specs']);
                            if (!empty($thumb)) {
                                $goods[$k]['thumb'] = $thumb;
                            }
                        }
                        if (!empty($v['optionvirtual'])) {
                            $goods[$k]['virtual'] = $v['optionvirtual'];
                        }
                        if (!empty($v['optionweight'])) {
                            $goods[$k]['weight'] = $v['optionweight'];
                        }

                        //秒杀信息
                        $goods[$k]['seckillinfo'] = plugin_run('seckill::getSeckill', $v['goodsid'], $v['optionid'], true, $_W['openid']);
                        if (!empty($goods[$k]['seckillinfo']['maxbuy']) && $goods[$k]['total'] > $goods[$k]['seckillinfo']['maxbuy'] - $goods[$k]['seckillinfo']['selfcount']) {
                            $this->message('您已购买了' . $goods[$k]['seckillinfo']['selfcount'] . '最多购买' . $goods[$k]['seckillinfo']['maxbuy'] . '件', null, 'danger');
                        }
                    }
                    $merch_dif = array_flip(array_flip($merch_dif));
                    if ($exchangepostage && !is_array($_SESSION['exchange_postage_info'])) {
                        // 兑换中心按单计算运费时，有多商户拆单情况下的计算运费
                        $exchange_postage_count = count($merch_dif) * $exchangepostage;
                        $exchangerealprice = $exchangerealprice - $exchangepostage + $exchange_postage_count;
                        $exchangepostage = $exchange_postage_count;
                    }
                    $goods = m("goods")->wholesaleprice($goods);

                    foreach ($goods as $k => $v) {
                        if ($v['type'] == 4) {
                            $goods[$k]['marketprice'] = $v['wholesaleprice'];
                        }
                    }
                }
                $fromcart = 1;
            } else if (!empty($id) && !empty($iswholesale)) {
                //批发商品
                $show_card = false;
                $sql = 'SELECT id as goodsid,type,title,weight,issendfree,isnodiscount,ispresell,presellprice,'
                    . ' thumb,marketprice,storeids,isverify,isforceverifystore,deduct,hasoption,preselltimeend,presellsendstatrttime,presellsendtime,presellsendtype,'
                    . ' manydeduct,manydeduct2,`virtual`,maxbuy,usermaxbuy,discounts,total as stock,deduct2,showlevels,'
                    . ' ednum,edmoney,edareas,edareas_code,unite_total,'
                    . ' diyformtype,diyformid,diymode,dispatchtype,dispatchid,dispatchprice,cates,minbuy, '
                    . ' isdiscount,isdiscount_time,isdiscount_time_start,isdiscount_discounts, '
                    . ' virtualsend,invoice,needfollow,followtip,followurl,merchid,checked,merchsale, '
                    . ' buyagain,buyagain_islong,buyagain_condition, buyagain_sale ,intervalprice ,intervalfloor '
                    . ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
                $data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $id));

                if (empty($data) || $data['type'] != 4) {
                    $this->message('商品不存在!', '', 'error');
                }
                $diyformdata = $this->diyformData($member);
                extract($diyformdata);
                $intervalprice = iunserializer($data['intervalprice']);

                if ($data['intervalfloor'] > 0) {
                    $data['intervalprice1'] = $intervalprice[0]['intervalprice'];
                    $data['intervalnum1'] = $intervalprice[0]['intervalnum'];
                }
                if ($data['intervalfloor'] > 1) {
                    $data['intervalprice2'] = $intervalprice[1]['intervalprice'];
                    $data['intervalnum2'] = $intervalprice[1]['intervalnum'];
                }
                if ($data['intervalfloor'] > 2) {
                    $data['intervalprice3'] = $intervalprice[2]['intervalprice'];
                    $data['intervalnum3'] = $intervalprice[2]['intervalnum'];
                }


                $buyoptions = $_GPC['buyoptions'];
                $optionsdata = json_decode(htmlspecialchars_decode($buyoptions, ENT_QUOTES), true);

                if (empty($optionsdata) || !is_array($optionsdata)) {
                    $this->message('商品' . $data['title'] . '的规格不存在,请重新选择规格!', '', 'error');
                }

                $follow = m("user")->followed($openid);
                if (!empty($data['needfollow']) && !$follow && is_weixin()) {
                    $followtip = empty($goods['followtip']) ? "如果您想要购买此商品，需要您关注我们的公众号，点击【确定】关注后再来购买吧~" : $goods['followtip'];
                    $followurl = empty($goods['followurl']) ? $_W['shopset']['share']['followurl'] : $goods['followurl'];
                    $this->message($followtip, $followurl, 'error');
                }

                $total = 0;

                foreach ($optionsdata as $option) {

                    $good = $data;
                    $num = intval($option['total']);

                    if ($num <= 0) {
                        continue;
                    }

                    $total = $total + $num;
                    $good['total'] = $num;
                    $good['optionid'] = $option['optionid'];

                    if ($option['optionid'] > 0) {
                        $option = pdo_fetch('select id,title,marketprice,presellprice,goodssn,productsn,`virtual`,stock,weight,specs from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $uniacid, ':goodsid' => $id, ':id' => $option['optionid']));
                        if (!empty($option)) {
                            $good['optiontitle'] = $option['title'];
                            $good['virtual'] = $option['virtual'];

                            if (empty($data['unite_total'])) {
                                $data['stock'] = $option['stock'];


                                if ($option['stock'] < $num && $option['stock'] > -1) {
                                    $this->message('商品' . $data['title'] . '的购买数量超过库存剩余数量,请重新选择规格!', '', 'error');
                                }
                            }

                            if (!empty($option['weight'])) {
                                $data['weight'] = $option['weight'];
                            }
                            //读取规格的图片
                            if (!empty($option['specs'])) {
                                $thumb = m('goods')->getSpecThumb($option['specs']);
                                if (!empty($thumb)) {
                                    $data['thumb'] = $thumb;
                                }
                            }
                        } else {
                            if (!empty($data['hasoption'])) {
                                $this->message('商品' . $data['title'] . '的规格不存在,请重新选择规格!', '', 'error');
                            }
                        }
                    }

                    $goods[] = $good;
                }

                $goods = m("goods")->wholesaleprice($goods);


                foreach ($goods as $k => $v) {
                    if ($v['type'] == 4) {
                        $goods[$k]['marketprice'] = $v['wholesaleprice'];
                    }
                }

            } else {

                $threensql = "";
                if (p('threen') && !empty($threenprice)) {
                    $threensql .= ",threen";
                }

                $ishotelsql = "";
                if (p('hotelreservation')) {
                    //$ishotelsql .= ",ishotel";
                }

                //直接购买
                $sql = 'SELECT id as goodsid,type,title,weight,issendfree,isnodiscount,ispresell,presellprice,'
                    . ' thumb,marketprice,liveprice,islive,storeids,isverify,isforceverifystore,deduct,hasoption,preselltimeend,presellsendstatrttime,presellsendtime,presellsendtype,'
                    . ' manydeduct,manydeduct2,`virtual`,maxbuy,usermaxbuy,discounts,total as stock,deduct2,showlevels,'
                    . ' ednum,edmoney,edareas,edareas_code,unite_total,diyfields,'
                    . ' diyformtype,diyformid,diymode,dispatchtype,dispatchid,dispatchprice,cates,minbuy, '
                    . ' isdiscount,isdiscount_time,isdiscount_time_start,isdiscount_discounts,isfullback, '
                    . ' virtualsend,invoice,needfollow,followtip,followurl,merchid,checked,merchsale, '
                    . ' buyagain,buyagain_islong,buyagain_condition, buyagain_sale,bargain' . $threensql . $ishotelsql
                    . ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
                $data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $id));
                $threenprice = json_decode($data['threen'], 1);
                if ($data['merchid'] > 0) {

                    $merchstatus = pdo_fetch(" SELECT status FROM " . tablename('ewei_shop_merch_user') . " WHERE id=:id AND uniacid=:uniacid ", array(':id' => $data['merchid'], ':uniacid' => $_W['uniacid']));

                    if ($merchstatus['status'] != 1) {
                        $this->message('商品不存在或已下架', '', 'error');
                    }
                }

                if ($data['bargain'] > 0) {
                    if ($data['diyformtype'] == 2) {
//                    自定义表单
                        $diy = unserialize($data['diyfields']);
                        $diyformdata = $this->diyformData($member, $diy);

                    } elseif ($data['diyformtype'] == 1) {
                        $diyformdata = $this->diyformData($member, false, $data['diyformid']);

                    } else {
                        $diyformdata = $this->diyformData($member);

                    }
                } else {

                    $diyformdata = $this->diyformData($member);
                }

                extract($diyformdata);


                if ($data['ispresell'] > 0 && ($data['preselltimeend'] == 0 || $data['preselltimeend'] > time())) {
                    $data['marketprice'] = $data['presellprice'];
                    $show_card = false;//预售商品不能会员卡
                }


                // 直播价格处理 Step.2
                if (!empty($liveid)) {
                    $isLiveGoods = p('live')->isLiveGoods($data['goodsid'], $liveid);
                    if (!empty($isLiveGoods)) {
                        $defaultcardid = $this->getdefaultMembercardId();
                        if ($defaultcardid) {
                            $live_product = pdo_fetch("SELECT *  FROM " . tablename('ewei_shop_goods') . " WHERE id = '{$data['goodsid']}'");
                            if ($live_product) {
                                $data['marketprice'] = $live_product['marketprice'];
                            }

                        } else {
                            $data['marketprice'] = price_format($isLiveGoods['liveprice']);
                        }
                    }
                }


                //批发商品需要走批发流程
                if ($data['type'] == 4) {
                    $this->message('商品信息错误!', '', 'error');
                }

                //秒杀信息
                $data['seckillinfo'] = plugin_run('seckill::getSeckill', $data['goodsid'], $optionid, true, $_W['openid']);
                //秒杀不能用会员卡
                if ($data['seckillinfo']) {
                    $show_card = false;
                }

                if ($data['seckillinfo'] && $data['seckillinfo']['status'] == 0) {

                    //秒杀不管赠品

                } else if ($data['isverify'] == 2) {

                    //核销不管赠品
                } else {
                    if ($giftid) {
                        $gift = pdo_fetch("select id,title,thumb,activity,giftgoodsid,goodsid from " . tablename('ewei_shop_gift') . "
                where uniacid = " . $uniacid . " and id = " . $giftid . " and status = 1 and starttime <= " . time() . " and endtime >= " . time() . " ");
                        if (!strstr($gift['goodsid'], (string)$goodsid)) {
                            $this->message('赠品与商品不匹配或者商品没有赠品!', '', 'error');
                        }

                        $giftGood = array();
                        if (!empty($gift['giftgoodsid'])) {
                            $giftGoodsid = explode(',', $gift['giftgoodsid']);
                            if ($giftGoodsid) {
                                foreach ($giftGoodsid as $key => $value) {
                                    $giftGood[$key] = pdo_fetch("select id,title,thumb,marketprice from " . tablename('ewei_shop_goods') . " where uniacid = " . $uniacid . " and total > 0 and status = 2 and id = " . $value . " and deleted = 0 ");
                                }
                                $giftGood = array_filter($giftGood);
                            }
                        }
                    }
                }
                if (!empty($bargain_act)) {
                    $data['marketprice'] = $bargain_act['now_price'];//??
                }
                //全返商品---咖啡2017-03-17
                $fullbackgoods = array();
                if ($data['isfullback']) {
                    $fullbackgoods = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_fullback_goods') . " WHERE goodsid = :goodsid and uniacid = :uniacid and status = 1 limit 1 ", array(':goodsid' => $data['goodsid'], ':uniacid' => $uniacid));
                }

                if (empty($data) || (!empty($data['showlevels']) && !strexists($data['showlevels'], $member['level'])) || ($data['merchid'] > 0 && $data['checked'] == 1) || ($is_openmerch == 0 && $data['merchid'] > 0)) {
                    $err = true;
                    include $this->template('goods/detail');
                    exit;
                }
                $follow = m("user")->followed($openid);
                if (!empty($data['needfollow']) && !$follow && is_weixin()) {
                    $followtip = empty($data['followtip']) ? "如果您想要购买此商品，需要您关注我们的公众号，点击【确定】关注后再来购买吧~" : $data['followtip'];
                    $followurl = empty($data['followurl']) ? $_W['shopset']['share']['followurl'] : $data['followurl'];
                    $followqrcode = empty($_W['shopset']['share']['followqrcode']) ? $_W['account']['qrcode'] : tomedia($_W['shopset']['share']['followqrcode']);
                    //$followurl = empty($followurl) ? $followqrcode: $followurl;
                    $followurl = empty($followqrcode) ? $followurl : $followqrcode;//优先跳转二维码页面
                    $this->message($followtip, $followurl, 'error');
                }

                if ($data['minbuy'] > 0 && $total < $data['minbuy']) {
                    $total = $data['minbuy'];
                }

                //秒杀数量为1
                if ($data['seckillinfo'] && $data['seckillinfo']['status'] == 0) {
                    $total = 1;
                }

                $data['total'] = $total;

                $data['optionid'] = $optionid;
                if (!empty($optionid)) {
                    $option = pdo_fetch('select id,title,marketprice,liveprice,islive,presellprice,goodssn,productsn,`virtual`,stock,weight,specs,
                    `day`,allfullbackprice,fullbackprice,allfullbackratio,fullbackratio,isfullback
                    from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $uniacid, ':goodsid' => $id, ':id' => $optionid));
                    if (!empty($option)) {
                        $data['optionid'] = $optionid;
                        $data['optiontitle'] = $option['title'];
                        $data['marketprice'] = (intval($data['ispresell']) > 0 && ($data['preselltimeend'] > time() || $data['preselltimeend'] == 0)) ? $option['presellprice'] : $option['marketprice'];

                        if ($isliving && !empty($option['islive']) && $option['liveprice'] > 0) {
                            $data['marketprice'] = $option['liveprice'];
                        }


                        // 直播价格处理 Step.3
                        if (!empty($liveid)) {
                            $liveOption = p('live')->getLiveOptions($data['goodsid'], $liveid, array($option));
                            $defaultcardid = $this->getdefaultMembercardId();
                            if ($defaultcardid) { //有会员卡按照售价来
                                $gopdata = m('goods')->getOption($data['goodsid'], $optionid);;
                                if (empty($gopdata) != true) {
                                    $data['marketprice'] = price_format($gopdata['marketprice']);
                                }
                            } else {
                                if (!empty($liveOption) && !empty($liveOption[0])) {
                                    $data['marketprice'] = price_format($liveOption[0]['marketprice']);
                                }
                            }

                        }

                        $data['virtual'] = $option['virtual'];
                        if ($option['isfullback'] && !empty($fullbackgoods)) {
                            $fullbackgoods['minallfullbackallprice'] = $option['allfullbackprice'];
                            $fullbackgoods['fullbackprice'] = $option['fullbackprice'];
                            $fullbackgoods['minallfullbackallratio'] = $option['allfullbackratio'];
                            $fullbackgoods['fullbackratio'] = $option['fullbackratio'];
                            $fullbackgoods['day'] = $option['day'];
                        }

                        if (empty($data['unite_total'])) {
                            $data['stock'] = $option['stock'];
                        }

                        if (!empty($option['weight'])) {
                            $data['weight'] = $option['weight'];
                        }
                        //读取规格的图片
                        if (!empty($option['specs'])) {
                            $thumb = m('goods')->getSpecThumb($option['specs']);
                            if (!empty($thumb)) {
                                $data['thumb'] = $thumb;
                            }
                        }
                    } else {
                        if (!empty($data['hasoption'])) {
                            $this->message('商品' . $data['title'] . '的规格不存在,请重新选择规格!', '', 'error');
                        }

                    }
                }

                //可以调整数量
                if ($giftid) {
                    $changenum = false;
                } else {
                    $changenum = true;
                }


                //秒杀不能修改数量
                if ($data['seckillinfo'] && $data['seckillinfo']['status'] == 0) {
                    $changenum = false;
                }
                $goods[] = $data;
            }
            $goods = set_medias($goods, 'thumb');

            foreach ($goods as &$g) {
                if (($g['type'] == 4) || ($g['ispresell'] > 0 && ($g['preselltimeend'] == 0 || $g['preselltimeend'] > time()))) {
                    $show_card = false;//预售和批发不能使用会员卡
                }
                if ($g['seckillinfo'] && $g['seckillinfo']['status'] == 0) {
                    //秒杀不管任务
                    $g['is_task_goods'] = 0;
                    $show_card = false;//秒杀不能使用会员卡

                } else {

                    if (p('task')) {
                        $task_id = intval($_SESSION[$id . '_task_id']);
                        if (!empty($task_id)) {
                            $rewarded = pdo_fetchcolumn("SELECT `rewarded` FROM " . tablename('ewei_shop_task_extension_join') . " WHERE id = :id AND openid = :openid AND  uniacid = :uniacid", array(':id' => $task_id, ':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
                            $taskGoodsInfo = unserialize($rewarded);
                            $taskGoodsInfo = $taskGoodsInfo['goods'][$id];
                            if (!empty($optionid) && !empty($taskGoodsInfo['option']) && $optionid == $taskGoodsInfo['option']) {
                                $taskgoodsprice = $taskGoodsInfo['price'];
                            } elseif (empty($optionid)) {
                                $taskgoodsprice = $taskGoodsInfo['price'];
                            }
                        }
                    }


                    //任务活动购买商品
                    $rank = intval($_SESSION[$id . '_rank']);
                    $log_id = intval($_SESSION[$id . '_log_id']);
                    $join_id = intval($_SESSION[$id . '_join_id']);

                    $task_goods_data = m('goods')->getTaskGoods($openid, $id, $rank, $log_id, $join_id, $optionid);
                    if (empty($task_goods_data['is_task_goods'])) {
                        $g['is_task_goods'] = 0;
                    } else {
                        $allow_sale = false;
                        $g['is_task_goods'] = $task_goods_data['is_task_goods'];
                        $g['is_task_goods_option'] = $task_goods_data['is_task_goods_option'];
                        $g['task_goods'] = $task_goods_data['task_goods'];
                    }
                }


                //备注多商户商品有BUG缺少$g['merchid']>0判断
                if ($is_openmerch == 1 && $g['merchid'] > 0) {
                    $merchid = $g['merchid'];
                    $merch_array[$merchid]['goods'][] = $g['goodsid'];
                }

                if ($g['isverify'] == 2) {
                    //记次时商品
                    $isverify = true;

                }

                if ($g['isforceverifystore']) {
                    $isforceverifystore = true;
                }

                if (!empty($g['virtual']) || $g['type'] == 2 || $g['type'] == 3 || $g['type'] == 20) {
                    //虚拟商品
                    $isvirtual = true;

                    //是否虚拟物品自动发货
                    if ($g['virtualsend']) {
                        $isvirtualsend = true;
                    }

                    if ($g['type'] == 3) {
                        $isvirtualsend = true;
                    }
                }

                if ($g['invoice']) {
                    $hasinvoice = $g['invoice'];
                }

                //判断是否为纯记次时商品订单
                if ($g['type'] != 5) {
                    $isonlyverifygoods = false;
                }


                //最大购买量
                //库存
                $totalmaxbuy = $g['stock'];

                //最大购买量 秒杀只读取自己的总购买数限制 无二次购买
                if (!empty($g['seckillinfo']) && $g['seckillinfo']['status'] == 0) {

                    $seckilllast = 0;
                    if ($g['seckillinfo']['maxbuy'] > 0) {
                        $seckilllast = $g['seckillinfo']['maxbuy'] - $g['seckillinfo']['selfcount'];
                    }

                    $g['totalmaxbuy'] = $g['total'];


                } else {

                    //最大购买量
                    if ($g['maxbuy'] > 0) {

                        if ($totalmaxbuy != -1) {
                            if ($totalmaxbuy > $g['maxbuy']) {
                                $totalmaxbuy = $g['maxbuy'];
                            }
                        } else {
                            $totalmaxbuy = $g['maxbuy'];
                        }

                    }

                    //总购买量
                    if ($g['usermaxbuy'] > 0) {
                        $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('ewei_shop_order_goods') . ' og '
                            . ' left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id '
                            . ' where og.goodsid=:goodsid and  o.status>=0 and o.openid=:openid  and og.uniacid=:uniacid ', array(':goodsid' => $g['goodsid'], ':uniacid' => $uniacid, ':openid' => $openid));
                        $last = $data['usermaxbuy'] - $order_goodscount;
                        if ($last <= 0) {
                            $last = 0;
                        }
                        if ($totalmaxbuy != -1) {
                            if ($totalmaxbuy > $last) {
                                $totalmaxbuy = $last;
                            }
                        } else {
                            $totalmaxbuy = $last;
                        }
                    }

                    if (!empty($g['is_task_goods'])) {
                        if ($totalmaxbuy > $g['task_goods']['total']) {
                            $totalmaxbuy = $g['task_goods']['total'];
                        }
                    }

                    $g['totalmaxbuy'] = $totalmaxbuy;

                    if ($g['total'] > $g['totalmaxbuy'] && !empty($g['totalmaxbuy'])) {
                        $g['total'] = $g['totalmaxbuy'];
                    }


                    if (floatval($g['buyagain']) > 0 && empty($g['buyagain_sale'])) {
                        //第一次后买东西享受优惠
                        if (m('goods')->canBuyAgain($g)) {
                            $buyagain_sale = false;
                        }
                    }

                }

            }
            unset($g);
            $invoice_arr = "{}";
            if ($hasinvoice) {
                $invoicename = pdo_fetchcolumn('select invoicename from ' . tablename('ewei_shop_order') . " where openid=:openid and uniacid=:uniacid and ifnull(invoicename,'')<>'' order by id desc limit 1", array(':openid' => $openid, ':uniacid' => $uniacid));
                // 解析发票格式
                $invoice_arr = m('sale')->parseInvoiceInfo($invoicename);
                if ($invoice_arr['title'] === false) {
                    $invoicename = '';
                }
                $invoice_arr = json_encode($invoice_arr);
                $invoice_type = m('common')->getSysset('trade');
                $invoice_type = (int)($invoice_type['invoice_entity']);
                if ($invoice_type === 0) {
                    $invoicename = str_replace('电子', '纸质', $invoicename);
                } elseif ($invoice_type === 1) {
                    $invoicename = str_replace('纸质', '电子', $invoicename);
                }
            }

            if ($is_openmerch == 1) {
                //读取多商户营销设置
                foreach ($merch_array as $key => $value) {
                    if ($key > 0) {
                        $merch_id = $key;
                        $merch_array[$key]['set'] = $merch_plugin->getSet('sale', $key);
                        $merch_array[$key]['enoughs'] = $merch_plugin->getEnoughs($merch_array[$key]['set']);
                    }
                }
            }


            //商品总重量
            $weight = 0;

            //计算初始价格
            $total = 0; //商品数量
            $goodsprice = 0; //商品价格
            $realprice = 0; //需支付
            $deductprice = 0; //积分抵扣的
            $taskdiscountprice = 0; //任务活动优惠
            $carddiscountprice = 0; //会员卡折扣优惠
            $lotterydiscountprice = 0; //游戏活动优惠
            $card_lotterydiscountprice = 0; //会员卡游戏活动优惠
            $discountprice = 0; //会员优惠
            $isdiscountprice = 0; //促销优惠
            $deductprice2 = 0; //余额抵扣限额
            $stores = array(); //核销门店
            $address = false; //默认地址
            $carrier = false; //自提地点
            $carrier_list = array(); //自提点
            $dispatch_list = false;
            $dispatch_price = 0; //邮费

            $seckill_dispatchprice = 0; //秒杀商品的运费
            $seckill_price = 0;//秒杀减少的金额
            $seckill_payprice = 0;//秒杀的消费金额

            $ismerch = 0;

            if ($is_openmerch == 1) {
                if (!empty($merch_array)) {
                    if (count($merch_array) > 1) {
                        $ismerch = 1;
                    }
                }
            }
            if (empty($merch_array) != true && count($goods) == count($merch_array)) {
                $show_card = false;//多商户不能选择会员卡

            }


            if (!$isverify && !$isvirtual && !$ismerch) { //虚拟 或 卡密 或 不同多商户的商品 不读取自提点
                if ($merch_id > 0) {
                    $carrier_list = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 and type in(1,3) order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid'], ':merchid' => $merch_id));
                } else {
                    $carrier_list = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 and type in(1,3) order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid']));
                }
            }

            //营销插件
            $sale_plugin = com('sale');
            $saleset = false;
            if ($if_bargain) {
                $allow_sale = false;
            }
            if ($sale_plugin && $buyagain_sale && $allow_sale) {
                $saleset = $_W['shopset']['sale'];
                $saleset['enoughs'] = $sale_plugin->getEnoughs();
            }

            $card_id = $this->getdefaultMembercardId($goods);
            $goods_dispatch = array();
            //计算产品成交价格及是否包邮
            foreach ($goods as &$g) {

                if (empty($g['total']) || intval($g['total']) < 1) {
                    $g['total'] = 1;
                }

                //秒杀无优惠
                if ($taskcut || $g['seckillinfo'] && $g['seckillinfo']['status'] == 0) {
                    $gprice = $g['marketprice'] * $g['total'];
                    $g['ggprice'] = $g['seckillinfo']['price'] * $g['total'];
                    $seckill_payprice += $g['seckillinfo']['price'] * $g['total'];
                    $seckill_price += ($g['marketprice'] * $g['total'] - $seckill_payprice);


                } else {
                    //商品原价
                    $gprice = $g['marketprice'] * $g['total'];

                    //促销或会员折扣
                    $prices = m('order')->getGoodsDiscountPrice($g, $level, 0, $card_id);

                    $g['ggprice'] = $prices['price'];
                    $g['unitprice'] = $prices['unitprice'];
                }


                if ($is_openmerch == 1) {
                    $merchid = $g['merchid'];
                    $merch_array[$merchid]['ggprice'] += $g['ggprice'];
                    $merchs[$merchid] += $g['ggprice'];
                }

                $g['dflag'] = intval($g['ggprice'] < $gprice);


                if ($g['seckillinfo'] && $g['seckillinfo']['status'] == 0) {
                    // if ($g['seckillinfo'] && $g['seckillinfo']['status'] == 0|| $_SESSION['taskcut']) {
                    //秒杀不管优惠
                } else {
                    if (empty($bargain_id)) {//如果不是砍价订单,执行下面语句
                        //任务活动优惠
                        $taskdiscountprice += $prices['taskdiscountprice'];
                        if ($card_id) {

                            $lotterydiscountprice = 0;
                        } else {
                            $lotterydiscountprice += $prices['lotterydiscountprice'];
                        }

                        $card_lotterydiscountprice += $prices['lotterydiscountprice'];//会员卡使用的游戏活动优惠的价格
                        //折扣价格
                        $g['taskdiscountprice'] = $prices['taskdiscountprice'];
                        $g['lotterydiscountprice'] = $prices['lotterydiscountprice'];
                        $g['discountprice'] = $prices['discountprice'];
                        $g['isdiscountprice'] = $prices['isdiscountprice'];
                        $g['discounttype'] = $prices['discounttype'];
                        $g['isdiscountunitprice'] = $prices['isdiscountunitprice'];
                        $g['discountunitprice'] = $prices['discountunitprice'];

                        $buyagainprice += $prices['buyagainprice'];

                        if ($prices['discounttype'] == 1) {
                            //促销优惠
                            $isdiscountprice += $prices['isdiscountprice'];
                        } else if ($prices['discounttype'] == 2 && empty($bargain_id)) {
                            //会员优惠
                            $discountprice += $prices['discountprice'];
                        }
                        if ($threenprice && !empty($threenprice['price'])) {
                            $discountprice += $g['marketprice'] - $threenprice['price'];
                        } elseif ($threenprice && !empty($threenprice['discount'])) {
                            $discountprice += (10 - $threenprice['discount']) / 10 * $g['marketprice'];
                        }

                        //2018-07-22游戏营销使用会员卡重新获取优惠促销和会员促销
                        $task_goods_data = m('goods')->getTaskGoods($openid, $id, $rank, $log_id, $join_id, $optionid);
                        if ($task_goods_data['is_task_goods'] && $log_id && $card_id) {
                            $g['is_task_goods'] = 0;
                            $youxi_prices = m('order')->getGoodsDiscountPrice($g, $level);
                            $g['discountprice'] = $youxi_prices['discountprice'];
                            $g['isdiscountprice'] = $youxi_prices['isdiscountprice'];
                            $g['discounttype'] = $youxi_prices['discounttype'];
                            $g['isdiscountunitprice'] = $youxi_prices['isdiscountunitprice'];
                            $g['discountunitprice'] = $youxi_prices['discountunitprice'];
                            if ($youxi_prices['discounttype'] == 1) {
                                //促销优惠
                                $isdiscountprice += $youxi_prices['isdiscountprice'];
                            } else if ($youxi_prices['discounttype'] == 2) {
                                //会员优惠
                                $discountprice += $youxi_prices['discountprice'];
                            }
                        }
                        //游戏营销使用会员卡结束
                    }
                }


                //需要支付
                $realprice += $g['ggprice'];


                //商品原价
                //$goodsprice += $gprice;
                if ($gprice > $g['ggprice']) {
                    $goodsprice += $gprice;
                } else {
                    $goodsprice += $g['ggprice'];
                }

                //商品数据
                $total += $g['total'];


                if (empty($bargain_id)) {//如果不是砍价订单,执行下面语句

                    if ($g['seckillinfo'] && $g['seckillinfo']['status'] == 0) {
                        //秒杀不参与二次购买
                        $g['deduct'] = 0;
                    } else {
                        if (floatval($g['buyagain']) > 0 && empty($g['buyagain_sale'])) {
                            //第一次后买东西享受优惠
                            if (m('goods')->canBuyAgain($g)) {
                                $g['deduct'] = 0;
                            }
                        }
                    }

                    if ($g['seckillinfo'] && $g['seckillinfo']['status'] == 0) {
                        //秒杀不参与抵扣
                    } else {


                        if ($open_redis) {

                            if ($g['deduct'] > $g['ggprice']) {
                                $g['deduct'] = $g['ggprice'];
                            }

                            //积分抵扣
                            if ($g['manydeduct']) {
                                $deductprice += $g['deduct'] * $g['total'];
                            } else {
                                $deductprice += $g['deduct'];
                            }

                            //余额抵扣限额
                            $deccredit2 = 0;        //可抵扣的余额
                            if ($g['deduct2'] == 0) {
                                //全额抵扣
                                //$deductprice2 += $g['ggprice'];
                                $deccredit2 = $g['ggprice'];
                            } else if ($g['deduct2'] > 0) {
                                $temp_ggprice = $g['ggprice'] / $g['total'];
                                if ($g['deduct2'] > $temp_ggprice) {
                                    $deccredit2 = $temp_ggprice;
                                } else {
                                    $deccredit2 = $g['deduct2'];
                                }
                            }

                            if ($g['manydeduct2']) {
                                $deccredit2 = $deccredit2 * $g['total'];
                            }

                            $deductprice2 += $deccredit2;

                        }
                    }
                }

            }
            unset($g);

            if ($isverify) {
                //核销单 所有核销门店
                $storeids = array();
                $merchid = 0;
                foreach ($goods as $g) {
                    $merchid = $g['merchid'];
                    if (!empty($g['storeids'])) {
                        $storeids = array_merge(explode(',', $g['storeids']), $storeids);

                    }
                }


                if (empty($storeids)) {
                    //门店加入支持核销的判断
                    if ($merchid > 0) {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3) order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
                    } else {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 and type in(2,3) order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid']));
                    }
                } else {
                    if ($merchid > 0) {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3) order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
                    } else {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1 and type in(2,3) order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid']));
                    }
                }
            } else {
                //默认地址
                $address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where openid=:openid and deleted=0 and isdefault=1  and uniacid=:uniacid limit 1'
                    , array(':uniacid' => $uniacid, ':openid' => $openid));
                if (!empty($carrier_list)) {
                    $carrier = $carrier_list[0];
                }
                //实体物品计算运费
                if (!$isvirtual && !$isonlyverifygoods) {
                    $dispatch_array = m('order')->getOrderDispatchPrice($goods, $member, $address, $saleset, $merch_array, 0);
                    $dispatch_price = $dispatch_array['dispatch_price'] - $dispatch_array['seckill_dispatch_price'];
                    $seckill_dispatchprice = $dispatch_array['seckill_dispatch_price'];
                }
            }


            //多商户满减
            if ($is_openmerch == 1) {
                if (empty($bargain_id)) {
                    $merch_enough = m('order')->getMerchEnough($merch_array);
                    $merch_array = $merch_enough['merch_array'];
                    $merch_enough_total = $merch_enough['merch_enough_total'];
                    $merch_saleset = $merch_enough['merch_saleset'];

                    if ($merch_enough_total > 0) {
                        $realprice -= $merch_enough_total;
                    }
                }
            }

            if ($saleset) {
                //满额减
                if (empty($bargain_id)) {
                    foreach ($saleset['enoughs'] as $e) {
                        if ($realprice - $seckill_payprice >= floatval($e['enough']) && floatval($e['money']) > 0) { //减掉秒杀的金额再算满减
                            $saleset['showenough'] = true;
                            $saleset['enoughmoney'] = $e['enough'];
                            $saleset['enoughdeduct'] = $e['money'];
                            $realprice -= floatval($e['money']);
                            break;
                        }
                    }
                }
                $include_dispath = false;
                //余额抵扣加上运费
                if (empty($saleset['dispatchnodeduct'])) {
                    $deductprice2 += $dispatch_price;
                    if (!empty($dispatch_price)) {
                        $include_dispath = true;
                    }
                }
            }

            $realprice += $dispatch_price + $seckill_dispatchprice;

            $deductcredit = 0; //抵扣需要扣除的积分
            $deductmoney = 0; //抵扣的钱
            $deductcredit2 = 0; //余额抵扣的钱


            //积分抵扣
            if (!empty($saleset)) {
                if (!empty($saleset['creditdeduct'])) {
                    $credit = $member['credit1'];
                    if ($credit > 0) {
                        $credit = floor($credit);
                    }
                    $pcredit = intval($saleset['credit']); //积分比例
                    $pmoney = round(floatval($saleset['money']), 2); //抵扣比例

                    if ($pcredit > 0 && $pmoney > 0) {
                        if ($credit % $pcredit == 0) {
                            $deductmoney = round(intval($credit / $pcredit) * $pmoney, 2);
                        } else {
                            $deductmoney = round((intval($credit / $pcredit) + 1) * $pmoney, 2);
                        }
                    }
                    if ($deductmoney > $deductprice) {
                        $deductmoney = $deductprice;
                    }
                    if ($deductmoney > $realprice - $seckill_payprice) {  //减掉秒杀的金额再抵扣
                        $deductmoney = $realprice - $seckill_payprice;
                    }
                    if ($pmoney * $pcredit != 0) {
                        $deductcredit = ceil($deductmoney / $pmoney * $pcredit);
                    }
                }

                if (!empty($saleset['moneydeduct'])) {

                    $deductcredit2 = m('member')->getCredit($openid, 'credit2');
                    if ($deductcredit2 > $realprice - $seckill_payprice) {  //减掉秒杀的金额再抵扣
                        $deductcredit2 = $realprice - $seckill_payprice;
                    }
                    if ($deductcredit2 > $deductprice2) {
                        $deductcredit2 = $deductprice2;
                    }

                }
            }

            //商品数据
            $goodsdata = array();
            $goodsdata_temp = array();
            //订单满金额赠品
            $gifts = array();


            foreach ($goods as $g) {


                $goodsdata[] = array('goodsid' => $g['goodsid'], 'total' => $g['total'], 'optionid' => $g['optionid'], 'marketprice' => $g['marketprice']
                , 'merchid' => $g['merchid'], 'cates' => $g['cates'], 'discounttype' => $g['discounttype'], 'isdiscountprice' => $g['isdiscountprice']
                , 'discountprice' => $g['discountprice'], 'isdiscountunitprice' => $g['isdiscountunitprice'], 'discountunitprice' => $g['discountunitprice']
                , 'type' => $g['type'], 'intervalfloor' => $g['intervalfloor']
                , 'intervalprice1' => $g['intervalprice1'], 'intervalnum1' => $g['intervalnum1']
                , 'intervalprice2' => $g['intervalprice2'], 'intervalnum2' => $g['intervalnum2']
                , 'intervalprice3' => $g['intervalprice3'], 'intervalnum3' => $g['intervalnum3']
                , 'wholesaleprice' => $g['wholesaleprice'], 'goodsalltotal' => $g['goodsalltotal'], 'isnodiscount' => $g['isnodiscount'], 'deduct' => $g['deduct'], 'deduct2' => $g['deduct2'], 'ggprice' => $g['ggprice'], 'manydeduct' => $g['manydeduct'], 'manydeduct2' => $g['manydeduct2']);
                if ($g['seckillinfo'] && $g['seckillinfo']['status'] == 0) {
                    //秒杀不管二次购买
                } else {
                    if (floatval($g['buyagain']) > 0) {
                        //第一次后买东西享受优惠
                        if (!m('goods')->canBuyAgain($g) || !empty($g['buyagain_sale'])) {
                            $goodsdata_temp[] = array('goodsid' => $g['goodsid'], 'total' => $g['total'], 'optionid' => $g['optionid'], 'marketprice' => $g['marketprice']
                            , 'merchid' => $g['merchid'], 'cates' => $g['cates'], 'discounttype' => $g['discounttype'], 'isdiscountprice' => $g['isdiscountprice']
                            , 'discountprice' => $g['discountprice'], 'isdiscountunitprice' => $g['isdiscountunitprice'], 'discountunitprice' => $g['discountunitprice']
                            , 'type' => $g['type'], 'intervalfloor' => $g['intervalfloor']
                            , 'intervalprice1' => $g['intervalprice1'], 'intervalnum1' => $g['intervalnum1']
                            , 'intervalprice2' => $g['intervalprice2'], 'intervalnum2' => $g['intervalnum2']
                            , 'intervalprice3' => $g['intervalprice3'], 'intervalnum3' => $g['intervalnum3']
                            , 'wholesaleprice' => $g['wholesaleprice'], 'goodsalltotal' => $g['goodsalltotal'], 'isnodiscount' => $g['isnodiscount']);
                        }
                    } else {
                        $goodsdata_temp[] = array('goodsid' => $g['goodsid'], 'total' => $g['total'], 'optionid' => $g['optionid'], 'marketprice' => $g['marketprice']
                        , 'merchid' => $g['merchid'], 'cates' => $g['cates'], 'discounttype' => $g['discounttype'], 'isdiscountprice' => $g['isdiscountprice']
                        , 'discountprice' => $g['discountprice'], 'isdiscountunitprice' => $g['isdiscountunitprice'], 'discountunitprice' => $g['discountunitprice']
                        , 'type' => $g['type'], 'intervalfloor' => $g['intervalfloor']
                        , 'intervalprice1' => $g['intervalprice1'], 'intervalnum1' => $g['intervalnum1']
                        , 'intervalprice2' => $g['intervalprice2'], 'intervalnum2' => $g['intervalnum2']
                        , 'intervalprice3' => $g['intervalprice3'], 'intervalnum3' => $g['intervalnum3']
                        , 'wholesaleprice' => $g['wholesaleprice'], 'goodsalltotal' => $g['goodsalltotal'], 'isnodiscount' => $g['isnodiscount']);
                    }
                }


                if ($g['seckillinfo'] && $g['seckillinfo']['status'] == 0) {
                    //秒杀不管赠品

                } else if ($g['isverify'] == 2) {

                    // @author 青椒 @date 2018/2/3 核销不管赠品
                } else {
                    //指定商品赠品
                    if ($giftid) {
                        $gift = array();
                        $giftdata = pdo_fetch("select giftgoodsid from " . tablename('ewei_shop_gift') . " where uniacid = " . $uniacid . " and id = " . $giftid . " and status = 1 and starttime <= " . time() . " and endtime >= " . time() . " ");
                        if ($giftdata['giftgoodsid']) {
                            $giftgoodsid = explode(',', $giftdata['giftgoodsid']);

                            foreach ($giftgoodsid as $key => $value) {
                                $giftinfo = pdo_fetch("select id as goodsid,title,thumb from " . tablename('ewei_shop_goods') . " where uniacid = " . $uniacid . " and total > 0 and status = 2 and id = " . $value . " and deleted = 0 ");
                                if ($giftinfo) {
                                    $gift[$key] = $giftinfo;
                                    $gift[$key]['total'] = 1;
                                }
                            }
                            if ($gift) {
                                $gift = array_filter($gift);
                                $goodsdata = array_merge($goodsdata, $gift);
                            }
                        }
                    } else {//订单满额赠品
                        $isgift = 0;
                        $giftgoods = array();
                        $gift_price = array();
                        $gifts = pdo_fetchall("select id,goodsid,giftgoodsid,thumb,title ,orderprice from " . tablename('ewei_shop_gift') . "
                    where uniacid = " . $uniacid . " and status = 1 and starttime <= " . time() . " and endtime >= " . time() . " and orderprice <= " . $goodsprice . " and activity = 1 ");
                        foreach ($gifts as $key => $value) {
                            $giftgoods = explode(",", $value['giftgoodsid']);
                            array_push($gift_price, $value['orderprice']);
                            foreach ($giftgoods as $k => $val) {
                                $giftgoodsdetail = pdo_fetch("select id,title,thumb,marketprice,total from " . tablename('ewei_shop_goods') . " where uniacid = " . $uniacid . " and deleted = 0 and status = 2 and id = " . $val . " ");
                                if ($giftgoodsdetail) {
                                    $gifts[$key]['gift'][$k] = $giftgoodsdetail;
                                    $isgift = 1;
                                }
                                if ($giftgoodsdetail['total'] <= 0) {
                                    $gifts[$key]['canchose'] = 0;
                                } else {
                                    $gifts[$key]['canchose'] = 1;
                                }
                            }
                            $gifts = array_filter($gifts);
                            $gifttitle = $gifts[$key]['gift'][$key]['title'] ? $gifts[$key]['gift'][$key]['title'] : '赠品';
                        }
                    }
                }
            }

            if (!empty($gifts) && count($gifts) == 1) {
                $giftid = $gifts[0]['id'];
            }


            //可用优惠券(减掉秒杀的商品及总价)


            $couponcount = com_run('coupon::consumeCouponCount', $openid, $realprice, $merch_array, $goodsdata_temp);
            $couponcount += com_run('wxcard::consumeWxCardCount', $openid, $merch_array, $goodsdata_temp);
            if (empty($goodsdata_temp) || !$allow_sale) {
                $couponcount = 0;
            }


            // 强制绑定手机号
            $mustbind = 0;
            if (!empty($_W['shopset']['wap']['open']) && !empty($_W['shopset']['wap']['mustbind']) && empty($member['mobileverify'])) {
                $mustbind = 1;
            }

            if ($is_openmerch == 1) {
                $merchs = $merch_plugin->getMerchs($merch_array);
            }


            //订单创建数据
            $createInfo = array(
                'id' => $id,
                'gdid' => intval($_GPC['gdid']),
                'fromcart' => $fromcart,
                'addressid' => !empty($address) && !$isverify && !$isvirtual ? $address['id'] : 0,
//                'storeid' => !empty($carrier_list) && !$isverify && !$isvirtual ? $carrier_list[0]['id'] : 0,
                'storeid' => 0,
                'couponcount' => $couponcount,
                'coupon_goods' => $goodsdata_temp,
                'isvirtual' => $isvirtual,
                'isverify' => $isverify,
                'isonlyverifygoods' => $isonlyverifygoods,
                'isforceverifystore' => $isforceverifystore,
                'goods' => $goodsdata,
                // 'goods' => $goodsdata_temp,
                'merchs' => $merchs,
                'orderdiyformid' => $orderdiyformid,
                'has_fields' => $has_fields,
                'giftid' => $giftid,
                'mustbind' => $mustbind,
                'fromquick' => intval($quickid),
                // 'liveid' => intval($liveid),
                'liveid' => intval($card_live_id),
                'new_area' => $new_area,
                'address_street' => $address_street,
                'city_express_state' => empty($dispatch_array['city_express_state']) ? 0 : $dispatch_array['city_express_state']
            );

            $buyagain = $buyagainprice;
        } else {
            $level = m('member')->getLevel($openid);
            //套餐多商户
            $merchdata = $this->merchData();
            extract($merchdata);

            $merch_array = array();
            $merchs = array();


            $g = $_GPC['goods'];
            $g = json_decode(htmlspecialchars_decode($g, ENT_QUOTES), true);


            $package = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_package') . " WHERE uniacid = " . $uniacid . " and id = " . $packageid . " ");
            $package = set_medias($package, array('thumb'));
            if ($package['starttime'] > time()) {
                $this->message('套餐活动还未开始，请耐心等待!', '', 'error');
            }
            if ($package['endtime'] < time()) {
                $this->message('套餐活动已结束，谢谢您的关注，请浏览其他套餐或商品！', '', 'error');
            }

            $goods = array();
            $goodsprice = 0;
            $marketprice = 0;
            $allgoods = array();
            $card_id = $this->getdefaultMembercardId($g);
            foreach ($g as $key => $value) {

                $tablename = tablename('ewei_shop_goods');
                $sql = <<<EOF
            SELECT id as goodsid,type,title,weight,issendfree,isnodiscount,isfullback,ispresell,presellprice,preselltimeend,presellsendstatrttime,presellsendtime,presellsendtype,
                  thumb,marketprice,storeids,isverify,isforceverifystore,deduct,
                  manydeduct,manydeduct2,`virtual`,maxbuy,usermaxbuy,discounts,total as stock,deduct2,showlevels,
                  ednum,edmoney,edareas,edareas_code,
                  diyformtype,diyformid,diymode,dispatchtype,dispatchid,dispatchprice,cates,minbuy, 
                  isdiscount,isdiscount_time,isdiscount_time_start,isdiscount_discounts,
                  virtualsend,invoice,needfollow,followtip,followurl,merchid,checked,merchsale,
                  buyagain,buyagain_islong,buyagain_condition, buyagain_sale from {$tablename} where id = {$value['goodsid']}  and uniacid = $uniacid;
EOF;

                $goods[$key] = pdo_fetch($sql);

                if ($is_openmerch == 1) {
                    $merchid = $goods[$key]['merchid'];
                    $merch_array[$merchid]['goods'][] = $goods[$key]['id'];
                }
                $option = array();
                $packagegoods = array();
                if ($value['optionid'] > 0) {
                    $option = pdo_fetch("select title,packageprice,marketprice from " . tablename('ewei_shop_package_goods_option') . "
                            where optionid = " . $value['optionid'] . " and goodsid=" . $value['goodsid'] . " and uniacid = " . $uniacid . " and pid = " . $packageid . " ");
                    $goods[$key]['packageprice'] = $option['packageprice'];
                    $goods[$key]['marketprice'] = $option['marketprice'];
                } else {
                    $packagegoods = pdo_fetch("select title,packageprice,marketprice from " . tablename('ewei_shop_package_goods') . "
                            where goodsid=" . $value['goodsid'] . " and uniacid = " . $uniacid . " and pid = " . $packageid . " ");
                    $goods[$key]['packageprice'] = $packagegoods['packageprice'];

                }

                $goods[$key]['optiontitle'] = !empty($option['title']) ? $option['title'] : '';
                $goods[$key]['optionid'] = !empty($value['optionid']) ? $value['optionid'] : 0;;
                $goods[$key]['goodsid'] = $value['goodsid'];
                $goods[$key]['total'] = 1;
                if ($option) {
                    $goods[$key]['packageprice'] = $option['packageprice'];
                } else {
                    $goods[$key]['packageprice'] = $goods[$key]['packageprice'];
                }

                /*
                 * 多商户
                 * */
                if ($is_openmerch == 1) {
                    $merch_array[$merchid]['ggprice'] += $goods[$key]['packageprice'];
                }
                $goodsprice += $goods[$key]['packageprice'];
                $marketprice += $goods[$key]['marketprice'];
            }

            /*if ($is_openmerch == 1) {
                foreach ($merch_array as $key => $value) {
                    if ($key > 0) {
                        $merch_id = $key;
                        $merch_array[$key]['set'] = p('merch')->getSet('sale', $key);
                        $merch_array[$key]['enoughs'] = p('merch')->getEnoughs($merch_array[$key]['set']);
                    }
                }
            }*/
            //默认地址
            $address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where openid=:openid and deleted=0 and isdefault=1  and uniacid=:uniacid limit 1'
                , array(':uniacid' => $uniacid, ':openid' => $openid));

            $total = count($goods);

            /*
             * 运费计算
             * */
            if ($package['dispatchtype'] > 0) {
                $dispatch_array = m('order')->getOrderDispatchPrice($goods, $member, $address, false, $merch_array, 0);
                $dispatch_price = $dispatch_array['dispatch_price'] - $dispatch_array['seckill_dispatch_price'];
            } else {
                $dispatch_price = $package['freight'];
            }

            /*
           * 套餐产品使用会员卡的时候按照系统价格计算
       * */
            if ($packageid && $card_id) {
                //营销插件
                $sale_plugin = com('sale');
                $saleset = false;
                if ($sale_plugin && $allow_sale) {
                    $saleset = $_W['shopset']['sale'];
                    $saleset['enoughs'] = $sale_plugin->getEnoughs();
                }
                $dispatch_array = m('order')->getOrderDispatchPrice($goods, $member, $address, $saleset, $merch_array, 0);
                $dispatch_price = $dispatch_array['dispatch_price'] - $dispatch_array['seckill_dispatch_price'];
            }


            $realprice = $goodsprice + $dispatch_price;

            $packprice = $goodsprice + $dispatch_price;
            $goodsprice = 0;
            $isdiscountprice = 0;
            $discountprice = 0;
            foreach ($goods as $key => &$value) {
                //促销或会员折扣
                $prices = m('order')->getGoodsDiscountPrice($value, $level);
                $value['discountprice'] = $prices['discountprice'];
                $value['isdiscountprice'] = $prices['isdiscountprice'];
                $value['discounttype'] = $prices['discounttype'];
                $value['isdiscountunitprice'] = $prices['isdiscountunitprice'];
                $value['discountunitprice'] = $prices['discountunitprice'];

                if ($prices['discounttype'] == 1) {
                    //促销优惠
                    $isdiscountprice += $prices['isdiscountprice'];
                } else if ($prices['discounttype'] == 2) {
                    //会员优惠
                    $discountprice += $prices['discountprice'];
                }

                $goodsprice += $value['marketprice'];
            }

            unset($value);

            if ($card_id) {
                $packageid = 0;
            }

            //订单创建数据
            $createInfo = array(
                'id' => 0,
                'gdid' => intval($_GPC['gdid']),
                'fromcart' => 0,
                'packageid' => $packageid,
                'card_packageid' => $_GPC['packageid'],
                'addressid' => $address['id'],
                'storeid' => 0,
                'couponcount' => 0,
                'isvirtual' => 0,
                'isverify' => 0,
                'isonlyverifygoods' => 0,
                'goods' => $goods,
                // 'goods' => $allgoods,
                'merchs' => $merchs,
                'orderdiyformid' => 0,
                'mustbind' => 0,
                'fromquick' => intval($quickid),
                'new_area' => $new_area,
                'address_street' => $address_street
            );
        }

        $goods_list = array();
        if ($ismerch) {
            $getListUser = $merch_plugin->getListUser($goods);
            $merch_user = $getListUser['merch_user'];

            foreach ($getListUser['merch'] as $k => $v) {
                if (empty($merch_user[$k]['merchname'])) {
                    $goods_list[$k]['shopname'] = $_W['shopset']['shop']['name'];
                } else {
                    $goods_list[$k]['shopname'] = $merch_user[$k]['merchname'];
                }
                $goods_list[$k]['goods'] = $v;
            }
        } else {
            if ($merchid == 0) {
                $goods_list[0]['shopname'] = $_W['shopset']['shop']['name'];
            } else {
                $merch_data = $merch_plugin->getListUserOne($merchid);
                $goods_list[0]['shopname'] = $merch_data['merchname'];
            }
            $goods_list[0]['goods'] = $goods;
        }

        $_W['shopshare']['hideMenus'] = array('menuItem:share:qq', 'menuItem:share:QZone', 'menuItem:share:email', 'menuItem:copyUrl', 'menuItem:openWithSafari', 'menuItem:openWithQQBrowser', 'menuItem:share:timeline', 'menuItem:share:appMessage');
        if (p('exchange')) {
            $exchangecha = $goodsprice - $exchangeprice;
        }
        if ($taskgoodsprice) {
            $goodsprice = $taskgoodsprice;
        }
        $taskreward = $_SESSION['taskcut'];


        //任务中心新版下单
        if ($taskreward && p('task')) {
            if ($card_id) {
                $taskcut = 0; //有会员卡的时候任务中心优惠的价格为0
            } else {
                $taskcut = $goodsprice - $taskreward['price'];
            }

            $card_taskcut = $goodsprice - $taskreward['price'];//会员卡使用的值

        }
        if ($card_id) {
            $taskreward = null;
        }


        if (!p('membercard')) {
            $show_card = false;
        }
        if (p('membercard')) {
            // 拼装商品id
            $goodsids = '';
            $goodsids_arr = array_column($goods, 'goodsid');
            $my_card_list = p('membercard')->get_Mycard('', 0, 100, $goodsids_arr);
            if (empty($my_card_list['list'])) {
                $show_card = false;
            }
        }
        $default_cardid = 0;                //默认使用的会员卡的id

        if ($show_card && p('membercard')) {
            $goodsids = '';
            $goodsids_arr = array_column($goods, 'goodsid');
            $default_cardid = $this->getdefaultMembercardId($goodsids_arr);
        }

        $createInfo['card_id'] = $default_cardid;
        $createInfo['taskcut'] = $card_taskcut > 0 ? $card_taskcut : 0;
        $createInfo['lotterydiscountprice'] = $card_lotterydiscountprice;
        $createInfo['discountprice'] = $discountprice;
        $createInfo['isdiscountprice'] = $isdiscountprice;
        $createInfo['deductenough_money'] = '';
        $createInfo['deductenough_enough'] = '';
        $createInfo['merch_deductenough_enough'] = $merch_saleset['merch_enoughmoney'];
        $createInfo['merch_deductenough_money'] = $merch_saleset['merch_enoughdeduct'];
        if (!empty($exchangeOrder)) {
            $createInfo['dispatch_price'] = $exchangepostage;
        } elseif ($taskgoodsprice) {
            $createInfo['dispatch_price'] = $taskgoodsprice;
        } else {
            $createInfo['dispatch_price'] = $dispatch_price;
        }
        $createInfo['gift_price'] = is_array($gift_price) && empty($gift_price) != true ? min($gift_price) : 0;
        $createInfo['show_card'] = $show_card;
        $createInfo['goods_dispatch'] = $dispatch_array['goods_dispatch'];

        include $this->template();


    }

    function getcouponprice()
    {
        global $_GPC;
        $couponid = intval($_GPC['couponid']);
        $goodsarr = $_GPC['goods'];
        $goodsprice = $_GPC['goodsprice'];
        $discountprice = $_GPC['discountprice'];
        $isdiscountprice = $_GPC['isdiscountprice'];

        $contype = intval($_GPC['contype']);
        $wxid = intval($_GPC['wxid']);
        $wxcardid = $_GPC['wxcardid'];
        $wxcode = $_GPC['wxcode'];
        $real_price = $_GPC['real_price'];

        $result = $this->caculatecoupon($contype, $couponid, $wxid, $wxcardid, $wxcode, $goodsarr, $goodsprice, $discountprice, $isdiscountprice, '', '', '', $real_price);

        if (empty($result)) {
            show_json(0);
        } else {
            show_json(1, $result);
        }
    }

    function caculatecoupon($contype, $couponid, $wxid, $wxcardid, $wxcode, $goodsarr, $totalprice, $discountprice, $isdiscountprice, $isSubmit = 0, $discountprice_array = array(), $merchisdiscountprice = 0, $real_price = 0)
    {
        global $_W;

        $openid = $_W['openid'];
        $uniacid = $_W['uniacid'];

        if (empty($goodsarr)) {
            return false;
        }


        if ($contype == 0) {
            return null;
        } else if ($contype == 1) {
            //平台户优惠券
            $sql = "select id,uniacid,card_type,logo_url,title, card_id,least_cost,reduce_cost,discount,merchid,limitgoodtype,limitgoodcatetype,limitgoodcateids,limitgoodids,merchid,limitdiscounttype  from " . tablename('ewei_shop_wxcard');
            $sql .= "  where uniacid=:uniacid  and id=:id and card_id=:card_id   limit 1";
            $data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $wxid, ':card_id' => $wxcardid));

            $merchid = intval($data['merchid']);

        } else if ($contype == 2) {
            $sql = 'SELECT d.id,d.couponid,c.enough,c.backtype,c.deduct,c.discount,c.backmoney,c.backcredit,c.backredpack,c.merchid,c.limitgoodtype,c.limitgoodcatetype,c.limitgoodids,c.limitgoodcateids,c.limitdiscounttype  FROM ' . tablename('ewei_shop_coupon_data') . " d";
            $sql .= " left join " . tablename('ewei_shop_coupon') . " c on d.couponid = c.id";
            $sql .= ' where d.id=:id and d.uniacid=:uniacid and d.openid=:openid and d.used=0  limit 1';
            $data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $couponid, ':openid' => $openid));

            $merchid = intval($data['merchid']);
        }

        if (empty($data)) {
            return null;
        }

        if (is_array($goodsarr)) {

            $goods = array();

            foreach ($goodsarr as $g) {

                if (empty($g)) {
                    continue;
                }

                if ($merchid > 0 && $g['merchid'] != $merchid) {
                    continue;
                }

                $cates = explode(',', $g['cates']);
                $limitcateids = explode(',', $data['limitgoodcateids']);
                $limitgoodids = explode(',', $data['limitgoodids']);

                $pass = 0;

                if ($data['limitgoodcatetype'] == 0 && $data['limitgoodtype'] == 0) {
                    $pass = 1;
                }

                if ($data['limitgoodcatetype'] == 1) {
                    $result = array_intersect($cates, $limitcateids);
                    if (count($result) > 0) {
                        $pass = 1;
                    }
                }

                if ($data['limitgoodtype'] == 1) {
                    $isin = in_array($g['goodsid'], $limitgoodids);
                    if ($isin) {
                        $pass = 1;
                    }
                }
                if ($pass == 1) {
                    $goods[] = $g;
                }
            }

            $limitdiscounttype = intval($data['limitdiscounttype']);
            $coupongoodprice = 0;
            $gprice = 0;

            foreach ($goods as $k => $g) {

                $gprice = (float)$g['marketprice'] * (float)$g['total'];

                switch ($limitdiscounttype) {
                    case 1:
                        $coupongoodprice += $gprice - (float)$g['discountunitprice'] * (float)$g['total'];
                        $discountprice_array[$g['merchid']]['coupongoodprice'] += $gprice - (float)$g['discountunitprice'] * (float)$g['total'];

                        if ($g['discounttype'] == 1) {
                            $isdiscountprice -= (float)$g['isdiscountunitprice'] * (float)$g['total'];
                            $discountprice += (float)$g['discountunitprice'] * (float)$g['total'];

                            if ($isSubmit == 1) {
                                //计算价格
                                $totalprice = $totalprice - $g['ggprice'] + $g['price2'];
                                $discountprice_array[$g['merchid']]['ggprice'] = $discountprice_array[$g['merchid']]['ggprice'] - $g['ggprice'] + $g['price2'];
                                $goodsarr[$k]['ggprice'] = $g['price2'];
                                //重现计算多商户优惠

                                $discountprice_array[$g['merchid']]['isdiscountprice'] -= (float)$g['isdiscountunitprice'] * (float)$g['total'];
                                $discountprice_array[$g['merchid']]['discountprice'] += (float)$g['discountunitprice'] * (float)$g['total'];
                                //重现计算多商户促销优惠
                                if (!empty($data['merchsale'])) {
                                    $merchisdiscountprice -= (float)$g['isdiscountunitprice'] * (float)$g['total'];
                                    $discountprice_array[$g['merchid']]['merchisdiscountprice'] -= (float)$g['isdiscountunitprice'] * (float)$g['total'];
                                }
                            }
                        }
                        break;
                    case 2:
                        $coupongoodprice += $gprice - (float)$g['isdiscountunitprice'] * (float)$g['total'];
                        $discountprice_array[$g['merchid']]['coupongoodprice'] += $gprice - (float)$g['isdiscountunitprice'] * (float)$g['total'];
                        if ($g['discounttype'] == 2) {
                            $discountprice -= (float)$g['discountunitprice'] * (float)$g['total'];

                            if ($isSubmit == 1) {
                                //计算价格
                                $totalprice = $totalprice - $g['ggprice'] + $g['price1'];
                                $discountprice_array[$g['merchid']]['ggprice'] = $discountprice_array[$g['merchid']]['ggprice'] - $g['ggprice'] + $g['price1'];
                                $goodsarr[$k]['ggprice'] = $g['price1'];

                                $discountprice_array[$g['merchid']]['discountprice'] -= (float)$g['discountunitprice'] * (float)$g['total'];
                            }
                        }
                        break;
                    case 3:
                        $coupongoodprice += $gprice;
                        $discountprice_array[$g['merchid']]['coupongoodprice'] += $gprice;
                        if ($g['discounttype'] == 1) {
                            $isdiscountprice -= (float)$g['isdiscountunitprice'] * (float)$g['total'];

                            if ($isSubmit == 1) {
                                $totalprice = $totalprice - $g['ggprice'] + $g['price0'];
                                $discountprice_array[$g['merchid']]['ggprice'] = $discountprice_array[$g['merchid']]['ggprice'] - $g['ggprice'] + $g['price0'];
                                $goodsarr[$k]['ggprice'] = $g['price0'];

                                //重现计算多商户促销优惠
                                if (!empty($data['merchsale'])) {
                                    $merchisdiscountprice -= $g['isdiscountunitprice'] * (float)$g['total'];
                                    $discountprice_array[$g['merchid']]['merchisdiscountprice'] -= $g['isdiscountunitprice'] * (float)$g['total'];
                                }
                                $discountprice_array[$g['merchid']]['isdiscountprice'] -= $g['isdiscountunitprice'] * (float)$g['total'];
                            }
                        } else if ($g['discounttype'] == 2) {
                            $discountprice -= (float)$g['discountunitprice'] * (float)$g['total'];

                            if ($isSubmit == 1) {
                                $totalprice = $totalprice - $g['ggprice'] + $g['price0'];
                                $goodsarr[$k]['ggprice'] = $g['price0'];

                                $discountprice_array[$g['merchid']]['ggprice'] = $discountprice_array[$g['merchid']]['ggprice'] - $g['ggprice'] + $g['price0'];
                                $discountprice_array[$g['merchid']]['discountprice'] -= (float)$g['discountunitprice'] * (float)$g['total'];
                            }
                        }
                        break;
                    default:
                        if ($g['discounttype'] == 1) {
                            //促销优惠
                            $coupongoodprice += $gprice - (float)$g['isdiscountunitprice'] * (float)$g['total'];
                            $discountprice_array[$g['merchid']]['coupongoodprice'] += $gprice - (float)$g['isdiscountunitprice'] * (float)$g['total'];
                        } else if ($g['discounttype'] == 2) {
                            //会员优惠
                            $coupongoodprice += $gprice - (float)$g['discountunitprice'] * (float)$g['total'];
                            $discountprice_array[$g['merchid']]['coupongoodprice'] += $gprice - (float)$g['discountunitprice'] * (float)$g['total'];
                        } else if ($g['discounttype'] == 0) {
                            $coupongoodprice += $gprice;
                            $discountprice_array[$g['merchid']]['coupongoodprice'] += $gprice;
                        }
                        break;
                }
            }


            if ($contype == 1) {
                $deduct = ((float)$data['reduce_cost'] / 100);
                $discount = (float)(100 - intval($data['discount'])) / 10;
                if ($data['card_type'] == "CASH") {
                    $backtype = 0;
                } else if ($data['card_type'] == "DISCOUNT") {
                    $backtype = 1;
                }


            } else if ($contype == 2) {
                $deduct = ((float)$data['deduct']);
                $discount = ((float)$data['discount']);
                $backtype = ((float)$data['backtype']);
            }


            $deductprice = 0;
            $coupondeduct_text = '';

            if ($real_price && $coupongoodprice == 0) {
                $coupongoodprice = $real_price;
            }
            if ($deduct > 0 && $backtype == 0 && $coupongoodprice > 0) {
                if ($deduct > $coupongoodprice) {
                    $deduct = $coupongoodprice;
                }
                if ($deduct <= 0) {
                    $deduct = 0;
                }
                $deductprice = $deduct;
                $coupondeduct_text = '优惠券优惠';

                foreach ($discountprice_array as $key => $value) {
                    $discountprice_array[$key]['deduct'] = ((float)$value['coupongoodprice']) / (float)$coupongoodprice * $deduct;
                }
            } else if ($discount > 0 && $backtype == 1) {
                $deductprice = $coupongoodprice * (1 - $discount / 10);
                if ($deductprice > $coupongoodprice) {
                    $deductprice = $coupongoodprice;
                }
                if ($deductprice <= 0) {
                    $deductprice = 0;
                }

                foreach ($discountprice_array as $key => $value) {
                    $discountprice_array[$key]['deduct'] = ((float)$value['coupongoodprice']) * (1 - $discount / 10);
                }


                if ($merchid > 0) {

                    $coupondeduct_text = '店铺优惠券折扣(' . $discount . '折)';
                } else {

                    $coupondeduct_text = '优惠券折扣(' . $discount . '折)';
                }
            }
        }
        $totalprice -= $deductprice;


        $return_array = array();
        //根据优惠券规则计算后的促销优惠
        $return_array['isdiscountprice'] = (float)$isdiscountprice;
        //根据优惠券规则计算后的会员折扣
        $return_array['discountprice'] = (float)$discountprice;
        //优惠券折扣
        $return_array['deductprice'] = (float)$deductprice;
        //参与优惠券优惠的商品总价
        $return_array['coupongoodprice'] = (float)$coupongoodprice;
        //优惠券标题
        $return_array['coupondeduct_text'] = $coupondeduct_text;
        //根据优惠券规则计算后的商品总价
        $return_array['totalprice'] = (float)$totalprice;
        //多商户订单信息
        $return_array['discountprice_array'] = $discountprice_array;
        //多商户优惠券价格
        $return_array['merchisdiscountprice'] = $merchisdiscountprice;
        //优惠券多商户ID
        $return_array['couponmerchid'] = $merchid;
        //商品信息更新
        $return_array['$goodsarr'] = $goodsarr;


        return $return_array;
    }


    function caculate()
    {
        global $_W, $_GPC;
        $open_redis = function_exists('redis') && !is_error(redis());

        $openid = $_W['openid'];
        $uniacid = $_W['uniacid'];
        $card_id = intval($_GPC['card_id']);
        $packageid = intval($_GPC['packageid']);

        //多商户
        $merchdata = $this->merchData();
        extract($merchdata);

        $merch_array = array();

        //允许参加优惠
        $allow_sale = true;

        //需支付
        $realprice = 0;

        //是否免邮
        $nowsendfree = false;

        //是否为核销单
        $isverify = false;

        //是否为秒杀单
        $isseckill = false;

        //是否为虚拟物品(虚拟或卡密)
        $isvirtual = false;

        //任务活动优惠
        $taskdiscountprice = 0;

        //会员卡折扣优惠
        $carddiscountprice = 0;

        //游戏活动优惠
        $lotterydiscountprice = 0;

        //会员优惠
        $discountprice = 0;

        //促销优惠
        $isdiscountprice = 0;

        //积分抵扣的
        $deductprice = 0;

        //余额抵扣限额
        $deductprice2 = 0;

        //余额抵扣的钱
        $deductcredit2 = 0;

        //是否支持优惠
        $buyagain_sale = true;


        //是否为纯记次时商品订单
        $isonlyverifygoods = true;

        $buyagainprice = 0;

        $seckill_price = 0; //秒杀商品的总金额
        $seckill_payprice = 0; //秒杀的支付金额
        $seckill_dispatchprice = 0; //秒杀的运费

        // 直播价格处理 Step.1
        $liveid = intval($_GPC['liveid']);
        if (p('live') && !empty($liveid)) {
            $isliving = p('live')->isLiving($liveid);
            if (!$isliving) {
                $liveid = 0;
            }
        }

        if (!empty($packageid)) {
            //套餐详情
            $package = pdo_fetch("SELECT id,title,price,freight,cash,starttime,endtime,dispatchtype FROM " . tablename('ewei_shop_package') . "
                    WHERE uniacid = " . $uniacid . " and id = " . $packageid . " and deleted = 0 and status = 1  ORDER BY id DESC");

        }


        $dispatchid = intval($_GPC['dispatchid']);

        $totalprice = floatval($_GPC['totalprice']);

        //快递还是自提 true为自提
        $dflag = $_GPC['dflag'];

        $addressid = intval($_GPC['addressid']);
        $address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where  id=:id and openid=:openid and uniacid=:uniacid limit 1'
            , array(':uniacid' => $uniacid, ':openid' => $openid, ':id' => $addressid));

        $member = m('member')->getMember($openid, true);
        $level = m('member')->getLevel($openid);
        $weight = floatval($_GPC['weight']);
        $dispatch_price = 0;
        $deductenough_money = 0; //满额减
        $deductenough_enough = 0;

        $goodsarr = $_GPC['goods'];

        if (is_array($goodsarr)) {

            $weight = 0;

            //所有商品
            $allgoods = array();
            //$goodsarr = m('goods')->wholesaleprice($goodsarr);

            foreach ($goodsarr as &$g) {
                if (empty($g)) {
                    continue;
                }


                // 周期购商品
                if ($g['type'] == 9) {
                    show_json(0, '商品' . $g['title'] . '为周期购商品，不可在此下单!');
                }

                $goodsid = $g['goodsid'];
                $optionid = $g['optionid'];
                $goodstotal = $g['total'];


                if ($goodstotal < 1) {
                    $goodstotal = 1;
                }
                if (empty($goodsid)) {
                    $nowsendfree = true;
                }
                $sql = 'SELECT id as goodsid,title,type, weight,total,issendfree,isnodiscount, thumb,marketprice,cash,isverify,isforceverifystore,goodssn,productsn,sales,istime,'
                    . ' timestart,timeend,usermaxbuy,maxbuy,unit,buylevels,buygroups,deleted,status,deduct,ispresell,preselltimeend,manydeduct,manydeduct2,`virtual`,'
                    . ' discounts,deduct2,ednum,edmoney,edareas,edareas_code,diyformid,diyformtype,diymode,dispatchtype,dispatchid,dispatchprice,presellprice,'
                    . ' isdiscount,isdiscount_time,isdiscount_time_start,isdiscount_discounts ,virtualsend,merchid,merchsale,'
                    . ' buyagain,buyagain_islong,buyagain_condition, buyagain_sale,bargain,unite_total,islive,liveprice'
                    . ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
                $data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $goodsid));


                $data['seckillinfo'] = plugin_run('seckill::getSeckill', $goodsid, $optionid, true, $_W['openid']);
                if ($data['ispresell'] > 0 && ($data['preselltimeend'] == 0 || $data['preselltimeend'] > time())) {
                    $data['marketprice'] = $data['presellprice'];
                }


                // 直播价格处理 Step.2
                if (!empty($liveid)) {
                    $isLiveGoods = p('live')->isLiveGoods($data['goodsid'], $liveid);
                    if (!empty($isLiveGoods)) {
                        if (intval($_GPC['card_id']) > 0) {   //直播有会员卡按照会员卡的价格来
                            $live_product = pdo_fetch("SELECT *  FROM " . tablename('ewei_shop_goods') . " WHERE id = '{$data['goodsid']}'");
                            if ($live_product) {
                                $data['marketprice'] = $live_product['marketprice'];
                            }
                        } else {
                            $data['marketprice'] = price_format($isLiveGoods['liveprice']);
                        }
                    }

                }


                if (empty($data)) {
                    $nowsendfree = true;
                }
                if ($data['status'] == 2) {
                    $data['marketprice'] = 0;
                }

                //任务活动购买商品
                if ($data['seckillinfo'] && $data['seckillinfo']['status'] == 0) {
                    //秒杀不管任务
                    $data['is_task_goods'] = 0;

                } else {

                    if (p('task')) {
                        $task_id = intval($_SESSION[$goodsid . '_task_id']);
                        if (!empty($task_id)) {
                            $rewarded = pdo_fetchcolumn("SELECT `rewarded` FROM " . tablename('ewei_shop_task_extension_join') . " WHERE id = :id AND openid = :openid AND uniacid = :uniacid", array(':id' => $task_id, ':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
                            $taskGoodsInfo = unserialize($rewarded);
                            $taskGoodsInfo = $taskGoodsInfo['goods'][$goodsid];
                            if (!empty($optionid) && !empty($taskGoodsInfo['option']) && $optionid == $taskGoodsInfo['option']) {
                                $taskgoodsprice = $taskGoodsInfo['price'];
                            } elseif (empty($optionid)) {
                                $taskgoodsprice = $taskGoodsInfo['price'];
                            }

                        }
                    }


                    $rank = intval($_SESSION[$goodsid . '_rank']);
                    $log_id = intval($_SESSION[$goodsid . '_log_id']);
                    $join_id = intval($_SESSION[$goodsid . '_join_id']);

                    $task_goods_data = m('goods')->getTaskGoods($openid, $goodsid, $rank, $log_id, $join_id, $optionid);
                    if (empty($task_goods_data['is_task_goods'])) {
                        $data['is_task_goods'] = 0;
                    } else {
                        $allow_sale = false;
                        $data['is_task_goods'] = $task_goods_data['is_task_goods'];
                        $data['is_task_goods_option'] = $task_goods_data['is_task_goods_option'];
                        $data['task_goods'] = $task_goods_data['task_goods'];
                    }
                }

                $data['stock'] = $data['total'];
                $data['total'] = $goodstotal;
                if (!empty($optionid)) {
                    $option = pdo_fetch('select id,title,marketprice,presellprice,goodssn,productsn,stock,`virtual`,weight,liveprice,islive from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $uniacid, ':goodsid' => $goodsid, ':id' => $optionid));
                    if (!empty($option)) {
                        $data['optionid'] = $optionid;
                        $data['optiontitle'] = $option['title'];
                        $data['marketprice'] = (intval($data['ispresell']) > 0 && ($data['preselltimeend'] > time() || $data['preselltimeend'] == 0)) ? $option['presellprice'] : $option['marketprice'];


                        // 直播价格处理 Step.3
                        if (!empty($liveid)) {
                            $liveOption = p('live')->getLiveOptions($data['goodsid'], $liveid, array($option));
                            if ($_GPC['card_id'] > 0) {  //直播商品按照原价来使用会员卡
                                $gopdata = m('goods')->getOption($data['goodsid'], $optionid);;
                                if (empty($gopdata) != true) {
                                    $data['marketprice'] = price_format($gopdata['marketprice']);
                                }
                            } else {
                                if (!empty($liveOption) && !empty($liveOption[0])) {
                                    $data['marketprice'] = price_format($liveOption[0]['marketprice']);
                                }
                            }
                        }


                        if (empty($data['unite_total'])) {
                            $data['stock'] = $option['stock'];
                        }
                        if (!empty($option['weight'])) {
                            $data['weight'] = $option['weight'];
                        }
                    }
                }

                if ($data['type'] == 4) {
                    $data['marketprice'] = $g['wholesaleprice'];
                    $data['wholesaleprice'] = $g['wholesaleprice'];
                }


                if ($data['seckillinfo'] && $data['seckillinfo']['status'] == 0) {
                    $data['ggprice'] = $data['seckillinfo']['price'] * $g['total'];

                    $seckill_payprice += $data['ggprice'];

                    $seckill_price += $data['marketprice'] * $g['total'];

                } else {
                    //计算折扣或促销后成交价格


                    $prices = m('order')->getGoodsDiscountPrice($data, $level);
                    $data['ggprice'] = $prices['price'];
                    $data['discounttype'] = $prices['discounttype'];
                    $data['isdiscountunitprice'] = $prices['isdiscountunitprice'];
                    $data['discountunitprice'] = $prices['discountunitprice'];

                }

                if ($is_openmerch == 1) {
                    $merchid = $data['merchid'];
                    $merch_array[$merchid]['goods'][] = $data['goodsid'];
                    $merch_array[$merchid]['ggprice'] += $data['ggprice'];
                }

                if ($data['isverify'] == 2) {
                    $isverify = true;
                }

                if (!empty($data['virtual']) || $data['type'] == 2 || $data['type'] == 3 || $data['type'] == 20) {
                    $isvirtual = true;
                }

                if ($data['seckillinfo'] && $data['seckillinfo']['status'] == 0) {
                    //秒杀不管其他活动
                    $g['taskdiscountprice'] = 0;
                    $g['lotterydiscountprice'] = 0;
                    $g['discountprice'] = 0;
                    $g['isdiscountprice'] = 0;
                    $g['discounttype'] = 0;
                    $isseckill = true;
                } else {

                    $g['taskdiscountprice'] = $prices['taskdiscountprice'];
                    $g['lotterydiscountprice'] = $prices['lotterydiscountprice'];
                    $g['discountprice'] = $prices['discountprice'];
                    $g['isdiscountprice'] = $prices['isdiscountprice'];
                    $g['discounttype'] = $prices['discounttype'];

                    $taskdiscountprice += $prices['taskdiscountprice'];
                    $lotterydiscountprice += $prices['lotterydiscountprice'];

                    //重复购买的优惠价格
                    $buyagainprice += $prices['buyagainprice'];

                }


                if ($data['seckillinfo'] && $data['seckillinfo']['status'] == 0 || $_SESSION['taskcut']) {

                    //秒杀不管优惠
                } else {
                    if ($prices['discounttype'] == 1) {
                        //促销优惠
                        $isdiscountprice += $prices['isdiscountprice'];

                    } else if ($prices['discounttype'] == 2) {
                        //会员优惠
                        $discountprice += $prices['discountprice'];
                    }
                }

                //砍价活动不参与会员折扣
                if (!empty($_SESSION['bargain_id']) && p('bargain')) {
                    $discountprice = 0;
                }


                //实际支付加等于 商品价格
                $realprice += $data['ggprice'];

                $allgoods[] = $data;

                if ($data['seckillinfo'] && $data['seckillinfo']['status'] == 0) {
                    //二次
                } else {
                    if (floatval($g['buyagain']) > 0 && empty($g['buyagain_sale'])) {
                        //第一次后买东西享受优惠
                        if (m('goods')->canBuyAgain($g)) {
                            $buyagain_sale = false;
                        }
                    }
                }
//                //如果订单中有砍价产品那么就不能有满额抵扣了
                if (p('bargain')) {
                    $bargain_act_id = pdo_fetchall("SELECT *  FROM " . tablename('ewei_shop_bargain_goods') . " WHERE goods_id = '{$g['goodsid']}' and status = 0");
                    if (!empty($bargain_act_id)) {
                        $allow_sale = false;
                    }
                }

            }
            unset($g);

            if ($is_openmerch == 1) {
                //读取多商户营销设置
                foreach ($merch_array as $key => $value) {
                    if ($key > 0) {
                        $merch_array[$key]['set'] = $merch_plugin->getSet('sale', $key);
                        $merch_array[$key]['enoughs'] = $merch_plugin->getEnoughs($merch_array[$key]['set']);
                    }
                }
            }

            //营销
            $sale_plugin = com('sale');
            $saleset = false;
            if ($sale_plugin && $buyagain_sale && $allow_sale) {
                $saleset = $_W['shopset']['sale'];
                $saleset['enoughs'] = $sale_plugin->getEnoughs();
            }

            foreach ($allgoods as $g) {


                //判断是否为纯记次时商品订单
                if ($g['type'] != 5) {
                    $isonlyverifygoods = false;
                }

                if ($g['seckillinfo'] && $g['seckillinfo']['status'] == 0) {
                    //秒杀不管二次
                    $g['deduct'] = 0;
                } else {
                    if (floatval($g['buyagain']) > 0 && empty($g['buyagain_sale'])) {
                        //第一次后买东西享受优惠
                        if (m('goods')->canBuyAgain($g)) {
                            $g['deduct'] = 0;
                        }
                    }
                }
                if ($g['seckillinfo'] && $g['seckillinfo']['status'] == 0) {

                    //秒杀不管抵扣
                } else {

                    if ($open_redis) {

                        //积分抵扣 抵扣额度大于商品实际支付价格时，则抵扣额度用商品支付价格，防止抵扣其他商品
                        if ($g['deduct'] > $g['ggprice']) {
                            $g['deduct'] = $g['ggprice'];
                        }

                        //积分抵扣
                        if ($g['manydeduct']) {
                            $deductprice += $g['deduct'] * $g['total'];
                        } else {
                            $deductprice += $g['deduct'];
                        }

                        //余额抵扣限额
                        $deccredit2 = 0;        //可抵扣的余额

                        if ($g['deduct2'] == 0) {
                            //全额抵扣
                            //$deductprice2 += $g['ggprice'];
                            $deccredit2 = $g['ggprice'];
                        } else if ($g['deduct2'] > 0) {
                            $temp_ggprice = $g['ggprice'] / $g['total'];
                            if ($g['deduct2'] > $temp_ggprice) {
                                $deccredit2 = $temp_ggprice;
                            } else {
                                $deccredit2 = $g['deduct2'];
                            }
                        }
                        //todo 第一次访问
                        if ($g['manydeduct2']) {
                            $deccredit2 = $deccredit2 * $g['total'];
                        }
                        $deductprice2 += $deccredit2;

                    }
                }
            }

            if ($isverify || $isvirtual) {
                $nowsendfree = true;
            }

            if (!empty($allgoods) && !$nowsendfree && !$isonlyverifygoods) {
                //计算运费
                $dispatch_array = m('order')->getOrderDispatchPrice($allgoods, $member, $address, $saleset, $merch_array, 1);

                $dispatch_price = $dispatch_array['dispatch_price'] - $dispatch_array['seckill_dispatch_price'];
                $nodispatch_array = $dispatch_array['nodispatch_array'];
                $seckill_dispatchprice = $dispatch_array['seckill_dispatch_price'];

            }

            if (!empty($packageid)) {
                if ($package['dispatchtype'] == 1) {
                    $dispatch_array = m('order')->getOrderDispatchPrice($allgoods, $member, $address, false, $merch_array, 0);
                    $dispatch_price = $dispatch_array['dispatch_price'] - $dispatch_array['seckill_dispatch_price'];
                } else {
                    $dispatch_price = $package['freight'];
                }
            }


            $return_card_array = $this->caculatecard($card_id, $dispatch_price, $lotterydiscountprice, '', $goodsarr, $totalprice, $discountprice, $isdiscountprice, '', 1, $dispatch_array['goods_dispatch']);
            if (empty($return_card_array) != true) {
                if ($return_card_array['shipping'] == 1) {
                    $include_dispath = 0;
                    $nowsendfree = true;
                    $dispatch_price = 0;
                }
            }


            //走到这里赋值不对  在这之上$realprice  =150  180  开启不开启会员卡都相同

            //TODO
            if (empty($return_card_array) != true) {
                $deductprice2 = $return_card_array['cardgoodprice'];//最多抵扣

                //在这之上，$realprice 如果开启了会员卡  五件150  六件180
                // $realprice 如果没开启会员卡   五件190  六件 220

                $realprice = $return_card_array['cardgoodprice'];

            }

            //多商户满减
            if ($is_openmerch == 1) {
                $merch_enough = m('order')->getMerchEnough($merch_array);
                $merch_array = $merch_enough['merch_array'];
                $merch_enough_total = $merch_enough['merch_enough_total'];
                $merch_saleset = $merch_enough['merch_saleset'];

                if ($merch_enough_total > 0) {

                    //在这之上如果开启会员卡  $reaplrice = 五件115   六件130
                    //如果没开启会员卡      五件190  六件 220
                    $realprice -= $merch_enough_total;   //$merch_enough = 40
                }
            }


            if ($saleset) {
                //满额减 (减掉秒杀金额)
                foreach ($saleset['enoughs'] as $e) {
                    if ($realprice - $seckill_payprice >= floatval($e['enough']) && floatval($e['money']) > 0) {
                        $deductenough_money = floatval($e['money']);
                        $deductenough_enough = floatval($e['enough']);
                        //如果不开启会员卡，在这上面$realprice = 五件150  六件180
                        //如果开启会员卡，五件为75  六件为90
                        $realprice -= floatval($e['money']);
                        break;
                    }
                }
            }


            //使用快递

            if ($dflag != '1') {
                $include_dispath = 0;
                //余额抵扣加上运费
                if (empty($saleset['dispatchnodeduct'])) {
                    $deductprice2 += $dispatch_price;
                    if (!empty($dispatch_price)) {
                        $include_dispath = 1;
                    }
                }
            }


            $goodsdata_coupon = array();

            foreach ($allgoods as $g) {

                if ($g['seckillinfo'] && $g['seckillinfo']['status'] == 0) {
                    //秒杀不管优惠券

                } else {

                    if (floatval($g['buyagain']) > 0) {
                        //第一次后买东西享受优惠
                        if (!m('goods')->canBuyAgain($g) || !empty($g['buyagain_sale'])) {
                            $goodsdata_coupon[] = array('goodsid' => $g['goodsid'], 'total' => $g['total'], 'optionid' => $g['optionid'], 'marketprice' => $g['marketprice']
                            , 'merchid' => $g['merchid'], 'cates' => $g['cates'], 'discounttype' => $g['discounttype'], 'isdiscountprice' => $g['isdiscountprice']
                            , 'discountprice' => $g['discountprice'], 'isdiscountunitprice' => $g['isdiscountunitprice'], 'discountunitprice' => $g['discountunitprice']
                            , 'type' => $g['type'], 'wholesaleprice' => $g['wholesaleprice']);
                        }
                    } else {
                        $goodsdata_coupon[] = array('goodsid' => $g['goodsid'], 'total' => $g['total'], 'optionid' => $g['optionid'], 'marketprice' => $g['marketprice']
                        , 'merchid' => $g['merchid'], 'cates' => $g['cates'], 'discounttype' => $g['discounttype'], 'isdiscountprice' => $g['isdiscountprice']
                        , 'discountprice' => $g['discountprice'], 'isdiscountunitprice' => $g['isdiscountunitprice'], 'discountunitprice' => $g['discountunitprice']
                        , 'type' => $g['type'], 'wholesaleprice' => $g['wholesaleprice']);
                    }
                }
            }

            //是否合适的优惠券
            $couponcount = com_run('coupon::consumeCouponCount', $openid, $realprice - $seckill_payprice, $merch_array, $goodsdata_coupon);
            $couponcount += com_run('wxcard::consumeWxCardCount', $openid, $merch_array, $goodsdata_coupon);

            if (empty($goodsdata_coupon) || !$allow_sale) {
                $couponcount = 0;
            }


            //在这之上，$realprice  如果使用会员卡为五件65  六件80
            //如果不使用会员卡  五件为140  六件为 170
            //$dispatch_price = 50   $seckil_dispatchprice = 0;  开启不开启相同
            $realprice += $dispatch_price + $seckill_dispatchprice;
            //修复商城BUG直播商品还打折的BUG
            if ($liveid) {
                $realprice += $discountprice + $isdiscountprice;
                $discountprice = 0;
                $isdiscountprice = 0;

            }

            $deductcredit = 0; //抵扣需要扣除的积分
            $deductmoney = 0; //抵扣的钱


            if (!empty($saleset)) {
                //积分抵扣
                $credit = $member['credit1'];
                if ($credit > 0) {
                    $credit = floor($credit);
                }
                if (!empty($saleset['creditdeduct'])) {
                    $pcredit = intval($saleset['credit']); //积分比例
                    $pmoney = round(floatval($saleset['money']), 2); //抵扣比例


                    if ($pcredit > 0 && $pmoney > 0) {
                        if ($credit % $pcredit == 0) {
                            $deductmoney = round(intval($credit / $pcredit) * $pmoney, 2);
                        } else {
                            $deductmoney = round((intval($credit / $pcredit) + 1) * $pmoney, 2);
                        }
                    }
                    if ($deductmoney > $deductprice) {
                        $deductmoney = $deductprice;
                    }
                    if ($deductmoney > $realprice - $seckill_payprice) {  //减掉秒杀抵扣的金额
                        $deductmoney = $realprice - $seckill_payprice;
                    }
                    $deductcredit = ceil($pmoney * $pcredit == 0 ? 0 : $deductmoney / $pmoney * $pcredit);
                }

                //余额抵扣
                if (!empty($saleset['moneydeduct'])) {

                    //等于会员的余额
                    $deductcredit2 = $member['credit2'];
                    //TODO
                    //如果会员的余额 大于实际价格 减去 秒杀抵扣

                    //$realprice   如果不使用会员卡，六件是220  五件是190
                    //$realprice   如果使用会员卡，6件是130 5件是115
                    if ($deductcredit2 > $realprice - $seckill_payprice) {  //减掉秒杀抵扣的金额

                        //走了这里
                        //那么会员的余额等于真实的价格减去秒杀抵扣的金额
                        $deductcredit2 = $realprice - $seckill_payprice;
                    }
                    if ($deductcredit2 > $deductprice2) {
                        $deductcredit2 = $deductprice2;
                    }
                }


            }


        }


        if ($is_openmerch == 1) {
            $merchs = $merch_plugin->getMerchs($merch_array);
        }

        $giftid = intval($_GPC['giftid']);
        $isgift = 0;
        if (!$giftid && !$isverify && !$isseckill) {
            $goodsprice = $data['marketprice'] * $data['total'];
            $gifts = pdo_fetchall("select id,goodsid,giftgoodsid,thumb,title ,orderprice from " . tablename('ewei_shop_gift') . "
                    where uniacid = " . $uniacid . " and status = 1 and starttime <= " . time() . " and endtime >= " . time() . " and orderprice <= " . $goodsprice . " and activity = 1 ");
            foreach ($gifts as $key => $value) {
                $giftgoods = explode(",", $value['giftgoodsid']);
                foreach ($giftgoods as $k => $val) {
                    $giftgoodsdetail = pdo_fetch("select id,title,thumb,marketprice,total from " . tablename('ewei_shop_goods') . " where uniacid = " . $uniacid . " and deleted = 0 and status = 2 and id = " . $val . " ");
                    if ($giftgoodsdetail) {
                        $isgift = 1;
                    }
                }
                $gifts = array_filter($gifts);
            }
        }

        $return_array = array();
        $return_array['isgift'] = $isgift;
        $return_array['goods'] = $allgoods;
        $return_array['price'] = $dispatch_price + $seckill_dispatchprice;
        $return_array['couponcount'] = $couponcount;
        $return_array['realprice'] = $realprice;
        $return_array['deductenough_money'] = $deductenough_money;
        $return_array['deductenough_enough'] = $deductenough_enough;
        $return_array['deductcredit2'] = $deductcredit2;
        $return_array['include_dispath'] = $include_dispath;
        $return_array['deductcredit'] = $deductcredit;
        $return_array['deductmoney'] = $deductmoney;
        $return_array['taskdiscountprice'] = $taskdiscountprice;
        $return_array['lotterydiscountprice'] = $lotterydiscountprice;
        $return_array['isdiscountprice'] = $isdiscountprice;
        if ($allgoods[0]['bargain'] > 0) {
            $return_array['discountprice'] = 0;
        }
        $return_array['merch_showenough'] = $merch_saleset['merch_showenough'];
        $return_array['merch_deductenough_money'] = $merch_saleset['merch_enoughdeduct'];
        $return_array['merch_deductenough_enough'] = $merch_saleset['merch_enoughmoney'];
        $return_array['merchs'] = $merchs;
        $return_array['buyagain'] = $buyagainprice;

        $return_array['seckillprice'] = $seckill_price - $seckill_payprice;
        $return_array['city_express_state'] = empty($dispatch_array['city_express_state']) == true ? 0 : $dispatch_array['city_express_state'];
        if (!empty($nodispatch_array['isnodispatch'])) {
            $return_array['isnodispatch'] = 1;
            $return_array['nodispatch'] = $nodispatch_array['nodispatch'];
        } else {
            $return_array['isnodispatch'] = 0;
            $return_array['nodispatch'] = '';
        }
        show_json(1, $return_array);
    }

    function submit()
    {
        global $_W, $_GPC;
//        cache_write($_W['openid'] . '_bargain_id', $bargain_id);
        $bargainid = cache_load($_W['openid'] . '_bargain_id');
        $_SESSION['bargain_id'] = $bargainid;
        cache_delete($_W['openid'] . '_bargain_id');
        $openid = $_W['openid'];
        $uniacid = $_W['uniacid'];
        $real_price = $_GPC['real_price'];//优惠券使用的价格
        $cardid = intval($_GPC['card_id']);//会员卡ID

        $open_redis = function_exists('redis') && !is_error(redis());

        if ($open_redis) {
            $redis_key = "{$_W['uniacid']}_order_submit_{$openid}";
            $redis = redis();
            if (!is_error($redis)) {
                if ($redis->setnx($redis_key, time())) {
                    $redis->expireAt($redis_key, time() + 2);
                } else {
                    if ($redis->get($redis_key) + 2 < time()) {
                        $redis->del($redis_key);
                    } else {
                        show_json(0, '不要短时间重复下单!');
                    }
                }
            }
        }

        //会员
        $member = m('member')->getMember($openid);
        //是黑名单
        if ($member['isblack'] == 1) {
            show_json(0);
        }


        if (p('quick') && !empty($_GPC['fromquick'])) {
            $_GPC['fromcart'] = 0;
        }


        //  验证手机号
        if (!empty($_W['shopset']['wap']['open']) && !empty($_W['shopset']['wap']['mustbind']) && empty($member['mobileverify'])) {
            $sendtime = $_SESSION['verifycodesendtime'];
            if (empty($sendtime) || $sendtime + 60 < time()) {
                $endtime = 0;
            } else {
                $endtime = 60 - (time() - $sendtime);
            }
            show_json(3, array(
                'endtime' => $endtime,
                'imgcode' => $_W['shopset']['wap']['smsimgcode']
            ));
        }

        /*        // 验证是否必须绑定手机
                if (!empty($_W['shopset']['wap']['mustbind']) && empty($member['mobileverify'])) {
                    show_json(0, array('message' => "请先绑定手机", 'url' => mobileUrl('member/bind', null, true)));
                }*/

        //允许参加优惠
        $allow_sale = true;

        //是否为套餐订单
        $packageid = intval($_GPC['packageid']);
        $package = array();         //套餐详情
        $packgoods = array();       //套餐商品详情

        $packageprice = 0;
        if (!empty($packageid)) {
            //套餐详情
            $package = pdo_fetch("SELECT id,title,price,freight,cash,starttime,endtime,dispatchtype FROM " . tablename('ewei_shop_package') . "
                    WHERE uniacid = " . $uniacid . " and id = " . $packageid . " and deleted = 0 and status = 1  ORDER BY id DESC");
            if (empty($package)) {
                show_json(0, '未找到套餐！');
            }
            if ($package['starttime'] > time()) {
                show_json(0, '套餐活动未开始，请耐心等待！');
            }
            if ($package['endtime'] < time()) {
                show_json(0, '套餐活动已结束，谢谢您的关注，请您浏览其他套餐或商品！');
            }

            //套餐商品
            $packgoods = pdo_fetchall("SELECT id,title,thumb,packageprice,`option`,goodsid FROM " . tablename('ewei_shop_package_goods') . "
                    WHERE uniacid = " . $uniacid . " and pid = " . $packageid . "  ORDER BY id DESC");

            if (empty($packgoods)) {
                show_json(0, '未找到套餐商品！');
            }
        }

        if ($cardid > 0) {
            $packageid = 0;
        }


        $data = $this->diyformData($member);
        extract($data);

        //多商户
        $merchdata = $this->merchData();
        extract($merchdata);

        $merch_array = array();

        $ismerch = 0;
        $discountprice_array = array();

        //会员等级
        $level = m('member')->getLevel($openid);

        $dispatchid = intval($_GPC['dispatchid']);

        //配送方式
        $dispatchtype = intval($_GPC['dispatchtype']);


        $carrierid = intval($_GPC['carrierid']);

        $goods = $_GPC['goods'];

        $goods[0]['bargain_id'] = $_SESSION['bargain_id'];//砍价订单的价格传递
//        $_SESSION['bargain_id'] = null;
//        dump();
        if (!empty($goods[0]['bargain_id'])) {
            $allow_sale = false;
        }

        //判断是否有自定义表单

        $sql = 'SELECT diyformtype,diyfields,bargain,diyformid FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
        $diyinfo = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $goods[0]['goodsid']));

        if (empty($goods) || !is_array($goods)) {
            show_json(0, '未找到任何商品');
        }


        // 直播价格处理 Step.1
        $liveid = intval($_GPC['liveid']);
        if (p('live') && !empty($liveid)) {
            $isliving = p('live')->isLiving($liveid);
            if (!$isliving) {
                $liveid = 0;
            }
        }


        //所有商品
        $allgoods = array();
        $task_reward_goods = array();//任务中心的商品
        $tgoods = array();

        $totalprice = 0; //总价
        $goodsprice = 0; //商品总价
        $grprice = 0; //商品实际总价
        $weight = 0; //总重量
        $taskdiscountprice = 0; //任务活动优惠
        $lotterydiscountprice = 0; //游戏活动优惠
        $discountprice = 0; //折扣的钱
        $isdiscountprice = 0; //促销优惠的钱
        $merchisdiscountprice = 0; //多商户促销优惠的钱
        $cash = 1; //是否支持货到付款


        $deductprice = 0; //抵扣的钱

        $deductprice2 = 0; // 余额最多可抵扣
        $virtualsales = 0; //虚拟卡密的虚拟销量

        $dispatch_price = 0;

        $seckill_price = 0;//秒杀的商品价格
        $seckill_payprice = 0; //秒杀商品的总价格，此部分钱不参加活动
        $seckill_dispatchprice = 0; //秒杀商品的运费，不参与余额抵扣抵扣

        //是否支持重购优惠
        $buyagain_sale = true;

        $buyagainprice = 0;

        $sale_plugin = com('sale'); //营销插件
        $saleset = false;
        if ($sale_plugin && $allow_sale) {
            $saleset = $_W['shopset']['sale'];
            if ($packageid) {
                $saleset['enoughs'] = "";
            } else {
                $saleset['enoughs'] = $sale_plugin->getEnoughs();
            }
        }
        $isvirtual = false;
        $isverify = false;

        //是否为纯记次时商品订单
        $isonlyverifygoods = true;

        $isendtime = 0;
        $endtime = 0;
        $verifytype = 0; //核销类型
        $isvirtualsend = false;

        $couponmerchid = 0; //使用的优惠券merchid

        $total_array = array();

        //赠品
        $giftid = intval($_GPC['giftid']);

        if ($giftid) {
            $gift = array();
            $giftdata = pdo_fetch("select giftgoodsid from " . tablename('ewei_shop_gift') . " where uniacid = " . $uniacid . " and id = " . $giftid . " and status = 1 and starttime <= " . time() . " and endtime >= " . time() . " ");
            if ($giftdata['giftgoodsid']) {
                $giftgoodsid = explode(',', $giftdata['giftgoodsid']);
                foreach ($giftgoodsid as $key => $value) {
                    $giftinfo = pdo_fetch("select id as goodsid,title,thumb from " . tablename('ewei_shop_goods') . " where uniacid = " . $uniacid . " and status = 2 and total>0 and id = " . $value . " and deleted = 0 ");

                    if (empty($giftinfo)) continue;
                    $gift[$key] = $giftinfo;
                    $gift[$key]['giftStatus'] = 1;
                    //$gift[$key]['marketprice'] = 0;
                }
                $gift = array_filter($gift);
                if (!empty($gift)) {
                    $goods = array_merge($goods, $gift);
                }

            }
        }


        // 周期购商品
        if ($g['type'] == 9) {
            show_json(0, '商品' . $g['title'] . '为周期购商品，不可在此下单!');
        }

        foreach ($goods as $g) {
            if (empty($g)) {
                continue;
            }
            $goodsid = intval($g['goodsid']);
            $goodstotal = intval($g['total']);
            $total_array[$goodsid]['total'] += $goodstotal;
        }
        if (p('threen')) {
            $threenvip = p('threen')->getMember($_W['openid']);
            if (!empty($threenvip)) {
                $threenprice = true;
            }
        }
        //从新计算批发商品数量
        $goods = m('goods')->wholesaleprice($goods);

        $need_deduct_num = 0;//需要积分抵扣的订单商品的数量,为了取出最后一个抵扣订单商品
        $need_deduct2_num = 0;//需要余额抵扣的订单商品的数量,为了取出最后一个抵扣订单商品

        foreach ($goods as $g) {
            if (empty($g)) {
                continue;
            }

            $goodsid = intval($g['goodsid']);
            array_push($task_reward_goods, $goodsid);//任务中心作用的
            $optionid = intval($g['optionid']);
            $goodstotal = intval($g['total']);
            if ($goodstotal < 1) {
                $goodstotal = 1;
            }

            if (empty($goodsid)) {
                show_json(0, '参数错误');
            }
            if (p('exchange')) {
                $sql_condition = 'exchange_stock,';
            } else {
                $sql_condition = '';
            }
            $threensql = "";
            if (p('threen') && !empty($threenprice)) {
                $threensql .= ",threen";
            }
            $sql = 'SELECT id as goodsid,' . $sql_condition . 'title,type,intervalfloor,intervalprice, weight,total,issendfree,isnodiscount, thumb,marketprice,liveprice,cash,isverify,isforceverifystore,verifytype,'
                . ' goodssn,productsn,sales,istime,timestart,timeend,hasoption,isendtime,usetime,endtime,ispresell,presellprice,preselltimeend,'
                . ' usermaxbuy,minbuy,maxbuy,unit,buylevels,buygroups,deleted,unite_total,'
                . ' status,deduct,manydeduct,manydeduct2,`virtual`,discounts,deduct2,ednum,edmoney,edareas,edareas_code,diyformtype,diyformid,diymode,'
                . ' dispatchtype,dispatchid,dispatchprice,merchid,merchsale,cates,'
                . ' isdiscount,isdiscount_time,isdiscount_time_start,isdiscount_discounts, virtualsend,'
                . ' buyagain,buyagain_islong,buyagain_condition, buyagain_sale ,verifygoodslimittype,verifygoodslimitdate  ' . $threensql
                . ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
            $data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $goodsid));
            $data['seckillinfo'] = plugin_run('seckill::getSeckill', $goodsid, $optionid, true, $_W['openid']);
            if ($data['ispresell'] > 0 && ($data['preselltimeend'] == 0 || $data['preselltimeend'] > time())) {
                $data['marketprice'] = $data['presellprice'];
            }


            //判断是否为纯记次时商品订单
            if ($data['type'] != 5) {
                $isonlyverifygoods = false;
            } else {
                if (!empty($data['verifygoodslimittype'])) {
                    $verifygoodslimitdate = intval($data['verifygoodslimitdate']);
                    if ($verifygoodslimitdate < time()) {
                        show_json(0, '商品:"' . $data['title'] . '"的使用时间已失效,无法购买!');

                    }
                    if (($verifygoodslimitdate - 1800) < time()) {
                        show_json(0, '商品:"' . $data['title'] . '"的使用时间即将失效,无法购买!');
                    }
                }
            }


            // 直播价格处理 Step.2
            if (!empty($liveid)) {
                $isLiveGoods = p('live')->isLiveGoods($data['goodsid'], $liveid);
                if (!empty($isLiveGoods)) {
                    if (intval($_GPC['card_id'])) {   //直播有会员卡按照会员卡的价格来
                        $live_product = pdo_fetch("SELECT *  FROM " . tablename('ewei_shop_goods') . " WHERE id = '{$data['goodsid']}'");
                        if ($live_product) {
                            $data['marketprice'] = $live_product['marketprice'];
                        }
                    } else {
                        $data['marketprice'] = price_format($isLiveGoods['liveprice']);
                    }

                }
            }


            if ($data['status'] == 2) {
                $data['marketprice'] = 0;
            }


            if (!empty($_SESSION['exchange']) && p('exchange')) {//如果是兑换中心订单,不判断是否下架
                if (empty($data['status']) || !empty($data['deleted'])) {
                    show_json(0, $data['title'] . '<br/> 已下架!');
                }
            }


            if (!empty($data['hasoption'])) {
                $opdata = m('goods')->getOption($data['goodsid'], $optionid);
                if (empty($opdata) || empty($optionid)) {
                    show_json(0, '商品' . $data['title'] . '的规格不存在,请到购物车删除该商品重新选择规格!');
                }
            }

            //任务活动购买商品
            $rank = intval($_SESSION[$goodsid . '_rank']);
            $log_id = intval($_SESSION[$goodsid . '_log_id']);
            $join_id = intval($_SESSION[$goodsid . '_join_id']);
            if (p('task')) {
                $task_id = intval($_SESSION[$goodsid . '_task_id']);
                if (!empty($task_id)) {
                    $rewarded = pdo_fetchcolumn("SELECT `rewarded` FROM " . tablename('ewei_shop_task_extension_join') . " WHERE id = :id AND openid = :openid AND uniacid = :uniacid", array(':id' => $task_id, ':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
                    $taskGoodsInfo0 = unserialize($rewarded);
                    $taskGoodsInfo = $taskGoodsInfo0['goods'][$goodsid];
                    if (!empty($optionid) && !empty($taskGoodsInfo['option']) && $optionid == $taskGoodsInfo['option']) {
                        $taskgoodsprice = $taskGoodsInfo['price'];
                    } elseif (empty($optionid)) {
                        $taskgoodsprice = $taskGoodsInfo['price'];
                    }
                }
            }
            if (($data['seckillinfo'] && $data['seckillinfo']['status'] == 0) || ($_GPC['card_id'] > 0)) {
                //秒杀不管任务
                $data['is_task_goods'] = 0;
                $tgoods = false;
            } else {
                $task_goods_data = m('goods')->getTaskGoods($openid, $goodsid, $rank, $log_id, $join_id, $optionid);

                if (p('lottery')) {
                    $lottery_id = pdo_get('ewei_shop_lottery_log', array('log_id' => $log_id), array('lottery_id'));
                    if ($lottery_id['lottery_id']) {
                        $is_goods = pdo_get('ewei_shop_lottery', array('lottery_id' => $lottery_id['lottery_id']), array('is_goods'));
                        $is_goods = $is_goods['is_goods'];
                    }
                }

                if (empty($task_goods_data['is_task_goods'])) {
                    $data['is_task_goods'] = 0;
                } else {
                    $allow_sale = false;
                    $tgoods['title'] = $data['title'];
                    $tgoods['openid'] = $openid;
                    $tgoods['goodsid'] = $goodsid;
                    $tgoods['optionid'] = $optionid;
                    $tgoods['total'] = $goodstotal;

                    $data['is_task_goods'] = $task_goods_data['is_task_goods'];
                    $data['is_task_goods_option'] = $task_goods_data['is_task_goods_option'];
                    $data['task_goods'] = $task_goods_data['task_goods'];
                }

            }

            $merchid = $data['merchid'];
            $merch_array[$merchid]['goods'][] = $data['goodsid'];

            if ($merchid > 0) {
                $ismerch = 1;
            }

            $virtualid = $data['virtual'];
            $data['stock'] = $data['total'];
            $data['total'] = $goodstotal;
            if ($data['cash'] != 2) {
                $cash = 0;
            }
            //套餐配送方式
            if (!empty($packageid)) {
                $cash = $package['cash'];
            }

            $unit = empty($data['unit']) ? '件' : $data['unit'];

            //一次购买量，总购买量，限时购，会员级别，会员组判断
            //最低购买
            if ($data['seckillinfo'] && $data['seckillinfo']['status'] == 0) {

                $check_buy = plugin_run('seckill::checkBuy', $data['seckillinfo'], $data['title'], $data['unit']);
                if (is_error($check_buy)) {
                    show_json(-1, $check_buy['message']);
                }

            } else {

                if ($data['type'] != 4) {
                    if ($data['minbuy'] > 0) {
                        if ($goodstotal < $data['minbuy']) {
                            show_json(0, $data['title'] . '<br/> ' . $data['minbuy'] . $unit . "起售!");
                        }
                    }
                    if ($data['maxbuy'] > 0) {
                        if ($goodstotal > $data['maxbuy']) {
                            show_json(0, $data['title'] . '<br/> 一次限购 ' . $data['maxbuy'] . $unit . "!");
                        }
                    }
                }
                if ($data['usermaxbuy'] > 0) {
                    $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('ewei_shop_order_goods') . ' og '
                        . ' left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id '
                        . ' where og.goodsid=:goodsid and  o.status>=0 and o.openid=:openid  and og.uniacid=:uniacid ', array(':goodsid' => $data['goodsid'], ':uniacid' => $uniacid, ':openid' => $openid));
                    if ($order_goodscount >= $data['usermaxbuy']) {
                        show_json(0, $data['title'] . '<br/> 最多限购 ' . $data['usermaxbuy'] . $unit . "!");
                    }
                }
                if (!empty($data['is_task_goods'])) {
                    if ($goodstotal > $data['task_goods']['total']) {
                        show_json(0, $data['title'] . '<br/> 任务活动优惠限购 ' . $data['task_goods']['total'] . $unit . "!");
                    }
                }
                //判断限时购
                if ($data['istime'] == 1) {
                    if (time() < $data['timestart']) {
                        show_json(0, $data['title'] . '<br/> 限购时间未到!');
                    }
                    if (time() > $data['timeend']) {
                        show_json(0, $data['title'] . '<br/> 限购时间已过!');
                    }
                }

                $levelid = intval($member['level']);
                if (empty($member['groupid'])) {
                    $groupid = array();
                } else {
                    $groupid = explode(',', $member['groupid']);
                }
                //判断会员权限
                if ($data['buylevels'] != '') {
                    $buylevels = explode(',', $data['buylevels']);
                    if (!in_array($levelid, $buylevels)) {
                        show_json(0, '您的会员等级无法购买<br/>' . $data['title'] . '!');
                    }
                }
                //会员组权限
                if ($data['buygroups'] != '') {
                    if (empty($groupid)) {
                        $groupid[] = 0;
                    }
                    $buygroups = explode(',', $data['buygroups']);
                    $intersect = array_intersect($groupid, $buygroups);
                    if (empty($intersect)) {
                        show_json(0, '您所在会员组无法购买<br/>' . $data['title'] . '!');
                    }
                }
            }

            if (p('exchange')) {
                $sql_condition = ',exchange_stock';
            } else {
                $sql_condition = '';
            }

            //如果是批发商品,则设置价格为批发价
            if ($data['type'] == 4) {
                if (!empty($g['wholesaleprice'])) {
                    $data['wholesaleprice'] = intval($g['wholesaleprice']);
                }

                if (!empty($g['goodsalltotal'])) {
                    $data['goodsalltotal'] = intval($g['goodsalltotal']);
                }

                $data['marketprice'] == 0;
                $intervalprice = iunserializer($data['intervalprice']);

                foreach ($intervalprice as $intervalprice) {
                    if ($intervalprice['intervalnum'] <= $data['goodsalltotal']) {
                        $data['marketprice'] = $intervalprice['intervalprice'];
                    }
                }

                if ($data['marketprice'] == 0) {
                    show_json(0, $data['title'] . '<br/> ' . $data['minbuy'] . $unit . "起批!");
                }
            }

            if (!empty($optionid)) {

                $option = pdo_fetch('select id,title,marketprice,liveprice,presellprice,goodssn,productsn,stock,`virtual`,weight' . $sql_condition . ' from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $uniacid, ':goodsid' => $goodsid, ':id' => $optionid));

                if (!empty($option)) {
                    if (!empty($_SESSION['exchange']) && p('exchange')) {//如果是兑换中心订单
                        if ($option['exchange_stock'] <= 0) {//有兑换库存
                            show_json(-1, $data['title'] . "<br/>" . $option['title'] . " 库存不足!");
                        } else {
                            pdo_query("UPDATE " . tablename('ewei_shop_goods_option') . " SET exchange_stock = exchange_stock - 1 WHERE id = :id AND uniacid = :uniacid", array(':id' => $optionid, ':uniacid' => $_W['uniacid']));//减库存
                        }
                    } else {//如果不是兑换中心订单

                        if ($data['seckillinfo'] && $data['seckillinfo']['status'] == 0) {
                        } else {
                            if (empty($data['unite_total'])) {
                                $stock_num = $option['stock'];
                            } else {
                                $stock_num = $data['stock'];
                            }

                            if ($stock_num != -1) {
                                if (empty($stock_num)) {
                                    show_json(-1, $data['title'] . "<br/>" . $option['title'] . " 库存不足!stock=" . $stock_num);
                                }

                                if (!empty($data['unite_total'])) {
                                    if ($stock_num - intval($total_array[$goodsid]['total']) < 0) {
                                        show_json(-1, $data['title'] . "<br/>总库存不足!当前总库存为" . $stock_num);
                                    }
                                }
                            }
                        }
                    }


                    $data['optionid'] = $optionid;
                    $data['optiontitle'] = $option['title'];

                    //非批发商品
                    if ($data['type'] != 4) {
                        $data['marketprice'] = (intval($data['ispresell']) > 0 && ($data['preselltimeend'] > time() || $data['preselltimeend'] == 0)) ? $option['presellprice'] : $option['marketprice'];


                        // 直播价格处理 Step.3
                        if (!empty($liveid)) {
                            $liveOption = p('live')->getLiveOptions($data['goodsid'], $liveid, array($option));
                            if ($_GPC['card_id']) {  //直播商品按照原价来使用会员卡
                                $gopdata = m('goods')->getOption($data['goodsid'], $optionid);;
                                if (empty($gopdata) != true) {
                                    $data['marketprice'] = price_format($gopdata['marketprice']);
                                }
                            } else {
                                if (!empty($liveOption) && !empty($liveOption[0])) {
                                    $data['marketprice'] = price_format($liveOption[0]['marketprice']);
                                }
                            }


                        }


                        //套餐规格
                        $packageoption = array();
                        if ($packageid) {
                            $packageoption = pdo_fetch("select packageprice ,marketprice from " . tablename('ewei_shop_package_goods_option') . "
                                where uniacid = " . $uniacid . " and goodsid = " . $goodsid . " and optionid = " . $optionid . " and pid = " . $packageid . " ");
                            $data['marketprice'] = $packageoption['packageprice'];
                            if ($cardid > 0) {
                                $packageprice += $packageoption['marketprice'];//参套产品会员卡按照售价来
                            } else {
                                $packageprice += $packageoption['packageprice'];
                            }

                        }
                    }


                    $virtualid = $option['virtual'];

                    if (!empty($option['goodssn'])) {
                        $data['goodssn'] = $option['goodssn'];
                    }
                    if (!empty($option['productsn'])) {
                        $data['productsn'] = $option['productsn'];
                    }
                    if (!empty($option['weight'])) {
                        $data['weight'] = $option['weight'];
                    }
                }
            } else {
                //套餐无规格
                if ($packageid) {
                    if (empty($g['giftStatus'])) {
                        $pg = pdo_fetch("select packageprice,marketprice from " . tablename('ewei_shop_package_goods') . "
                                    where uniacid = " . $uniacid . " and goodsid = " . $goodsid . " and pid = " . $packageid . " ");
                        $data['marketprice'] = $pg['packageprice'];

                        if ($cardid > 0) {
                            $packageprice += $pg['marketprice'];//参套产品会员卡按照售价来
                        } else {
                            $packageprice += $pg['packageprice'];
                        }

                    } else {
                        if ($data['stock'] != -1) {
                            if (empty($data['stock'])) {
                                show_json(0, $data['title'] . "<br/>库存不足!");
                            }
                        }
                    }
                }


                if (!empty($_SESSION['exchange']) && p('exchange')) {//如果是兑换中心订单
                    if ($data['exchange_stock'] > 0) {//有兑换库存
                        pdo_query("UPDATE " . tablename('ewei_shop_goods') . " SET exchange_stock = exchange_stock - 1 WHERE id = :id AND uniacid = :uniacid", array(':id' => $data['goodsid'], ':uniacid' => $_W['uniacid']));//减库存
                    } else if ($data['status'] != 2) {//如果库存不足
                        show_json(0, $data['title'] . " 库存不足!");
                    }
                } else {//如果不是兑换中心订单
                    if ($data['stock'] != -1) {
                        if (empty($data['stock'])) {
                            show_json(0, $data['title'] . "<br/>库存不足!");
                        }
                    }
                }

            }

            $data['diyformdataid'] = 0;
            $data['diyformdata'] = iserializer(array());
            $data['diyformfields'] = iserializer(array());
            if (intval($_GPC['fromcart']) == 1) {

                if ($diyform_plugin) {
                    $cartdata = pdo_fetch('select id,diyformdataid,diyformfields,diyformdata from ' . tablename('ewei_shop_member_cart') . " "
                        . " where goodsid=:goodsid and optionid=:optionid and openid=:openid and deleted=0 order by id desc limit 1"
                        , array(':goodsid' => $data['goodsid'], ':optionid' => intval($data['optionid']), ':openid' => $openid));
                    if (!empty($cartdata)) {
                        $data['diyformdataid'] = $cartdata['diyformdataid'];
                        $data['diyformdata'] = $cartdata['diyformdata'];
                        $data['diyformfields'] = $cartdata['diyformfields'];
                    }
                }
            } else {
                if (!empty($data['diyformtype']) && $diyform_plugin) {

                    $temp_data = $diyform_plugin->getOneDiyformTemp($_GPC['gdid'], 0);

                    $data['diyformfields'] = $temp_data['diyformfields'];
                    $data['diyformdata'] = $temp_data['diyformdata'];

                    if ($data['diyformtype'] == 2) {
                        $data['diyformid'] = 0;
                    } else {
                        $data['diyformid'] = $data['diyformid'];

                    }
                }
            }

            if ($data['seckillinfo'] && $data['seckillinfo']['status'] == 0) {


                //秒杀价格
                $data['ggprice'] = $gprice = $data['seckillinfo']['price'] * $goodstotal;
                $seckill_payprice += $gprice;
                $seckill_price += $data['marketprice'] * $goodstotal - $gprice;
                $goodsprice += $data['marketprice'] * $goodstotal;

                $data['taskdiscountprice'] = 0;
                $data['lotterydiscountprice'] = 0;
                $data['discountprice'] = 0;
                $data['discountprice'] = 0;
                $data['discounttype'] = 0;
                $data['isdiscountunitprice'] = 0;
                $data['discountunitprice'] = 0;
                $data['price0'] = 0;
                $data['price1'] = 0;
                $data['price2'] = 0;
                $data['buyagainprice'] = 0;

                //秒杀不管折扣

            } else {
                //游戏活动使用会员卡优惠为0
                if (intval($_GPC['card_id']) > 0) {
                    $data['lotterydiscountprice'] = 0;
                    $data['isdiscountunitprice'] = 0;
                    $data['discountunitprice'] = 0;
                    $data['taskdiscountprice'] = 0;
                }
                $gprice = $data['marketprice'] * $goodstotal;
                $goodsprice += $gprice;


                //成交价格
                $prices = m('order')->getGoodsDiscountPrice($data, $level, 0, $cardid);

                if (empty($packageid)) {
                    $data['ggprice'] = $prices['price'];
                } else {
                    $data['ggprice'] = $data['marketprice'];
                }
                //游戏活动使用会员卡价格是原价
//                if (intval($_GPC['card_id'])>0){
//                    $data['ggprice'] = $data['marketprice'];
//                }


                $data['taskdiscountprice'] = $prices['taskdiscountprice'];
                $data['lotterydiscountprice'] = $prices['lotterydiscountprice'];
                $data['discountprice'] = $prices['discountprice'];
                $data['discounttype'] = $prices['discounttype'];;
                $data['isdiscountunitprice'] = $prices['isdiscountunitprice'];
                $data['discountunitprice'] = $prices['discountunitprice'];
                $data['price0'] = $prices['price0'];
                $data['price1'] = $prices['price1'];
                $data['price2'] = $prices['price2'];
                $data['buyagainprice'] = $prices['buyagainprice'];

                $buyagainprice += $prices['buyagainprice'];
                $taskdiscountprice += $prices['taskdiscountprice'];
                $lotterydiscountprice += $prices['lotterydiscountprice'];


                if ($prices['discounttype'] == 1) {
                    $isdiscountprice += $prices['isdiscountprice'];
                    // $discountprice += $prices['discountprice']; 促销的时候会员价格应该为0

                    if (!empty($data['merchsale'])) {
                        $merchisdiscountprice += $prices['isdiscountprice'];
                        $discountprice_array[$merchid]['merchisdiscountprice'] += $prices['isdiscountprice'];
                    }

                    $discountprice_array[$merchid]['isdiscountprice'] += $prices['isdiscountprice'];
                } else if ($prices['discounttype'] == 2) {
                    $discountprice += $prices['discountprice'];
                    $discountprice_array[$merchid]['discountprice'] += $prices['discountprice'];
                }

                $discountprice_array[$merchid]['ggprice'] += $prices['ggprice'];

            }
            $threenprice = json_decode($data['threen'], 1);
            if ($threenprice && !empty($threenprice['price'])) {
                $data['ggprice'] -= $data['price0'] - $threenprice['price'];
            } elseif ($threenprice && !empty($threenprice['discount'])) {
                $data['ggprice'] -= (10 - $threenprice['discount']) / 10 * $data['price0'];
            }
            $merch_array[$merchid]['ggprice'] += $data['ggprice'];
            $totalprice += $data['ggprice'];


            if ($data['isverify'] == 2 && $data['type'] != 3) {
                $isverify = true;
                $verifytype = $data['verifytype'];
                $isendtime = $data['isendtime'];
                if ($isendtime == 0) {
                    if ($data['usetime'] > 0) {
                        $endtime = time() + 3600 * 24 * intval($data['usetime']);
                    } else {
                        $endtime = 0;
                    }
                } else {
                    $endtime = $data['endtime'];
                }
            }
            if (!empty($data['virtual']) || $data['type'] == 2 || $data['type'] == 3 || $data['type'] == 20) {
                $isvirtual = true;

                if ($data['type'] == 20 && p('ccard')) {
                    $ccard = 1;
                }

                if ($data['virtualsend']) {
                    $isvirtualsend = true;
                }
            }

            if ($data['seckillinfo'] && $data['seckillinfo']['status'] == 0) {

                //秒杀不管二次，抵扣
            } else {


                if (floatval($data['buyagain']) > 0 && empty($data['buyagain_sale'])) {
                    //第一次后买东西享受优惠
                    if (m('goods')->canBuyAgain($data)) {
                        $data['deduct'] = 0;
                        $saleset = false;
                    }
                }


                if ($open_redis) {

                    //积分抵扣 抵扣额度大于商品实际支付价格时，则抵扣额度用商品支付价格，防止抵扣其他商品
                    if ($data['deduct'] > $data['ggprice']) {
                        $data['deduct'] = $data['ggprice'];
                    }

                    $deduct_price = 0;
                    if ($data['manydeduct']) {
                        $deduct_price = $data['deduct'] * $data['total'];
                    } else {
                        $deduct_price = $data['deduct'];
                    }
                    $deductprice += $deduct_price;

                    //余额抵扣限额
                    $deduct_price2 = 0;
                    if ($data['deduct2'] == 0) {
                        //全额抵扣
                        $deduct_price2 = $data['ggprice'];
                    } else if ($data['deduct2'] > 0) {
                        //最多抵扣
                        $temp_ggprice = $data['ggprice'] / $data['total'];
                        if ($data['deduct2'] > $temp_ggprice) {
                            $deduct_price2 = $temp_ggprice;
                        } else {
                            $deduct_price2 = $data['deduct2'];
                        }
                    }
                    if ($data['manydeduct2']) {
                        $deduct_price2 = $deduct_price2 * $data['total'];
                    }
                    $deductprice2 += $deduct_price2;
                }

                //如果积分抵扣金额不为空则数量加1
                if (!empty($deduct_price)) {
                    $need_deduct_num++;
                }

                //如果余额抵扣金额不为空则数量加1
                if (!empty($deduct_price2)) {
                    $need_deduct2_num++;
                }

                $data['need_deduct'] = $deduct_price;//商品的积分抵扣限额
                $data['need_deduct2'] = $deduct_price2;//商品的余额抵扣限额
            }

            $virtualsales += $data['sales'];

            $allgoods[] = $data;
        }
        $grprice = $totalprice;


        isset($_SESSION['exchangeprice']) && $grprice = $_SESSION['exchangeprice'];


        if (count($goods) > 1 && !empty($tgoods)) {
            show_json(0, '任务活动优惠商品' . $tgoods['title'] . '不能放入购物车下单,请单独购买');
        }

        if (empty($allgoods)) {
            show_json(0, '未找到任何商品');
        }
        $couponid = intval($_GPC['couponid']);
        $contype = intval($_GPC['contype']);
        $wxid = intval($_GPC['wxid']);
        $wxcardid = $_GPC['wxcardid'];
        $wxcode = $_GPC['wxcode'];

        if ($contype == 1) {
            //$ref = com('wxcard')->wxCardGetCodeInfo($wxcode,$wxcardid);
            $ref = com_run('wxcard::wxCardGetCodeInfo', $wxcode, $wxcardid);
            if (empty($ref)) {
                show_json(0, '卡券分权出错!');
            }
            if (!is_wxerror($ref)) {
                //$ref = com('wxcard')->wxCardConsume($wxcode,$wxcardid);
                $ref = com_run('wxcard::wxCardConsume', $wxcode, $wxcardid);

                if (empty($ref) || is_wxerror($ref)) {
                    show_json(0, '您的卡券未到使用日期或已经超出使用次数限制!');
                }
            } else {
                show_json(0, '您的卡券未到使用日期或已经超出使用次数限制!');
            }
        }


        if ($cardid > 0) {
            $lotterydiscountprice = 0;
        }

        if (p('membercard')) {
            $card_data = p('membercard')->getMemberCard($cardid);

        }

        if (!empty($card_data)) {
            if ($card_data['discount'] == 0 || $card_data['discount_rate'] <= 0) //禁用或者折扣<0按照原价来
            {
                $totalprice = $totalprice + $isdiscountprice;//禁用折上折按照原价来
//                $discountprice=0;
                $isdiscountprice = 0;
            }
        }

        if (empty($package)) {
            if ($is_openmerch == 1) {
                //读取多商户营销设置
                foreach ($merch_array as $key => $value) {
                    if ($key > 0) {
                        $merch_array[$key]['set'] = $merch_plugin->getSet('sale', $key);
                        if (!$packageid) {
                            $merch_array[$key]['set'] = $merch_plugin->getSet('sale', $key);
                            $merch_array[$key]['enoughs'] = $merch_plugin->getEnoughs($merch_array[$key]['set']);
                        }

                    }
                }
                if ($allow_sale && empty($_SESSION['taskcut'])) {
                    //多商户满额减
                    $merch_enough = m('order')->getMerchEnough($merch_array);
                    $merch_array = $merch_enough['merch_array'];
                    $merch_enough_total = $merch_enough['merch_enough_total'];
                    $merch_saleset = $merch_enough['merch_saleset'];

                    if ($merch_enough_total > 0) {
                        $totalprice -= $merch_enough_total;
                    }
                }
            }
        }


        //满额减
        $deductenough = 0;
        if ($saleset['enoughs']) {
            foreach ($saleset['enoughs'] as $e) {
                if ($totalprice - $seckill_payprice >= floatval($e['enough']) && floatval($e['money']) > 0) {
                    $deductenough = floatval($e['money']);
                    if ($deductenough > $totalprice - $seckill_payprice) {
                        $deductenough = $totalprice - $seckill_payprice;
                    }
                    break;
                }
            }
        }


        $goodsdata_coupon = array();
        $goodsdata_coupon_temp = array();

        $max_goods_need_deduct = 0;//累加出除最后一个抵扣商品外所需的积分数
        $max_goods_need_deduct2 = 0;//累加出除最后一个抵扣商品外所需的余额数
        foreach ($allgoods as &$g) {

            //计算每个订单商品积分抵扣所需积分--单品退换货（此处不需要考虑秒杀，因为秒杀不支持购物车）
            $g['consume_deduct'] = 0;//商品积分抵扣所使用的积分数
            //如果开启了积分抵扣，并且抵扣积分数不为空
            if (!empty($_GPC['deduct']) && !empty($saleset['creditdeduct']) && !empty($g['need_deduct'])) {
                //会员积分
                $credit1 = $member['credit1'];
                if ($credit1 > 0) {
                    $credit1 = floor($credit1);
                }

                $pcredit = intval($saleset['credit']); //积分比例
                $pmoney = round(floatval($saleset['money']), 2); //抵扣比例
                $order_need_deduct = ceil($deductprice / ($pcredit * $pmoney));//订单积分抵扣所需的积分
                $goods_need_deduct = floor($g['need_deduct'] / ($pcredit * $pmoney));//商品积分抵扣所需的积分

                //如果会员没有足够的积分抵扣则根据 会员积分 X（商品所需积分/订单所需积分）来计算
                if ($order_need_deduct > $credit1) {
                    $g['consume_deduct'] = floor($credit1 * ($goods_need_deduct / $order_need_deduct));
                } else {
                    $g['consume_deduct'] = $goods_need_deduct;
                }
                //如果是最后一个积分抵扣商品，则 商品积分抵扣所使用的积分数=订单积分抵扣所需的积分-其他商品积分抵扣总和
                if ($need_deduct_num == 1) {

                    if ($order_need_deduct > $credit1) {
                        $g['consume_deduct'] = $credit1 - $max_goods_need_deduct;
                    } else {
                        $g['consume_deduct'] = $order_need_deduct - $max_goods_need_deduct;
                    }
                }
                //抵扣数量减1,并且$max_goods_need_deduct累加
                $need_deduct_num--;
                $max_goods_need_deduct += $g['consume_deduct'];
            }

            //计算每个订单商品余额抵扣所需余额--单品退换货（此处不需要考虑秒杀，因为秒杀不支持购物车）
            $g['consume_deduct2'] = 0;//商品积分抵扣所使用的积分数
            //如果开启了余额抵扣，并且抵扣金额不为空
            if (!empty($_GPC['deduct2']) && !empty($saleset['moneydeduct']) && !empty($g['need_deduct2'])) {
                //会员余额
                $credit2 = $member['credit2'];

                $order_need_deduct2 = $deductprice2;//

                $goods_need_deduct2 = price_format(floor($g['need_deduct2'] * 100) / 100);//商品余额抵扣所需的余额


                //如果会员没有足够的余额抵扣则根据 会员余额 X（商品所需余额/订单所需余额）来计算
                if ($order_need_deduct2 > $credit2) {
                    $g['consume_deduct2'] = floor($credit2 * ($goods_need_deduct2 / $order_need_deduct2));
                } else {
                    $g['consume_deduct2'] = $goods_need_deduct2;
                }
                //如果是最后一个积分抵扣商品，则 商品积分抵扣所使用的积分数=订单积分抵扣所需的积分-其他商品积分抵扣总和
                if ($need_deduct2_num == 1) {

                    if ($order_need_deduct2 > $credit2) {
                        $g['consume_deduct2'] = $credit2 - $max_goods_need_deduct2;
                    } else {
                        $g['consume_deduct2'] = $order_need_deduct2 - $max_goods_need_deduct2;
                    }
                }
                //抵扣数量减1,并且$max_goods_need_deduct2累加
                $need_deduct2_num--;
                $max_goods_need_deduct2 += $g['consume_deduct2'];
            }

            /* //批发商品不享受优惠
            if($g['type']==4){
                $goodsdata_coupon_temp[] = $g;
            }*/
            if ($g['seckillinfo'] && $g['seckillinfo']['status'] == 0) {
                //秒杀商品不使用优惠券
                $goodsdata_coupon_temp[] = $g;
            } else {
                if (floatval($g['buyagain']) > 0) {
                    //第一次后买东西享受优惠
                    if (!m('goods')->canBuyAgain($g) || !empty($g['buyagain_sale'])) {
                        $goodsdata_coupon[] = $g;
                    } else {
                        $goodsdata_coupon_temp[] = $g;
                    }
                } else {
                    $goodsdata_coupon[] = $g;
                }
            }
        }
        unset($g);


        //直播商品商城BUG-可以在直播价的基础上打折-改成不能打折

        if ($liveid > 0 && $cardid <= 0) {
            $totalprice += $discountprice + $isdiscountprice;
            $discountprice = 0;
            $isdiscountprice = 0;
        }


        //满额减
        $totalprice -= $deductenough;
        $return_array = $this->caculatecoupon($contype, $couponid, $wxid, $wxcardid, $wxcode, $goodsdata_coupon, $totalprice, $discountprice, $isdiscountprice, 1, $discountprice_array, $merchisdiscountprice, $totalprice);
        $couponprice = 0;
        $coupongoodprice = 0;
        if (!empty($return_array)) {
            $isdiscountprice = $return_array['isdiscountprice'];
            $discountprice = $return_array['discountprice'];
            $couponprice = $return_array['deductprice'];
            $totalprice = $return_array['totalprice'];
            $discountprice_array = $return_array['discountprice_array'];
            $merchisdiscountprice = $return_array['merchisdiscountprice'];
            $coupongoodprice = $return_array['coupongoodprice'];
            $couponmerchid = $return_array['couponmerchid'];
            $allgoods = $return_array['$goodsarr'];
            $allgoods = array_merge($allgoods, $goodsdata_coupon_temp);
        }
        //地址
        $addressid = intval($_GPC['addressid']);
        $address = false;
        if (!empty($addressid) && $dispatchtype == 0 && !$isonlyverifygoods) {
            $address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and openid=:openid and uniacid=:uniacid   limit 1'
                , array(':uniacid' => $uniacid, ':openid' => $openid, ':id' => $addressid));
            if (empty($address)) {
                show_json(0, '未找到地址');
            } else {
                if (empty($address['province']) || empty($address['city'])) {
                    show_json(0, '地址请选择省市信息');
                }
                //新地址库判断
                $area_set = m('util')->get_area_config_set();
                $new_area = intval($area_set['new_area']);
                if (!empty($new_area)) {
                    if (empty($address['datavalue']) || trim($address['datavalue'] == 'null null null')) {
                        //如果新地址库，code为空的情况
                        show_json(-1, '地址库信息已升级，需要您重新编辑保存您的地址');
                    }
                }
            }
        }
        //$sysset = m('common')->getSysset('trade');
        //如果线下核销产品没有开启联系人信息,而且还没有绑定手机号的话
//        if(array_key_exists('set_realname',$sysset) && array_key_exists('set_mobile',$sysset) && empty($addressid)){
//            if($sysset['set_realname'] ==1 && $sysset['set_mobile'] ==1){
//                if(empty($member['carrier_mobile']) || empty($member['mobile'])){
//                    show_json(0, '当前没有联系人手机号,请绑定手机号!');
//                }
//            }
//        }
        //$isvirtual 实体物品计算运费
        //$isverify  非核销计算运费
        //$dispatchtype 选择了快递(非自提)计算运费
        if (!$isvirtual && !$isverify && !$isonlyverifygoods && $dispatchtype == 0 && !$isonlyverifygoods) {
            if (empty($addressid)) {
                show_json(0, '请选择地址');
            }

            $dispatch_array = m('order')->getOrderDispatchPrice($allgoods, $member, $address, $saleset, $merch_array, 2);

            $dispatch_price = $dispatch_array['dispatch_price'] - $dispatch_array['seckill_dispatch_price'];
            $seckill_dispatchprice = $dispatch_array['seckill_dispatch_price'];
            $nodispatch_array = $dispatch_array['nodispatch_array'];

            if (!empty($nodispatch_array['isnodispatch'])) {
                show_json(0, $nodispatch_array['nodispatch']);
            }
        }
        $return_card_array = $this->caculatecard($cardid, $dispatch_price, $lotterydiscountprice, '', $allgoods, $totalprice, $discountprice, $isdiscountprice, '', 1, $_GPC['goods_dispatch']);
        $carddeductprice = 0;
        if (!empty($return_card_array)) {
            $totalprice = $return_card_array['totalprice'];
            $carddeductprice = $return_card_array['carddeductprice'];//会员卡折扣之后的价格
            $deductprice2 = $return_card_array['deductcredit2'];//重新计算最多抵扣
        }
        if ($isonlyverifygoods) {
            $addressid = 0;
        }

        if (!empty($return_card_array)) {
            $dispatch_price = $return_card_array['dispatch_price'];
        }


        //满额减
        //   $totalprice -= $deductenough;
        //运费
        $totalprice += $dispatch_price + $seckill_dispatchprice;

        //余额最多抵扣+运费
        if ($saleset && empty($saleset['dispatchnodeduct'])) {
            $deductprice2 += $dispatch_price;
        }

        if (empty($goods[0]['bargain_id'])) {
            //积分抵扣
            $deductcredit = 0; //抵扣需要扣除的积分
            $deductmoney = 0; //抵扣的钱
            $deductcredit2 = 0; //可抵扣的余额

            if ($sale_plugin) {
                //积分抵扣
                if (!empty($_GPC['deduct'])) {
                    //会员积分
                    $credit = $member['credit1'];
                    if ($credit > 0) {
                        $credit = floor($credit);
                    }
                    if (!empty($saleset['creditdeduct'])) {
                        $pcredit = intval($saleset['credit']); //积分比例
                        $pmoney = round(floatval($saleset['money']), 2); //抵扣比例

                        if ($pcredit > 0 && $pmoney > 0) {
                            if ($credit % $pcredit == 0) {
                                $deductmoney = round(intval($credit / $pcredit) * $pmoney, 2);
                            } else {
                                $deductmoney = round((intval($credit / $pcredit) + 1) * $pmoney, 2);
                            }
                        }
                        if ($deductmoney > $deductprice) {
                            $deductmoney = $deductprice;
                        }
                        if ($deductmoney > $totalprice - $seckill_payprice) { //减掉秒杀的钱再抵扣
                            $deductmoney = $totalprice - $seckill_payprice;
                        }
                        $deductcredit = ceil($deductmoney / $pmoney * $pcredit);
                    }
                }
                $totalprice -= $deductmoney;
            }

            //余额抵扣
            if (!empty($saleset['moneydeduct'])) {
                if (!empty($_GPC['deduct2'])) {
                    $deductcredit2 = $member['credit2'];
                    if ($deductcredit2 > $totalprice - $seckill_payprice) {  //减掉秒杀的钱再抵扣
                        $deductcredit2 = $totalprice - $seckill_payprice;
                    }
                    if ($deductcredit2 > $deductprice2) {
                        $deductcredit2 = $deductprice2;
                    }
                }
                $totalprice -= $deductcredit2;
            }

        }

        //生成核销消费码
        $verifyinfo = array();
        $verifycode = "";
        $verifycodes = array();
        if (($isverify || $dispatchtype) && !$isonlyverifygoods) {

            if ($isverify) {
                if ($verifytype == 0 || $verifytype == 1) {
                    //一次核销+ 按次核销（一个码 )
                    $verifycode = random(8, true);
                    while (1) {
                        $count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where verifycode=:verifycode and uniacid=:uniacid limit 1', array(':verifycode' => $verifycode, ':uniacid' => $_W['uniacid']));
                        if ($count <= 0) {
                            break;
                        }
                        $verifycode = random(8, true);
                    }
                } else if ($verifytype == 2) {
                    //按码核销
                    $totaltimes = intval($allgoods[0]['total']);
                    if ($totaltimes <= 0) {
                        $totaltimes = 1;
                    }
                    for ($i = 1; $i <= $totaltimes; $i++) {

                        $verifycode = random(8, true);
                        while (1) {
                            $count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where concat(verifycodes,\'|\' + verifycode +\'|\' ) like :verifycodes and uniacid=:uniacid limit 1', array(':verifycodes' => "%{$verifycode}%", ':uniacid' => $_W['uniacid']));
                            if ($count <= 0) {
                                break;
                            }
                            $verifycode = random(8, true);
                        }
                        $verifycodes[] = "|" . $verifycode . "|";
                        $verifyinfo[] = array(
                            'verifycode' => $verifycode,
                            'verifyopenid' => '',
                            'verifytime' => 0,
                            'verifystoreid' => 0
                        );
                    }
                }
            } else if ($dispatchtype) {
                //自提码
                $verifycode = random(8, true);
                while (1) {
                    $count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where verifycode=:verifycode and uniacid=:uniacid limit 1', array(':verifycode' => $verifycode, ':uniacid' => $_W['uniacid']));
                    if ($count <= 0) {
                        break;
                    }
                    $verifycode = random(8, true);
                }
            }
        }
        $carrier = $_GPC['carriers'];
        $carriers = is_array($carrier) ? iserializer($carrier) : iserializer(array());

        if ($totalprice <= 0) {
            $totalprice = 0;
        }


        if ($ismerch == 0 || ($ismerch == 1 && count($merch_array) == 1)) {
            //需要创建一个订单
            $multiple_order = 0;
        } else {
            //需要创建多个订单
            $multiple_order = 1;
        }

        //生成订单号
        if ($ismerch > 0) {
            $ordersn = m('common')->createNO('order', 'ordersn', 'ME');
        } else {
            $ordersn = m('common')->createNO('order', 'ordersn', 'SH');
        }
        if (!empty($goods[0]['bargain_id']) && p('bargain')) {//???
            $bargain_act = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_bargain_actor') . " WHERE id = :id AND openid = :openid ", array(':id' => $goods[0]['bargain_id'], ':openid' => $_W['openid']));
            if (empty($bargain_act)) {
                die('没有这个商品');
            }
            if ($_SESSION['taskcut']) {
                $dispatch_price = 0;
            }
            $totalprice = $bargain_act['now_price'] + $dispatch_price;
            $goodsprice = $bargain_act['now_price'];
            if (!pdo_update('ewei_shop_bargain_actor', array('status' => 1), array('id' => $goods[0]['bargain_id'], 'openid' => $_W['openid']))) {
                die('下单失败');
            }

            $ordersn = substr_replace($ordersn, 'KJ', 0, 2);//砍价订单号
        }
        //套餐订单价格
        $is_package = 0;
        if (!empty($packageid)) {
            $goodsprice = $packageprice;
            if ($package['dispatchtype'] == 1) {
                $dispatch_array = m('order')->getOrderDispatchPrice($allgoods, $member, $address, false, $merch_array, 0);
                $dispatch_price = $dispatch_array['dispatch_price'] - $dispatch_array['seckill_dispatch_price'];
            } else {
                $dispatch_price = $package['freight'];
            }
            $totalprice = $packageprice + $dispatch_price;
            //套餐产品使用了会员卡重新计算价格
            if ($cardid > 0 && p('membercard')) {
                $card_data = p('membercard')->getMemberCard($cardid);
                if (empty($card_data) != true) {
                    if ($card_data['discount'] == 0) {
                        $totalprice = $totalprice + $discountprice + $isdiscountprice;//禁用折上折按照原价来
                        $discountprice = 0;
                        $isdiscountprice = 0;
                    }

                }

                $return_card_array = $this->caculatecard($cardid, $dispatch_price, $lotterydiscountprice, '', $allgoods, $totalprice, $discountprice, $isdiscountprice, '', 1);
                $carddeductprice = 0;
                if (!empty($return_card_array)) {
                    $totalprice = $return_card_array['totalprice'];
                }
            }

            $is_package = 1;
            $discountprice = 0;
        }

        if ($taskgoodsprice) {
            $totalprice = $taskgoodsprice;
            $goodsprice = $taskgoodsprice;

            if ($taskGoodsInfo0['goods'][$goodsid]['num'] <= 1) {
                unset($taskGoodsInfo0['goods'][$goodsid]);
            } else {
                $taskGoodsInfo0['goods'][$goodsid]['num']--;
            }
            pdo_update('ewei_shop_task_extension_join', array('rewarded' => serialize($taskGoodsInfo0)), array('id' => $task_id, 'uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
            unset($_SESSION[$goodsid . '_task_id']);
        }


        //订单数据
        $order = array();
        $order['ismerch'] = $ismerch;
        $order['parentid'] = 0;
        $order['uniacid'] = $uniacid;
        $order['openid'] = $openid;
        $order['ordersn'] = $ordersn;
        $order['price'] = $totalprice;
        $order['oldprice'] = $totalprice;
        $order['grprice'] = $grprice;
        $order['taskdiscountprice'] = $taskdiscountprice;
        if ($_SESSION['taskcut']) {
            $order['taskdiscountprice'] = $order['price'] - $_SESSION['taskcut']['price'];
        }
        $order['lotterydiscountprice'] = $lotterydiscountprice;
        $order['discountprice'] = $discountprice;
        //砍价没有会员折扣
        if (!empty($goods[0]['bargain_id']) && p('bargain')) {
            $order['discountprice'] = 0;
        }
        $order['isdiscountprice'] = $isdiscountprice;
        $order['merchisdiscountprice'] = $merchisdiscountprice;
        $order['cash'] = $cash;
        $order['status'] = 0;
        $order['remark'] = trim($_GPC['remark']);
        $order['addressid'] = empty($dispatchtype) ? $addressid : 0;
        $order['goodsprice'] = $goodsprice;
        $order['dispatchprice'] = $dispatch_price + $seckill_dispatchprice;

        if (!is_null($_SESSION['exchangeprice']) && !empty($_SESSION['exchange']) && p('exchange')) {
            if ($_GPC['dispatchtype'] === '1') {// 兑换中心自提单运费为0
                $_SESSION['exchangepostage'] = 0;
            }
            $order['price'] = $_SESSION['exchangeprice'] + $_SESSION['exchangepostage'];
            $order['ordersn'] = m('common')->createNO('order', 'ordersn', 'DH');
            $order['goodsprice'] = $_SESSION['exchangeprice'];
            $order['dispatchprice'] = $_SESSION['exchangepostage'];

            if (!empty($_SESSION['diyform'])) {
                $exchange_diyform = $_SESSION['diyform'];
            }
        }
        $taskreward = $_SESSION['taskcut'];
        $_SESSION['taskcut'] = null;
        //任务中心新版下单

        //这里加上数组判断的原因，任务中心可能里面很有赠品，此时$goodsid不正确导致不走这一步
        if (($taskreward['reward_data'] == $goodsid && p('task')) || (in_array($taskreward['reward_data'], $task_reward_goods) && p('task'))) {
            if ($cardid > 0) { //任务中心使用会员卡
                $order['price'] = $return_card_array['totalprice'] + $dispatch_price;
            } else {
                $order['price'] = $taskreward['price'] + $dispatch_price;
                $order['goodsprice'] = $taskreward['price'];
                $order['dispatchprice'] = $dispatch_price;
                $deductenough = 0;//满额立减为0；
                $order['discountprice'] = 0;//会员优惠为0
                $order['isdiscountprice'] = 0;//促销优惠为0
            }

            //处理状态
            p('task')->setTaskRewardGoodsSent($taskreward['id']);
        }

        //新增判断套餐运费
//        if($package){
//        if($package['dispatchtype']==1) {
//            $dispatch_array = m('order')->getOrderDispatchPrice($allgoods, $member, $address, false, $merch_array, 0);
//            $dispatch_price = $dispatch_array['dispatch_price'] -  $dispatch_array['seckill_dispatch_price'];
//        }else {
//            $dispatch_price = $package['freight'];
//
//        }
//            $order['dispatchprice'] = $dispatch_price;
//
//        }

        //开启包邮之后总价-运费
        if (empty($return_card_array) != true && $return_card_array['shipping'] == 1) {
            $dispatch_price = 0;
            $seckill_dispatchprice = 0;
            $order['dispatchprice'] = 0;
            $order['price'] = $totalprice - $dispatch_price - $seckill_dispatchprice;
        }

        if (!empty($_SESSION['exchange']) && p('exchange')) {
            $deductenough = 0;//兑换产品没有满额减活动
        }
        $order['dispatchtype'] = $dispatchtype;
        $order['dispatchid'] = $dispatchid;
        $order['storeid'] = $carrierid;
        $order['carrier'] = $carriers;
        $order['createtime'] = time();
        $order['olddispatchprice'] = $dispatch_price + $seckill_dispatchprice;
        $order['contype'] = $contype;
        $order['couponid'] = $couponid;
        $order['wxid'] = $wxid;
        $order['wxcardid'] = $wxcardid;
        $order['wxcode'] = $wxcode;

        $order['couponmerchid'] = $couponmerchid;
        $order['paytype'] = 0; //如果是上门取货，支付方式为3
        $order['deductprice'] = $deductmoney;
        $order['deductcredit'] = $deductcredit;
        $order['deductcredit2'] = $deductcredit2;
        $order['deductenough'] = $deductenough;
        $order['merchdeductenough'] = $merch_enough_total;
        $order['couponprice'] = $couponprice;
        $order['merchshow'] = 0;
        $order['buyagainprice'] = $buyagainprice;
        $order['ispackage'] = $is_package;
        $order['packageid'] = $packageid;

        $order['seckilldiscountprice'] = $seckill_price;

        $order['quickid'] = intval($_GPC['fromquick']);
        //华仔定制邀请码
        $order['officcode'] = intval($_GPC['officcode']);

        // 直播间
        $order['liveid'] = $liveid;

        if (!empty($ccard)) {
            $order['ccard'] = 1;
        }

        //创始人字段
        $author = p('author');
        if ($author) {
            $author_set = $author->getSet();
            if (!empty($member['agentid']) && !empty($member['authorid'])) {
                $order['authorid'] = $member['authorid'];
            }
            if (!empty($author_set['selfbuy']) && !empty($member['isauthor']) && !empty($member['authorstatus'])) {
                $order['authorid'] = $member['id'];
            }
        }

        if ($multiple_order == 0) {
            //创建一个订单的字段
            $order_merchid = current(array_keys($merch_array));
            $order['merchid'] = intval($order_merchid);
            $order['isparent'] = 0;
            $order['transid'] = '';
            $order['isverify'] = $isverify ? 1 : 0;
            $order['verifytype'] = $verifytype;
            $order['verifyendtime'] = $endtime;
            $order['verifycode'] = $verifycode;
            $order['verifycodes'] = implode('', $verifycodes);
            $order['verifyinfo'] = iserializer($verifyinfo);
            $order['virtual'] = $virtualid;
            $order['isvirtual'] = $isvirtual ? 1 : 0;
            $order['isvirtualsend'] = $isvirtualsend ? 1 : 0;

            $order['invoicename'] = trim($_GPC['invoicename']);
            $order['coupongoodprice'] = $coupongoodprice;
            $order['city_express_state'] = empty($dispatch_array['city_express_state']) == true ? 0 : $dispatch_array['city_express_state'];
        } else {
            //创建多个订单的字段
            $order['isparent'] = 1;
            $order['merchid'] = 0;
        }
//自定义表单
        if (!empty($diyinfo) && $diyinfo['bargain'] > 0) {
            if ($diyinfo['diyformtype'] == 1) {
                $order_formInfo = $diyform_plugin->getDiyformInfo($diyinfo['diyformid']);
                $fields = $order_formInfo['fields'];
                $diyform_data = $diyform_plugin->getInsertData($fields, $_GPC['diydata']);

                $idata = $diyform_data['data'];
                $order['diyformfields'] = iserializer($fields);
                $order['diyformdata'] = $idata;

                $order['diyformid'] = $diyinfo['diyformid'];
            } elseif ($diyinfo['diyformtype'] == 2) {
                $fields = unserialize($diyinfo['diyfields']);
                $diyform_data = $diyform_plugin->getInsertData($fields, $_GPC['diydata']);
                $idata = $diyform_data['data'];
                $order['diyformfields'] = iserializer($fields);
                $order['diyformdata'] = $idata;
                $order['diyformid'] = '999999999';
            } else {
            }
        } else {
            if ($diyform_plugin) {
                if (is_array($_GPC['diydata']) && !empty($order_formInfo)) {
                    $diyform_data = $diyform_plugin->getInsertData($fields, $_GPC['diydata']);
                    $idata = $diyform_data['data'];
                    $order['diyformfields'] = iserializer($fields);
                    $order['diyformdata'] = $idata;
                    $order['diyformid'] = $order_formInfo['id'];
                }
            }
        }


        if (!empty($address)) {
            $order['address'] = iserializer($address);
        }

        //如果是全付通,则生成改价次数
        list(, $payment) = m('common')->public_build();
        if ($payment['type'] == '4') {
            $order['ordersn2'] = 100;
        }

        pdo_insert('ewei_shop_order', $order);
        $orderid = pdo_insertid();

        //将订单数据存到redis里面
        if (function_exists('redis_setarr')) {
            $redis_order = $order;
            $redis_order['id'] = $orderid;
            redis_setarr($_W['uniacid'] . '_order_' . $orderid, $redis_order);

        }

        //记录会员卡使用记录
        if (!empty($return_card_array) && $orderid) {

            if (p('membercard')) {
                p('membercard')->member_card_use_record($orderid, $cardid, $carddeductprice, $_W['openid']);
            }

        }

        if (!empty($goods[0]['bargain_id']) && p('bargain')) {
            pdo_update('ewei_shop_bargain_actor', array('order' => $orderid), array('id' => $goods[0]['bargain_id'], 'openid' => $_W['openid']));
        }
        if ($multiple_order == 0) {
            //开始创建一个订单
            $exchangepriceset = $_SESSION['exchangepriceset'];
            //保存订单商品
            $exchangetitle = '';
            foreach ($allgoods as $goods) {
                $order_goods = array();
                if (!empty($bargain_act) && p('bargain')) {
                    $goods['total'] = 1;
                    $goods['ggprice'] = $bargain_act['now_price'];
                    pdo_query("UPDATE " . tablename('ewei_shop_goods') . " SET sales = sales + 1 WHERE id = :id AND uniacid = :uniacid", array(':id' => $goods['goodsid'], ':uniacid' => $uniacid));
                }
                $order_goods['merchid'] = $goods['merchid'];
                $order_goods['merchsale'] = $goods['merchsale'];
                $order_goods['uniacid'] = $uniacid;
                $order_goods['orderid'] = $orderid;
                $order_goods['goodsid'] = $goods['goodsid'];
                $order_goods['price'] = $goods['marketprice'] * $goods['total'];
                $order_goods['total'] = $goods['total'];
                $order_goods['optionid'] = $goods['optionid'];
                $order_goods['createtime'] = time();
                $order_goods['optionname'] = $goods['optiontitle'];
                $order_goods['title'] = $goods['title'];
                $order_goods['goodssn'] = $goods['goodssn'];
                $order_goods['productsn'] = $goods['productsn'];
                $order_goods['realprice'] = $goods['ggprice'];
                $order_goods['consume'] = iserializer(array('consume_deduct' => $goods['consume_deduct'], 'consume_deduct2' => $goods['consume_deduct2']));//序列化商品抵扣所使用的积分和余额
                $isfullback = $this->isfullbackgoods($goods['goodsid'], $goods['optionid']);
                $order_goods['fullbackid'] = $isfullback;
                $exchangetitle .= $goods['title'];//exchange
                if (p('exchange') && is_array($exchangepriceset)) {
                    $order_goods['realprice'] = 0;

                    foreach ($exchangepriceset as $ke => $va) {
                        if (empty($goods['optionid']) && is_array($va) && $goods['goodsid'] == $va[0] && $va[1] == 0) {//无规格
                            $order_goods['realprice'] = $va[2];
                            break;
                        }
                        if (!empty($goods['optionid']) && is_array($va) && $goods['optionid'] == $va[0] && $va[1] == 1) {//规格
                            $order_goods['realprice'] = $va[2];
                            break;
                        }
                    }
                }
                $order_goods['oldprice'] = $goods['ggprice'];

                if ($goods['discounttype'] == 1) {
                    $order_goods['isdiscountprice'] = $goods['isdiscountprice'];
                } else {
                    $order_goods['isdiscountprice'] = 0;
                }
                $order_goods['openid'] = $openid;

                if ($diyform_plugin) {
                    if ($goods['diyformtype'] == 2) {
                        //商品使用了独立自定义的表单
                        $order_goods['diyformid'] = 0;
                    } else {
                        //商品使用了表单模板
                        $order_goods['diyformid'] = $goods['diyformid'];
                    }
                    $order_goods['diyformdata'] = $goods['diyformdata'];
                    $order_goods['diyformfields'] = $goods['diyformfields'];
                }
                if (!empty($exchange_diyform)) {
                    $order_goods['diyformdata'] = iserializer($exchange_diyform[$goods['goodsid']]['data']);
                    $order_goods['diyformfields'] = iserializer($exchange_diyform[$goods['goodsid']]['fields']);
                }

                if (floatval($goods['buyagain']) > 0) {
                    //数据库是否有购买过的商品没用掉的
                    if (!m('goods')->canBuyAgain($goods)) {
                        $order_goods['canbuyagain'] = 1;
                    }
                }
                if ($goods['seckillinfo'] && $goods['seckillinfo']['status'] == 0) {
                    $order_goods['seckill'] = 1;
                    $order_goods['seckill_taskid'] = $goods['seckillinfo']['taskid'];
                    $order_goods['seckill_roomid'] = $goods['seckillinfo']['roomid'];
                    $order_goods['seckill_timeid'] = $goods['seckillinfo']['timeid'];
                    //如果开启了过期抢购而且 $seckillinfo 的timeid和options的timeid不一样的话用options里边的
                    if (!empty($goods['seckillinfo']['options']) && $goods['seckillinfo']['options'][0]['timeid'] != $order_goods['seckill_timeid']) {
                        $order_goods['seckill_timeid'] = $goods['seckillinfo']['options'][0]['timeid'];
                    }
                }
                //单独商品的时候,将优惠券折扣算到商品里边去
                if (count($allgoods) == 1 && $order['couponprice'] > 0) {
                    $order_goods['realprice'] = $order_goods['realprice'] - $order['couponprice'];
                }
                pdo_insert('ewei_shop_order_goods', $order_goods);
                unset($_SESSION['diyform']);

                if ($goods['seckillinfo'] && $goods['seckillinfo']['status'] == 0) {
                    plugin_run("seckill::setSeckill", $goods['seckillinfo'], $goods, $_W['openid'], $orderid, 0, $order['createtime']);
                }

            }

        } else {
            //开始创建多个子订单

            //记录订单商品中的订单id
            $og_array = array();
            $ch_order_data = m('order')->getChildOrderPrice($order, $allgoods, $dispatch_array, $merch_array, $sale_plugin, $discountprice_array, $orderid);
            foreach ($merch_array as $key => $value) {
                $merchid = $key;
                $is_exchange = (p('exchange') && $_SESSION['exchange']);
                if ($is_exchange) {
                    $order_head = 'DH';
                } else {
                    if (!empty($merchid)) {
                        $order_head = 'ME';
                    } else {
                        $order_head = 'SH';
                    }
                }

                //生成子订单号
                $order['ordersn'] = m('common')->createNO('order', 'ordersn', $order_head);


                $order['merchid'] = $merchid;
                $order['parentid'] = $orderid;
                $order['isparent'] = 0;
                $order['merchshow'] = 1;

                $order['dispatchprice'] = $dispatch_array['dispatch_merch'][$merchid];
                $order['olddispatchprice'] = $dispatch_array['dispatch_merch'][$merchid];

                if (empty($packageid)) {
                    $order['merchisdiscountprice'] = $discountprice_array[$merchid]['merchisdiscountprice'];
                    $order['isdiscountprice'] = $discountprice_array[$merchid]['isdiscountprice'];
                    $order['discountprice'] = $discountprice_array[$merchid]['discountprice'];
                }

                $order['price'] = $ch_order_data[$merchid]['price'];
                $order['grprice'] = $ch_order_data[$merchid]['grprice'];
                $order['goodsprice'] = $ch_order_data[$merchid]['goodsprice'];

                $order['deductprice'] = $ch_order_data[$merchid]['deductprice'];
                $order['deductcredit'] = $ch_order_data[$merchid]['deductcredit'];
                $order['deductcredit2'] = $ch_order_data[$merchid]['deductcredit2'];

                $order['merchdeductenough'] = $ch_order_data[$merchid]['merchdeductenough'];
                $order['deductenough'] = $ch_order_data[$merchid]['deductenough'];


                //多商户参与优惠券计算的商品价格(参与活动之后的价格)
                $order['coupongoodprice'] = $discountprice_array[$merchid]['coupongoodprice'];

                $order['couponprice'] = $discountprice_array[$merchid]['deduct'];

                if (empty($order['couponprice'])) {
                    $order['couponid'] = 0;
                    $order['couponmerchid'] = 0;
                } else if ($couponmerchid > 0) {
                    if ($merchid == $couponmerchid) {
                        $order['couponid'] = $couponid;
                        $order['couponmerchid'] = $couponmerchid;
                    } else {
                        $order['couponid'] = 0;
                        $order['couponmerchid'] = 0;
                    }
                }

                pdo_insert('ewei_shop_order', $order);

                //子订单id
                $ch_orderid = pdo_insertid();

                $merch_array[$merchid]['orderid'] = $ch_orderid;

                if ($couponmerchid > 0) {
                    if ($merchid == $couponmerchid) {
                        $couponorderid = $ch_orderid;
                    }
                }
                foreach ($value['goods'] as $k => $v) {
                    //$v 商品id
                    $og_array[$v] = $ch_orderid;
                }
            }

            //子订单保存订单商品
            foreach ($allgoods as $goods) {

                $goodsid = $goods['goodsid'];

                $order_goods = array();
                $order_goods['parentorderid'] = $orderid;
                $order_goods['merchid'] = $goods['merchid'];
                $order_goods['merchsale'] = $goods['merchsale'];
                $order_goods['orderid'] = $og_array[$goodsid];

                $order_goods['uniacid'] = $uniacid;
                $order_goods['goodsid'] = $goodsid;
                $order_goods['price'] = $goods['marketprice'] * $goods['total'];
                $order_goods['total'] = $goods['total'];
                $order_goods['optionid'] = $goods['optionid'];
                $order_goods['createtime'] = time();
                $order_goods['optionname'] = $goods['optiontitle'];
                $order_goods['goodssn'] = $goods['goodssn'];
                $order_goods['productsn'] = $goods['productsn'];
                $order_goods['realprice'] = $goods['ggprice'];
                $order_goods['oldprice'] = $goods['ggprice'];
                $order_goods['isdiscountprice'] = $goods['isdiscountprice'];
                $order_goods['openid'] = $openid;
                $order_goods['consume'] = iserializer(array('consume_deduct' => $goods['consume_deduct'], 'consume_deduct2' => $goods['consume_deduct2']));//序列化商品抵扣所使用的积分和余额

                $isfullback = $this->isfullbackgoods($goodsid, $goods['optionid']);
                $order_goods['fullbackid'] = $isfullback;
                if ($diyform_plugin) {
                    if ($goods['diyformtype'] == 2) {
                        //商品使用了独立自定义的表单
                        $order_goods['diyformid'] = 0;
                    } else {
                        //商品使用了表单模板
                        $order_goods['diyformid'] = $goods['diyformid'];
                    }
                    $order_goods['diyformdata'] = $goods['diyformdata'];
                    $order_goods['diyformfields'] = $goods['diyformfields'];
                }

                if (!empty($exchange_diyform)) {
                    $order_goods['diyformdata'] = iserializer($exchange_diyform[$goods['goodsid']]['data']);
                    $order_goods['diyformfields'] = iserializer($exchange_diyform[$goods['goodsid']]['fields']);
                }

                if (floatval($goods['buyagain']) > 0) {
                    //数据库是否有购买过的商品没用掉的
                    if (!m('goods')->canBuyAgain($goods)) {
                        $order_goods['canbuyagain'] = 1;
                    }
                }
                pdo_insert('ewei_shop_order_goods', $order_goods);
                unset($_SESSION['diyform']);
            }
        }


        if ($data['type'] == 3) {

            $order_v = array();
            //如果$order_v 为空时从redis里面取
            if (function_exists('redis_getarr')) {
                $key = $_W['uniacid'] . '_order_' . $orderid;
                $order_v = redis_getarr($key);
            }

            if (empty($order_v)) {
                $order_v = pdo_fetch('select id,ordersn, price,openid,dispatchtype,addressid,carrier,status,isverify,deductcredit2,`virtual`,isvirtual,couponid,isvirtualsend,isparent,paytype,merchid,agentid,createtime,buyagainprice,istrade,tradestatus from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and id = :id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $orderid));
            }

            if (com('virtual')) {
                $virtual_res = com('virtual')->pay_befo($order_v);
                if (is_array($virtual_res) && $virtual_res['message']) {
                    pdo_update('ewei_shop_order', array('status' => -1, 'canceltime' => time()), array('id' => $orderid, 'uniacid' => $_W['uniacid']));
                    show_json(0, $virtual_res['message']);
                }
            }
        }

        if (!empty($_SESSION['exchange']) && p('exchange')) {
            $codeResult = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_exchange_code') . " WHERE `key` = :key AND uniacid =:uniacid", array(':key' => $_SESSION['exchange_key'], ':uniacid' => $_W['uniacid']));
            if ($codeResult['status'] == 2) {
                show_json(0, '兑换失败:此兑换码已兑换');
            }
            $groupResult = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_exchange_group') . " WHERE id = :id AND uniacid = :uniacid ", array(':uniacid' => $_W['uniacid'], ':id' => $codeResult['groupid']));
            $record_exsit = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_exchange_record') . " WHERE `key`=:key AND uniacid = :uniacid", array(':key' => $_SESSION['exchange_key'], ':uniacid' => $_W['uniacid']));

            if (empty($record_exsit)) {
//                pdo_query("INSERT INTO ".tablename('ewei_shop_exchange_record')."(`key`,`uniacid`,`title`,`serial`) VALUES('{$_SESSION['exchange_key']}','{$_W['uniacid']}','{$_SESSION['exchangetitle']}','{$_SESSION['exchangeserial']}')");
                $data = array('key' => $_SESSION['exchange_key'], 'uniacid' => $_W['uniacid'], 'title' => $_SESSION['exchangetitle'], 'serial' => $_SESSION['exchangeserial']);
                pdo_insert('ewei_shop_exchange_record', $data);
            }

            if (empty($_SESSION['exchange_key']) || is_null($_SESSION['exchangeprice']) || is_null($_SESSION['exchangepostage'])) {
                show_json(0, '兑换超时,请重试');
            } else {
                $checkSubmit = $this->checkSubmit('exchange_plugin');
                if (is_error($checkSubmit)) {
                    show_json(0, $checkSubmit['message']);
                }
                $checkSubmit = $this->checkSubmitGlobal('exchange_key_' . $_SESSION['exchange_key']);
                if (is_error($checkSubmit)) {
                    show_json(0, $checkSubmit['message']);
                }


                if ($groupResult['mode'] != 6) {
                    $exchange_res = pdo_update('ewei_shop_exchange_code', array('status' => 2), array('key' => $_SESSION['exchange_key'], 'status' => 1, 'uniacid' => $_W['uniacid']));//置状态
                    pdo_query("UPDATE " . tablename('ewei_shop_exchange_group') . " SET `use` = `use` + 1 WHERE id = :id AND uniacid = :uniacid", array(':id' => $_SESSION['groupid'], ':uniacid' => $_W['uniacid']));
                } else {
                    pdo_update('ewei_shop_exchange_code', array('goodsstatus' => 2), array('key' => $_SESSION['exchange_key'], 'uniacid' => $_W['uniacid']));//置状态
                    if (($codeResult['balancestatus'] == 2 || empty($groupResult['balance_type'])) && ($codeResult['scorestatus'] == 2 || empty($groupResult['score_type'])) && ($codeResult['redstatus'] == 2 || empty($groupResult['red_type'])) && ($codeResult['couponstatus'] == 2 || empty($groupResult['coupon_type']))) {
                        pdo_update("ewei_shop_exchange_code", array('status' => 2), array('key' => $_SESSION['exchange_key'], 'status' => 1, 'uniacid' => $_W['uniacid']));
                    }
                }
            }
        }

        if (!is_null($_SESSION['exchangeprice']) && !empty($_SESSION['exchange']) && p('exchange')) {
            $exchangeinfo = m('member')->getInfo($_W['openid']);
            if ($groupResult['mode'] != 6) {//记录不存在
                $exchangedata = array('key' => $_SESSION['exchange_key'], 'uniacid' => $_W['uniacid'], 'goods' => $_SESSION['exchangegoods'], 'orderid' => $orderid, 'time' => time(), 'openid' => $_W['openid'], 'mode' => 1, 'nickname' => $exchangeinfo['nickname'], 'groupid' => $_SESSION['groupid'], 'title' => $_SESSION['exchangetitle'], 'serial' => $_SESSION['exchangeserial'], 'ordersn' => $order['ordersn'], 'goods_title' => $exchangetitle);
                pdo_update('ewei_shop_exchange_record', $exchangedata, array('key' => $_SESSION['exchange_key'], 'uniacid' => $_W['uniacid']));
            } else {//记录已存在,说明是组合兑换
                $exchangedata = array('goods' => $_SESSION['exchangegoods'], 'orderid' => $orderid, 'time' => time(), 'openid' => $_W['openid'], 'mode' => 6, 'nickname' => $exchangeinfo['nickname'], 'ordersn' => $order['ordersn'], 'goods_title' => $exchangetitle);
                pdo_update('ewei_shop_exchange_record', $exchangedata, array('key' => $_SESSION['exchange_key'], 'uniacid' => $_W['uniacid']));
            }
            pdo_update('ewei_shop_exchange_cart', array('selected' => 0), array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'selected' => 1));
        }

        //创建优惠券发送任务 数据$orderid
        if (com('coupon') && !empty($orderid)) {
            com('coupon')->addtaskdata($orderid); //订单支付
        }
        //更新会员信息
        /*if (is_array($carrier)) {
            $up = array('realname' => $carrier['carrier_realname'], 'carrier_mobile' => $carrier['carrier_mobile']);
            pdo_update('ewei_shop_member', $up, array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
            if (!empty($member['uid'])) {
                load()->model('mc');
                mc_update($member['uid'], $up);
            }
        }
        徐子轩 2019.8.21  BUG号8633自提人信息填写后后台的真实姓名会跟随改变
        */

        //删除购物车
        if ($_GPC['fromcart'] == 1) {
            //删除购物车
            pdo_query('update ' . tablename('ewei_shop_member_cart') . ' set deleted=1 where  openid=:openid and uniacid=:uniacid and selected=1 ', array(':uniacid' => $uniacid, ':openid' => $openid));
        }

        if (p('quick') && !empty($_GPC['fromquick'])) {
            // 删除快速购买购物车
            pdo_update("ewei_shop_quick_cart", array("deleted" => 1), array("quickid" => intval($_GPC['fromquick']), "uniacid" => $_W['uniacid'], "openid" => $_W['openid']));
        }

        if ($deductcredit > 0) {
            //扣除抵扣积分
            m('member')->setCredit($openid, 'credit1', -$deductcredit, array('0', $_W['shopset']['shop']['name'] . "购物积分抵扣 消费积分: {$deductcredit} 抵扣金额: {$deductmoney} 订单号: {$ordersn}"));
        }

        if ($buyagainprice > 0) {
            m('goods')->useBuyAgain($orderid);
        }

        if ($deductcredit2 > 0) {
            //扣除抵扣余额
            m('member')->setCredit($openid, 'credit2', -$deductcredit2, array('0', $_W['shopset']['shop']['name'] . "购物余额抵扣: {$deductcredit2} 订单号: {$ordersn}"));
        }


        if (empty($virtualid)) {
            //卡密的 付款才计算库存
            //设置库存
            m('order')->setStocksAndCredits($orderid, 0);

        } else {
            //虚拟卡密虚拟销量
            if (isset($allgoods[0])) {
                $vgoods = $allgoods[0];
//                虚拟卡密不在增加虚拟销量
//                pdo_update('ewei_shop_goods', array('sales' => $vgoods['sales'] + $vgoods['total']), array('id' => $vgoods['goodsid']));
            }
        }

        //任务活动下单成功
        if (!empty($tgoods)) {
            $rank = intval($_SESSION[$tgoods['goodsid'] . '_rank']);
            $log_id = intval($_SESSION[$tgoods['goodsid'] . '_log_id']);
            $join_id = intval($_SESSION[$tgoods['goodsid'] . '_join_id']);
            m('goods')->getTaskGoods($tgoods['openid'], $tgoods['goodsid'], $rank, $log_id, $join_id, $tgoods['optionid'], $tgoods['total']);
            $_SESSION[$tgoods['goodsid'] . '_rank'] = 0;
            $_SESSION[$tgoods['goodsid'] . '_log_id'] = 0;
            $_SESSION[$tgoods['goodsid'] . '_join_id'] = 0;
        }

        //模板消息
        m('notice')->sendOrderMessage($orderid);

        //打印机打印
        com_run('printer::sendOrderMessage', $orderid);

        //分销设置
        $pluginc = p('commission');

        if ($taskreward || isset($task_goods_data['task_goods']['is_goods']) && ($task_goods_data['task_goods']['is_goods'] === '0' || $task_goods_data['task_goods']['is_goods'] === 0)) {
            //游戏营销禁用分销
            $pluginc = false;
        }

        if ($pluginc) {
            //分销订单检测
            if ($multiple_order == 0) {
                $pluginc->checkOrderConfirm($orderid);
            } else {
                //处理子订单
                if (!empty($merch_array)) {
                    foreach ($merch_array as $key => $value) {
                        $pluginc->checkOrderConfirm($value['orderid']);
                    }
                }
            }
        }
        unset($_SESSION[$openid . "_order_create"]);
        // 释放兑换中心暂存数据
        if (p('exchange') && $_SESSION['exchange']) {
            $_SESSION['exchange'] = null;
            $exchangepostage = null;
            $exchangeprice = null;
            unset($_SESSION['exchangeprice']);
            unset($_SESSION['exchangepostage']);
        }

        //团队分红
        $dividend = p('dividend');
        if ($dividend) {
            //分红订单检测
            if ($multiple_order == 0) {
                $a = $dividend->checkOrderConfirm($orderid);
            } else {
                //处理子订单
                if (!empty($merch_array)) {
                    foreach ($merch_array as $key => $value) {
                        $dividend->checkOrderConfirm($value['orderid']);
                    }
                }
            }
        }

        show_json(1, array('orderid' => $orderid));
    }


    //单品模板
    protected function singleDiyformData($id = 0)
    {

        global $_W, $_GPC;
        //单品
        $goods_data = false;
        $diyformtype = false;
        $diyformid = 0;
        $diymode = 0;
        $formInfo = false;
        $goods_data_id = 0;
        $diyform_plugin = p('diyform');
        if ($diyform_plugin && !empty($id)) {

            $sql = 'SELECT id as goodsid,type,diyformtype,diyformid,diymode,diyfields FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
            $goods_data = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':id' => $id));
            if (!empty($goods_data)) {
                $diyformtype = $goods_data['diyformtype'];
                $diyformid = $goods_data['diyformid'];
                $diymode = $goods_data['diymode'];
                if ($goods_data['diyformtype'] == 1) {
                    $formInfo = $diyform_plugin->getDiyformInfo($diyformid);
                } else if ($goods_data['diyformtype'] == 2) {
                    $fields = iunserializer($goods_data['diyfields']);
                    if (!empty($fields)) {
                        $formInfo = array(
                            'fields' => $fields
                        );
                    }
                }
            }
        }

        return array(
            'goods_data' => $goods_data,
            'diyformtype' => $diyformtype,
            'diyformid' => $diyformid,
            'diymode' => $diymode,
            'formInfo' => $formInfo,
            'goods_data_id' => $goods_data_id,
            'diyform_plugin' => $diyform_plugin
        );
    }


    function diyform()
    {
        global $_W, $_GPC;
        $goodsid = intval($_GPC['id']);
        $cartid = intval($_GPC['cartid']);
        $openid = $_W['openid'];
        $data = $this->singleDiyformData($goodsid);
        extract($data);

        if ($diyformtype == 2) {
            $diyformid = 0;
        } else {
            $diyformid = $goods_data['diyformid'];
        }

        $fields = $formInfo['fields'];

        $insert_data = $diyform_plugin->getInsertData($fields, $_GPC['diyformdata']);
        $idata = $insert_data['data'];

        $corder_plugin = p('corder');
        if ($corder_plugin) {
            $corder_plugin->check_data($idata);
        }

        $goods_temp = $diyform_plugin->getGoodsTemp($goodsid, $diyformid, $openid);

        $insert = array(
            'cid' => $goodsid,
            'openid' => $openid,
            'diyformid' => $diyformid,
            'type' => 3,
            'diyformfields' => iserializer($fields),
            'diyformdata' => $idata,
            'uniacid' => $_W['uniacid']
        );

        if (empty($goods_temp)) {
            pdo_insert('ewei_shop_diyform_temp', $insert);
            $gdid = pdo_insertid();
        } else {
            pdo_update('ewei_shop_diyform_temp', $insert, array('id' => $goods_temp['id']));
            $gdid = $goods_temp['id'];
        }

        if (!empty($cartid)) {
            $cart_data = array(
                'diyformid' => $insert['diyformid'],
                'diyformfields' => $insert['diyformfields'],
                'diyformdata' => $insert['diyformdata']
            );
            pdo_update('ewei_shop_member_cart', $cart_data, array('id' => $cartid));
        }
        show_json(1, array('goods_data_id' => $gdid));
    }

    function getcardprice()
    {
        global $_GPC;
        $card_id = intval($_GPC['card_id']);
        $card_price = intval($_GPC['card_price']);
        $goodsarr = $_GPC['goods'];
        $goodsprice = $_GPC['goodsprice'];//没有折扣前的商品总价
        $discountprice = $_GPC['discountprice'];//会员优惠
        $isdiscountprice = $_GPC['isdiscountprice'];//促销优惠
        $dispatch_price = $_GPC['dispatch_price'];
        $lotterydiscountprice = $_GPC['lotterydiscountprice'];//游戏营销
        $taskcut = $_GPC['taskcut'];//任务中心
        $liveid = intval($_GPC['liveid']);
        if ($liveid > 0) {
            $live_product = pdo_fetch("SELECT *  FROM " . tablename('ewei_shop_goods') . " WHERE id = '{$goodsarr[0]['goodsid']}'");
            if ($live_product) {
                $goodsprice = $live_product['marketprice'] * $goodsarr[0]['total'];
            }
        }

        $result = $this->caculatecard($card_id, $dispatch_price, $lotterydiscountprice, $taskcut, $goodsarr, $goodsprice, $discountprice, $isdiscountprice, $liveid, 0, $_GPC['goods_dispatch']);

        if (empty($result)) {
            show_json(0);
        } else {
            show_json(1, $result);
        }
    }

    function caculatecard($card_id, $dispatch_price, $lotterydiscountprice, $taskcut, $goodsarr, $totalprice, $discountprice, $isdiscountprice, $liveid, $isSubmit = 0, $goods_dispatch = array())
    {
        global $_W;
        $open_redis = function_exists('redis') && !is_error(redis());
        $openid = $_W['openid'];
        $member = m('member')->getMember($openid, true);
        $openid = $_W['openid'];
        $uniacid = $_W['uniacid'];
        if (empty($goodsarr)) {
            return false;
        }
        if (p('membercard')) {
            //有效期之内的会员卡
            $data = p('membercard')->getMemberCard($card_id);
        }
        if (empty($data)) {
            return null;
        }
        if (is_array($goodsarr)) {
            $goods = array();
            $cardmodel = $data['cardmodel'];
            $allowgoodslist = explode(',', $data['goodsids']);
            $member_discount = intval($data['member_discount']);
            $discount_rate = floatval($data['discount_rate']);

            $gprice = 0;
            $cardgoodprice = 0;
            $newtotalprice = 0;
            $deductprice = 0;
            $discountprice = 0;
            $isdiscountprice = 0;
            //积分抵扣的
            $deductprice1 = 0;
            //余额抵扣的钱
            $deductcredit2 = 0;
            //余额抵扣限额
            $deductprice2 = 0;
            foreach ($goodsarr as $k => &$g) {
                //如果包邮
                if (isset($data['shipping']) && $data['shipping'] == 1 && ($cardmodel == 1 || ($cardmodel == 2 && in_array($g['goodsid'], $allowgoodslist)))) {
                    $dispatch_price -= $goods_dispatch[$g['goodsid']];
                }
                //排除赠品
                if ($g['status'] != 2) {
                    if ($liveid > 0) {
                        $live_product = pdo_fetch("SELECT *  FROM " . tablename('ewei_shop_goods') . " WHERE id = '{$g['goodsid']}'");
                        if ($live_product) {
                            $g['marketprice'] = $live_product['marketprice'];
                        }
                    }
                    $gprice = (float)$g['marketprice'] * (float)$g['total'];//商品价格
                    $newtotalprice = +$gprice;
                    //会员优惠
                    if ($g['discounttype'] == 2) {
                        if ($data['discount'] > 0) {
                            if ($discount_rate > 0 && ($cardmodel == 1 || ($cardmodel == 2 && in_array($g['goodsid'], $allowgoodslist)))) { //折扣大于0
                                $cardgoodprice += ($gprice - (float)$g['discountunitprice'] * (float)$g['total']) * ($discount_rate / 10);//会员优惠之后的会员卡价格
                                $goodprice = $gprice - (float)$g['discountunitprice'] * (float)$g['total'];
                                $deductprice += $goodprice * (1 - $discount_rate / 10);//会员卡折扣的价格
                                $discountprice += (float)$g['discountunitprice'] * (float)$g['total'];//会员优惠价格
                            } else {
                                $cardgoodprice += $gprice;
                                $discountprice += (float)$g['discountunitprice'] * (float)$g['total'];
//                                $discountprice =0;
                            }
                        } else {
                            //$discountprice -= (float)$g['discountunitprice'] * (float)$g['total'];//会员优惠为0
                            $discountprice = 0;//禁止折上折会员优惠为0
                            if ($discount_rate > 0) {
                                $deductprice += $gprice * (1 - $discount_rate / 10);//售价*折扣
                                $cardgoodprice += $gprice * ($discount_rate / 10);//直接总价*折扣
                            } else {
                                $cardgoodprice += $gprice;
                            }

                        }

                    } elseif ($g['discounttype'] == 1) {

                        if ($data['discount'] > 0 && ($cardmodel == 1 || ($cardmodel == 2 && in_array($g['goodsid'], $allowgoodslist)))) {

                            if ($discount_rate > 0) {
                                $cardgoodprice += ($gprice - (float)$g['isdiscountunitprice'] * (float)$g['total']) * ($discount_rate / 10);//促销优惠之后的价格
                                $goodprice = $gprice - (float)$g['isdiscountunitprice'] * (float)$g['total'];
                                $deductprice += $goodprice * (1 - $discount_rate / 10);//促销价*会员卡折扣
                                $isdiscountprice += $g['isdiscountunitprice'] * (float)$g['total'];//显示的促销价
                            } else {
                                $cardgoodprice += $gprice;
                                $isdiscountprice = 0;//促销优惠为0
                            }

                        } else {

                            //新逻辑
                            // $isdiscountprice -= (float)$g['isdiscountunitprice'] * (float)$g['total'];//促销优惠为0
                            $isdiscountprice = 0;//促销优惠为0
                            if ($discount_rate > 0) {
                                $deductprice += $gprice * (1 - $discount_rate / 10);//售价*折扣
                                $cardgoodprice += $gprice * ($discount_rate / 10);//直接总价*折扣
                            } else {
                                $cardgoodprice += $gprice;
                            }

                        }


                    } else if ($g['discounttype'] == 0) {

                        if ($g['isnodiscount'] == 1) {   //不参与会员折扣
                            if ($data['discount'] && ($cardmodel == 1 || ($cardmodel == 2 && in_array($g['goodsid'], $allowgoodslist)))) {
                                if ($discount_rate > 0) {
                                    $goodprice = $gprice;
                                    $deductprice += $goodprice * (1 - $discount_rate / 10);
                                    $cardgoodprice += $gprice * ($discount_rate / 10);

                                } else {
                                    $cardgoodprice += $gprice;
                                }
                            } else {
                                if ($discount_rate > 0) {
                                    $deductprice += $gprice * (1 - $discount_rate / 10);//售价*折扣
                                    $cardgoodprice += $gprice * ($discount_rate / 10);
                                } else {
                                    $cardgoodprice += $gprice;
                                }

                            }

                        } else {

                            if ($data['discount']) {
                                if ($discount_rate > 0 && ($cardmodel == 1 || ($cardmodel == 2 && in_array($g['goodsid'], $allowgoodslist)))) {
                                    $goodprice = $gprice;
                                    $deductprice += $goodprice * (1 - $discount_rate / 10);
                                    $cardgoodprice += $gprice * ($discount_rate / 10);
                                } else {
                                    $cardgoodprice += $gprice;
                                }
                            } else {
                                if ($discount_rate > 0 && ($cardmodel == 1 || ($cardmodel == 2 && in_array($g['goodsid'], $allowgoodslist)))) {
                                    $deductprice += $gprice * (1 - $discount_rate / 10);
                                    $cardgoodprice += $gprice * ($discount_rate / 10);
                                } else {
                                    $cardgoodprice += $gprice;
                                }

                            }
                        }

                    }

                }

                if ($open_redis) {
                    if ($g['deduct'] > $g['ggprice']) {
                        $g['deduct'] = $g['ggprice'];
                    }

                    //积分抵扣最多抵扣额
                    if ($cardgoodprice > 0 && $cardgoodprice < $g['deduct']) {
                        $g['deduct'] = $cardgoodprice;
                    }
                    //积分抵扣
                    if ($g['manydeduct']) {
                        $deductprice1 += $g['deduct'] * $g['total'];
                    } else {
                        $deductprice1 += $g['deduct'];
                    }

                    //余额抵扣限额
                    if ($g['deduct2'] == 0) {
                        //全额抵扣
                        // $deductprice2 += $g['ggprice'];
                        $deductprice2 += $cardgoodprice;
                    } else if ($g['deduct2'] > 0) {

                        //最多抵扣
                        if ($g['deduct2'] > $cardgoodprice) {
                            $deductprice2 += $cardgoodprice;
                        } else {
                            $deductprice2 += $g['deduct2'];
                        }
                    }
                }
            }
            if ($dispatch_price < 0) {
                $dispatch_price = 0;
            }

            $carddeduct_text = '';
            if ($data['discount_rate']) {

                // $carddeduct_text = '会员卡折扣(' . $data['discount_rate'] . '折)';
                $carddeduct_text = '会员卡优惠';
            }
        }

//满额减
        $sale_plugin = com('sale');
        $saleset = false;
        if ($sale_plugin) {
            $saleset = $_W['shopset']['sale'];
            $saleset['enoughs'] = $sale_plugin->getEnoughs();
        }

        /*if ($saleset) {
            //满额减
            foreach ($saleset['enoughs'] as $e) {
                if ($totalprice - $discountprice-$isdiscountprice-$deductprice >= floatval($e['enough']) && floatval($e['money']) > 0) {
                    $deductenough_money = floatval($e['money']);
                    $deductenough_enough = floatval($e['enough']);
                    $deductprice2-=$deductenough_money;//去掉满额减
                    break;
                }
            }
        }*/

        //任务中心优惠
        $taskreward = $_SESSION['taskcut'];
        if ($taskreward && p('task')) {
            $taskcut = 0;
        }

        $totalprice = $totalprice - $deductprice;

        $deductcredit = 0; //抵扣需要扣除的积分
        $deductmoney = 0; //抵扣的钱


        if (!empty($saleset)) {
            //积分抵扣
            $credit = $member['credit1'];
            if ($credit > 0) {
                $credit = floor($credit);
            }
            if (!empty($saleset['creditdeduct'])) {
                $pcredit = intval($saleset['credit']); //积分比例
                $pmoney = round(floatval($saleset['money']), 2); //抵扣比例


                if ($pcredit > 0 && $pmoney > 0) {
                    if ($credit % $pcredit == 0) {
                        $deductmoney = round(intval($credit / $pcredit) * $pmoney, 2);
                    } else {
                        $deductmoney = round((intval($credit / $pcredit) + 1) * $pmoney, 2);
                    }
                }
                if ($deductmoney > $deductprice1) {
                    $deductmoney = $deductprice1;
                }
                //todo sunchao 这里是减去会员等级优惠的钱 老子注释的
//                if ($deductmoney > $totalprice-$isdiscountprice-$discountprice-$deductenough_money) {
//                    $deductmoney = $totalprice-$isdiscountprice-$discountprice-$deductenough_money;
                if ($deductmoney > $totalprice - $isdiscountprice - $deductenough_money) {
                    $deductmoney = $totalprice - $isdiscountprice - $deductenough_money;
                }
                $deductcredit = ceil($pmoney * $pcredit == 0 ? 0 : $deductmoney / $pmoney * $pcredit);
            }
            //余额抵扣
            if (!empty($saleset['moneydeduct'])) {
                $deductcredit2 = $member['credit2'];

                if ($deductcredit2 > $totalprice - $isdiscountprice - $deductenough_money + $dispatch_price) {
                    $deductcredit2 = $totalprice - $isdiscountprice - $deductenough_money + $dispatch_price;
                }
                if ($deductcredit2 > $deductprice2) {
                    if ($g['manydeduct2'] == 1) {
                        $deductcredit2 = $deductprice2 * $g['total'];
                    } else {
                        $deductcredit2 = $deductprice2;
                    }
                }
                //余额抵扣加上运费
                if (empty($saleset['dispatchnodeduct'])) {
                    $deductcredit2 += $dispatch_price;
                }

            }
            if ($saleset) {
                //满额减
                foreach ($saleset['enoughs'] as $e) {
                    if ($totalprice - $discountprice - $isdiscountprice - $deductprice >= floatval($e['enough']) && floatval($e['money']) > 0) {
                        $deductenough_money = floatval($e['money']);
                        $deductenough_enough = floatval($e['enough']);
                        $deductprice2 -= $deductenough_money;//去掉满额减
                        break;
                    }
                }
            }

        }

        $return_array = array();
        //会员卡减掉的价格-会员卡优惠
        $return_array['carddeductprice'] = (float)$deductprice;
        //参与会员卡优惠的商品总价
        $return_array['cardgoodprice'] = (float)$cardgoodprice;
        //会员卡标题
        $return_array['carddeduct_text'] = $carddeduct_text;
        //根据会员卡规则计算后的商品总价
        $return_array['totalprice'] = (float)$totalprice;
        //根据会员卡规则计算后的运费
        $return_array['dispatch_price'] = (float)$dispatch_price;
        //返回会员卡名称
        $return_array['cardname'] = $data['name'];
        //返回任务中心优惠
        $return_array['taskcut'] = (float)$taskcut;
        //游戏营销
        //  $return_array['lotterydiscountprice'] = (float)$lotterydiscountprice;
        $return_array['lotterydiscountprice'] = 0;
        //会员优惠
        $return_array['discountprice'] = (float)$discountprice;
        //促销优惠
        $return_array['isdiscountprice'] = (float)$isdiscountprice;
        $return_array['live_id'] = $liveid;
        $return_array['shipping'] = $data['shipping'];
        $return_array['goodsprice'] = (float)$newtotalprice;
        //返回满额减信息
        $return_array['deductenough_money'] = (float)$deductenough_money;
        $return_array['deductenough_enough'] = (float)$deductenough_enough;
        $return_array['deductcredit2'] = number_format($deductcredit2, 2);
        $return_array['deductcredit'] = $deductcredit;
        $return_array['deductmoney'] = $deductmoney;
        //商品信息更新
        $return_array['$goodsarr'] = $goodsarr;
        return $return_array;
    }

    protected function getdefaultMembercardId($goods = array())
    {
        global $_W;
        if (p('membercard')) {
            if (!empty($goods)) {
                $goodsids_arr = array_column($goods, 'goodsid');
                $mycard = p('membercard')->get_Mycard($_W['openid'], 1, 100, $goodsids_arr);
            } else {
                $mycard = p('membercard')->get_Mycard($_W['openid'], 1, 100);
            }

            if ($mycard['list']) {
                $all_mycardlist = $mycard['list'];
                $card_info['all_mycardlist'] = $all_mycardlist;

                $availablecard_count = $mycard['total'];
                $c_discount = array();
                $a_discount = array();
                foreach ($all_mycardlist as $ckey => $cvalue) {
                    //跳过没有会员折扣的
                    if (empty($cvalue['member_discount'])) continue;
                    $c_discount[$cvalue['id']] = (string)$cvalue['discount_rate'];
                }
                foreach ($all_mycardlist as $akey => $avalue) {
                    //跳过没有会员折扣并且禁用折上折的
                    if (empty($avalue['member_discount']) || $avalue['discount'] == 0) continue;
                    $a_discount[$avalue['id']] = (string)$avalue['discount_rate'];
                }
                //查找出折扣力度最大的会员卡作为默认选中的会员卡
                $max_discount_cardid = 0;
                if (!empty($a_discount)) {
                    $max_discount = min($a_discount);
                    $ex_discount = @array_flip($a_discount);
                    $max_discount_cardid = $ex_discount[$max_discount];
                } else if (!empty($c_discount)) {
                    $max_discount = min($c_discount);
                    $ex_discount = @array_flip($c_discount);
                    $max_discount_cardid = $ex_discount[$max_discount];
                }
                $default_cardid = empty($max_discount_cardid) ? $all_mycardlist[0]['id'] : $max_discount_cardid;
                return $default_cardid;
            } else {
                return false;
            }
        } else {
            return false;
        }


    }

    /**
     * 是否为全返订单
     * @param int $goodsid
     * @param int $optionid
     * author 德昂
     * @return int
     */
    function isfullbackgoods($goodsid = 0, $optionid = 0)
    {
        global $_W;
        $fullbackid = 0;
        if ($optionid > 0) {
            $fullback_goods = pdo_fetch('select id from' . tablename('ewei_shop_fullback_goods') . "where goodsid=:goodsid and hasoption =1 and status=1 and uniacid=:uniacid and find_in_set('{$optionid}',optionid)", array(':uniacid' => $_W['uniacid'], ':goodsid' => $goodsid));
        } else {
            $fullback_goods = pdo_get('ewei_shop_fullback_goods', array('status' => 1, 'goodsid' => $goodsid, 'uniacid' => $_W['uniacid']), 'id');
        }
        if ($fullback_goods) {
            $fullbackid = $fullback_goods['id'];
        }
        return $fullbackid;
    }


}