<?php
$collections = array();

{
	require $app_root . '/config.inc.php';

	$link = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);
	
	if (mysqli_connect_error() == 0) {
		if ($stmt = $link->prepare('SELECT DISTINCT collection_name FROM lc_plants ORDER BY collection_name;')) {
			$stmt->execute();
			
	   		$stmt->bind_result($collName);
			
	   		while ($stmt->fetch()) {
	   			$collections[] = array('name'=>$collName, 'option'=>strtolower(str_replace(' ', '_', $collName)));
	   		}
	   		
	   		$stmt->close();
		}
		
		$link->close();
	}
}

function buildCollectionsOptionsList($linePrefix) {
	global $collections;
	echo $linePrefix . '<option value=""> </option>' . "\n";
	foreach ($collections as $collectionEntry) {
		echo $linePrefix . '<option value="' . $collectionEntry['option'] . '"' . '>'
				. $collectionEntry['name'] . '</option>' . "\n";
	}
}

function collectionNameForOption($collectionOption) {
	global $collections;
	foreach ($collections as $collectionEntry) {
		if ($collectionEntry['option'] == $collectionOption) {
			return $collectionEntry['name'];
		}
	}
	return '';
}
?>
