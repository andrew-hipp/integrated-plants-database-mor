<?php

require_once $app_root . '/modules/header_common.php';

function sciname_null($resultData, $fieldName) {
	if (!isNull($resultData[$fieldName])) {
		return $resultData[$fieldName];
	} elseif (false) {
		return 'NULL(' . $fieldName . ')';
	}
}

function sciname_name($sciname) {
	echo '<b>';
	switch ($sciname['genus_designator']) {
		case 'h':
		case 'x':
			echo 'x';
			
		default:
			echo sciname_null($sciname, 'genus_name');
			break;
	}
	switch ($sciname['species_designator']) {
		case 'h':
			echo '(';
			break;
			
		case 'x':
			echo 'x';
			break;
			
		default:
			break;
	}
	echo '</b>';
	echo ' <b>' . sciname_null($sciname, 'species_name') . '</b> ';
	echo '<b>';
	switch ($sciname['species_designator']) {
		case 'h':
			echo ') ';
			break;
			
		default:
			echo ' ';
			break;
	}
	echo '</b>';
}

function sciname_long($resultData, $sciname, $displayImage = false, $linkToLCDetails = false) {
	if ($displayImage && isset($sciname['image_thumb']) && isset($sciname['image_large'])) {
		echo '<table border="0"><tr valign="top"><td>';
	}
	// "PLEASE OBSERVE ITALICIZED ITEMS IN THE FOLLOWING:
	// (if genus_designator = ""h"" or ""x"", 'x') + /genus_name/ + "" ""
	// + (if species_designator = ""h"" or ""x"", 'x') + /species_name/
	// + (if subspecies_name not NULL, ' ssp. ' else "" "") + /subspecies_name/
	// + (if variety_name not NULL, ' var. ' else "" "") + /variety_name/
	// + (if forma_name not NULL, ' f. ' else "" "") + /forma_name/
	// + (if cultivar_name not NULL, space + single quote, else "" "") + cultivar_name
	// + (if cultivar_name not NULL, single quote + space) + author_name
	// + (if plant_patent_no is not NULL plant_patent_no + "" "" else "" "") + family_name
	// + "" - "" + family_common_name + LINEBREAK
	// + (if trademark is not NULL {""Trade Names: "" + trademark + LINEBREAK})
	// + (if common_names is not NULL {""Common Names: "" + common_names + LINEBREAK})
	// + (if range is not NULL {""Range: "" + range + LINEBREAK)
	// + (if usda_zone_lo is not NULL {""USDA Hardiness Zone: "" + usda_zone_lo + "" - "" + usda_zone_hi + "". ""}) "
	
	if ($linkToLCDetails) {
		echo '<a href="' . getRootUrl() . '/details_living_coll.php?sciname=' . urlencode($sciname['scientific_name_id']) . '">';
	}
	echo '<b>';
	switch ($sciname['genus_designator']) {
		case 'h':
		case 'x':
			echo 'x';
			
		default:
			echo ' <b>' . sciname_null($sciname, 'genus_name') . '</b> ';
			break;
	}
	switch ($sciname['species_designator']) {
		case 'h':
			echo '(';
			break;
			
		case 'x':
			echo 'x';
			break;
			
		default:
			break;
	}
	echo '</b>';
	echo ' <b>' . sciname_null($sciname, 'species_name') . ' </b>';
	switch ($sciname['species_designator']) {
		case 'h':
			echo ')';
			break;
			
		default:
			break;
	}
	if (!isNull($sciname['subspecies_name'])) {
		echo ' ssp. <b>' . $sciname['subspecies_name'] . ' </b>';
	}
	echo ' ';
	if (!isNull($sciname['variety_name'])) {
		echo 'var. <b>' . $sciname['variety_name'] . ' </b> ';
	}
	if (!isNull($sciname['forma_name'])) {
		echo 'f. <b>' . $sciname['forma_name'] . ' </b> ';
	}
	if (!isNull($sciname['cultivar_name'])) {
		echo '<b>\'' . $sciname['cultivar_name'] . '\' </b>';
	}
	if (!isNull($sciname['author_name'])) {
		echo '<b>'.$sciname['author_name'].'</b>';
	}
	if ($linkToLCDetails) {
		echo '</a> ';
	} else {
		echo ' ';
	}
	echo '<b>';  // start bold
	if (!isNull($sciname['plant_patent_no'])) {
		echo '<br />Plant patent: ' . $sciname['plant_patent_no'] . ' ';
	}
	if (!isNull($sciname['family_name']) && !isNull($sciname['family_common_name'])) {
		echo $sciname['family_name'] . ' &mdash; ' . $sciname['family_common_name'] . ' FAMILY';
	}
	echo '<br />';
	if (!isNull($sciname['trademark'])) {
		echo 'Trademark Names: ';
		$lastTwoChars = substr($sciname['trademark'], strlen($sciname['trademark']) - 3, 3); 
		if ($lastTwoChars == ' T ') {
			echo substr($sciname['trademark'], 0, strlen($sciname['trademark']) - 3) . '&trade;';
		} elseif ($lastTwoChars == ' R ') {
			echo substr($sciname['trademark'], 0, strlen($sciname['trademark']) - 3) . '&reg;';
		} else {
			echo $sciname['trademark'];
		}
		echo '<br />';
	}
	if (!isNull($sciname['common_names']) && $sciname['common_names'] != 'null') {
		echo 'Common Names: ' . strtoupper($sciname['common_names']) . '<br />';
	}
	if (!isNull($sciname['range'])) {
		echo 'Range: ' . $sciname['range'] . '<br />';
	}
	if (!isNull($sciname['usda_zone_lo'])) {
		echo 'USDA Hardiness Zone: ' . $sciname['usda_zone_lo'];
		if (!isNull($sciname['usda_zone_hi'])) {
			echo  ' - ' . $sciname['usda_zone_hi'];
		}
		echo  '. ';
	}
	echo '</b>';  // end bold
	if ($displayImage && isset($sciname['image_thumb']) && isset($sciname['image_large'])) {
		echo '</td><td width="10px">';
		// Sciname images are bring up the image browser for this sciname ID
		echo '<a href="' . getRootUrl() . '/image_browser.php?sciname=' . urlencode($sciname['scientific_name_id']) . '"><img src="' . getRootUrl() . '/images/AAA-IMAGE-ICON-cam.gif" /></a>';
		echo '</td></tr></table>';
	}	
}

function sciname_short($resultData, $sciname) {
	// "PLEASE OBSERVE ITALICIZED ITEMS IN THE FOLLOWING:
	// (if genus_designator = ""h"" or ""x"", 'x') + /genus_name/ + "" ""
	// + (if species_designator = ""h"" or ""x"", 'x') + /species_name/
	// + (if subspecies_name not NULL, ' ssp. ' else "" "") + /subspecies_name/
	// + (if variety_name not NULL, ' var. ' else "" "") + /variety_name/
	// + (if forma_name not NULL, ' f. ' else "" "") + /forma_name/
	// + (if cultivar_name not NULL, space + single quote, else "" "") + cultivar_name
	// + (if cultivar_name not NULL, single quote + space) + author_name
	// + (if plant_patent_no is not NULL plant_patent_no + "" "" else "" "") + family_name
	// + "" - "" + family_common_name + LINEBREAK
	// + (if trademark is not NULL {""Trade Names: "" + trademark + LINEBREAK})
	// + (if common_names is not NULL {""Common Names: "" + common_names + LINEBREAK})
	// + (if range is not NULL {""Range: "" + range + LINEBREAK)
	// + (if usda_zone_lo is not NULL {""USDA Hardiness Zone: "" + usda_zone_lo + "" - "" + usda_zone_hi + "". ""}) "
	switch ($sciname['genus_designator']) {
		case 'h':
		case 'x':
			echo 'x';
			
		default:
			echo '<b>' . sciname_null($sciname, 'genus_name') . '</b> ';
			break;
	}
	switch ($sciname['species_designator']) {
		case 'h':
			echo '(';
			break;
			
		case 'x':
			echo 'x';
			break;
			
		default:
			break;
	}
	echo '<b>' . sciname_null($sciname, 'species_name') . '</b>';
	switch ($sciname['species_designator']) {
		case 'h':
			echo ') ';
			break;
			
		default:
			echo ' ';
			break;
	}
	if (!isNull($sciname['subspecies_name'])) {
		echo 'ssp. <b>' . $sciname['subspecies_name'] . '</b> ';
	}
	if (!isNull($sciname['variety_name'])) {
		echo 'var. <b>' . $sciname['variety_name'] . '</b> ';
	}
	if (!isNull($sciname['forma_name'])) {
		echo 'f. <b>' . $sciname['forma_name'] . '</b> ';
	}
	if (!isNull($sciname['cultivar_name'])) {
		echo '<b>\'' . $sciname['cultivar_name'] . '\' </b>';
	}
	if (!isNull($sciname['author_name'])) {
		echo '<b>'.$sciname['author_name'] . '</b> ';
	}
	if (!isNull($sciname['plant_patent_no'])) {
		echo '<b>Plant patent: ' . $sciname['plant_patent_no'] . '</b> ';
	}
	if (!isNull($sciname['family_name']) && !isNull($sciname['family_common_name'])) {
		echo '<b>'.$sciname['family_name'] . ' &mdash; ' . $sciname['family_common_name'] . ' FAMILY </b>';
	}
	echo '<br />';
	if (!isNull($sciname['usda_zone_lo'])) {
		echo '<b>USDA zone ' . $sciname['usda_zone_lo'];
		if (!isNull($sciname['usda_zone_hi'])) {
			echo ' - ' . $sciname['usda_zone_hi'];
		}
		echo '. </b>';
	}
	if (!isNull($sciname['common_names'])) {
		echo $sciname['common_names'];
	}
	if (!isNull($sciname['trademark'])) {
		echo '<b>Trademark Names: ';
		$lastTwoChars = substr($sciname['trademark'], strlen($sciname['trademark']) - 3, 3); 
		if ($lastTwoChars == ' T ') {
			echo substr($sciname['trademark'], 0, strlen($sciname['trademark']) - 3) . '&trade;';
		} elseif ($lastTwoChars == ' R ') {
			echo substr($sciname['trademark'], 0, strlen($sciname['trademark']) - 3) . '&reg;';
		} else {
			echo $sciname['trademark'];
		}
		echo '</b>';
	}
}