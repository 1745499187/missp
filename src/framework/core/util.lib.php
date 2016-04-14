<?php
class CUtil {
	public static function dumpvar($var) {
		ob_start();
		var_dump($var);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	public static function getGPC($k, $type='GP') {
        $type = strtoupper($type);
        switch($type) {
                case 'G': $var = &$_GET; break;
                case 'P': $var = &$_POST; break;
                case 'C': $var = &$_COOKIE; break;
                default:
                        if(isset($_GET[$k])) {
                                $var = &$_GET;
                        } else {
                                $var = &$_POST;
                        }
                        break;
        }

        return isset($var[$k]) ? $var[$k] : NULL;
	}
	
	public static function getGPC2($k, $defVal, $type='GP') {
		$type = strtoupper($type);
		switch($type) {
			case 'G': $var = &$_GET; break;
			case 'P': $var = &$_POST; break;
			case 'C': $var = &$_COOKIE; break;
			default:
				if(isset($_GET[$k])) {
					$var = &$_GET;
				} else {
					$var = &$_POST;
				}
				break;
		}
	
		return isset($var[$k]) ? $var[$k] : $defVal;
	
	}
}