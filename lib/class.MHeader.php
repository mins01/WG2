<?
/**
* MHeader
* 해더관련 기능 모음, static 메소드로 구성(PHP4 호환성 때문에 static 표시안함)
*/
class MHeader{
  
	/**
	* Cache-Control, expires 제어
	* $sec : 지정 시간만큼 브라우저 캐시를 사용하게 한다.(강제로 사용하는건 아님, 새로 고침으로 무효화 된다.)
	*/
	public static function cacheControl($sec){
		if($sec<=0){
			return MHeader::noCache();
		}else{
			return MHeader::expires($sec);
		}
	}
	public static function expires($sec){
		if(!is_numeric($sec)){
			return false;
		}
		header('Cache-Control:public, max-age='.$sec); // HTTP/1.1
		header("Expires: ".gmdate("D, d M Y H:i:s", time()+$sec)." GMT");	//캐시
		return true;
	}
	/**
	* no-cache
	* 캐시를 사용하지 않도록 한다.
	*/
	public static function noCache(){
		header('Pragma: no-cache');
		header('Expires: Thu, 01 Jan 1970 16:00:00 GMT');
		header('Cache-Control: max-age = 0, no-cache');
		return true;
	}

	/**
	* Last-Modified 제어
	* $sec : 지정 시간만큼 재요청 시 HTTP CODE 304를 발생시켜서 트래픽을 줄인다.
	* $HTTP_IF_MODIFIED_SINCE : 수동으로 입력받으면, 그 값으로 기준으로 처리한다.(날짜 형식 주의)
	* return : true일 경우 exit()로 페이지를 끝내는걸 추천.
	*/
	public static function lastModified($sec,$iHTTP_IF_MODIFIED_SINCE=NULL){
		$HTTP_IF_MODIFIED_SINCE = isset($iHTTP_IF_MODIFIED_SINCE)?$iHTTP_IF_MODIFIED_SINCE:(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'][0])?$_SERVER['HTTP_IF_MODIFIED_SINCE']:NULL);
		if(isset($HTTP_IF_MODIFIED_SINCE[0]) &&  strtotime($HTTP_IF_MODIFIED_SINCE) > time()){
			//header("x-304-reason: Last-Modified");
			header("HTTP/1.1 304 Not Modified",true,304); 
			return true;
			//exit();
		}else{
			header('Last-Modified: '.gmdate("D, d M Y H:i:s", time()+$sec)." GMT");
		}
		return false;
	}
	/**
	* etag 제어
	* $etag : etag의 값을 기준으로 재요청시 HTTP CODE 304를 발생시켜서 트래픽을 줄인다.
	* $iHTTP_IF_NONE_MATCH : 수동으로 입력받으면, 그 값으로 기준으로 처리한다.
	* return : true일 경우 exit()로 페이지를 끝내는걸 추천.
	*/
	public static function etag($etag,$iHTTP_IF_NONE_MATCH=NULL){
		$HTTP_IF_NONE_MATCH = isset($iHTTP_IF_NONE_MATCH)?$iHTTP_IF_NONE_MATCH:(isset($_SERVER['HTTP_IF_NONE_MATCH'][0])?trim($_SERVER['HTTP_IF_NONE_MATCH']):NULL);
		if(isset($HTTP_IF_NONE_MATCH) && $HTTP_IF_NONE_MATCH == $etag){
			//header("x-304-reason: Etag");			
			header("HTTP/1.1 304 Not Modified",true,304); 
			return true;
			//exit();
		}else{
			header("Etag: ".$etag);	//etag
		}
		return false;
	}
  
  
	
}


?>