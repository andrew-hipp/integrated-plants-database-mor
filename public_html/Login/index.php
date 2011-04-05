<?php
$app_root = realpath('..');
require_once $app_root . '/modules/page_intro_common.php';

if (isset($_SESSION['SIGNATURE']) && isset($_SESSION['username'])) {
	if ($_SESSION['SIGNATURE'] != sha1($_SERVER['HTTP_USER_AGENT'] . dirname($_SERVER["REQUEST_URI"]))) {
		unset($_SESSION['username']);
		$_SESSION['SIGNATURE'] = sha1($_SERVER['HTTP_USER_AGENT'] . dirname($_SERVER["REQUEST_URI"]));
	}
} else {
	$_SESSION['SIGNATURE'] = sha1($_SERVER['HTTP_USER_AGENT'] . dirname($_SERVER["REQUEST_URI"]));
}

$timedout = false;
// Timeout after 10 minutes of inactivity.
if (isset($_SESSION['lastActivity'])) {
	$session_life = time() - $_SESSION['lastActivity'];

	if ($session_life > 10 * 60) {
		$timedout = true;
		unset($_SESSION['username']);
	}
}

$_SESSION['lastActivity'] = time();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
      "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>Morton Arboretum</title>
  <link rel="stylesheet" type="text/css" href="../libraries/main.php" />
  <script type="text/javascript" src="../libraries/sha1.js"></script>
  <script type="text/javascript" src="../libraries/main.js"></script>
</head>
<body style="font-family: Arial;">
<?php
$pageLocation = "Administration";
$noSearch = true;
require_once $app_root . '/modules/header_common.php';
?><div id="body" class="login_body">
<div id="content' class="content">
<b>Login</b>
<div id="login_background" class="login_background">
<div id="login_form_box" class="login_form_box">
<form name="realLoginForm" action="dashboard.php" method="post">
<input type="hidden" name="username" value=""></input><input type="hidden" name="password" value=""></input>
</form>
<form name="loginForm" action="javascript:doRealLogin(document.realLoginForm, document.loginForm)">
<table>
<tr>
<td><label for="username"><b>Username:</b></label></td>
<?php
$focusField = "";
$adminUser = "";
if (isset($_COOKIE['MortonArbAdminUser'])) {
	$adminUser = base64_decode($_COOKIE['MortonArbAdminUser']);
}
if ($adminUser != '') {
	echo "<td><input name=\"username\" type=\"text\" size=\"42\" tabindex=\"1\" value=\"" . $adminUser . "\" /></input></td>";
	$focusField = "password";
}
else {
	echo "<td><input name=\"username\" type=\"text\" size=\"42\" tabindex=\"1\" /></input></td>";
	$focusField = "username";
}
?>
</tr>
<tr height="32px">
<td colspan="2"> </td>
</tr>
<tr>
<td><label for="password"><b>Password:</b></label></td>
<td><input name="password" type="password" size="42" tabindex="2"></input></td>
</tr>
<tr height="22px">
<td colspan="2"> </td>
</tr>
<tr>
<td colspan="2" align="right"><button type="submit" value="Login">Login</button></td>
</tr>
</table>
</form>
<?php echo "<script type=\"text/javascript\">\n"
	. "document.loginForm." . $focusField . ".focus();\n"
	. "</script>";
?>
</div>
</div>
</div>
</div>
</body>
</html>
