<?
require_once('conf/inc.conf.php');
require_once($_WG2_ROOT.'/lib/class.MDirInfo.php');


$callback = isset($_REQEST['callback'])?$_REQEST['callback']:'';
$var = isset($_REQEST['var'])?$_REQEST['var']:'dirInfo';
$mode = isset($callback[0])?'jsonp':'js';

$mdi = new MDirInfo();
$mdi->sortF = 'mtime';
$mdi->sortR = 1;

$mdi->setBaseDir($_WG2_CFG['baseDir']);

$rows = $mdi->fileListAtBase('.',2,true);

//-- 폴더 속 파일 수 제한하기
foreach($rows as & $v){
	unset($v['path'],$v['dirname']); //불필요 정보 삭제
	if(isset($v['contents'])){
		$v['contents'] = array_slice($v['contents'],0,$_WG2_CFG['dirContentLimit']);
		foreach($v['contents'] as & $v2){
			unset($v2['path'],$v2['dirname']); //불필요 정보 삭제
		}
	}
	
}

//print_r($rows);
if($mode=='js'){
	header("Content-Type: application/javascript");
	echo 'var ',$var,'=',json_encode($rows);
}else if($mode=='jsonp'){
	header("Content-Type: application/javascript");
	echo $callback,'(',json_encode($rows),')';
}
?>