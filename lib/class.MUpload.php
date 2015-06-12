<?
/**
* MUpload
* 업로드 관련 관련.
*/
class MUpload{
	var $max_size = 1024;
	var $allow_extensions = array('*');
	var $logfile_name = 'MUpload.log';
	var $use_log = true;
	var $no_log_error_4 = true; //빈파일 업로드는 로그에 남기지 않는다.
	var $to_charset = 'euc-kr//TRANSLIT';//언어셋 변경
	
	function MDownload(){
		return $this->__construct();
	}
	function  __construct(){
		
	}
	function init(){
		
	}
	function _log($dir,$arr){
		if($this->use_log){
			if($this->no_log_error_4 && isset($arr['error']) && $arr['error']==4){
				return false;
			}
			if(!is_dir($dir)){
				return false;
			}
			$logFile = $dir.'/'.$this->logfile_name;
			$t = array();
			$t[] = date('Y-m-d H:i:s');
			$t[] = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'offline';
			$t[] = serialize($arr);
			return error_log(implode(' ',$t)."\n",3,$logFile);
		}
	}
	
	function setAllow_extensions($allowExt){
		if(is_array($allowExt)){
			return $this->allow_extensions = $allowExt;
		}else if(is_string($allowExt)){
			return $this->allow_extensions = explode(',',$allowExt);
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
		$path = iconv('utf-8',$this->to_charset,$path);
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
				$this->_log($dir,$f); continue;
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
				$this->_log($dir,$f); continue;
			}
			$tmp_path = $f['tmp_name'];
			$path = $this->_getUniqePath($dir.'/'.$f['name']);
			if($path===false){
				$f['result'] = false;
				$f['error_msg'] = 'error exists filename';
				$this->_log($dir,$f); continue;
			}
			if(!$this->_checkAllotExt($path)){
				$f['result'] = false;
				$f['error_msg'] = 'error not allow extension';
				$this->_log($dir,$f); continue;
			}
			//echo $path ;			exit();
			if(!move_uploaded_file ( $tmp_path , $path )){
				$f['result'] = false;
				$f['error_msg'] = 'error move_uploaded_file()"';
				$this->_log($dir,$f); continue;
			}
			$f['uploaed_path'] = $path;
			$f['uploaed_name'] = basename($path);
			$f['result'] = true;
			$f['error_msg'] = '';
			$this->_log($dir,$f);
		}
		return $fs;
	}
	
}


?>