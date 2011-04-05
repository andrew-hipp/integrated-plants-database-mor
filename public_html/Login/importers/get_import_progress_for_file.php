<?php
$app_root=realpath('../..');
require_once $app_root . '/modules/page_intro_common.php';

include $app_root . '/config.inc.php';

$retVal = 'N/A';
if (isset($_REQUEST['db_name'])) {
	$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

	if (mysqli_connect_error() == 0) {
		if ($stmt = $dbLink->prepare('SELECT value FROM longProcess WHERE `key`=? AND `subkey`=\'importComplete\'')) {
			$stmt->bind_param('s', $_REQUEST['db_name']);
			
			$stmt->execute();
			
			$stmt->bind_result($value);
			
			if ($stmt->fetch()) {
				if ($value == 1) {
					$retVal = '100.0';
				} else {
					$retVal = 'error';
				}
			}
			
			$stmt->close();
		}
		
		if (($retVal == 'N/A') && ($stmt = $dbLink->prepare('SELECT value FROM longProcess WHERE `key`=? AND `subkey`=\'importProgress\''))) {
			$stmt->bind_param('s', $_REQUEST['db_name']);
			
			$stmt->execute();
			
			$stmt->bind_result($value);
			
			if ($stmt->fetch()) {
				$retVal = $value;
			}
		}
		
		$dbLink->close();
	}
}

echo $retVal;
