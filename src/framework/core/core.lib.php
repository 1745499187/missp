<?php
if(! defined('FWK_ROOT')) {
	define('FWK_ROOT', __DIR__.'/..');
}

if(! defined('APP_ROOT')) {
	exit('Missing APP_ROOT.');
}

define('INSIDE', true);

// require_once __DIR__.'/cvcm_exception.lib.php';
require_once FWK_ROOT.'/core/util.lib.php';
require_once FWK_ROOT.'/core/db.lib.php';
require_once FWK_ROOT.'/core/application.lib.php';

class CCore{
	private static $_instance = null;

	// system configs, dont modify them!
	private $_config = array();
	private $_user = array();
	
	private $isTestMode = false;

	private static function &_instance(){
		if(!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	//在构造器执行常量的初始化
	private function __construct() {
		$this->_init_config();
		$this->_init_app();
	}
	
	//单独调用init方法执行数据库的初始化，因为数据库连接比较费时
	public function init() {
		$this->init_db();
	}

	//__clone方法防止对象被复制克隆
	private function __clone(){
		trigger_error('Clone is not allow!',E_USER_ERROR);
	}

	private function _init_config(){
		$_config = null;
		require APP_ROOT.'/protected/config/main.php';
		if(empty($_config)){
			exit('Missing config file.');
		}
		$this->_config = &$_config;
		
		$this->isTestMode = $this->_config['testmode'];
	}
	
	private function _init_app(){
		// add includes to include_path
		$include_path = get_include_path();
		
		$additional_path = APP_ROOT.'/protected/controller';
		$include_path = $include_path.PATH_SEPARATOR.$additional_path;
		set_include_path($include_path);
		
		// init Smarty
		require_once FWK_ROOT.'/core/smarty.lib.php';
		
		// init base controller
		require_once FWK_ROOT.'/core/controller.lib.php';
	}

	private function _init_session() {
		session_start();
	}

	private function init_db(){
		if(isset($this->_config['db']) && isset($this->_config['db']['on']) && $this->_config['db']['on']==true) {
			$db_class = 'db_'.$this->_config['db']['type'];
			$this->_db = DB::instance($db_class, $this->_config['db']);
			$this->_db->connect();
		}
	}

	public static function &instance(){
		return self::_instance();
	}

	public static function reinit(){
		self::free();
		self::instance();
	}

	public static function free(){
		self::instance()->_db->close();
		self::$_instance = null;
	}

	public static function &ENV(){
		return self::instance()->_env;
	}

	public static function &CFG(){
		return self::instance()->_config;
	}

	public static function &DB(){
		return self::instance()->_db;
	}

	public static function &USER(){
		return self::instance()->_user;
	}

	public static function &GVAR(){
		return self::instance()->_var;
	}
	
	public static function isTestMode(){
		return self::instance()->isTestMode;
	}
}

class DB{
	private static $_instance = null;

	public static function &instance($db_class = 'db_mysql', $db_cfg = null){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new $db_class();
			self::$_instance->set_cfg($db_cfg);
		}
		return self::$_instance;
	}

	public static function connect(){
		self::instance()->connect();
	}
	public static function close(){
		self::instance()->close();
	}
	public static function query($query){
		return self::instance()->query($query);
	}
	public static function result($result, $row = 0){
		return self::instance()->result($result, $row);
	}
	public static function num_rows($result){
		return self::instance()->num_rows($result);
	}
	public static function num_fields($result){
		return self::instance()->num_fields($result);
	}
	public static function free_result($result){
		return self::instance()->free_result($result);
	}
	public static function fetch_row($result){
		return self::instance()->fetch_row($result);
	}
	public static function fetch_assoc($result){
		return self::instance()->fetch_assoc($result);
	}
	public static function fetch_fields($result){
		return self::instance()->fetch_fields($result);
	}
}

class oxy extends CCore {
	
}

// inititalization
oxy::instance();
// init others, like: DB
oxy::instance()->init();
