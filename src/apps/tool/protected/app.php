<?php
define('FWK_ROOT', __DIR__.'/../../../framework');

define('APP_ROOT', __DIR__.'/..');

require_once FWK_ROOT.'/core/core.lib.php';

class MyApp extends CApplication {
	protected function preProcess() {
	}
	
	protected function afterProcess() {
	}
}