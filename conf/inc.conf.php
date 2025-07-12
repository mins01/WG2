<?

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);

define('_WG_UPLOAD_OVERWRITE',0);//이름 중복시 덮어씀

$_WG2_ROOT = dirname(dirname(__FILE__));
require_once($_WG2_ROOT.'/lib/class.WG2Helper.php');

$_WG2_CFG = array();
$_WG2_CFG['domain'] = WG2Helper::currentDomain($_SERVER);
$_WG2_CFG['webDir'] = $_WG2_CFG['domain'].'/WG2';
//$_WG2_CFG['baseDir'] = $_WG2_ROOT.'/../web_work/web/WC/files';
$_WG2_CFG['baseDir'] = $_WG2_ROOT.'/_files.files';
$_WG2_CFG['baseDirUrl'] = '/WG2';
$_WG2_CFG['dirContentLimit'] = 4;
$_WG2_CFG['allowExt'] = 'png,gif,jpg,jpeg,webp,wcbjson,json,zip'; //이 확장자만 보인다. *이 있다면 모든 파일이 보인다.
$_WG2_CFG['upload_allowExt'] = 'png,gif,jpg,jpeg,webp,wcbjson,json,zip'; //업로드 가능 확장자
$_WG2_CFG['upload_file_accept'] = '.png,.gif,.jpg,.jpeg,webp,.wcbjson,.json,.zip'; //업로드용 input file에서 보이는 확장자 및 타입
$_WG2_CFG['use_upload_form'] = false; //업로드 폼 사용여부
$_WG2_CFG['cfgExt'] = array();
$_WG2_CFG['cfgExt']['#DEF#'] = array(
	'previewurl'=>$_WG2_CFG['baseDirUrl'].'/down.php?mode=preview&rel_path={{rel_path}}', //미리보기 이미지용
	'viewurl'=>$_WG2_CFG['baseDirUrl'].'/down.php?mode=view&rel_path={{rel_path}}', //이미지 클릭시
	'downurl'=>$_WG2_CFG['baseDirUrl'].'/down.php?mode=down&rel_path={{rel_path}}', //타이틀 클릭시
);
$t = $_WG2_CFG['domain'].'/WC2/WC2.html?open='.urlencode($_WG2_CFG['baseDirUrl'].'/down.php?mode=down&rel_path=').'{{rel_path}}'; //WC2 

$_WG2_CFG['cfgExt']['jpg'] = array(
	'editurl'=>$t,
);
$_WG2_CFG['cfgExt']['jpeg'] = array(
	'editurl'=>$t,
);
$_WG2_CFG['cfgExt']['gif'] = array(
	'editurl'=>$t,
);
$_WG2_CFG['cfgExt']['png'] = array(
	'editurl'=>$t,
);
$_WG2_CFG['cfgExt']['webp'] = array(
	'editurl'=>$t,
);
$_WG2_CFG['cfgExt']['wcbjson'] = array(
	'viewurl'=>$t,
	'editurl'=>$t,
	'is_image'=>true
);

$_WG2_CFG['corsDomainRegExp'] = '/^(127\.0\.0\.1|mins01\.com|[^.]\.mins01\.com|mins01\.github\.io)$/';


//---
