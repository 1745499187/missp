<?php
class CApplication {
	private $ctrlName = null;
	private $actionName = null;
	
	function __construct($ctrl = 'index') {
		$this->ctrlName = $ctrl;
		$this->actionName = CUtil::getGPC2('action', 'index');
	}
	
	// overwrite in sub class
	protected function preProcess() {}

	// overwrite in sub class
	protected function afterProcess() {}

	public final function process() {
		try {
			$this->preProcess();
				
			$ctrlClass = ucfirst(strtolower($this->ctrlName)).'Controller';
			require_once $ctrlClass.'.php';
			$controller = new $ctrlClass();
			$controller->process($this->actionName);
				
			$this->afterProcess();
		} catch(Exception $e) {
			$exp = CUtil::dumpvar($e);
			$output = <<<html
<html>
<body>
<div><pre>$exp</pre></div>
</body>
</html>
html;
			echo $output;
		}
	}
}