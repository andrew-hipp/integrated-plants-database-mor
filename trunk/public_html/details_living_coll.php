<?php
$app_root = realpath('.');
require_once $app_root . '/modules/page_intro_common.php';
require_once $app_root . '/config.inc.php';

$results = array();
$scinames = array();
if ((!isset($_REQUEST['sciname']) && isset($_REQUEST['id']) && ($_REQUEST['id'] != '')
	|| (!isset($_REQUEST['id']) && isset($_REQUEST['sciname']) && ($_REQUEST['sciname'] != '')))) {
	$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);
	
	if (mysqli_connect_error() == 0) {
		$stmt = null;
		$querySciname = null;
		$queryStr = '';
		$queryValue = '';
		if (isset($_REQUEST['id'])) {
			$queryStr = 'SELECT lc_accession.id, lc_accession.accession_no, lc_accession.provenance, sciname.scientific_name,'
				. ' sciname.common_names, lc_accession.how_received, lc_accession.source_name, lc_accession.collector_name,'
				. ' lc_accession.collector_site, lc_accession.collector_no, lc_accession.collector_subcountry_2, lc_accession.collector_subcountry_2_dsg,'
				. ' lc_accession.collector_subcountry_1, lc_accession.collector_subcountry_1_dsg, lc_accession.collector_country,'
				. ' lc_accession.collector_habitat, lc_accession.collector_lat, lc_accession.collector_lat_dsg, lc_accession.collector_long,'
				. ' lc_accession.collector_long_dsg, lc_accession.collector_elevation_low, lc_accession.collector_elevation_high, lc_accession.collector_elevation_units,'
				. ' lc_accession.no_received, lc_accession.received_sciname, lc_accession.understock, lc_accession.collector_township_range, '
				. ' lc_accession.collector_date, '
				. ' sciname.id, sciname.scientific_name, sciname.sort_scientific_name, sciname.family_name, sciname.common_names, sciname.usda_zone_lo,'
				. ' sciname.usda_zone_hi, sciname.genus_designator, sciname.genus_name, sciname.species_designator,'
				. ' sciname.species_name, sciname.subspecies_name, sciname.variety_name, sciname.forma_name, sciname.cultivar_name,'
				. ' sciname.author_name, sciname.plant_patent_no, sciname.family_common_name, sciname.trademark, sciname.range'
				. ' FROM lc_accession, sciname'
				. ' WHERE lc_accession.sciname_id = sciname.scientific_name_id AND lc_accession.accession_no = ?';
			$queryValue = $_REQUEST['id'];
		} elseif (isset($_REQUEST['sciname'])) {
			$queryStr = 'SELECT lc_accession.id, lc_accession.accession_no, lc_accession.provenance, sciname.scientific_name,'
				. ' sciname.common_names, lc_accession.how_received, lc_accession.source_name, lc_accession.collector_name,'
				. ' lc_accession.collector_site, lc_accession.collector_no, lc_accession.collector_subcountry_2, lc_accession.collector_subcountry_2_dsg,'
				. ' lc_accession.collector_subcountry_1, lc_accession.collector_subcountry_1_dsg, lc_accession.collector_country,'
				. ' lc_accession.collector_habitat, lc_accession.collector_lat, lc_accession.collector_lat_dsg, lc_accession.collector_long,'
				. ' lc_accession.collector_long_dsg, lc_accession.collector_elevation_low, lc_accession.collector_elevation_high, lc_accession.collector_elevation_units,'
				. ' lc_accession.no_received, lc_accession.received_sciname, lc_accession.understock, lc_accession.collector_township_range, '
				. ' lc_accession.collector_date, '
				. ' sciname.id, sciname.scientific_name, sciname.sort_scientific_name, sciname.family_name, sciname.common_names, sciname.usda_zone_lo,'
				. ' sciname.usda_zone_hi, sciname.genus_designator, sciname.genus_name, sciname.species_designator,'
				. ' sciname.species_name, sciname.subspecies_name, sciname.variety_name, sciname.forma_name, sciname.cultivar_name,'
				. ' sciname.author_name, sciname.plant_patent_no, sciname.family_common_name, sciname.trademark, sciname.range'
				. ' FROM lc_accession, sciname'
				. ' WHERE lc_accession.sciname_id = sciname.scientific_name_id AND lc_accession.sciname_id = ?';
			$queryValue = urldecode($_REQUEST['sciname']); 
		}
		
		if ($stmt = $dbLink->prepare($queryStr)) {
			$stmt->bind_param('s', $queryValue);
			$stmt->execute();
			
			$stmt->bind_result($fetchResultData['lc_id'], $fetchResultData['lc_accession_no'], $fetchResultData['lc_provenance'], $sciname['scientific_name'], $sciname['common_names'],
					$fetchResultData['lc_how_received'], $fetchResultData['lc_source_name'], $fetchResultData['lc_collector_name'], $fetchResultData['lc_collector_site'], $fetchResultData['lc_collector_no'],
					$fetchResultData['lc_collector_subcountry_2'], $fetchResultData['lc_collector_subcountry_2_dsg'], $fetchResultData['lc_collector_subcountry_1'],
					$fetchResultData['lc_collector_subcountry_1_dsg'], $fetchResultData['lc_collector_country'], $fetchResultData['lc_collector_habitat'],
					$fetchResultData['lc_collector_lat'], $fetchResultData['lc_collector_lat_dsg'], $fetchResultData['lc_collector_long'],
					$fetchResultData['lc_collector_long_dsg'], $fetchResultData['lc_collector_elevation_low'], $fetchResultData['lc_collector_elevation_high'],
					$fetchResultData['lc_collector_elevation_units'],
					$fetchResultData['lc_no_received'], $fetchResultData['lc_received_sciname'], $fetchResultData['lc_understock'], $fetchResultData['lc_collector_township_range'],
					$fetchResultData['lc_collector_date'], 
					$fetchResultData['sciname_id'],
					$fetchSciname['scientific_name'], $fetchSciname['sort_scientific_name'], $fetchSciname['family_name'], $fetchSciname['common_names'], $fetchSciname['usda_zone_lo'],
					$fetchSciname['usda_zone_hi'], $fetchSciname['genus_designator'], $fetchSciname['genus_name'],
					$fetchSciname['species_designator'], $fetchSciname['species_name'], $fetchSciname['subspecies_name'],
					$fetchSciname['variety_name'], $fetchSciname['forma_name'], $fetchSciname['cultivar_name'],
					$fetchSciname['author_name'], $fetchSciname['plant_patent_no'], $fetchSciname['family_common_name'],
					$fetchSciname['trademark'], $fetchSciname['range']); 
			
			while ($stmt->fetch()) {
				unset($resultData);
				foreach ($fetchResultData as $key => $data) {
					$resultData[$key] = $data;
				}
				unset($sciname);
				foreach ($fetchSciname as $key => $data) {
					$sciname[$key] = $data;
				}
				$resultData['db'] = 'lc';
				$resultData['lc_how_received'] = ucfirst(strtolower($resultData['lc_how_received']));
				$resultData['wild_collected'] = false;
				switch ($resultData['lc_provenance']) {
					case 'W':
					case 'Z':
						$resultData['origin_collected'] = 'Wild collected ';
						$resultData['wild_collected'] = true;
						break;
						
					default:
						$resultData['origin_collected'] = '';
				}
				$resultData['collector_location'] = '';
				if (!isNull($resultData['lc_collector_subcountry_2'])) {
					$resultData['collector_location'] .= trim($resultData['lc_collector_subcountry_2']); 
					if (!isNull($resultData['lc_collector_subcountry_2_dsg'])) {
						$resultData['collector_location'] .= ' ' . $resultData['lc_collector_subcountry_2_dsg'];
					}
					$resultData['collector_location'] .= ', ';
				}
				if (!isNull($resultData['lc_collector_subcountry_1'])) {
					$resultData['collector_location'] .= $resultData['lc_collector_subcountry_1']; 
					if (($resultData['lc_collector_subcountry_1_dsg'] != 'State') || ($resultData['lc_collector_country'] != 'U.S.A.')) {
						$resultData['collector_location'] .=  ' ' . trim($resultData['lc_collector_subcountry_1_dsg']);
					}
					$resultData['collector_location'] .= ', ';
				}
				if (!isNull($resultData['lc_collector_country'])) {
					$resultData['collector_location'] .= trim($resultData['lc_collector_country']);
				}
				
				$resultData['otherItems'] = array();
				$resultData['relatedHerbarium'] = array();
				
				$results[] = array();
				$resultEntry =& $results[count($results) - 1];
				foreach ($resultData as $key => $data) {
					$resultEntry[$key] = $data;
				}
				if (!isset($scinames[$resultData['sciname_id']])) {
					$scinames[$resultData['sciname_id']] = $sciname;
				}
			}
			
			$stmt->close();
			
			$resultIdx = 0;
			while ($resultIdx < count($results)) {
				$resultData =& $results[$resultIdx];
				$resultIdx++;
				if ($stmt2 = $dbLink->prepare('SELECT lc_plants.plant_id, collection_preposition, collection_name, grid_loc, coord_loc, annotation, subarea1, subarea2, subarea3, no_grid, hide_location, lat_long.latitude, lat_long.longitude FROM lc_plants LEFT JOIN lat_long ON lc_plants.plant_id = lat_long.plant_id WHERE accession_no = ? ORDER BY collection_preposition, collection_name, subarea1')) {
					$stmt2->bind_param('s', $resultData['lc_accession_no']);
					
					$stmt2->execute();
					
					$stmt2->bind_result($plantId, $collPrep, $collName, $gridLoc, $coordLoc, $annotation, $subarea1, $subarea2, $subarea3, $noGrid, $hideLoc, $lat, $long);

					$dataIdx = 0;
					while ($stmt2->fetch()) {
						unset($gmapsLink);
						unset($kmlCheckbox);
						$filteredSciname = preg_replace(array('/\(/', '/\)/'), array('[', ']'), $scinames[$resultData['sciname_id']]['scientific_name']);
						if(!isNull($lat) && !isNull($long))
						{
							$gmapsLink = '(<a href="http://maps.google.com/maps?q=' . $lat . ',' . $long . urlencode(' (' . $filteredSciname . ', ' . $plantId . ' - ' . $collName . ' - ' . $gridLoc . ')') . '&t=h&z=21&output=embed">View on Google Maps</a>)';
							$kmlCheckbox = '<input type="checkbox" name="plant_id[]" class="cb" value="' . $plantId . '"/>';
						}
						if (($dataIdx > 0)
							&& ($resultData['otherItems'][$dataIdx - 1]['preposition'] == $collPrep)
							&& ($resultData['otherItems'][$dataIdx - 1]['location'] == $collName)) {
							$resultData['otherItems'][$dataIdx - 1]['count'] = $resultData['otherItems'][$dataIdx - 1]['count'] + $noGrid;
							$resultData['otherItems'][$dataIdx - 1]['grid'] .= '<br /> ' . $kmlCheckbox . ' <a class="redlink" href="'
									. $map_url . '&layer=plants&layer=photos&layer=highlight&plantid=' . $plantId . '&mode=browse">' . $gridLoc . '/';
							if ($annotation == 'T') {
								$resultData['otherItems'][$dataIdx - 1]['grid'] .= '<u>' . $coordLoc . '</u>';
							} else {
								$resultData['otherItems'][$dataIdx - 1]['grid'] .= $coordLoc;
							}
							$resultData['otherItems'][$dataIdx - 1]['grid'] .= '</a> ';
							$resultData['otherItems'][$dataIdx - 1]['grid'] .= $gmapsLink;
						} else {
							$resultData['otherItems'][$dataIdx]['location'] = $collName;
							$resultData['otherItems'][$dataIdx]['count'] = $noGrid;
							if($hideLoc == 'N')
							{
								if(!isNull($lat) && !isNull($long))
								{
									$gmapsLink = '(<a href="http://maps.google.com/maps?q=' . $lat . ',' . $long . urlencode(' (' . $filteredSciname . ', ' . $plantId . ' - ' . $collName . ' - ' . $gridLoc. ')') . '&t=h&z=21&output=embed">View on Google Maps</a>)';
									$kmlCheckbox = '<input type="checkbox" name="plant_id[]" class="cb" value="' . $plantId . '"/>';
								}
								$resultData['otherItems'][$dataIdx]['preposition'] = $collPrep;
								$resultData['otherItems'][$dataIdx]['grid'] = $kmlCheckbox . ' <a class="redlink" href="'
										. $map_url . '&layer=plants&layer=photos&layer=highlight&plantid=' . $plantId . '&mode=browse">' . $gridLoc . '/';
								if ($annotation == 'T') {
									$resultData['otherItems'][$dataIdx]['grid'] .= '<u>' . $coordLoc . '</u>';
								} else {
									$resultData['otherItems'][$dataIdx]['grid'] .= $coordLoc;
								}
								$resultData['otherItems'][$dataIdx]['grid'] .= '</a>';
								$resultData['otherItems'][$dataIdx]['grid'] .= ' ' . $gmapsLink;
								$resultData['otherItems'][$dataIdx]['subarea1'] = $subarea1;
								$resultData['otherItems'][$dataIdx]['subarea2'] = $subarea2;
								$resultData['otherItems'][$dataIdx]['subarea3'] = $subarea3;
							}
							$dataIdx++;
						}
					}
					
					$stmt2->close();
				}
				
				$resultData['relatedHerbarium'] = array();
				
				if ($stmt2 = $dbLink->prepare('SELECT herb_nbr, collector_primary, collector_addl, collector_no, coll_date FROM hrb_accession WHERE lc_acc = ?')) {
					$stmt2->bind_param('s', $resultData['lc_accession_no']);
					
					$stmt2->execute();
					
					$stmt2->bind_result($herb_nbr, $coll_primary, $coll_addl, $coll_no, $coll_date);
					$dataIdx = 0;
					while ($stmt2->fetch()) {
						$resultData['relatedHerbarium'][$dataIdx]['acc_nbr'] = $herb_nbr;
						$coll = $coll_primary;
						if (!isNull($coll_addl)) {
							$coll .= ', ' . $coll_addl;
						}
						$resultData['relatedHerbarium'][$dataIdx]['collector'] = $coll;
						$resultData['relatedHerbarium'][$dataIdx]['exp_nbr'] = $coll_no;
						$coll_date_parts = explode('-', sprintf('%s', $coll_date));
						$resultData['relatedHerbarium'][$dataIdx]['exp_date'] = date('d M Y', mktime(0, 0, 0, $coll_date_parts[1], $coll_date_parts[2], $coll_date_parts[0]));
						$dataIdx++;
					}
					
					$stmt2->close();
				}
			}
		}
		
		$dbLink->close();
	}
}

if (count($results) == 0) {
	header('Location: ' . getRootUrl() . '/');
	return;
}

function compElements($l, $r) {
	global $scinames;
	
	$retVal = 0;
	
	// Oldest to youngest.  The db values are the same at this point.
	$l_parts = explode('-', $l['lc_accession_no']);
	$r_parts = explode('-', $r['lc_accession_no']);
	if ($l_parts[1] < $r_parts[1]) {
		$retVal = -1;
	} elseif ($l_parts[1] > $r_parts[1]) {
		$retVal = 1;
	} else {
		if ($l_parts[0] < $r_parts[0]) {
			$retVal = -1;
		} elseif ($l_parts[0] > $r_parts[0]) {
			$retVal = 1;
		}
	}

	return $retVal;
}

usort($results, 'compElements');

require_once 'modules/page_tab_Intro_common.php';
require_once 'modules/collections_common.php';
if (!isset($_REQUEST['download_file'])) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
      "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>Morton Arboretum</title>
  <link rel="stylesheet" type="text/css" href="libraries/main.php" />
  <script type="text/javascript" src="libraries/main.js"></script>
  <script type="text/javascript" src="libraries/jquery.min.js"></script>
  <script type="text/javascript">
  function toggleChecked(status) {
	$(".cb").each( function() {
		$(this).attr("checked", status);
	})
  }
  </script>
  <style type='text/css'>
  #error
  {
  	border: 2px solid red;
  	padding: 0.5em;
  	background-color: #FF9999;
  	text-align: center;
  }
  </style>
</head>
<body>
	<form name="gotoPageForm" action="." method="post">
		<input type="hidden" name="page" value=""></input>
	</form>
<?php
$pageLocation = 'Search Results';
require_once 'modules/header_common.php';
?>	<div id="body" class="body">
		<div id="content" class="content">
<?php
showTabs($pages_info, null, 'results');
?>
<?php
	if(isset($_GET['notice']) && $_GET['notice'] == 'noplants')
	{
?>
<p id='error'>No plants selected for export.</p>
<?php	
	}
?>
			<table border="0" width="80%">
				<tr>
					<td>
						<table border="0" width="100%">
						<tr>
							<td>
<?php
require_once $app_root . '/modules/sciname_common.php';

sciname_long($results[0], $scinames[$results[0]['sciname_id']]);
?>							</td>
							<td rowSpan="2">
								<button name="download_file" type="button" onclick="javascript:window.location='<?php echo getRootUrl(); ?>/details_living_coll.php?download_file=&<?php echo $_SERVER['QUERY_STRING']; ?>';">Download CSV file</button><br />
								<form name="kml" method="POST" id="frmKML" action="earthkml.php">
								<input type="submit" name="kml" value="Download checked as KML" /><br />
								<input type="checkbox" name="checkAll" onclick="toggleChecked(this.checked)" /> Check All
							</td>
						</tr>
						<tr>
							<td></td>
						</tr>
						</table>
					</td>
				</tr>
<?php
$showAccessNbr = (count($results) > 1);
$needHR = false;

unset($resultData);
foreach ($results as $resultData) {
	$sciname = $scinames[$resultData['sciname_id']];
?>				<tr>
					<td>
						<?php
	if ($needHR) {
		// echo '<hr /><hr />' . "\n";
	}
	$needHR = true;
	if ($showAccessNbr) {
		echo '<table width="100%" border="0"><tr valign="top"><td width="75px" align="right"><b>' . $resultData['lc_accession_no'] . '</b></td><td width="10px"></td><td align="left">' . "\n";
	}
	if (count($results) == 1) {
		echo "<b>&nbsp;" . $resultData['lc_accession_no'] . "</b> &mdash; ";
	}
	echo $resultData['lc_how_received'];
	if (!isNull($resultData['lc_no_received']) && ($resultData['lc_no_received'] > 1)) {
		echo 's (' . $resultData['lc_no_received'] . ')';
	}
	echo ' from ' . $resultData['lc_source_name'];
	if (!isNull($resultData['lc_understock']) && (trim($resultData['lc_understock']) != '')) {
		echo ' growing on ' . trim($resultData['lc_understock']) . ' understock';
	}
	if (!isNull($resultData['lc_received_sciname']) && ($scinames[$resultData['sciname_id']] != $resultData['lc_received_sciname'])) {
		echo ' (received as ' . $resultData['lc_received_sciname'] . ')';
	}
	echo '. ';
	if (!isNull($resultData['lc_collector_name'])) {
		echo ucfirst(strtolower($resultData['origin_collected']) . 'by ') . $resultData['lc_collector_name'];
	} else {
		echo ucfirst(strtolower(trim($resultData['origin_collected'])));
	}
	if ($resultData['wild_collected'] && !isNull($resultData['lc_collector_no']) && $resultData['lc_collector_no'] != ' NULL' ) {
		echo ' (No. ' . $resultData['lc_collector_no'] . ')';
		if (!isNull($resultData['lc_collector_date'])) {
			$dateParts = explode('-', $resultData['lc_collector_date']);
			echo ' on ' . date('d M Y', mktime(0, 0, 0, $dateParts[1], $dateParts[2], $dateParts[0]));
		}
	}
	if (!isNull($resultData['lc_collector_site'])) {
		if ($resultData['wild_collected']) {
			echo '. Site:';
		}
		echo ' ' . trim($resultData['lc_collector_site']);
	}
	if (!isNull($resultData['lc_collector_township_range']) && ($resultData['lc_collector_township_range'] != '')) {
		echo ' ' . trim($resultData['lc_collector_township_range']);
	}
	if ($resultData['collector_location'] != '') {
		echo ', ' . $resultData['collector_location'];
	}
	if (!isNull($resultData['lc_collector_habitat'])) {
		echo '. Habitat: ' . $resultData['lc_collector_habitat'];
	}
	if (!isNull($resultData['lc_collector_lat']) && !isNull($resultData['lc_collector_long'])) {
		echo ', Lat ' . $resultData['lc_collector_lat'] . ' ' . (($resultData['lc_collector_lat'] > 0) ? 'N' : 'S')
				. ', Long ' . $resultData['lc_collector_long'] . ' '
				. (($resultData['lc_collector_long'] > 0) ? 'E' : 'W');
	}
	if (!isNull($resultData['lc_collector_elevation_low']) && !isNull($resultData['lc_collector_elevation_units'])) {
		echo ', Elevation ' . $resultData['lc_collector_elevation_low'];
		if (!isNull($resultData['lc_collector_elevation_high'])) {
			echo ' - ' . $resultData['lc_collector_elevation_high'];
		}
		echo ' ' . $resultData['lc_collector_elevation_units'];
	}
	if ($showAccessNbr) {
		echo '</td></tr></table>' . "\n";
	}
?>					</td>
				</tr>
<?php
	if (count($resultData['otherItems']) > 0) {
?>				<tr>
					<td>
						<table border="0" width="100%">
<?php
		foreach ($resultData['otherItems'] as $otherItems) {
?>							<tr valign="top">
								<td width="30px">
								</td>
								<td align="right" width="40px">
<?php
								echo $otherItems['count'];
?>
								</td>
								<td width="10px"> - </td>
								<td>
<?php
			echo $otherItems['preposition'] . ' ' . $otherItems['location'] . ': <br />' . $otherItems['grid'];
?>								</td>
							</tr>
<?php
	}
?>						</table>
					</td>
				</tr>
<?php
	}
	if (count($resultData['relatedHerbarium']) > 0) {
?>				<tr>
					<td>
					</td>
				</tr>
				<tr>
					<table>
						<tr>
							<td width="73px">
							</td>
					<td align="left">
						<b>Associated herbarium specimens</b>
						<table border="0" width="100%">
<?php
		foreach ($resultData['relatedHerbarium'] as $otherHerb) {
?>							<tr valign="top">
								<td width="10px">
								</td>
								<td align="right" width="10px">
<?php
	if(isNull($otherHerb['acc_nbr']) || $otherHerb['acc_nbr'] == 'NEW')
	{
		echo '									<font color="red">&nbsp;</font></a>';
	}
	else
	{
		echo '									<a href="' . getRootUrl() . '/details_herbarium.php?id=' . $otherHerb['acc_nbr'] . '"><font color="red"><b>' . $otherHerb['acc_nbr'] . '</b></font></a>';
	}
?>								</td>
								<td width="1px">&mdash;</td>
								<td>
<?php		
			if(isset($otherHerb['exp_nbr']) && !is_null($otherHerb['exp_nbr']) && $otherHerb['exp_nbr'] != 'NULL')
			{
				echo $otherHerb['collector'] . ', ' . $otherHerb['exp_nbr'] . ', ' . $otherHerb['exp_date'];
			} else {
				echo $otherHerb['collector'] . ', ' . $otherHerb['exp_date'];
			}
			
?>
								</td>
							</tr>
<?php
		}
?>						</table>
					</td>
						</tr>
					</table>
				</tr>
<?php
	}
}
?>		</div>
		</form>
	</div>
</body>
</html>
<?php
} else {
	require_once $app_root . '/modules/downloadEntryRow.php';
	
	dump_results('morton_details.csv', 'lc', $results, $scinames);
}
?>
