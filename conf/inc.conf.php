<?
$_WG2_ROOT = dirname(dirname(__FILE__));

$_WG2_CFG = array();
$_WG2_CFG['baseDir'] = $_WG2_ROOT.'/../web_work/web/WC/files';
$_WG2_CFG['baseDirUrl'] = dirname(isset($_SERVER['PHP_SELF'])?$_SERVER['PHP_SELF']:'');
$_WG2_CFG['dirContentLimit'] = 4;
$_WG2_CFG['allowExt'] = 'png;gif;jpg;jpeg;wcbjson'; //이 확장자만 보인다. *이 있다면 모든 파일이 보인다.
$_WG2_CFG['cfgExt'] = array();
$_WG2_CFG['cfgExt']['#DEF#'] = array(
	'previewurl'=>'./down.php?mode=preview&rel_path={{rel_path}}'
	,'viewurl'=>'./down.php?mode=view&rel_path={{rel_path}}'
	,'downurl'=>'./down.php?mode=down&rel_path={{rel_path}}'
);
$_WG2_CFG['cfgExt']['jpg'] = array(
	'editurl'=>'/WC2/WC2.html?open='.urlencode($_WG2_CFG['baseDirUrl'].'/down.php?mode=down&rel_path=').'{{rel_path}}',
);
$_WG2_CFG['cfgExt']['jpeg'] = array(
	'editurl'=>'/WC2/WC2.html?open='.urlencode($_WG2_CFG['baseDirUrl'].'/down.php?mode=down&rel_path=').'{{rel_path}}',
);
$_WG2_CFG['cfgExt']['gif'] = array(
	'editurl'=>'/WC2/WC2.html?open='.urlencode($_WG2_CFG['baseDirUrl'].'/down.php?mode=down&rel_path=').'{{rel_path}}',
);
$_WG2_CFG['cfgExt']['png'] = array(
	'editurl'=>'/WC2/WC2.html?open='.urlencode($_WG2_CFG['baseDirUrl'].'/down.php?mode=down&rel_path=').'{{rel_path}}',
);
$_WG2_CFG['cfgExt']['wcbjson'] = array(
	'previewurl'=>'./down.wcbjson.php?mode=preview&rel_path={{rel_path}}',
	'editurl'=>'/WC2/WC2.html?open='.urlencode($_WG2_CFG['baseDirUrl'].'/down.php?mode=down&rel_path=').'{{rel_path}}',
	'is_image'=>true
);