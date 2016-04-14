<?php
$_config = array();

$_config['db']['on'] = true;
$_config['db']['type'] = 'mysql';
$_config['db']['server']['host'] = 'localhost';
$_config['db']['server']['user'] = 'root';
$_config['db']['server']['password'] = 'root';
$_config['db']['server']['database'] = 'aicire_data';

$_config['theme']['default'] = 'default';

$_config['path']['cache'] = "cache";
$_config['path']['assets'] = "assets";

// chs, en
$_config['language'] = 'chs';

// true: force refresh templates
$_config['testmode'] = true;

?>