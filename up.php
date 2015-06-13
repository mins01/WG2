<?
require_once('./conf/inc.conf.php');
require_once($_WG2_ROOT.'/lib/class.MUpload.php');
require_once($_WG2_ROOT.'/lib/class.MDirInfo.php');

$dir = isset($_REQUEST['dir'])?$_REQUEST['dir']:date('Y/m');  //값이 없으면 자동으로 업로드 위치 선택
$todir = $_WG2_CFG['baseDir'].'/'.$dir;

$mdi = new MDirInfo();
$mdi->sortF = 'mtime';
$mdi->sortR = 1;
//$mdi->allowExt = allowExt
$mdi->setBaseDir($_WG2_CFG['baseDir']);
$mdi->setConfigExtension($_WG2_CFG['cfgExt']);


$mup = new MUpload();
$mup->max_size = 1024*1000;
$mup->setAllow_extensions($_WG2_CFG['upload_allowExt']);


$rs = $mup->upload($todir,$_FILES['upf']);
//var_dump($rs);
//-- 결과 정리
$rows = array();
foreach($rs as &$r){
	if($r['result']){
		$r = array_merge($r,$mdi->stat($r['uploaed_path']));
		$r = WG2Helper::relURL2absURLInRow($_WG2_CFG['webDir'],$r);
		$rows[] = $r;
	}else if($r['error']==4){
		continue;//빈 업로드는 무시한다.
		//$r =  array_merge($r,$mdi->stat($r['path'].'/empty'));
		//$rows[] = $r;
	}else{
		$r =  array_merge($r,array(
				'basename'=>$r['name'],
				'rel_path'=>$r['name'],
				'type'=>'error',
				'error_msg'=>'[Fail] '.$r['error_msg'],
			));
		$rows[] = $r;
	}
	
}

//--- JSONP로 동작시킨다.
$callback = isset($_REQUEST['callback'])?$_REQUEST['callback']:null;
if(isset($callback)){
	echo $callback.'('.json_encode($rows).')';
	exit;
}


$var = isset($_REQUEST['var'])?$_REQUEST['var']:'finfo';
?>
<!DOCTYPE html>
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
		<title>up</title>
		
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
		<script>
		//<!--
		
		
		function backToGallery(){
			var url = document.referrer;
			url = url.replace(/&?t=[^&]*/g,'');
			if(url.indexOf('?')==-1){
				url+='?t='+(new Date()).getTime();
			}else{
				url+='&t='+(new Date()).getTime();
			}
			document.location.replace(url);
		}
		setTimeout(backToGallery,5*1000);
		
		//-->
		</script>
		
	</head>
	<body>
		<header id="header">
			result : Upload to <?=htmlspecialchars($dir)?>
		</header>
		<section id="file-contents">
			<button type="button" onclick="backToGallery()" class="btn btn-default glyphicon glyphicon-th"> Back To Gallery</button>
			<div data-wc2-dir="/" id="pNode">

			</div>
			<button type="button" onclick="backToGallery()" class="btn btn-default glyphicon glyphicon-th"> Back To Gallery</button>
		</section>
		<footer  id="footer">
		<? /*
		<section id="upfile-contents">
			<button type="button" onclick="backToGallery()" class="btn btn-default glyphicon glyphicon-th"> Back To Gallery</button>
			<hr>
			<ul>
			<? 
			foreach($rts as $k=>$v){ 
				if($v['error']==4){continue;}
				$c = $v['result']?'result-true':'result-false'
			?>
				<li class="<?=$c?>">
				<?=htmlspecialchars(isset($v['uploaed_name'])?$v['uploaed_name']:$v['basename'])?> : <?=$v['result']?'Upload Success':$v['error_msg']?> 		
				<? if($v['is_image']){ ?>
					<div>
					<img src="<?=htmlspecialchars($v['previewurl'])?>" >
					</div>
				<? } ?>
				</li>
			<? 
			}
			?>
			</ul>
			<hr>
			<button type="button" onclick="backToGallery()" class="btn btn-default glyphicon glyphicon-th"> Back To Gallery</button>
		</section>
		*/ ?>
		<footer  id="footer">
			
		</footer>
		<div id="postBottom"> 최고 밑 부분에 계속 붙어 있어야한다.</div>
		<section  id="hidden-section">
			
			<div id="defNode" class="finfo finfo-file" data-wg2-type="file" data-wg2-basename="<?=htmlspecialchars($r['basename'])?>">
				<a class="title" id="" href="#"></a>
				<div class="previewbox" data-wg2-comment="">
					<a><img src="./img/file.gif"></a>
					<a class="editurl btn btn-default glyphicon glyphicon-edit" href="#" title="edit" onclick="return confirm('수정하시겠습니까?')"></a>
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
			wg2.showPreview();
		}
		wg2.showPreview();
		</script>
	</body>
</html>