<?php
//dezend by http://www.yunlu99.com/
if (!defined('ES_PATH')) {
	exit('Access Denied');
}

class EmptyController extends Controller
{
	public function index()
	{
		global $controller;
		trigger_error(' Controller <b>' . $controller . '</b> Not Found !');
	}
}

?>
