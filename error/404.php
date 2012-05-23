<?php
	if (!defined('INCLUDE_SECURE')) die();
	$page_title = 'Operis - 404 Page Not Found';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="title" content="Operis NUS School of Computing Impressions System" /> 
<title><?php echo($page_title);?></title>
<link rel="stylesheet" type="text/css" href="<?php echo($URL_BASE_DIR);?>r/css/reset.css" />
<link rel="stylesheet" type="text/css" href="<?php echo($URL_BASE_DIR);?>r/css/operis.css" />
<link rel="stylesheet" type="text/css" href="<?php echo($URL_BASE_DIR);?>r/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo($URL_BASE_DIR);?>r/css/bootstrap-responsive.min.css" />
</head>
<body>

<div id="front-container" class="container">
	<div class="row">
		<div class="span12 left">
			<img class="logo" src="<?php echo($URL_BASE_DIR);?>/r/img/404.png" /><br /><br /><br /><br />
			Did you just get lost?
			<footer>&copy; Copyright 2012 National University of Singapore. All Rights Reserved.</footer>
		</div>
	</div>
</div>

</body>
</html>