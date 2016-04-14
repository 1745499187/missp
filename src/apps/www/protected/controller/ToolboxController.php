<?php

class ToolboxController extends CController {
	protected $layout = 'index';
	
	public function indexAction() {
		$output = array();

		$this->render('toolbox/index', $output);
	}
	
	public function showAction() {
		$serviceName = CUtil::getGPC('serviceName');
		$output = array();
		$screen = null;
		if($serviceName == "showJson") {
			$screen = "showJson";
		}
		else {
			$screen = "index";
		}
		$this->render('toolbox/'.$screen, $output);
	}

	public function doServiceAction() {
		$serviceName = CUtil::getGPC('serviceName');
		$output = array();
		$screen = null;
		if($serviceName == "showJson") {
			$message = CUtil::getGPC('message');
			$resultObj = $this->showJson($message);
			$this->responseJsonStr($resultObj);
		}
		else {
			echo '{"wrong invoke"}';
		}
	}

	private function showJson($message) {
		return $message;
	}
}
