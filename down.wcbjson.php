<?
require_once('conf/inc.conf.php');
require_once($_WG2_ROOT.'/lib/class.MHeader.php');
require_once($_WG2_ROOT.'/lib/class.MDownload.php');

$baseDir = $_WG2_CFG['baseDir'];

$rel_path = isset($_REQUEST['rel_path'])?$_REQUEST['rel_path']:'';
$rel_path = str_replace('..','.',$rel_path);
if(!isset($rel_path[0])){
	header("HTTP/1.0 400 Bad Request");
	exit();
}
$path = $baseDir.'/'.$rel_path;

$mdown = new MDownload();
/*$r = $mdown->setPath($path);
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
	//exit('etag 동작');
}else if(MHeader::lastModified($sec)){
	//$msgs[] = 'lastModified 동작'; //실제 출력되지 않는다.(304 발생이 되기 때문에)
	//exit('lastModified 동작');
}


$cont = file_get_contents($path);
//print_r($cont);
$stStr = '"preview":';
$st = strpos($cont,$stStr);
if($st===false){
	header("HTTP/1.0 404 Not Found");	header("Status: 404 Not Found"); exit('no preivew');
}
$stStr = '"dataURL":"';
$st = strpos($cont,$stStr,$st);
if($st===false){
	header("HTTP/1.0 404 Not Found");	header("Status: 404 Not Found"); exit('no preivew');
}
$st+=strlen($stStr);
$ed = strpos($cont,'"}',$st);
$dataURL = substr($cont,$st,$ed-$st);unset($cont);
//$json = json_decode(file_get_contents($path),true); //메모리를 너무 먹는다.
if(!isset($dataURL)){
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	exit('no dataURL');
}
$t = explode(',',$dataURL,2);
$ctnttype = preg_replace('/(^[^:]*:|;.*$)/','',$t[0]);
$cont = base64_decode($t[1]);

/*
header('Content-type: '.$ctnttype);
header('Content-Length: ' . strlen($cont));
echo $cont;
exit;
*/

$name = basename($path).'.png';

$r = $mdown->downloadByString($cont,$name);
if($r == false){
	header("HTTP/1.0 404 Not Found");
	exit($mdown->error);
}
ob_end_flush ();
exit();