<?
/**
* MUpload
* 업로드 관련 관련.
*/
class MUpload{
	var $max_size = 1024;
	var $allow_extensions = array('*');
	function MDownload(){
		return $this->__construct();
	}
	function  __construct(){
		
	}
	function init(){
		
	}
	function setAllow_extensions($allowExt){
		if(is_array($allowExt)){
			return $this->allow_extensions = explode(';',$allowExt);
		}else if(is_string($allowExt)){
			return $this->allow_extensions = explode(';',$allowExt);
		}
		return false;
	}
	function reArrayFILES($FILES){
		$fs = array();
		if(!is_array($FILES['name'])){
			$fs[] = $FILES;
		}else{
			for($i=0,$m=count($FILES['name']);$i<$m;$i++){
				$fs[] =array(
					'name'=>$FILES['name'][$i],
					'type'=>$FILES['type'][$i],
					'size'=>$FILES['size'][$i],
					'tmp_name'=>$FILES['tmp_name'][$i],
					'error'=>$FILES['error'][$i],
				);
			}
		}
		return $fs;
	}
	function _mkdir($dir){
		if(!is_dir($dir)){
			return mkdir($dir,077,true);
		}
		return true;
	}
	function _checkAllotExt($path){
		if($this->allow_extensions[0] == '*'){return true;}
		$pt = pathinfo($path);
		$t = isset($pt['extension'])?$pt['extension']:'';
		return in_array($t,$this->allow_extensions);
	}
	function _getUniqePath($path){
		$pt = pathinfo($path);
		$icnt = 0;
		while(file_exists($path) && ++$icnt < 1000){
			$name = $pt['filename'].'('.$icnt.')';
			if(isset($pt['extension'])){
				$name .= '.'.$pt['extension'];
			}
			$path = $pt['dirname'].'/'.$name;
		}
		return file_exists($path)?false:$path;
	}
	function upload($dir,$FILES){
		$fs = $this->reArrayFILES($FILES);
		$this->_mkdir($dir);
		foreach($fs as & $f){
			if($f['size']>$this->max_size){
				$f['result'] = false;
				$f['error_msg'] = "max size over : {$f['size']} > {$this->max_size}";
				continue;
			}
			switch($f['error']){
				case 0:break;
				case 1:
					$f['result'] = false;
					$f['error_msg'] = 'UPLOAD_ERR_INI_SIZE';
				break;
				case 2:
					$f['result'] = false;
					$f['error_msg'] = 'UPLOAD_ERR_FORM_SIZE';
				break;
				case 3:
					$f['result'] = false;
					$f['error_msg'] = 'UPLOAD_ERR_PARTIAL';
				break;
				case 4:
					$f['result'] = false;
					$f['error_msg'] = 'UPLOAD_ERR_NO_FILE';
				break;
				case 6:
					$f['result'] = false;
					$f['error_msg'] = 'UPLOAD_ERR_NO_TMP_DIR';
				break;
				case 7:
					$f['result'] = false;
					$f['error_msg'] = 'UPLOAD_ERR_CANT_WRITE';
				break;
				case 8:
					$f['result'] = false;
					$f['error_msg'] = 'UPLOAD_ERR_EXTENSION';
				break;
				default:
					$f['result'] = false;
					$f['error_msg'] = 'unknown error : '.$f['error'];
				break;
			}
			if($f['error']!=UPLOAD_ERR_OK){
				continue;
			}
			$tmp_path = $f['tmp_name'];
			$path = $this->_getUniqePath($dir.'/'.$f['name']);
			if($path===false){
				$f['result'] = false;
				$f['error_msg'] = 'error exists filename';
				continue;
			}
			if(!$this->_checkAllotExt($path)){
				$f['result'] = false;
				$f['error_msg'] = 'error not allow extension';
				continue;
			}
			
			if(!move_uploaded_file ( $tmp_path , $path )){
				$f['result'] = false;
				$f['error_msg'] = 'error move_uploaded_file()"';
				continue;
			}
			$f['uploaed_path'] = $path;
			$f['uploaed_name'] = basename($path);
			$f['result'] = true;
			$f['error_msg'] = '';
		}
		return $fs;
	}
	
}


?>