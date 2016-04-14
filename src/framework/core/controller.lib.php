<?php

class CController{
	protected $layout = 'index';
	
	public function process($action) {
		if(empty($action)) {
			$action = "index";
		}
		$actionName = strtolower($action).'Action';
		if(method_exists($this, $actionName)) {
			$this->$actionName();
		}
		else {
			exit('Missing action '.$actionName);
		}
	}
	
	public function render($tpl, $data = array()) {
		$smarty = new MySmarty();
		
		// 生成tpl内容
		$smarty->assign('output', $data);
		$content = $smarty->fetch($tpl.'.tpl.html');
		
		// 将生成的tpl内容放入layout中
		$smarty->clearAllAssign();
		$smarty->assign('content', $content);
		$smarty->display($this->layout.'.layout.html');
	}
	
	public function renderWithoutLayout($tpl, $data = array()) {
		$smarty = new MySmarty();
	
		// 生成tpl内容
		$smarty->assign('output', $data);
		$smarty->display($tpl.'.tpl.html');
	}
	
	public function responseJson($obj) {
		header("Content-Type: application/json; charset=utf-8");
		
		echo json_encode($obj);
	}
}