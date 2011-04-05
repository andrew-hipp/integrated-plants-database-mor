<?php
$app_root = realpath('..');
require_once $app_root . '/modules/page_intro_common.php';
require $app_root . '/Login/auth.php';


// OK, we either have a valid login here or not.  If we don't, then bring up the
// login page.
if (!$bypass_auth && $_SESSION['username'] == '') {
	session_destroy();

	$proto = 'http';
	if ($_SERVER["SERVER_PORT"] == 443)
	$proto = 'https';
	header('Location: ' . $proto . '://' . $_SERVER["HTTP_HOST"]
	. dirname($_SERVER["REQUEST_URI"]) . '/');
	return;
}
if (isset($_POST['page'])) {
	switch ($_POST['page']) {
		case 'dashboard':
			// This IS the dashboard!
			break;
				
		case 'logout':
			session_destroy();
			header('Location: .');
			exit();
			break;
	}
}

require_once $app_root . '/Login/importers/import_dir_common.php';
require_once $app_root . '/Login/importers/import_all.php';

// Show the Dashboard page.
function doTab($tabName, $tabOperation, $isCurrent) {
	if ($isCurrent) {
		echo "				<div class=\"currentTab\">" . $tabName . "</div>\n";
	} else {
		echo "				<div class=\"otherTab\" onClick=\"javascript:gotoPage(document.gotoPageForm, '" . $tabOperation . "');\">" . $tabName . "</div>\n";
	}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
      "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>Morton Arboretum</title>
<link rel="stylesheet" type="text/css" href="../libraries/main.php" />
<script type="text/javascript" src="../libraries/main.js"></script>
<?php
include $app_root . '/config.inc.php';

$db_names = array();

$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

$isImporterMachine = false;

if (mysqli_connect_error() == 0) {
	if ($stmt = $dbLink->prepare('SELECT value FROM longProcess WHERE `key`=\'importPost\' AND `subkey`=\'db_name\'')) {
		$stmt->execute();

		$stmt->bind_result($value);

		if ($stmt->fetch()) {
			$db_names = explode('/', $value);
		}

		$stmt->close();
	}
	
	if ($stmt = $dbLink->prepare('SELECT value FROM longProcess WHERE `key`=\'importPost\' AND `subkey` = \'importHost\'')) {
		$stmt->execute();
		
		$stmt->bind_result($value);
		
		if ($stmt->fetch()) {
			$isImporterMachine = true;
		}
		
		$stmt->close();
	}

	$dbLink->close();
}

if (isset($_POST['action'])) {
	if (isset($_POST['db_name'])) {
		$db_names = array($_POST['db_name']);
		$isImporterMachine = true;
	} else if ($_POST['action'] == 'import_all') {
		$db_names = ImportCommon::dbFileNames();
		$isImporterMachine = true;
	}
}

if (isset($_POST['action']) || (count($db_names) > 0)) {
	?>
<script language="JavaScript">
<!--
function updateProgressForNameInID(aDBName, aStatusID) {
	var client = new XMLHttpRequest();
	client.open("GET", "<?php echo getRootUrl(); ?>/Login/importers/get_import_progress_for_file.php?db_name=" + aDBName, false);
	client.send(null);
	var retVal = false;
	if (client.status == 200) {
		if (client.responseText == "N/A") {
			statusStr = "Importing " + aDBName + " (0% complete)";
			ReplaceContentInContainer(aStatusID, statusStr);
			retVal = false;
		} else if (client.responseText != "error") {
			statusStr = "Importing " + aDBName + " (" + client.responseText + "% complete)";
			ReplaceContentInContainer(aStatusID, statusStr);
			retVal = (client.responseText == "100.0");
		}
	} else {
		retVal = true;
	}
	return retVal;
}
function updateProgress() {
<?php

$updVars = array();
$idx = 1;
foreach ($db_names as $db_name) {
	echo '	updatesComplete' . $idx . ' = updateProgressForNameInID("' . $db_name . '", "' . ImportCommon::dbTableNameFromFileName($db_name) . '");' . "\n";
	$updVars[] = 'updatesComplete' . $idx;
	$idx++;
}
?>
	if (<?php echo implode(' && ', $updVars); ?>) {
		var t = setTimeout("location.reload()", 1000);
	} else {
		var t = setTimeout("updateProgress()", 1000);
	}
}
<?php
if ($isImporterMachine) {
?>
var req = null;
function continueImportDone() {
	// only if req shows "loaded"
	if (req.readyState == 4) {
		var importDone = false;
		
		// only if "OK"
		if (req.status == 200) {
			if (req.responseText == "Done") {
				importDone = true;
			}
		}

		if (importDone) {
			var t = setTimeout("location.reload()", 1000);
		} else {
			var t = setTimeout("continueImport()", 100);
		}
	}
}
function continueImport() {
	req = new XMLHttpRequest();
	req.onreadystatechange = continueImportDone;
	req.open("GET", "<?php echo getRootUrl(); ?>/Login/importers/continue_import_progress.php", true);
	req.send(null);
}
<?php
}
?>
function startUpdateProcess() {
	var t = setTimeout("updateProgress()", 1000);
<?php
if ($isImporterMachine) {
?>	var t2 = setTimeout("continueImport()", 1000);
<?php
}
?>}
//-->
  </script>
<?php
}
?>
</head>
<body<?php if (isset($_POST['action']) || (count($db_names) > 0)) { echo ' onLoad="javascript:startUpdateProcess();"'; } ?>>
<form name="gotoPageForm" action="dashboard.php" method="post">
<input type="hidden" name="page" value="" />
</form>
	<?php
	$pageLocation = "Administration";
	$noSearch = true;
	require_once $app_root . '/modules/header_common.php';
	?>
<div id="body" class="login_body">
<div id="content" class="content">
<div width="100%">
<div class="leftSideTab">
<div class="preTab">&nbsp;</div>
	<?php
	doTab('Import Databases', 'import_databases', true);
	?></div>
</div>
<div class="rightSideTab">
<div class="preTab">&nbsp;</div>
	<?php
	doTab('Log out', 'logout', false);
	?>
<div class="postTab">&nbsp;</div>
</div>
<div class="underTabs">&nbsp;</div>
<div><?php
$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

if (mysqli_connect_error() == 0) {
	if (isset($_POST['db_name'])) {
		$myPost['db_name'] = $_POST['db_name'];
	}
	if (isset($_POST['email'])) {
		$myPost['email'] = $_POST['email'];
	}
	if (isset($_POST['action'])) {
		$myPost['action'] = $_POST['action'];
		if ($myPost['action'] == 'import_all') {
			$myPost['db_name'] = implode('/', ImportCommon::dbFileNames());
		}
	}
	else if ($stmt = $dbLink->prepare('SELECT `subkey`, `value` FROM longProcess WHERE `key`=\'importPost\'')) {
		$stmt->execute();

		$stmt->bind_result($subkey, $value);

		while ($stmt->fetch()) {
			$myPost[$subkey] = $value;
		}

		$stmt->close();
	}
	if (isset($myPost['action'])) {
		foreach (explode('/', $myPost['db_name']) as $db_name) {
			echo '			<div id="' . ImportCommon::dbTableNameFromFileName($db_name) . '">Importing ' . $db_name . '</div>' . "\n";
		}
	} elseif (isset($_POST['do_now'])) {
		recreate_lc_images($dbLink);
		echo '			Re-built Living Collection image database' . "\n";
	} else {
		?>
<form name="import_db"
	action="<?php echo getRootUrl(); ?>/Login/dashboard.php" method="post">
<fieldset
	style="margin-top: 8px; padding: 2px; padding-left: 8px; padding-right: 8px;">
<input type="hidden" name="action" value="import_one" /> <label
	for="db_name">Available Databases:</label> <select id="db_name"
	name="db_name">
	<option value=""></option>
	<?php
	$dbImportDir = ImportCommon::dbExportDirPath();

	if (($dhand = opendir($dbImportDir)) !== false) {
		$dbFileNames = ImportCommon::dbFileNames();
		while (false !== ($file = readdir($dhand))) {
			if (($file == '.') || ($file == '..')) {
				continue;
			}
			$path = $dbImportDir . '/' . $file;
			if (is_dir($path)) {
				continue;
			}
			if (in_array($file, $dbFileNames)) {
				echo '						<option value="' . $file . '">' . $file . '</option>' . "\n";
			}
		}
			
		closedir($dhand);
	}
	?>
</select> <label for="email">E-mail address to mail when complete:</label>
<input type="text" name="email" /> <input type="submit"
	value="Import" /></fieldset>
</form>
<form name="import_all_db"
	action="<?php echo getRootUrl(); ?>/Login/dashboard.php" method="post">
<fieldset
	style="margin-top: 8px; padding: 2px; padding-left: 8px; padding-right: 8px;">
<input type="hidden" name="action" value="import_all" /> <label
	for="email">E-mail address to mail when complete:</label> <input
	type="text" name="email" /> <input type="submit"
	value="Import All" /></fieldset>
</form>
<form name="update_images"
	action="<?php echo getRootUrl(); ?>/Login/dashboard.php" method="post">
<fieldset
	style="margin-top: 8px; padding: 2px; padding-left: 8px; padding-right: 8px;">
<input type="hidden" name="do_now" value="update_images" /><input type="submit" value="Update Living Collection Images" />
</fieldset>
</form>
	<?php
	}
	?></div>
</div>
	<?php
	if (isset($_POST['action'])) {
		if ($stmt = $dbLink->prepare('INSERT INTO longProcess (`key`, `subkey`, `value`) VALUES (\'importPost\', \'action\', ?), (\'importPost\', \'email\', ?), (\'importPost\', \'db_name\', ?), (\'importPost\', \'importHost\', ?)')) {
			$stmt->bind_param('ssss', $myPost['action'], $myPost['email'], $myPost['db_name'], $_SERVER['REMOTE_ADDR']);

			$stmt->execute();

			$stmt->close();
		}
		if ($stmt = $dbLink->prepare('INSERT INTO longProcess (`key`, `subkey`, `value`) VALUES (\'importPost\', \'ind_db_name\', ?)')) {
			$stmt->bind_param('s', $db_name);
			foreach (explode('/', $myPost['db_name']) as $db_name) {
				$stmt->execute();
			}

			$stmt->close();
		}
	}

	$dbLink->close();
}
?></body>
</html>
<?php
if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case 'import_one':
			start_import_one_file($_POST['db_name']);
			break;
				
		case 'import_all':
			start_import_all();
			break;
	}
}
?>
