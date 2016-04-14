<?php

class IndexController extends CController {
	protected $layout = 'index';
	
	public function indexAction() {
		$output = array();
		$output['name'] = 'Edward';
		
		$this->render('index/index', $output);
	}
	
	public function contactAction() {
		$output = array();
		$output['name'] = 'Edward';
	
		$this->render('index/contact', $output);
	}
}