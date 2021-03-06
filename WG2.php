<?
require_once('conf/inc.conf.php');
require_once($_WG2_ROOT.'/lib/class.MDirInfo.php');
require_once($_WG2_ROOT.'/lib/class.MHeader.php');


$var = isset($_REQUEST['var'])?$_REQUEST['var']:'finfo';
$dir = isset($_REQUEST['dir'])?$_REQUEST['dir']:'/';
//if(strpos($dir,'/')===0){ $dir = substr($dir,1); }
$dir = str_replace('..','.',$dir);
if($dir[0]!='/'){
	$dir = '/'.$dir; //항상 /로 시작하게 함
}
$upDir = str_replace('\\','/', dirname($dir));
if($upDir=='.'){$upDir='/';}



$mdi = new MDirInfo();
$mdi->server_charset = 'utf-8';
$mdi->web_charset = 'utf-8';
$mdi->sortF = 'mtime';
$mdi->sortR = 1;
//$mdi->allowExt = allowExt
$mdi->setBaseDir($mdi->iconv($_WG2_CFG['baseDir']));
$mdi->setConfigExtension($_WG2_CFG['cfgExt']);

//-- 파일,폴더 목록 뽑기
$rows = $mdi->fileListInBaseAtWeb($dir,2,true);
//print_r($rows);
//exit;
if($rows===false){
	echo $mdi->error;
	exit();
}

//-- 확장자 제한하기
$rows = $mdi->filter_extension($rows,$_WG2_CFG['allowExt']);
//print_r($rows);exit();

//-- 폴더 속 파일 수 제한하기
foreach($rows as & $v){
	unset($v['path'],$v['dirname']); //불필요 정보 삭제
	$path = $dir.'/'.$v['basename'];
	if(isset($v['in_contents'])){
		$v['in_contents_count'] = count($v['in_contents']);
		$v['in_contents'] = array_slice($v['in_contents'],0,$_WG2_CFG['dirContentLimit']);
		foreach($v['in_contents'] as & $v2){
			unset($v2['path'],$v2['dirname']); //불필요 정보 삭제
			$path2 = $path.'/'.$v2['basename'];
		}
	}
	
}



//-- 웹캐시 설정
//*
$sec = 60;
$etag =  floor(time()/$sec).md5( serialize($rows).$dir );
if(MHeader::etag($etag)){
	//$msgs[] = 'etag 동작';//실제 출력되지 않는다.(304 발생이 되기 때문에)
	exit('etag 동작');
}
if(MHeader::lastModified($sec)){
	//$msgs[] = 'lastModified 동작'; //실제 출력되지 않는다.(304 발생이 되기 때문에)
	exit('lastModified 동작');
}
MHeader::expires($sec);
//*/


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
			<a type="button" class="btn btn-default glyphicon glyphicon-level-up" href="?dir=<?=htmlspecialchars(urlencode($upDir))?>"></a> <?=htmlspecialchars($dir)?> (<?=count($rows)?> files)
		</header>
		<? if($_WG2_CFG['use_upload_form']) { ?>
		<section id="file-upload">
			<form action="up.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="dir" value="<?=htmlspecialchars($dir)?>">
				<div class="row">
					<div class="col-lg-5">
						<span class="input-group  input-group-sm">
							<span class="input-group-addon" id="sizing-addon3">Upload 1.</span>
							<input class="form-control" multiple name="upf[]" type="file" placeholder="Select File..." accept="<?=htmlspecialchars($_WG2_CFG['upload_file_accept'])?>">
						</span>
					</div>
					<div class="col-lg-5">
						<span class="input-group  input-group-sm">
							<span class="input-group-addon" id="sizing-addon3">Upload 2.</span>
							<input class="form-control" name="upf[]" type="file" placeholder="Select File..." multiple accept="<?=htmlspecialchars($_WG2_CFG['upload_file_accept'])?>">
						</span>
					</div>
					<div class="col-lg-2" style="text-align:center">
						<span class="btn-group  btn-group-sm">
							<button class="btn btn-default ">Upload</button>
						</span>
					</div>
					
					
				</div>
			</form>
		</section>
		<? } ?>
		<section id="file-contents">
			<div data-wc2-dir="/" id="pNode">

			</div>
		</section>
		<footer  id="footer">
			
		</footer>
		<div id="postBottom"> 최고 밑 부분에 계속 붙어 있어야한다.</div>
		<section  id="hidden-section">
			
			<div id="defNode" class="finfo finfo-file" data-wg2-type="file" data-wg2-basename="<?=htmlspecialchars($r['basename'])?>">
				<a class="title" id="" href="#"></a>
				<div class="previewbox" data-wg2-comment="">
					<a><img src="./img/file.gif"></a>
					
				</div>
				<a class="editurl btn btn-default glyphicon glyphicon-edit" href="#" title="edit" onclick="return confirm('수정하시겠습니까?')"></a>
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
			wg2.showPreview();
		}
		wg2.showPreview();
		</script>
  </body>
</html>