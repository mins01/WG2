<?
/**
* MDownload
* 다운로드 관련.
*/
class MDownload{

	var $error = '';
	var $contentType = '';
	var $readBuffer = 1048576;

	//== 케릭터셋 관련
	var $server_charset = 'cp949';//서버쪽 언어셋
	var $web_charset = 'utf-8';//웹쪽 언어셋
	var $to_charset_option = '//TRANSLIT';
	
	function MDownload(){
		return $this->__construct();
	}
	function  __construct(){
		
	}
	function iconv($str,$isOut=false){
		if($this->web_charset == $this->server_charset){return $str;}
		return !$isOut?iconv($this->web_charset,$this->server_charset.$this->to_charset_option,$str):iconv($this->server_charset,$this->web_charset.$this->to_charset_option,$str);
	}
	//--- 기본 함수가 php5 에서 한글이 잘리는 버그가 있어서 preg_match로 따로 메소드 사용.
	function pathinfo($path) {
		preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im',$path,$m);
		if($m[1]) $ret['dirname']=$m[1];
		if($m[2]) $ret['basename']=$m[2];
		if($m[5]) $ret['extension']=$m[5];
		if($m[3]) $ret['filename']=$m[3];
		return $ret;
	}
	//--- 기본 함수가  php5 에서 한글이 잘리는 버그가 있어서 preg_match로 따로 메소드 사용
	function basename($path){
		return preg_replace( '/^.+[\\\\\\/]/', '', $path );
	}
	
	function init(){
		
	}


	function strContentDisposition($attachment,$name=null){
		$t = array();
		$t[] = ($attachment?'attachment':'inline');
		if(isset($name[0])){
			$t[] = '; ';
			$t[] = 'filename="'.$name.'"';
		}
		return implode('',$t);
	}
	//=== 확장자로 minetype 가져오기
	function get_mimetype($name){
		$t = pathinfo($name);
		$ext = isset($t['extension'])?strtolower($t['extension']):'';
		switch($ext){
			case 'png':
				return 'image/png';
			break;
			case 'gif':
				return 'image/gif';
			break;
			case 'jpeg':
			case 'jpg':
				return 'image/jpeg';
			break;
			default:
				return 'application/octet-stream';
			break;
		}
	}
	//=== 다운로드 : 모든 입력은 utf-8기준
	function download($path,$name='',$attachment=false){
		
		$header = array(
			'Content-Type'=>'application/octet-stream',
			'Content-Disposition'=>$attachment?'attachment':'inline',
			'Content-Transfer-Encoding'=>'binary',
			'Content-Length'=>-1,
		);
		if(!isset($name[0])){
			$name = basename($path);
		}
		$path = $this->iconv($path,0);
		if(!is_file($path)){
			$this->error = __METHOD__."::not exists file. ({$path})";
			return false;
		}
		
		$header['Content-Disposition']=$this->strContentDisposition($attachment,$name);
		$header['Content-Type'] = $this->get_mimetype($name);		
		$header['Content-Length'] = sprintf('%u',filesize($path));
		foreach($header as $k=>$v){
			header("{$k}: {$v}");
		}
		$fp = @fopen($path,'r+') ;
		if(!$fp){
			$this->error = 'error fopen';
			return false;
		}
		while (!feof($fp)) {
			set_time_limit(1);	//타임아웃 3초가 지났는데도 문제가 있다면 파일읽어오는 데 문제가 있다!
			echo fgets($fp, $this->readBuffer);
		}
		fclose($fp);
		return true;
	}
	function downloadByString(& $str,$name,$attachment=false){
		$header = array(
			'Content-Type'=>'application/octet-stream',
			'Content-Disposition'=>$attachment?'attachment':'inline',
			'Content-Transfer-Encoding'=>'binary',
			'Content-Length'=>-1,
		);

		$header['Content-Disposition']=$this->strContentDisposition($attachment,$name);
		$header['Content-Type'] = $this->get_mimetype($name);
		$header['Content-Length'] = sprintf('%u',strlen($str));
		foreach($header as $k=>$v){
			header("{$k}: {$v}");
			//echo "{$k}: {$v}\n";
		}
		echo $str;
		return true;
	}
}


?>