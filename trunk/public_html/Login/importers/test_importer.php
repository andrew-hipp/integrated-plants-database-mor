<?php
$app_root = realpath('../..');

include 'hrb_accession_importer.php';
include 'hrb_accom_importer.php';
include 'hrb_annotation_importer.php';
include 'lc_accession_importer.php';
include 'lc_accom_importer.php';
include 'lc_plantcom_importer.php';
include 'lc_plants_importer.php';
include 'sciname_comments_importer.php';
include 'sciname_importer.php';

function serverError($message) {
	die("Internal Server Error: $message");
}

$importerNames = array('hrb_accession', 'hrb_accom', 'hrb_annotation', 'lc_accession', 'lc_accom', 'lc_plantcom', 'lc_plants', 'sciname_comments', 'sciname');

$importState = array();

foreach ($importerNames as $importerName) {
	$testDB = $importerName . '_test';
	
	$importer = null;
	switch ($importerName)
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
	
	$importState[$importerName] = $importer->startImport($testDB);
	
	if (!$importState[$importerName]) {
		echo $importerName . ' importer failed to start.';
	}
}

foreach ($importerNames as $importerName) {
	$importState[$importerName] = false;
}

$allImportersFinished = false;
while (!$allImportersFinished) {
	$allImportersFinished = true;
	foreach ($importerNames as $importerName) {
		$importer = null;
		switch ($importerName)
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
	
		if (!$importState[$importerName]) {
			$allImportersFinished = false;
			$importState[$importerName] = $importer->continueImport(5.0);
		}
	}
}

foreach ($importerNames as $importerName) {
	$importer = null;
	switch ($importerName)
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
	
	if ($importState[$importerName]) {
		$importState[$importerName] = $importer->finishImport();
	
		if (!$importState[$importerName]) {
			echo $importerName . ' importer failed to finish.';
		}
	}
}
?>
	
