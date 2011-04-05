<?php
include $app_root . '/Login/importers/hrb_accession_importer.php';
include $app_root . '/Login/importers/hrb_accom_importer.php';
include $app_root . '/Login/importers/hrb_annotation_importer.php';
include $app_root . '/Login/importers/lat_long_importer.php';
include $app_root . '/Login/importers/lc_accession_importer.php';
include $app_root . '/Login/importers/lc_accom_importer.php';
include $app_root . '/Login/importers/lc_plantcom_importer.php';
include $app_root . '/Login/importers/lc_plants_importer.php';
include $app_root . '/Login/importers/sciname_comments_importer.php';
include $app_root . '/Login/importers/sciname_importer.php';

function start_import_one_file($filename) {
	$names = array(
				'hrb_accession',
				'hrb_accom',
				'hrb_annotation',
				'lat_long',
				'lc_accession',
				'lc_accom',
				'lc_plantcom',
				'lc_plants',
				'sciname_comments',
				'sciname'
			);
	foreach ($names as $name) {
		$importer = null;
		
		switch ($name)
		{
			case 'hrb_accession':
				$importer = new hrb_accession_importer();
				break;
				
			case 'hrb_accom':
				$importer = new hrb_accom_importer();
				break;
				
			case 'hrb_annotation':
				$importer = new hrb_annotation_importer();
				break;
				
			case 'lat_long':
				$importer = new lat_long_importer();
				break;
				
			case 'lc_accession':
				$importer = new lc_accession_importer();
				break;
				
			case 'lc_accom':
				$importer = new lc_accom_importer();
				break;
				
			case 'lc_plantcom':
				$importer = new lc_plantcom_importer();
				break;
				
			case 'lc_plants':
				$importer = new lc_plants_importer();
				break;
				
			case 'sciname_comments':
				$importer = new sciname_comments_importer();
				break;
				
			case 'sciname':
				$importer = new sciname_importer();
				break;
		}
		
		if ($filename == $importer->getExportFileName()) {
			return start_import_one($name, $filename);
		}
	}
	
	return false;
}

function start_import_one($name) {
	global $app_root;
	include $app_root . '/config.inc.php';
	
	$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

	if (mysqli_connect_error()) {
		return false;
	}
	
	switch ($name)
	{
		case 'hrb_accession':
			$importer = new hrb_accession_importer();
			break;
			
		case 'hrb_accom':
			$importer = new hrb_accom_importer();
			break;
			
		case 'hrb_annotation':
			$importer = new hrb_annotation_importer();
			break;
			
		case 'lat_long':
			$importer = new lat_long_importer();
			break;
			
		case 'lc_accession':
			$importer = new lc_accession_importer();
			break;
			
		case 'lc_accom':
			$importer = new lc_accom_importer();
			break;
			
		case 'lc_plantcom':
			$importer = new lc_plantcom_importer();
			break;
			
		case 'lc_plants':
			$importer = new lc_plants_importer();
			break;
			
		case 'sciname_comments':
			$importer = new sciname_comments_importer();
			break;
			
		case 'sciname':
			$importer = new sciname_importer();
			break;
	}
	
	if ($stmt = $dbLink->prepare('DELETE FROM `longProcess` WHERE `key` = \'importTable\''
		. ' AND `subkey` LIKE \'' . $_SERVER['REMOTE_ADDR'] . '-%\''
		. ' AND `value` = \'' . $name . '\'')) {
		$stmt->execute();
		$stmt->close();
	}
	
	$retVal = false;
	
	if ($importer->startImport($name . '_import')) {
		$insProg = 'INSERT INTO `longProcess` (`key`,`subkey`,`value`) VALUES (\'importTable\', \''
			. $_SERVER['REMOTE_ADDR'] . '-1\', \'' . $name . '\')';
		if ($progressStmt = $dbLink->prepare($insProg)) {
			$progressStmt->execute();
			$progressStmt->close();
		}
		$retVal = true;
	}
	
	$dbLink->close();
	
	return $retVal;
}

function start_import_all() {
	$names = array(
				'hrb_accession',
				'hrb_accom',
				'hrb_annotation',
				'lat_long',
				'lc_accession',
				'lc_accom',
				'lc_plantcom',
				'lc_plants',
				'sciname_comments',
				'sciname'
			);
	
	foreach ($names as $name) {
		if (!start_import_one($name))
			return false;
	}
	
	return true;
}

function continue_imports() {
	global $app_root;
	include $app_root . '/config.inc.php';
	
	$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

	if (mysqli_connect_error()) {
		return false;
	}
	
	$tableNames = array();
	
	$srch = 'SELECT `value` FROM `longProcess` WHERE `key` = \'importTable\''
		. ' AND `subkey` = \'' . $_SERVER['REMOTE_ADDR'] . '-1\'';
	if ($stmt = $dbLink->prepare($srch)) {
		$stmt->execute();
		$stmt->bind_result($tableName);
		
		while ($stmt->fetch()) {
			$tableNames[] = $tableName;
		}
		
		$stmt->close();
	}
	
	if (count($tableNames) == 0) {
		$dbLink->close();
		return true;
	}
	
	$endTime = time() + 15;
	foreach ($tableNames as $tableName) {
		$importer = null;
		
		switch ($tableName) {
			case 'hrb_accession':
				$importer = new hrb_accession_importer();
				break;
				
			case 'hrb_accom':
				$importer = new hrb_accom_importer();
				break;
				
			case 'hrb_annotation':
				$importer = new hrb_annotation_importer();
				break;
				
			case 'lat_long':
				$importer = new lat_long_importer();
				break;
				
			case 'lc_accession':
				$importer = new lc_accession_importer();
				break;
				
			case 'lc_accom':
				$importer = new lc_accom_importer();
				break;
				
			case 'lc_plantcom':
				$importer = new lc_plantcom_importer();
				break;
				
			case 'lc_plants':
				$importer = new lc_plants_importer();
				break;
				
			case 'sciname_comments':
				$importer = new sciname_comments_importer();
				break;
				
			case 'sciname':
				$importer = new sciname_importer();
				break;
		}
		
		if ($importer != null) {
			if ($importer->continueImport($endTime - time())) {
				if ($stmt = $dbLink->prepare('UPDATE `longProcess` SET `subkey`=\'' . $_SERVER['REMOTE_ADDR'] . '-2\''
					. ' WHERE `key`=\'importTable\' AND `subkey`=\'' . $_SERVER['REMOTE_ADDR'] . '-1\''
					. ' AND `value`=\'' . $tableName . '\'')) {
					$stmt->execute();
					$stmt->close();
				}
			}
		}
		
		if (time() >= $endTime) {
			break;
		}
	}

	$dbLink->close();
	
	return false;
}

function finish_imports() {
	global $app_root;
	include $app_root . '/config.inc.php';
	
	$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

	if (mysqli_connect_error()) {
		return false;
	}
	
	$tableNames = array();
		
	if ($stmt = $dbLink->prepare('SELECT `value` FROM `longProcess` WHERE `key` = \'importTable\''
		. ' AND `subkey` = \'' . $_SERVER['REMOTE_ADDR'] . '-2\'')) {
		$stmt->execute();
		$stmt->bind_result($tableName);
		
		while ($stmt->fetch()) {
			$tableNames[] = $tableName;
		}
		
		$stmt->close();
	}
	
	if (count($tableNames) == 0) {
		$emailAddress = '';
		if ($stmt = $dbLink->prepare('SELECT `value` FROM `longProcess` WHERE `key`=\'importPost\' AND `subkey`=\'email\'')) {
			$stmt->execute();
			$stmt->bind_result($emailAddress);
			
			if (!$stmt->fetch()) {
				$emailAddress = '';
			}
			
			$stmt->close();
		}
		if ($stmt = $dbLink->prepare('DELETE FROM `longProcess` WHERE `key`=\'importPost\'')) {
			$stmt->execute();
			$stmt->close();
		}
		
		$dbLink->close();

		if ($emailAddress != '') {
			mail($emailAddress,
				'Morton Import',
				'Import is complete at ' . date('g:i:s A T \o\n j-M-Y'),
				'From: noreply@mortonarb.org');
		}

		return true;
	}
	
	$endTime = time() + 15;
	foreach ($tableNames as $tableName) {
		$importer = null;
		
		switch ($tableName) {
			case 'hrb_accession':
				$importer = new hrb_accession_importer();
				break;
				
			case 'hrb_accom':
				$importer = new hrb_accom_importer();
				break;
				
			case 'hrb_annotation':
				$importer = new hrb_annotation_importer();
				break;
				
			case 'lat_long':
				$importer = new lat_long_importer();
				break;
				
			case 'lc_accession':
				$importer = new lc_accession_importer();
				break;
				
			case 'lc_accom':
				$importer = new lc_accom_importer();
				break;
				
			case 'lc_plantcom':
				$importer = new lc_plantcom_importer();
				break;
				
			case 'lc_plants':
				$importer = new lc_plants_importer();
				break;
				
			case 'sciname_comments':
				$importer = new sciname_comments_importer();
				break;
				
			case 'sciname':
				$importer = new sciname_importer();
				break;
		}
		
		if ($importer != null) {
			if ($importer->finishImport()) {
				if ($stmt = $dbLink->prepare('DELETE FROM `longProcess` WHERE `key`=\'importTable\' AND `subkey`=\'' . $_SERVER['REMOTE_ADDR'] . '-2\'')) {
					$stmt->execute();
					$stmt->close();
				}
			}
		}
		
		if (time() >= $endTime) {
			break;
		}
	}

	$dbLink->close();
	
	return false;
}
?>
