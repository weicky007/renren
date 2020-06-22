<?php
/*WEMECMS  http://shop258163088.taobao.com*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}

define('IA_ROOT', str_replace('\\', '/', dirname(dirname(__FILE__))));
require_once IA_ROOT . '/framework/library/phpexcel/PHPExcel/Reader/CSV.php';
require EWEI_SHOPV2_PLUGIN . 'merch/core/inc/page_merch.php';

class Pullin_EweiShopV2Page extends MerchWebPage
{
    public function main()
    {
        global $_W;
        global $_GPC;
        $uploadStart = '0';
        $uploadnum = '0';


        if ($_W['ispost']) {
            $this->model=p('leading');
            $fleds=['title'=>'商品标题',
                'shorttitle'=>'商品短标题',
                'thumb'=>'缩略图片',
                'thumb_url'=>'图片集',
                'unit'=>'单位',
                'dispatchprice'=>'运费',
                'goodssn'=>'商品编码',
                'specs'=>'商品规格',
                'options'=>'规格选项',
                'total'=>'商品数量',
                'productprice'=>'产品价格',
                'marketprice'=>'市场价格',
                'content'=>'内容详情',
                'status'=>'上架'
                ];
            $rows = m('excel')->import('excelfile',$fleds);

            $num = count($rows);
           // unset($rows[0]);


        //    $this->get_zip_originalsize($_FILES['zipfile']['tmp_name'], '../attachment/images/' . $_W['uniacid'] . '/' . date('Y') . '/' . date('m') . '/');
            $num = 0;

            foreach ($rows as  &$item) {
                $item['thumb_url']=explode('★',$item['thumb_url']);
                $item['options']=json_decode($item['options'],true);
                $item['specs']=json_decode($item['specs'],true);

                ++$num;
            }
            unset($item);

            session_start();
            $_SESSION['lanww_shopgoods'] = $rows;
            m('cache')->set('lanww_shopgoods', $rows, $_W['uniacid']);
            $uploadStart = '1';
            $uploadnum = $num;
        }

        include $this->template();
    }

    public function fetch()
    {
        global $_GPC;
        global $_W;
        set_time_limit(0);
        $num = intval($_GPC['num']);
        $totalnum = intval($_GPC['totalnum']);
        session_start();
        $items = $_SESSION['lanww_shopgoods'];

        if (empty($items)) {
            $items = m('cache')->get('lanww_shopgoods', $_W['uniacid']);
        }

        $ret = p('leading')->save_goods($items[$num],1,$_W['merchid']);


        if ($totalnum <= $num + 1) {
            unset($_SESSION['lanww_shopgoods']);
        }

        exit(json_encode($ret));
    }

    public function get_zip_originalsize($filename, $path)
    {
        if (!file_exists($filename)) {
            exit('文件 ' . $filename . ' 不存在！');
        }

        $filename = iconv('utf-8', 'gb2312', $filename);
        $path = iconv('utf-8', 'gb2312', $path);
        $resource = zip_open($filename);
        $i = 1;

        while ($dir_resource = zip_read($resource)) {
            if (zip_entry_open($resource, $dir_resource)) {
                $file_name = $path . zip_entry_name($dir_resource);
                $file_path = substr($file_name, 0, strrpos($file_name, '/'));

                if (!is_dir($file_path)) {
                    mkdir($file_path, 511, true);
                }

                if (!is_dir($file_name)) {
                    $file_size = zip_entry_filesize($dir_resource);

                    if ($file_size < 1024 * 1024 * 10) {
                        $file_content = zip_entry_read($dir_resource, $file_size);
                        $ext = strrchr($file_name, '.');

                        if ($ext == '.png') {
                            file_put_contents($file_name, $file_content);
                        }
                        else {
                            if ($ext == '.tbi') {
                                $file_name = substr($file_name, 0, strlen($file_name) - 4);
                                file_put_contents($file_name . '.png', $file_content);
                            }
                        }
                    }
                }

                zip_entry_close($dir_resource);
            }
        }

        zip_close($resource);
    }
}

?>
