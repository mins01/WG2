<?
/**
* Wcbjson
* wcbjson 파일 제어용
*/
class Wcbjson{
	var $json = null;
	var $error  = '';
	
	function Wcbjson(){
		return $this->__construct();
	}
	function __construct(){
	}
	function open($path){
		if(!is_file($path)){
			$this->error ='Not exists a file';
		}
		$cont = file_get_contents($path);
		if(strpos($cont,'{"dataType":"wcb"')!==0){
			$this->error ='wcbjson?';
			return false;
		}
		$this->json = json_decode($cont,true);
		return true;
	}
	function close(){
		unset($this->json);
	}
	function preview(){
		if(!isset($this->json['preview'])){
			$this->error ='no-preview ';
			return false;
		}
		return base64_decode(preg_replace('|^data:image/.{3,5};base64,|','',$this->json['preview']['dataURL']));
	}
	//-- 메모리 사용을 줄이기 위해서 사용.(json 제어는 메모리를 너무 많이 먹는다.
	function previewByPath($path){
		if(!is_file($path)){
			$this->error ='Not exists a file';
		}
		$cont = file_get_contents($path);
		$stStr = '"preview":';
		$st = strpos($cont,$stStr);
		if($st===false){
			$this->error ='no-preview ';
			return false;
		}
		$stStr = '"dataURL":"';
		$st = strpos($cont,$stStr,$st);
		if($st===false){
			$this->error ='no-dataURL';
			return false;
		}
		$st+=strlen($stStr);
		$ed = strpos($cont,'"}',$st);
		$dataURL = substr($cont,$st,$ed-$st);unset($cont);
		//$json = json_decode(file_get_contents($path),true); //메모리를 너무 먹는다.
		if(!isset($dataURL)){
			$this->error ='no-dataURL';
			return false;
		}
		$t = explode(',',$dataURL,2);
		$ctnttype = preg_replace('/(^[^:]*:|;.*$)/','',$t[0]);
		return base64_decode($t[1]);
		//$cont = base64_decode($t[1]);
		//return preg_replace('/^data:image/.{3,5};base64,/','',$this->json['preview']['dataURL']);
	}
	
}


?>