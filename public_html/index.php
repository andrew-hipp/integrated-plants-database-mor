<?php
$app_root = realpath('.');
require_once $app_root. '/modules/page_intro_common.php';
require_once $app_root. '/modules/page_tab_Intro_common.php';
require_once $app_root. '/modules/collections_common.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
      "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>Morton Arboretum</title>
  <link rel="stylesheet" type="text/css" href="libraries/main.php" />
  <script type="text/javascript" src="libraries/main.js"></script>
</head>
<body>
	<form name="gotoPageForm" action="." method="post">
		<input type="hidden" name="page" value=""></input>
	</form>
<?php
require_once $app_root. '/modules/header_common.php';
?>	<div id="body" class="body">
		<div id="content" class="content">
<?php
showTabs($pages_info, $curPage);
if ($curPage['name'] != '') {
	$fileContents = file_get_contents($pages_dir . '/' . $curPage['name']);
	$bodyStartPos = stripos($fileContents, '<body>');
	$bodyEndPos = stripos($fileContents, '</body>');
	echo substr($fileContents,
				$bodyStartPos + 6,
				$bodyEndPos - ($bodyStartPos + 6));
}
?>		</div>
<?php
require_once $app_root. '/modules/footer_common.php';
?>	</div>
</body>
</html>
