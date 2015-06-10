<?
require('class.MUpload.php');

$mup = new MUpload();

$mup->setAllow_extensions('png;jpg');

$dir = '../files';
$r = $mup->upload($dir,$_FILES['upf']);
var_dump($r);