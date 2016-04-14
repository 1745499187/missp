<?php

if(!defined('SMARTY_DIR')) {
	define('SMARTY_DIR', FWK_ROOT.'/libs/smarty/');
}

// load Smarty library
require(SMARTY_DIR.'Smarty.class.php');

// This file is a good place to load
// required application library files, and you
// can do that right here. An example:
// require('guestbook/guestbook.lib.php');

class MySmarty extends Smarty {

	function __construct()
	{
		parent::__construct();

		$this->setTemplateDir(APP_ROOT.'/view/templates/');
		$this->setConfigDir(APP_ROOT.'/view/configs/');
		
		$this->setCompileDir(oxy::CFG()['path']['cache'].'/smarty/templates_c/');
		$this->setCacheDir(oxy::CFG()['path']['cache'].'/smarty/cache/');
		
		if(oxy::isTestMode()) {
			$this->caching = Smarty::CACHING_OFF;
		}
		else {
			$this->caching = Smarty::CACHING_LIFETIME_CURRENT;
		}
		$this->configLoad('view.conf');
	}
}
