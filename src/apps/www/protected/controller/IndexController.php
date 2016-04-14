<?php

class IndexController extends CController {
	protected $layout = 'index';
	
	public function indexAction() {
		$output = array();

		$this->render('index/index', $output);
	}
	
	public function testindexAction() {
		$output = array();
		$output['name'] = 'Edward';
	
		$this->render('index/contact', $output);
	}
}