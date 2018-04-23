<?
require_once('conf/inc.conf.php');
require_once($_WG2_ROOT.'/lib/class.MHeader.php');
require_once($_WG2_ROOT.'/lib/class.MDownload.php');
require_once($_WG2_ROOT.'/lib/class.Wcbjson.php');

$baseDir = $_WG2_CFG['baseDir'];

$mode = isset($_REQUEST['mode'])?$_REQUEST['mode']:'view';

$rel_path = isset($_REQUEST['rel_path'])?$_REQUEST['rel_path']:'';
$rel_path = str_replace('..','.',$rel_path);
if(!isset($rel_path[0])){
	header("HTTP/1.0 400 Bad Request");
	exit();
}
$path = $baseDir.'/'.$rel_path;

$mdown = new MDownload();
/*
$r = $mdown->setPath($path);
if($r == false){
	header("HTTP/1.0 404 Not Found");
	exit($mdown->error);
}
*/



//-- 웹캐시 설정
$sec = 60*60*6;
$etag = floor(time()/$sec).md5($rel_path);
MHeader::expires($sec);
$msgs = array();
if(MHeader::etag($etag)){
	//$msgs[] = 'etag 동작';//실제 출력되지 않는다.(304 발생이 되기 때문에)
	exit('etag 동작');
}else if(MHeader::lastModified($sec)){
	//$msgs[] = 'lastModified 동작'; //실제 출력되지 않는다.(304 발생이 되기 때문에)
	exit('lastModified 동작');
}

switch($mode){
	case 'preview':
		if(preg_match('/\.wcbjson$/i',$rel_path)){
			$wcbjson = new Wcbjson();
			//$wcbjson->open($path);
			//$r = $wcbjson->preview();
			$r = $wcbjson->previewByPath($path); //이쪽이 메모리 사용이 적다.
			if($r===false){
				$error = $wcbjson->error;
			}else{
				$name = $mdown->basename($path).'.png';
				$r = $mdown->downloadByString($r,$name);
				if($r===false){
					$error = $mdown->error;
				}
			}
		}else{
			$r = $mdown->thumbnailFromWeb($path);
			if($r===false){
				$error = $mdown->error;
			}
		}
	break;
	case 'view':;
	case 'down':;
	default:
		$r = $mdown->downloadFromWeb($path,'',($mode=='down'));
		if($r===false){
			$error = $mdown->error;
		}
	break;
}



if($r == false){
	header("HTTP/1.1 501 Not Implemented");
	exit($error);
}
ob_end_flush ();
exit();