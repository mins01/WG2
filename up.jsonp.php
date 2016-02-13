<?
require_once('./conf/inc.conf.php');
require_once($_WG2_ROOT.'/lib/class.MUpload.php');
require_once($_WG2_ROOT.'/lib/class.MDirInfo.php');


$dir = $_WG2_CFG['baseDir'].'/'.date('Y/m'); //자동으로 업로드 위치 선택

$mdi = new MDirInfo();
$mdi->sortF = 'mtime';
$mdi->sortR = 1;
//$mdi->allowExt = allowExt
$mdi->setBaseDir($dir);
$mdi->setConfigExtension($_WG2_CFG['cfgExt']);


$mup = new MUpload();
$mup->max_size = 1024*1024*3;
$mup->setAllow_extensions('png;jpg;eml');


$rs = $mup->upload($_WG2_CFG['baseDir'],$_FILES['upf']);
//var_dump($rs);
//-- 결과 정리
$rts = array();
foreach($rs as &$r){
	if($r['result']){
		$r = array_merge($r,$mdi->stat($r['uploaed_path']));
		$rts[] = array(
			'result'=>$r['result'],
			'error_msg'=>$r['error_msg'],
			'rel_path'=>$r['rel_path'],
			'basename'=>$r['basename'],
			'previewurl'=>$r['previewurl'],
			'viewurl'=>$r['viewurl'],
			'downurl'=>$r['downurl'],
		);
	}else if($r['error']==4){
		continue;//빈 업로드는 무시한다.
	}else{
		$rts[] = array(
			'result'=>$r['result'],
			'error_msg'=>$r['error_msg'],
			'rel_path'=>$r['rel_path'],
			'basename'=>$r['basename'],
		);		
	}
	
}

//print_r($_POST);
//print_r($rs);
//print_r($rts);

$callback = isset($_POST['callback'])?$_POST['callback']:'';
echo $callback.'('.json_encode($rts).')';
?>

