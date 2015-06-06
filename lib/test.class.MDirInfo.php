<?
require('class.MDirInfo.php');

$mdi = new MDirInfo();
$mdi->sortF = 'mtime';
$mdi->sortR = 0;
//$mdi->sortF = 'basename';
$r = $mdi->fileListAtBase('.',2,1);

//print_r($r);
$r = $mdi->filter_only_file($r);
print_r($r);