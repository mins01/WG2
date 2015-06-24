<?
/**
* 라이브러리 모음
* 업로드 관련 관련.
*/
class WG2Helper{
	function currentURL($server){
		$isCLI = (php_sapi_name() == "cli");
		if($isCLI){ return false; }
		$isHTTPS = isset($server['HTTPS'][0]);
		$HTTP_HOST = isset($server['HTTP_HOST'][0])?$server['HTTP_HOST']:'';
		$REQUEST_URI = isset($server['REQUEST_URI'][0])?$server['REQUEST_URI']:''; //쿼리 스트링이 이미 포함됨
		$r = array();
		$r[] = ($isHTTPS)?'https':'http';
		$r[] ='://';
		$r[] = $HTTP_HOST;
		$r[] = $REQUEST_URI;
		return implode('',$r);
	}
	function currentDomain($server){
		$isCLI = (php_sapi_name() == "cli");
		if($isCLI){ return false; }
		$isHTTPS = isset($server['HTTPS'][0]);
		$HTTP_HOST = isset($server['HTTP_HOST'][0])?$server['HTTP_HOST']:'';
		//$REQUEST_URI = isset($server['REQUEST_URI'][0])?$server['REQUEST_URI']:''; //쿼리 스트링이 이미 포함됨
		$r = array();
		$r[] = ($isHTTPS)?'https':'http';
		$r[] ='://';
		$r[] = $HTTP_HOST;
		//$r[] = $REQUEST_URI;
		return implode('',$r);
	}
	function relURL2absURLInRow($pre,$row){
		$currentDomain = preg_replace('|(://[^/]*)(/.*)|','\\1',$pre);
		foreach($row as $k => & $v){
			if(strpos($k,'url')){
				if(strpos($v,'http')===0){
					continue;
				}else if(strpos($v,'/')===0){
					$v = $currentDomain.str_replace('//','/',$v);	
				}else if(strpos($v,'./')===0){
					$v = str_replace('//','/',$pre.'/'.$v);	
				}
				
			}
		}
		return $row;
	}
}


?>