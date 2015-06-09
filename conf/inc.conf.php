<?
$_WG2_ROOT = dirname(dirname(__FILE__));

$_WG2_CFG = array();
$_WG2_CFG['baseDir'] = $_WG2_ROOT.'/../web_work/web/WC/files';
$_WG2_CFG['dirContentLimit'] = 4;
$_WG2_CFG['allowExt'] = 'png;gif;jpg;jpeg;wcbjson'; //이 확장자만 보인다. *이 있다면 모든 파일이 보인다.
$_WG2_CFG['cfgExt'] = array();
$_WG2_CFG['cfgExt']['#DEF#'] = array(
	'previewurl'=>'./down.php?rel_path={{rel_path}}&mode=preview'
	,'viewurl'=>'./down.php?rel_path={{rel_path}}&mode=view'
	,'downurl'=>'./down.php?rel_path={{rel_path}}'
);
$_WG2_CFG['cfgExt']['png'] = array(
	'previewurl'=>'./down.php?rel_path={{rel_path}}&mode=preview'
	,'viewurl'=>'./down.php?rel_path={{rel_path}}&mode=view'
	,'downurl'=>'./down.php?rel_path={{rel_path}}'
);