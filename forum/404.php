<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Error 404 Not Found</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<LINK rel="stylesheet" type="text/css" media="all" href="/js/mount/style.css">
	<LINK rel="stylesheet" type="text/css" media="all" href="/css/404.css">
</head>
<body>
	<div id="block-on-center">
	<center><br/><br/><br/><h2>404 - страница не найдена</h2></center><br/>
	<center><a href="<? if (isset($_SERVER['HTTP_HOST'])): ?>http://<?= strtolower($_SERVER['HTTP_HOST']); ?><? endif; ?>">Перейти на сайт совместных <? if (isset($_SERVER['HTTP_HOST'])): ?><?= strtolower($_SERVER['HTTP_HOST']); ?><? endif; ?></a></center>
	</div>
</body>

</html>