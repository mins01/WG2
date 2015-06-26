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
	
	//== 썸네일
	var $thumnail_use = true;
	var $thumnail_minFilesize = 307200; //300KB 이상면 리사이즈, 아니면 그냥 출력
	var $thumnail_maxWidth = 300;
	var $thumnail_maxHeight = 300;
	var $thumnail_jpg_quality = 50;
	
	
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
		$t = $this->pathinfo($name);
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
	function _createHeaders($name,$attachment){
		$header = array(
			'Content-Type'=>'application/octet-stream',
			'Content-Disposition'=>$attachment?'attachment':'inline',
			'Content-Transfer-Encoding'=>'binary',
			'Content-Length'=>-1,
		);
		$header['Content-Disposition']=$this->strContentDisposition($attachment,$name);
		$header['Content-Type'] = $this->get_mimetype($name);
		if(isset($this->error[0])){
			$header['X-error'] = $this->error;
		}
		return $header;
	}
	//=== 다운로드 : web_charset 기준
	function downloadFromWeb($path,$name='',$attachment=false){
		if(!isset($name[0])){
			$name = $this->basename($path);
		}
		$path = $this->iconv($path,0);
		return $this->download($path,$name,$attachment);
	}
	//=== 다운로드 : server_charset 기준 ($name은 web_charset 기준)
	function download($path,$name='',$attachment=false){
		$this->error = '';
		if(!is_file($path)){
			$this->error = __METHOD__." : not exists file in server";
			return false;
		}
		if(!isset($name[0])){
			$name = $this->basename($path);
		}

		
		$header = $this->_createHeaders($name,$attachment);
		$header['Content-Length'] = sprintf('%u',filesize($path));
		if(isset($this->error[0])) $header['X-error'] = $this->error;
		
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
		$this->error = '';
		$header = $this->_createHeaders($name,$attachment);
		$header['Content-Length'] = sprintf('%u',strlen($str));
		foreach($header as $k=>$v){
			header("{$k}: {$v}");
		}
		echo $str;
		return true;
	}
	//=== 썸네일로 출력 : web_charset 기준 ($name은 web_charset 기준)
	function thumbnailFromWeb($path,$name='',$attachment=false){
		if(!isset($name[0])){
			$name = $this->basename($path);
		}
		$path = $this->iconv($path,0);
		return $this->thumbnail($path,$name,$attachment);
	}
	//=== 썸네일로 출력 : server_charset 기준
	function thumbnail($path,$name='',$attachment=false){
		$this->error = '';
		if(!$this->thumnail_use){
			$this->error = __METHOD__." : thumnail_use is false";
			return $this->download($path,$name,$attachment); //리사이즈 하지 않는다.
		}
		if(!is_file($path)){
			$this->error = __METHOD__." : not exists file in server";
			return false;
		}
		if($this->thumnail_minFilesize > filesize($path)){
			$this->error = __METHOD__." : filesize > minFilesize";
			return $this->download($path,$name,$attachment); //리사이즈 하지 않는다.
		}
		if(!function_exists('gd_info')){
			$this->error = __METHOD__." : not installed GD";
			return $this->download($path,$name,$attachment); //리사이즈 하지 않는다.
		}
		if(!isset($name[0])){
			$name = $this->basename($path);
		}
		
		
		//-- 크기 체크
		list($width, $height) = getimagesize($path);
		if(!$width){
			$this->error = __METHOD__." : file is not a image.";
			return $this->download($path,$name,$attachment); //리사이즈 하지 않는다.
		}
		
		if($width > $height){
			$new_width = $this->thumnail_maxWidth;
			$new_height = floor($height * $new_width/$width);
		}else if($width < $height){
			$new_height = $this->thumnail_maxHeight;
			$new_width = floor($width * $new_height/$height);
		}else{
			$new_width = $this->thumnail_maxWidth;
			$new_height = $this->thumnail_maxHeight;
		}
		
		if($width <= $new_width && $height <= $new_height){
			return $this->download($path,$name,$attachment); //리사이즈 하지 않는다.
		}
		//-- 이미지  체크

		$pif = $this->pathinfo($path);
		$image = null;
		switch(strtolower($pif['extension'])){
			case 'jepg':
			case 'jpg':$image = imagecreatefromjpeg($path); break;
			case 'gif':$image = imagecreatefromgif($path); break;
			case 'png':$image = imagecreatefrompng($path); break;
		}
		if($image===false){
			$this->error = __METHOD__." : file is not a image..";
			return false;
		}
		$name = 'th_'.$pif['filename'].'.jpg'; //jpg로 고정
		$header = $this->_createHeaders($name,$attachment);
		//$header['Content-Length'] = sprintf('%u',filesize($path));
		unset($header['Content-Length']); //length를 출력안하면 자동으로 Transfer-Encoding:chunked 가 설정된다.(설정 안될경우 disconnect 때 시간이 걸린다.
		
		
		$header['X-thumbnail'] = '1';		
		foreach($header as $k=>$v){
			header("{$k}: {$v}");
		}
		
		// Content type
		// Get new dimensions
		$image_p = imagecreatetruecolor($new_width, $new_height);
		// Resample
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		// Output
		imagejpeg($image_p, null, $this->thumnail_jpg_quality);
		imagedestroy($image_p);
		
		list($width, $height) = getimagesize($filePath);
		
	}
}


?>