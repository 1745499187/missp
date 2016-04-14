<?php
if(!defined('INSIDE')){
	exit("Access Denied");
}

interface db_if{
	public function set_cfg($cfg);
	public function is_connected();
	public function connect();
	public function close();
	public function query($query);
	public function result($result, $row = 0);
	public function num_rows($result);
	public function num_fields($result);
	public function free_result($result);
	public function fetch_row($result);
	public function fetch_assoc($result);
	public function fetch_fields($result);
}

class db_base{
	protected $_config = null;
	protected $_curr_link = null;
	protected $_connected = false;

	public function set_cfg($cfg){
		if(!empty($cfg)){
			$this->_config = &$cfg;
		}
	}
	public function is_connected(){
		return $this->_connected;
	}
}

class db_mysql extends db_base implements db_if {
	public function connect(){
		$host = $this->_config['server']['host'];
		$user = $this->_config['server']['user'];
		$pwd = $this->_config['server']['password'];
		$this->_curr_link = mysql_connect($host, $user, $pwd);
		mysql_select_db($this->_config['server']['database']);
		$this->_connected = true;
	}
	
	public function close(){
		@mysql_close($this->_curr_link);
		$this->_connected = false;
	}
	
	public function query($query){
		return mysql_query($query);
	}
	
	public function result($result, $row = 0) {
		return @mysql_result($result, $row);
	}
	
	public function num_rows($result) {
		return mysql_num_rows($result);
	}
	
	public function num_fields($result) {
		return mysql_num_fields($result);
	}
	
	public function free_result($result) {
		return mysql_free_result($result);
	}
	
	public function fetch_row($result) {
		return mysql_fetch_row($result);
	}
	
	public function fetch_assoc($result) {
		return mysql_fetch_assoc($result);
	}
	
	public function fetch_fields($result) {
		return mysql_fetch_field($result);
	}
}
?>