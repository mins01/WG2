<?
require_once('conf/inc.conf.php');
require_once($_WG2_ROOT.'/lib/class.MDirInfo.php');
require_once($_WG2_ROOT.'/lib/class.MHeader.php');


$var = isset($_REQUEST['var'])?$_REQUEST['var']:'finfo';
$dir = isset($_REQUEST['dir'])?$_REQUEST['dir']:'.';
if(strpos($dir,'/')===0){ $dir = substr($dir,1); }
$dir = str_replace('..','.',$dir);
$upDir = dirname($dir);




$mdi = new MDirInfo();
$mdi->sortF = 'mtime';
$mdi->sortR = 1;
//$mdi->allowExt = allowExt

$previewDir = '/web_work/web/WFL/_M.UI.FILELIST.down.php?file=%2F2012%2F01%2Funtitle_20120109233611.png&inline=1';

$mdi->setBaseDir($_WG2_CFG['baseDir']);

$rows = $mdi->fileListAtBase($dir,2,true);
if($rows==false){
	echo $mdi->error;
	exit();
}

//-- 확장자 제한하기
$rows = $mdi->filter_extension($rows,$_WG2_CFG['allowExt']);


//-- 폴더 속 파일 수 제한하기
foreach($rows as & $v){
	unset($v['path'],$v['dirname']); //불필요 정보 삭제
	$v['type'] = $v['is_dir']?'dir':'file';//dir과 file만 
	$path = $dir.'/'.$v['basename'];
	$v['preview'] = $v['is_dir']?$previewDir:'./down.php?rel_path='.$v['rel_path'];
	if(isset($v['in_contents'])){
		$v['in_contents_count'] = count($v['in_contents']);
		$v['in_contents'] = array_slice($v['in_contents'],0,$_WG2_CFG['dirContentLimit']);
		foreach($v['in_contents'] as & $v2){
			$v2['type'] = $v2['is_dir']?'dir':'file';//dir과 file만 
			unset($v2['path'],$v2['dirname']); //불필요 정보 삭제
			$path2 = $path.'/'.$v2['basename'];
			$v2['preview'] = $v2['is_dir']?$previewDir:'./down.php?rel_path='.$v2['rel_path'];
		}
	}
	
}

//print_r($rows);


//-- 웹캐시 설정
$sec = 60*1;
$etag =  floor(time()/$sec).md5( serialize($rows));
MHeader::expires($sec);
$msgs = array();
if(MHeader::etag($etag)){
	//$msgs[] = 'etag 동작';//실제 출력되지 않는다.(304 발생이 되기 때문에)
	//exit('etag 동작');
}else if(MHeader::lastModified($sec)){
	//$msgs[] = 'lastModified 동작'; //실제 출력되지 않는다.(304 발생이 되기 때문에)
	//exit('lastModified 동작');
}

//--


?><!DOCTYPE html>
<!-- 
2015-06-07 : 제작시작
임의사용 금지.

-->
<html lang="ko">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- 위 3개의 메타 태그는 *반드시* head 태그의 처음에 와야합니다; 어떤 다른 콘텐츠들은 반드시 이 태그들 *다음에* 와야 합니다 -->
		<title>WG2</title>
		
		<!-- 합쳐지고 최소화된 최신 CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

		<!-- 부가적인 테마 -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
		
		<link rel="stylesheet" type="text/css" href="css/wg2.css" />
		<script>
		//<!--
		
		<? echo 'var ',$var,'=',json_encode($rows); ?>
		
		//-->
		</script>
		
	</head>
	<body>
		<header id="header">
			<a type="button" class="btn btn-default glyphicon glyphicon-level-up" href="?dir=<?=htmlspecialchars($upDir)?>"></a> /<?=htmlspecialchars($dir)?> (<?=count($rows)?> files)
		</header>
		<section id="content">
			<div data-wc2-dir="/" id="pNode">

			</div>
		</section>
		<footer  id="footer">
			
		</footer>
		<div id="postBottom"> 최고 밑 부분에 계속 붙어 있어야한다.</div>
		<section  id="hidden-section">
			
			<div id="defNode" class="finfo finfo-file" data-wg2-type="file" data-wg2-basename="<?=htmlspecialchars($r['basename'])?>">
				<a class="title" id="" href="#"></a>
				<div class="previewbox">
					<img src="http://www.mins01.com/web_work/web/WFL/_M.UI.FILELIST.down.php?file=%2F2012%2F01%2Funtitle_20120109233611.png&amp;inline=1">
				</div>
			</div>
			
		</section>
		
		<!-- script  -->
		<script src="js/wg2.js"></script>
		<script>
		wg2.init();
		wg2.putRows(finfo);
		while(wg2.appendNode()){
		}
		//wg2.showPreview();
		document.onmousewheel = function(){
			wg2.showPreview();
		}
		document.onscroll = function(){
			wg2.showPreview();
		}
		window.onresize = function(){
			document.title='x';
			wg2.showPreview();
		}
		wg2.showPreview();
		</script>
  </body>
</html>