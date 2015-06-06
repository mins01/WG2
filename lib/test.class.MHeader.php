<?
$str = isset($_REQUEST['str'][0])?$_REQUEST['str']:'A한글Captcha캡차';
include('class.MHeader.php');

$sec = 5;
$etag = date('Hi').ceil(date('s')/$sec);
MHeader::expires($sec);
$msgs = array();
if(MHeader::etag($etag)){
	//$msgs[] = 'etag 동작';//실제 출력되지 않는다.(304 발생이 되기 때문에)
	exit('etag 동작');
}else if(MHeader::lastModified($sec)){
	//$msgs[] = 'lastModified 동작'; //실제 출력되지 않는다.(304 발생이 되기 때문에)
	exit('lastModified 동작');
}
?>
<!doctype html>
<html lang="ko">
<head>
	<title>MHeader</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<h1>MHeader</h1>
	Page Call
	<ul>
		<li><a href="?">자신을 다시 부르기(expires 동작 확인용)</a>
			<ul>
				<li>개발툴의 Network환경에서 from cache로 나오는지 확인</li>
			</ul>
		</li>
		<li><a href='?t=<?=microtime(1)?>'>URL을 변경하여 새로 부르기(캐시등이 동작하지 않도록 하기 위함.)</a>
			<ul>
				<li>또는 ctrl+F5로 캐시설정,etag 등을 무시하면서 부를 수 있다.</li>
			</ul>
		</li>
		<li>F5로 페이지 새로 부르기</a>
			<ul>
				<li>etag, lastModified 등 동작하는지 확인</li>
				<li>개발툴의 Network환경에서 HTTP code가 304로 나오는지 확인</li>
			</ul>
		</li>
	</ul>
	Setting Values
	<ul>
		<li>sec : <?=$sec?></li>
		<li>etag : <?=$etag?></li>
		<li>now : <?=date('Y-m-d H:i:s.u')?> (이 값이 안 바뀐다는건 캐시나 304가 발생했다는 뜻이다.즉 페이지 갱신이 안되고있음.)</li>
	</ul>
	Request Headers
	<ul>
		<li>HTTP_IF_MODIFIED_SINCE : <?=$_SERVER['HTTP_IF_MODIFIED_SINCE']?></li>
		<li>HTTP_IF_NONE_MATCH : <?=$_SERVER['HTTP_IF_NONE_MATCH']?></li>
	</ul>
	Response Headers
	<pre><?=var_dump(headers_list());?></pre>
	result
	<pre><?=var_dump($msgs);?></pre>
</pre>
</body>
</html>