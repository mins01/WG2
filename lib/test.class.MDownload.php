<?
require('class.MDownload.php');

$mdown = new MDownload();
$r = $mdown->setPath('test.class.MDownload.php','a.txt');
if($r == false){
	header("HTTP/1.0 404 Not Found");
	exit($mdown->error);
}
//var_dump($mdown );
//var_dump($r);
$mdown->download();