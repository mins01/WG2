<?
/**
* MDirInfo
*/

class MDirInfo{
	var $baseDir = 'E:\homepage\html\WG2\lib'; //루트 위치
	var $error = '';
	var $sortF = false; //정렬키
	var $sortR = false; //역순
	var $cnfExt = array(
		'#DEF#'=>array(
			'previewurl'=>'./down.php?rel_path={{rel_path}}&mode=preview'
			,'viewurl'=>'./down.php?rel_path={{rel_path}}&mode=view'
			,'downurl'=>'./down.php?rel_path={{rel_path}}'
		),
	);
	//== 케릭터셋 관련
	var $server_charset = 'cp949';//서버쪽 언어셋
	var $web_charset = 'utf-8';//웹쪽 언어셋
	var $to_charset_option = '//TRANSLIT';
	
	function MDirInfo(){
		return $this->__construct();
	}
	function  __construct(){
	}
	function setConfigExtension($cnfExt){
		foreach($cnfExt as $k=>$v){
			$this->cnfExt[$k] = array_merge($this->cnfExt['#DEF#'],$v);
		}
	}
	function getConfigExtension($ext){
		$ext = strtolower($ext);
		
		return isset($this->cnfExt[$ext])?$this->cnfExt[$ext]:$this->cnfExt['#DEF#'];
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
	/**
	* $path : filepath (encoding : $server_charset)
	*/
	function stat($path){
		$path = realpath($path);
		if(!file_exists($path)){
			$this->error = 'not exists file.';
			return false;
		}
		$stat = stat($path);
		$info = array();
		$info['path'] = $path;
		$info = array_merge($info,$this->pathinfo($this->iconv($path,1)));
		/*
		$info['dirname'], "\n";
		$info['basename'], "\n";
		$info['extension'], "\n";
		$info['filename'], "\n"; // since PHP 5.2.0
		*/
		$info['is_file'] = is_file($path);
		$info['is_dir'] = is_dir($path);
		$info['is_link'] = is_link($path);
		$info['is_readable'] = is_readable($path);
		$info['is_writable'] = is_writable($path);
		$info['is_image'] = isset($info['extension'])?$this->is_image($info['extension']):false;
		$info['size'] = sprintf('%u',$stat['size']);
		$info['atime'] = $stat['atime'];
		$info['mtime'] = $stat['mtime'];
		$info['ctime'] = $stat['ctime'];
		$info['type'] =  $info['is_file']?'file':($info['is_dir']?'dir':($info['is_link']?'link':'none'));
		
		$utf8_path = $info['dirname'].'/'.$info['basename'];
		
		$info['server_rel_path'] = str_replace('\\','/',str_replace($this->baseDir,'',$info['path']));
		$info['rel_path'] = str_replace('\\','/',str_replace($this->baseDir,'',$utf8_path));
		
		$shs = array();
		$rps = array();
		foreach($info as $k=>$v){
			$shs[] = '{{'.$k.'}}';
			$rps[] = urlencode($v);
		}
		
		$cnfExt = $this->getConfigExtension($info['extension']);
		foreach($cnfExt as $k=>$v){
			$info[$k] = str_replace($shs,$rps,$v);
		}
		
		//$info['rel_path'] = strpos($info['path'],$this->baseDir).$info['path'].";".$this->baseDir;
		//	print_r(stat($path));
		//print_r($info);	
		return $info;

	}
	function is_image($extension){
		$img_patten = '/^(png|jpg|jpeg|gif)$/i';
		return preg_match($img_patten,$extension);
	}
	//server_charset 기준
	function setBaseDir($baseDir){
		$this->baseDir = realpath($baseDir);
	}
	//web_charset 기준
	function fileListInBaseAtWeb($iDir,$depth=1,$sort=false){
		$iDir = $this->iconv($iDir);
		return $this->fileListInBase($iDir,$depth,$sort);
	}
	//server_charset 기준
	function fileListInBase($iDir,$depth=1,$sort=false){
		$dir = realpath($this->baseDir.'/'.$iDir);
		if(!is_dir($dir)){
			$this->error = 'not exists dir : '.$dir;
			return false;
		}
		return $this->fileList($dir,$depth,$sort);
	}
	//server_charset 기준
	function fileList($dir,$depth=1,$sort=false){
		if(!is_dir($dir)){
			$this->error = 'not exists dir : '.$dir;
			return false;
		}
		if($depth<1){
			return null;
		}
		$rows = array();
		$d = dir($dir);
		//echo "Handle: " . $d->handle . "\n";
		//echo "Path: " . $d->path . "\n";
		while (false !== ($entry = $d->read())) {
			if($entry=='.' || $entry=='..'){continue;}
			$path = $d->path.'/'.$entry;
			$row = $this->stat($path);
			if($row['is_dir']){
				if($depth>0){
					$row['in_contents'] = $this->fileList($path, ($depth-1),$sort);//하위 파일,폴더
				}else{
					$row['in_contents'] = null;//하위 파일,폴더
				}
			}
			$rows[] = $row;
			if($rows===false){
				return false;
			}
			//$row['name'] = $entry;
		   //print_r($row);
		}
		//print_r($rows);
		if($sort){
			$this->sort($rows);
		}
		return $rows;
	}
	//=== 소팅 관련 부가 함수
	function _cmp_basename($a,$b){
		$f = 'basename';
		return MDirInfo::_cmp($a,$b,$f);
	}
	function _cmp_basenameR($a,$b){
		$f = 'basename';
		return MDirInfo::_cmp($a,$b,$f,true);
	}
	function _cmp_mtime($a,$b){
		$f = 'mtime';
		return MDirInfo::_cmp($a,$b,$f);
	}
	function _cmp_mtimeR($a,$b){
		$f = 'mtime';
		return MDirInfo::_cmp($a,$b,$f,true);
	}
	function _cmp($a,$b,$f,$r){
		$rt = 0;
		if(!isset($a[$f])){ $rt = 1; }
		if(!isset($b[$f])){ $rt =  -1; }
		if ($a[$f] == $b[$f]) { $rt =  0; }
		$rt = ($a[$f] < $b[$f]) ? -1 : 1;
		if($r){
			$rt *= -1;
		}
		return $rt ;
	}
	//=== 소팅
	function sort(& $rows,$sortF = null,$sortR = null){
		if(!isset($sortF)){
			$sortF = $this->sortF;
		}
		if(!isset($sortR)){
			$sortR = $this->sortR;
		}

		switch($sortF){
			case 'basename':
				usort($rows, array('MDirInfo',$sortR?'_cmp_basenameR':'_cmp_basename'));
			break;
			case 'mtime':
				usort($rows, array('MDirInfo',$sortR?'_cmp_mtimeR':'_cmp_mtime'));
			break;
		}
	}
	//=== 필터
	function filter_extension($iRows,$allowExt='*'){
		$exts = explode(',',strtolower($allowExt));
		$rows = array();
		foreach($iRows as $v){
			if($v['is_dir']){
				if(isset($v['in_contents'])){
					$v['in_contents'] = $this->filter_extension($v['in_contents'],$allowExt);
				}
				$rows[] = $v;
			}else if($allowExt == '*'){
				$rows[] = $v;
			}else if($v['is_file'] && in_array(strtolower($v['extension']),$exts)){
				$rows[] = $v;
			}
		}
		return $rows;
	}

}


