<?
require_once('conf/inc.conf.php');
require_once($_WG2_ROOT.'/lib/class.MUpload.php');

$mup = new MUpload();

$mup->max_size = 1024*1000;
$mup->setAllow_extensions($_WG2_CFG['allowExt']);

$dir = isset($_REQUEST['dir'])?$_REQUEST['dir']:'/';
if(strpos($dir,'/')===0){ $dir = substr($dir,1); }
$dir = str_replace('..','.',$dir);
$upDir = dirname($dir);
if($upDir=='.'){$upDir='/';}



$todir = $_WG2_CFG['baseDir'].'/'.$dir;
$r = $mup->upload($todir,$_FILES['upf']);
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
		<title>WG2.upload</title>
		
		<!-- 합쳐지고 최소화된 최신 CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

		<!-- 부가적인 테마 -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
		
		<link rel="stylesheet" type="text/css" href="css/wg2.css" />
		<script>
		//<!--
		
		function backToGallery(){
			var url = document.referrer;
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
			/<?=htmlspecialchars($dir)?>
		</header>
		<section id="upfile-contents">
			<button type="button" onclick="backToGallery()" class="btn btn-default glyphicon glyphicon-th"> Back To Gallery</button>
			<hr>
			<ul>
			<? 
			foreach($r as $k=>$v){ 
				if($v['error']==4){continue;}
				$c = $v['result']?'result-true':'result-false'
			?>
				<li class="<?=$c?>"><?=htmlspecialchars(isset($v['uploaed_name'])?$v['uploaed_name']:$v['name'])?> : <?=$v['result']?'Upload Success':$v['error_msg']?> </li>
			<? 
			}
			?>
			</ul>
			<hr>
			<button type="button" onclick="backToGallery()" class="btn btn-default glyphicon glyphicon-th"> Back To Gallery</button>
		</section>
		<footer  id="footer">
			
		</footer>
	</body>
</html>