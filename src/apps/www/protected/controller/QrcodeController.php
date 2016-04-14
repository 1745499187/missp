<?php

class QrcodeController extends CController {
	protected $layout = 'index';
	
	public function indexAction() {
		$output = array();

		$this->render('qrcode/index', $output);
	}
	
	public function generateAction() {
		// 引入qrcode库
		require_once FWK_ROOT.'/core/qrcode.lib.php';

		// 数据字符集
		$_charset = CUtil::getGPC2('_charset', 'utf-8');

		// 容错级别
		$ecLevel = CUtil::getGPC2('ecLevel', QR_ECLEVEL_M);

		// 图像大小，单位pix
		$size = CUtil::getGPC2('brushPix', 4);

		// 边框空白间距
		$margin = CUtil::getGPC2('imgMargin', 4);

		// 数据
        $data = CUtil::getGPC('qrData');
        // urlencode
        $data = $this->parseUrl($data);

		// 是否输出到文件，如果需要，直接设置为文件路径
		$outfile = false;
		
		// 是否保存并展示
		$saveAndPrint = false;
		
		// 直接输出为image/png格式的图片字节流，客户端可以通过<img src='xxx' />来调用
		QRcode::png($data, false, $ecLevel, $size, $margin, $saveAndPrint);
    }

    private function doUrlencode($url) {
        if(strpos($url, 'http://')===0 || strpos($url, 'https://')===0) {
            $paramPos = strpos($url, '?');
            if($paramPos != false) {
                $host = substr($url, 0, $paramPos);
                $param = substr($url, $paramPos+1);
                return $host . '?' . urlencode($param);
            }
            else {
                return $url;
            }
        }

        return $url;
    }

    private function parseUrl($url) {
        $parsed = parse_url($url);
        if($parsed == false) {
            return $url;
        }
        else {
            $newUrl;
            if(isset($parsed['scheme'])) {
                $newUrl.=$parsed['scheme'].'://';
            }
            if(isset($parsed['user'])) {
                $newUrl.=$parsed['user'].':';
            }
            if(isset($parsed['pass'])) {
                $newUrl.=$parsed['pass'].'@';
            }
            if(isset($parsed['host'])) {
                $lastChar = substr($newUrl, -1);
                if(strcmp($lastChar, ':')) {
                    $newUrl.='@'.$parsed['host'];
                }
                else {
                    $newUrl.=$parsed['host'];
                }
            }
            if(isset($parsed['port'])) {
                $newUrl.=':'.$parsed['port'];
            }
            if(isset($parsed['path'])) {
                $newUrl.='/'.$parsed['path'];
            }
            if(isset($parsed['query'])) {
                parse_str($parsed['query'], $params);
                $newQuery = http_build_query($params);
                $newUrl.='?'.$newQuery;
            }
            return $newUrl;
        }
    }
}
