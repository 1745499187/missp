<?php

if(!defined('QRCODE_DIR')) {
	define('QRCODE_DIR', FWK_ROOT.'/libs/qrcode/');
}

// load QRCode library
require(QRCODE_DIR.'qrlib.php');
