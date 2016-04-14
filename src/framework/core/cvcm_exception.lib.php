<?php
/////
// cvcm异常和错误处理驱动
// 驱动由两部分构成
// 1.逻辑层
//      逻辑层包含cvcm异常类、cvcm错误类、默认异常处理函数、默认错误处理函数
//      以及两个默认函数的注册
// 2.表现层
//      表现层包含包装在cvcm错误类的show方法中，作用是把逻辑层提供的信息展示给用户
//
// 用法：尽可能的优先包含这个文件。
//
///
/////
// __cvcm_exception_handler
// 默认异常处理函数
// @param Exception $e
///
function __cvcm_exception_handler(Exception $e) {
	throw new cvcmError($e->getMessage(),$e->getCode(),$e->getFile(),$e->getLine());
}
/////
// __cvcm_error_handler
// 默认错误处理函数
// @param Exception $e
///
function __cvcm_error_handler($errorCode,$errorMessage,$errorFile,$errorLine) {
	throw new cvcmError($errorMessage,$errorCode,$errorFile,$errorLine);
}
//注册默认异常处理函数
set_exception_handler('__cvcm_exception_handler');
//注册默认错误处理函数
set_error_handler('__cvcm_error_handler');
/////
// cvcmException.class
// cvcm异常类
///
class cvcmException extends Exception{
	public function __construct($errorMessage, $errorCode=0, $previous= NULL ) {
		parent::__construct($errorMessage,  $errorCode, $previous);
		throw new cvcmError($errorMessage, $errorCode, parent::getFile(), parent::getLine());
	}
}
/////
// cvcmError.class
// cvcm错误类
///
class cvcmError extends ErrorException{
	private static $errorTitle='cvcm php 提示您，您的代码出错啦！';
	private static $errorMessage='';
	private static $backtrace='';//调用信息
	private static $gotoPath='/';
	private static $gotoFile='/index.php';
	private static $errorFile='';
	private static $errorLine=0;
	private static $errorCode='';
	private static $log_on=false;
	private static $log_path='';
	/////
	// 构造函数
	// @param string $errorMessage 提示信息
	// @param int $errorCode 提示代号
	// @param string $errorFile 出错的文件名
	// @param int $errorLine 出错的行号
	///
	public function __construct($errorMessage='',$errorCode=0,$errorFile='',$errorLine=0) {
		parent::__construct($errorMessage,$errorCode);
		$backtrace=debug_backtrace();
		$outBacktrace=array();
		foreach ($backtrace as $value){
			$lines=file($value['file']);
			$outBacktrace[]=array('file'=>$value['file'],'line'=>$value['line'],'code'=>trim
					($lines[$value['line']-1]));
		}
		self::$backtrace='<br/><pre>'.htmlspecialchars(print_r($outBacktrace, true),ENT_QUOTES).'</pre>';
		self::$errorMessage=$errorMessage;
		self::$errorFile=$errorFile==''?parent::getFile():$errorFile;
		self::$errorLine=$errorLine==0?parent::getLine():$errorLine;
		self::$errorCode=$errorCode;
		if (self::$log_on) self::write ();
		self::showError();
	}
	//获取ip地址，记录出错信息的时候，记录下ip信息
	private static function getIp(){
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
			$ip = getenv("HTTP_CLIENT_IP");
		else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
			$ip = getenv("REMOTE_ADDR");
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
			$ip = $_SERVER['REMOTE_ADDR'];
		else
			$ip = "unknown";
		return($ip);
	}
	//记录错误信息
	private static function write()
	{
		error_reporting(0);
		$test=FALSE;
		$log_path=self::$log_path;
		if($log_path=='') return self::$errorMessage=self::$errorMessage.'<br/>日志文件记录失败！';
		//检查日志记录目录是否存在
		if(!is_dir($log_path)) if(!mkdir($log_path,0755))return self::$errorMessage=self::$errorMessage.'<br/>日志文件记录失败！';//创建日志记录目录
		if(substr($log_path, -1) != '/') $log_path.= '/';
		$time=date('Y-m-d H:i:s');
		$ip=self::getIp();
		$destination =$log_path .date("Y-m-d").".log";
		//写入文件，记录错误信息
		if(!error_log($time.'|'.$ip.'|'.$_SERVER['PHP_SELF'].'|'.self::$errorMessage."\r\n".self::$backtrace."\r\n", 3,$destination))
			return self::$errorMessage=self::$errorMessage.'<br/>日志文件记录失败！';
		exit;
	}
	/////
	// 设置日志开关
	// @param bool $log_on
	// @return bool
	///
	public static function setLog_on($log_on) {
		return self::$log_on=$log_on;
	}
	/////
	// 设置日志文件及路径
	// @param string $log_file
	// @return string
	///
	public static function setLog_path($log_file) {
		return self::$log_path=$log_file;
	}
	/////
	// 设置出错后提示浏览者跳转到的目录
	// @param string $gotoPath
	// @return string
	///
	public static function setGotoPath($gotoPath) {
		return self::$gotoPath=$gotoPath;
	}
	/////
	// 设置出错后提示浏览者跳转到的文件
	// @param string $gotoFile
	// @return string
	///
	public static function setGotoPage($gotoFile) {
		return self::$gotoFile=$gotoFile;
	}
	/////
	// 输出错误提示
	///
	private static function showError() {
		//清除之前输出的内容
		ob_clean();
		$gotoPage=preg_replace('/\/+/', '/', self::$gotoPath.self::$gotoFile);
		//应用错误提示视图
		echo '
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>系统错误提示!</title>
				</head>
				<body>
				<div style="border:1px solid #9CF; margin:20px auto; width:800px;">
				<div style="border:1px solid #fff; padding:15px; background:#f0f6f9;">
				<div style="border-bottom:1px #9CC solid; font-size:26px;font-family:\'Microsoft Yahei\', Verdana, arial, sans-serif; line-height:40px; height:40px; font-weight:bold">
				'.self::$errorTitle.'
				</div>
				<div style="border:1px dotted #F90; border-left:6px solid #F60; padding:15px; background:#FFC">
				错误代码是：'.self::$errorCode.'
				</div>
				<div style="border:1px dotted #F90; border-left:6px solid #F60; padding:15px; background:#FFC">
				错误消息是：'.self::$errorMessage.'
				</div>
				<div style="border:1px dotted #F90; border-left:6px solid #F60; padding:15px; background:#FFC">
				出错的文件是：'.self::$errorFile.'
				</div>
				<div style="border:1px dotted #F90; border-left:6px solid #F60; padding:15px; background:#FFC">
				错误行是：'.self::$errorLine.'
				</div>
				<div style="border:1px dotted #F90; border-left:6px solid #F60; padding:15px; background:#FFC">
				调用信息如下：'.self::$backtrace.'
				</div>
				<br/>
				<div style="height:20px;">
				您可以选择 &nbsp;&nbsp;跳转到<a href="'.$gotoPage.'">'.$gotoPage.'</a>
				</div>
				<div style=" font-size:15px;">
				您也可以选择 &nbsp;&nbsp;
				<a href="'.$_SERVER['PHP_SELF'].'" title="重试">重试</a>
				&nbsp;&nbsp;
				<a href="javascript:history.back()" title="返回">返回</a>
				或者  &nbsp;&nbsp;
				<a href="/index.php" title="回到首页">回到首页</a>
				</div>
				</div>
				</div>
				<div style="font-size:12px; color:#666; text-align:center; line-height:24px; height:24px;">
				<a href="http://www.cvcmphp.com" title="CVCMPHP">CVCMPHP1.0正式版</a>
				</div>
				</body>
				</html>
				';
				//退出程序
				exit;
	}
}
?>