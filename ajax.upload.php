<?
require_once('./conf/inc.conf.php');
require_once($_WG2_ROOT.'/lib/class.MUpload.php');
require_once($_WG2_ROOT.'/lib/class.MDirInfo.php');


$dir = isset($_REQUEST['dir'][0])?$_REQUEST['dir']:date('Y/m');  //값이 없으면 자동으로 업로드 위치 선택
$todir = $_WG2_CFG['baseDir'].'/'.$dir;
$todir = preg_replace('/\.\.+/', '.', $todir);
$todir = preg_replace('!//+!', '/', $todir);

$mdi = new MDirInfo();
$mdi->server_charset = 'utf-8';
$mdi->web_charset = 'utf-8';
$mdi->sortF = 'mtime';
$mdi->sortR = 1;
//$mdi->allowExt = allowExt
$mdi->setBaseDir($_WG2_CFG['baseDir']);
$mdi->setConfigExtension($_WG2_CFG['cfgExt']);


$mup = new MUpload();
$mup->server_charset = 'utf-8';
$mup->web_charset = 'utf-8';
$mup->max_size = 1024*1024*3;
$mup->setAllow_extensions('png,jpg,gif');


$rs = $mup->upload($todir,$_FILES['upf']);
//var_dump($rs);
//-- 결과 정리
$rts = array();
foreach($rs as &$r){
	if($r['result']){
		$r = array_merge($r,$mdi->stat($r['uploaed_path']));
		$rts[] = array(
			'result'=>$r['result'],
			'error_msg'=>$r['error_msg'],
			// 'rel_path'=>$r['rel_path'],
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
			// 'rel_path'=>$r['rel_path'],
			'basename'=>$r['basename'],
		);		
	}
	
}

//print_r($_POST);
//print_r($rs);
//print_r($rts);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
echo json_encode($rts);
