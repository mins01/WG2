<?
require_once('conf/inc.conf.php');
require_once($_WG2_ROOT.'/lib/class.MDirInfo.php');


$mdi = new MDirInfo();
$mdi->sortF = 'mtime';
$mdi->sortR = 1;

$mdi->setBaseDir($_WG2_CFG['baseDir']);

$rows = $mdi->fileListAtBase('./',2,true);
if($rows==false){
	echo $mdi->error;
}
//-- 폴더 속 파일 수 제한하기
foreach($rows as & $v){
	unset($v['path'],$v['dirname']); //불필요 정보 삭제
	if(isset($v['contents'])){
		$v['contents_count'] = count($v['contents']);
		$v['contents'] = array_slice($v['contents'],0,$_WG2_CFG['dirContentLimit']);
		foreach($v['contents'] as & $v2){
			unset($v2['path'],$v2['dirname']); //불필요 정보 삭제
		}
	}
	
}

//print_r($rows);



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
		<link rel="stylesheet" type="text/css" href="css/WG2.css" />

		
	</head>
	<body>
		<header id="header">
			/2015/12/*.png
		</header>
		<section id="content">
			<ul data-wc2-dir="/">
			
				<?
				foreach($rows as $r){
					if($r['is_file']){
				?>
					<li class="finfo finfo-file" data-wg2-type="file" data-wg2-basename="<?=htmlspecialchars($r['basename'])?>">
						<img src="http://www.mins01.com/web_work/web/WFL/_M.UI.FILELIST.down.php?file=%2F2012%2F01%2Funtitle_20120109233611.png&amp;inline=1">
					</li>
				<? 
					}else if($r['is_dir']){
					?>
						<li class="finfo finfo-dir" data-wg2-type="dir" data-wg2-basename="<?=htmlspecialchars($r['basename'].'('.$r['contents_count'].')')?>">
							<ul>
								<? 
								foreach($r['contents'] as $r2){ 
									if($r2['is_file']){
								?>
								<li class="finfo finfo-file" data-wg2-type="file" data-wg2-basename="<?=htmlspecialchars($r2['basename'])?>">
									<img src="http://www.mins01.com/web_work/web/WFL/_M.UI.FILELIST.down.php?file=%2F2012%2F01%2Funtitle_20120109233611.png&amp;inline=1">
								</li>
								<? 
									}else if($r['is_dir']){
								?>
								<li class="finfo finfo-dir" data-wg2-type="dir"  data-wg2-basename="<?=htmlspecialchars($r2['basename'].'('.$r2['contents_count'].')')?>">
									DIR
								</li>
								<?
									}
								}
								?>
							</ul>
						</li>
					<?
					}
				} 
				?>
				<li class="finfo finfo-dir" data-wg2-type="file" data-wg2-basename="ssdasd.xxxx">
					<ul>
						<li class="finfo finfo-file" data-wg2-type="file" data-wg2-basename="xx123123123123123112312312312312332.xxxx">
							<img src="http://www.mins01.com/web_work/web/WFL/_M.UI.FILELIST.down.php?file=%2F2012%2F01%2Funtitle_20120109233611.png&amp;inline=1">
						</li>
						<li class="finfo finfo-file" data-wg2-type="file" data-wg2-basename="xx123123123123123112312312312312332.xxxx">
							<img src="http://www.mins01.com/web_work/web/WFL/_M.UI.FILELIST.down.php?file=%2F2012%2F01%2Funtitle_20120109233611.png&amp;inline=1">
						</li>
						<li class="finfo finfo-file" data-wg2-type="file" data-wg2-basename="xx123123123123123112312312312312332.xxxx">
							<img src="http://www.mins01.com/web_work/web/WFL/_M.UI.FILELIST.down.php?file=%2F2012%2F01%2Funtitle_20120109233611.png&amp;inline=1">
						</li>
						<li class="finfo finfo-file" data-wg2-type="file" data-wg2-basename="xx123123123123123112312312312312332.xxxx">
							<img src="http://www.mins01.com/web_work/web/WFL/_M.UI.FILELIST.down.php?file=%2F2012%2F01%2Funtitle_20120109233611.png&amp;inline=1">
						</li>
						
					</ul>
				</li>
			</ul>
		</section>
		<footer  id="footer">
			asd
		</footer>
  </body>
</html>