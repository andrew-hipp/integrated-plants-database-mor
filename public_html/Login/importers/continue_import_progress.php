<?php
$app_root = realpath('../..');

include_once $app_root . '/Login/importers/import_all.php';

$retVal = 'Importing';
if (continue_imports()) {
	if (finish_imports()) {
		$retVal = 'Done';
	}
}
echo $retVal;
?>