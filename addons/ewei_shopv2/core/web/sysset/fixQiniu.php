<?php

{
	exit( "Access Denied" );
}
class FixQiniu_EweiShopV2Page extends WebPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$_W['ispost'] && $this->handler();
		include $this->template();
	}
	protected function handler() 
	{
		global $_W;
		global $_GPC;
		$originDomain = $_GPC["originDomain"];
		$newDomain = $_GPC["newDomain"];
		if( empty($originDomain) || empty($newDomain) ) 
		{
			show_json(0, "原始域名和现在域名不能为空");
		}
		$originDomain = trim($originDomain, "/");
		$newDomain = trim($newDomain, "/");
		if( strpos($originDomain, "http") === false && strpos($originDomain, "https") === false ) 
		{
			show_json(0, "原域名请带http或https协议头");
		}
		if( strpos($newDomain, "http") === false && strpos($newDomain, "https") === false ) 
		{
			show_json(0, "新域名请带http或https协议头");
		}
		$goodsTable = tablename("ewei_shop_goods");
		$categoryTable = tablename("ewei_shop_category");
		$questionTable = tablename("ewei_shop_qa_question");
		try 
		{
			$this->createTableCopyIfNotExists($goodsTable, time());
			$this->createTableCopyIfNotExists($categoryTable, time());
			$this->createTableCopyIfNotExists($questionTable, time());
		}
		catch( Exception $exception ) 
		{
			show_json(0, $exception->getMessage());
		}
		$updateGoodsTableSQL = "            update " . $goodsTable . " \r\n            set \r\n              `thumb` = replace(`thumb`, '" . $originDomain . "', '" . $newDomain . "'),\r\n              `content` = replace(`content`, '" . $originDomain . "', '" . $newDomain . "'),\r\n              `thumb_url` = replace(`thumb_url`, '" . $originDomain . "', '" . $newDomain . "')\r\n            where \r\n              `uniacid` = " . $_W["uniacid"];
		$updateCategorySQL = "            update " . $categoryTable . " \r\n            set \r\n              `thumb` = replace(`thumb`, '" . $originDomain . "', '" . $newDomain . "')\r\n            where \r\n              `uniacid` = " . $_W["uniacid"];
		$updateQuestionSQL = "            update " . $questionTable . " \r\n            set \r\n              `content` = replace(`content`, '" . $originDomain . "', '" . $newDomain . "')\r\n            where \r\n              `uniacid` = " . $_W["uniacid"];
		pdo_run($updateGoodsTableSQL);
		pdo_run($updateCategorySQL);
		pdo_run($updateQuestionSQL);
		$account = pdo_fetchcolumn("select name from " . tablename("account_wechats") . " where uniacid = " . $_W["uniacid"]);
		$logInfo = array( "sql" => "\nupdateGoods:\n" . $updateGoodsTableSQL . PHP_EOL . "updateCategory:\n" . $updateCategorySQL . "\nupdateQuestion:\n" . $updateQuestionSQL, "originDomain" => $originDomain, "newDomain" => $newDomain, "uniacid" => $_W["uniacid"], "account" => $account );
		$this->log($logInfo);
		show_json(1, "修复成功");
	}
	protected function createTableCopyIfNotExists($tableName, $hash) 
	{
		$tableCopyName = trim($tableName, "`");
		$tableCopyPrefixName = $tableCopyName . "_copy_";
		$tableCopyName .= "_copy_" . $hash;
		$tableCopyExists = pdo_fetch("show tables like '" . $tableCopyPrefixName . "%'");
		if( !$tableCopyExists ) 
		{
			pdo_run("create table " . $tableCopyName . " select * from " . $tableName . " where 1");
			$tableCopyExists = pdo_fetch("show tables like '" . $tableCopyPrefixName . "%'");
		}
		if( !$tableCopyExists ) 
		{
			throw new Exception("备份失败,请检查当前数据库用户是否有创建表权限!");
		}
		return true;
	}
	protected function log($log, $append = true) 
	{
		$logPath = IA_ROOT . "/addons/ewei_shopv2/data/backup";
		if( !is_dir($logPath) ) 
		{
			mkdir($logPath);
		}
		$currentDay = date("Ymd", time());
		$logFile = $logPath . "/fixQiniu" . $currentDay . ".log";
		$logTime = date("Y-m-d H:i:s", time());
		$logFormat = "[时间]" . $logTime . ",\r\n[sql]" . $log["sql"] . ",\r\n[修复前域名]" . $log["originDomain"] . "\r\n[修复后域名]" . $log["newDomain"] . "\r\n[公众号ID]" . $log["uniacid"] . "\r\n[公众号名称]" . $log["account"];
		$logFormatContent = $logFormat . PHP_EOL . "-----------------------------------------------\n";
		$append = ($append == true ? FILE_APPEND : 0);
		file_put_contents($logFile, $logFormatContent, $append);
	}
}
?>