<?php

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class PluginMobilePage extends MobilePage
{

 
    protected $twig;
    const DEFAULT_TEMPLATE_SUFFIX = '.twig';

    public $model;
    public $set;

    public function __construct()
    {

        parent::__construct();
        $this->model = m('plugin')->loadModel($GLOBALS["_W"]['plugin']);
        $this->set = $this->model->getSet();
    }

    public function getSet()
    {
        return $this->set;
    }

    public function qr()
    {
        global $_W, $_GPC;
        $url = trim($_GPC['url']);
        require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
        QRcode::png($url, false, QR_ECLEVEL_L, 16, 1);
    }

 
    protected function resolveTemplatePath($template)
    {
        $template = trim($template);
        $replaceTemplate = str_replace(array('.', '/'), '/', $template);
        $params = explode('/', $replaceTemplate);
        $lastElement = array_pop($params);
        $templateFile = $lastElement . static::DEFAULT_TEMPLATE_SUFFIX;
        array_push($params, $templateFile);
        $relativePath = implode('/', $params);

        return $relativePath;
    }


    protected function view($template, $params = array())
    {
        global $_GPC;
        $templateFilePath = $this->resolveTemplatePath($template);
        $routeParams = isset($_GPC['r']) ? $_GPC['r'] : null;
        $routeParams = explode('.', $routeParams);
        $plugin = current($routeParams);
        $pluginTemplatePath = EWEI_SHOPV2_PLUGIN . "{$plugin}" . "/template/mobile/default/";


        if ($plugin == 'pc') {
            $loader = new FilesystemLoader($pluginTemplatePath);
            $this->twig = new Environment($loader, array(
                'debug' => true
            ));

            $this->addFunction();
            $this->addGlobal();
            $this->addFilter();
        }
        $defaultParams = array(
            'basePath' => EWEI_SHOPV2_LOCAL . "plugin/{$plugin}/static",
            'staticPath' => EWEI_SHOPV2_LOCAL . "static/",
            'appJsPath' => EWEI_SHOPV2_LOCAL . "static/js/app",
            'title' => '人人商城',
        );

        if (empty($params)) {
            $params = array();
        }

        $params = array_merge($defaultParams, $params);
        $templateFileRealPath = $pluginTemplatePath . $templateFilePath;
        if (!file_exists($templateFileRealPath)) {
            die("模板文件 {$templateFileRealPath} 不存在");
        }

        return $this->twig->render($templateFilePath, $params);
    }


    private function addFunction()
    {
        $extendFunctions = array(
            'tomedia' => function ($src) {
                return tomedia($src);
            },
            'pcUrl' => function ($do = '', $query = [], $full = false) {
                global $_W, $_GPC;
                $result = m('common')->getPluginSet('pc');
                if (strpos($do, 'pc') === false) {
                    $do = 'pc.' . $do;
                }
                if (isset($result['domain']) && mb_strlen($result['domain'])) {
                    return ($full === true ? $_W['siteroot'] : './') . (empty($do) ? '' : ('?r=' . $do . '&')) . http_build_query($query);
                } else {
                    return mobileUrl($do, $query, $full);
                }
            },
            'time' => function ($format = null) {
                if (!empty($format)) {
                    return date($format, time());
                }
                return time();
            },
            'ispc' => function () {
                $result = m('common')->getPluginSet('pc');
                if (mb_strlen($result['domain']) > 0) {
                    return true;
                }
                return false;
            },
            'count' => function ($array = array(), $model = COUNT_NORMAL) {
                return count($array, $model);
            },
            'dump' => function ($params) {
                return print_r($params);
            },
            'checkLogin' => function () {
                return $this->model->checkLogin();
            }
        );


        foreach ($extendFunctions as $functionName => $callback) {
            $function = new Twig_SimpleFunction($functionName, $callback);
            $this->twig->addFunction($function);
        }
    }


    protected function addGlobal()
    {
        global $_W, $_GPC;

        $params = array(
            'global' => p('pc')->getTemplateGlobalVariables(),
            'v' => str_replace('.', '', microtime(true)),
            'params' => json_encode($_GPC),
            'api' => json_encode(array(
                'addShopCart' => pcUrl('goods.addShopCart', array(), true),
                'commentList' => pcUrl('goods.comment_list', array(), true),
                'comments' => pcUrl('goods.comments', array(), true),
                'calcSpecGoodsPrice' => pcUrl('goods.calcSpecGoodsPrice', array(), true),
                'imageUpload' => pcUrl('foundation.imageUpload', array(), true)

            ), JSON_UNESCAPED_UNICODE),
        );

        foreach ($params as $key => $value) {
            $this->twig->addGlobal($key, $value);
        }
    }


    protected function addFilter()
    {
        
        $extendFilters = array(
            'float' => function ($number) {
                return (float)$number;
            },
            'bool' => function ($params) {
                return (bool)$params;
            },
            'format' => function ($string) {
                $output = $string;
                if (mb_strlen($output) > 8) {
                    $output = mb_substr($output, 0, 8, 'utf-8');
                }

                return $output;
            }
        );

        foreach ($extendFilters as $filterName => $extendFilter) {
            $filter = new Twig_SimpleFilter($filterName, $extendFilter);
            $this->twig->addFilter($filter);
        }
    }


}
