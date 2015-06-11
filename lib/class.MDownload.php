<?
/**
* MDownload
* 다운로드 관련.
*/
class MDownload{
	var $path = '';
	var $name = '';
	var $inline = true;
	var $header = array();
	var $error = '';
	var $contentType = '';
	var $readBuffer = 1048576;
	
	function MDownload(){
		return $this->__construct();
	}
	function  __construct(){
		
	}
	function init(){
		$this->reset();
	}
	function reset(){
		$this->inline = true;
		$this->header = array(
			'Content-Type'=>'application/octet-stream',
			'Content-Disposition'=>'inline',
			'Content-Transfer-Encoding'=>'binary',
			'Content-Length'=>-1,
		);
	}
	function setPath($path,$name=null){
		if(!is_file($path)){
			$this->error = "not exists file. ({$path})";
			return false;
		}
		$this->reset();
		$this->path = $path;
		$this->header['Content-Length'] = sprintf('%u',@filesize($path));
		if(!isset($name[0])){
			$name = basename($this->path);
		}
		$this->setName($name);
		return true;
	}
	function setName($name){
		$this->name = $name;
		$this->header['Content-Disposition']=$this->strContentDisposition($this->inline,$this->name);
		$this->header['Content-Type'] = $this->get_mimetype($this->name);
	}
	function strContentDisposition($inline,$name=null){
		$t = array();
		$t[] = ($this->inline?'inline':'attachment');
		if(!isset($name[0])){
			$name = $this->name;
		}
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
	function download($path=null,$name='',$inline='inline'){
		if(isset($path[0])){
			$this->setPath($path,$name);
			$this->strContentDisposition($inline,$name);
		}
		if(!is_file($this->path)){
			$this->error = "not exists file. ({$path})";
			return false;
		}
		foreach($this->header as $k=>$v){
			header("{$k}: {$v}");
		}
		$fp = @fopen($this->path,'r+') ;
		if(!$fp){
			$this->error = 'error fopen';
			return false;
		}
		while (!feof($fp)) {
			set_time_limit(3);	//타임아웃 3초가 지났는데도 문제가 있다면 파일읽어오는 데 문제가 있다!
			echo fgets($fp, $this->readBuffer);
		}
		fclose($fp);
		return true;
	}
	function downloadByString(& $str,$name=''){
		$this->header['Content-Length'] = sprintf('%u',strlen($str));
		$this->setName($name);
		foreach($this->header as $k=>$v){
			header("{$k}: {$v}");
		}
		echo $str;
		return true;
	}
}


?>