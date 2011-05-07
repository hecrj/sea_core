<?php header('HTTP/1.1 404 Not Found'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ERROR 404 - Page not found</title>
<style type="text/css">
body {
	font-family: Helvetica, Arial, sans-serif;
	font-size: 1.3em;
	width: 1000px; margin: 0 auto;
}
h1, h3 {
	color: #900;
}
div { border: 1px solid #DCB5B5; padding: 0 10px; border-radius: 10px; }
p {
	line-height: 1.5em;
	color: #333;
}
</style>
</head>

<body>
<h1>Ups! The requested page has not been found!</h1>

	<p>The page you are trying to access seems to be unavailable...</p>
	<p>You have requested the following page:</p>
	<p><strong>http://<?= $_SERVER['HTTP_HOST'] . $_SERVER['PATH_INFO'] ?></strong></p>
<?php if(isset($e)): ?>
<div>
		<h3>Some errors have ocurred during your request:</strong></h3>
		<p><?= $e->getMessage(); ?></p>
</div>
<?php endif; ?>
</body>
</html>