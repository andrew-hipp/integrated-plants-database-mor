<?php
error_reporting(E_ALL);

$app_root = realpath('.');

include $app_root . '/config.inc.php';
include_once 'libraries/kml.class.php';

if(!isset($_REQUEST['plant_id'])) {
	header('Location: ' . $_SERVER['HTTP_REFERER'] . '&notice=noplants');
	exit("No Plant ID provided.");
}

$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);

if (mysqli_connect_error() == 0) {

	foreach($_REQUEST['plant_id'] as $p)
	{
		$queryStr = null;
		$stmt = null;

		$accessionNo = null;
		$colName = null;
		$scinameId = null;
		$sciname = null;
		$lat = null;
		$long = null;

		$queryStr = "SELECT accession_no, collection_name FROM lc_plants WHERE plant_id = ?";
		if($stmt = $dbLink->prepare($queryStr))
		{
			$stmt->bind_param('s', $p);
			$stmt->execute();

			$stmt->bind_result($accessionNo, $collName);
			$stmt->fetch();

			$places[$p]['collName'] = $collName;
		}
		$queryStr = null;
		$stmt = null;


		$queryStr = "SELECT sciname_id FROM lc_accession WHERE accession_no = ?";
		if($stmt = $dbLink->prepare($queryStr))
		{
			$stmt->bind_param('s', $accessionNo);
			$stmt->execute();

			$stmt->bind_result($scinameId);
			$stmt->fetch();
		}
		$queryStr = null;
		$stmt = null;

		$queryStr = "SELECT sciname.scientific_name, lat_long.latitude, lat_long.longitude "
				. "FROM sciname, lat_long "
				. "WHERE sciname.scientific_name_id = ? AND lat_long.plant_id = ?";
		if($stmt = $dbLink->prepare($queryStr))
		{
			$stmt->bind_param('ss', $scinameId, $p);
			$stmt->execute();

			$stmt->bind_result($sciname, $lat, $long);
			$stmt->fetch();
		
			$places[$p]['id'] = $p;
			$places[$p]['lat'] = $lat;
			$places[$p]['long'] = $long;
		}
	}
}

$kml = new KML('Morton Arboretum');

$kmldoc = new KMLDocument('Living Collection', $sciname);

$plantFolder = new KMLFolder('', 'Plants');

	foreach($places as $i)
	{
		$plant = new KMLPlaceMark($i['id'], $i['id'], $i['collName'], true);
		$plant->setGeometry(new KMLPoint($i['long'], $i['lat'], 0, true, 'relativeToGround'));
		$plantFolder->addFeature($plant);
	}

$kmldoc->addFeature($plantFolder);

$kml->setFeature($kmldoc);

$kml->output('A', 'MortonArb-' . date('Mj-hi') . '.kml');
