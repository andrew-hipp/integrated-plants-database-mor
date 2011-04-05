<?php
ini_set('memory_limit', '384M');
$app_root = realpath('.');
require_once $app_root . '/modules/page_intro_common.php';
if ((!isset($_REQUEST['plantName']) || ($_REQUEST['plantName'] == ''))
		&& (!isset($_REQUEST['collectionCombo']) || ($_REQUEST['collectionCombo'] == ''))
		&& (!isset($_REQUEST['collectionSite']) || ($_REQUEST['collectionSite'] == ''))) {
	header('Location: ' . getRootUrl() . '/');
	return;
}

require_once $app_root . '/modules/page_tab_Intro_common.php';
require_once $app_root . '/modules/collections_common.php';
if (isset($_REQUEST['download_file'])) {
	switch ($_REQUEST['download_file']) {
		case 'lc':
		case 'hrb':
			break;
			
		default:
			unset($_REQUEST['download_file']);
			break;
	}
}

if (!isset($_REQUEST['download_file'])) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
      "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>Morton Arboretum</title>
  <link rel="stylesheet" type="text/css" href="libraries/main.php" />
  <script type="text/javascript" src="libraries/main.js"></script>
</head>
<body>
	<form name="gotoPageForm" action="." method="post">
		<input type="hidden" name="page" value=""></input>
	</form>
<?php
	$pageLocation = 'Search Results';
	require_once $app_root . '/modules/header_common.php';
?>	<div id="body" class="body">
		<div id="content" class="content">
<?php
	showTabs($pages_info, null, 'results');
?>		<table width="100%" border="0">
		<tr><td>
			<br /><b><em>Search term:
<?php
	$needLeadingComma = false;
	if ($_REQUEST['plantName'] != '') {
		echo '"' . $_REQUEST['plantName'] . '" in Plant name';
		$needLeadingComma = true;
	}
	if ($_REQUEST['collectionCombo'] != '') {
		if ($needLeadingComma)
			echo ', ';
		echo '"' . collectionNameForOption($_REQUEST['collectionCombo']) . '" in Living Collection';
		$needLeadingComma = true;
	}
	if ($_REQUEST['collectionSite'] != '')  {
		if ($needLeadingComma)
			echo ', ';
		echo '"' . $_REQUEST['collectionSite'] . '" in Collection site';
	}
?></em></b>
				<hr />
		</td>
		<td>
			<button name="download_file_lc" type="button" onclick="javascript:window.location='<?php echo getRootUrl(); ?>/search.php?download_file=lc&<?php echo $_SERVER['QUERY_STRING']; ?>';">Download Living Collection file</button><br />
			<button name="download_file_hrb" type="button" onclick="javascript:window.location='<?php echo getRootUrl(); ?>/search.php?download_file=hrb&<?php echo $_SERVER['QUERY_STRING']; ?>';">Download Herbarium file</button>
		</td></tr>
		</table>
<?php
}
$numTaxa = 0;
$numAccessions = 0;
$numLivingColl = 0;
$numHerbResults = 0;
$numLCResults = 0;

require $app_root . '/config.inc.php';

{
	$link = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);
	
	$kDBLC = 'lc';
	$kDBHRB = 'hrb';
	$results = array();
	$scinames = array();
	if (mysqli_connect_error() == 0) {
		$bindArrInfo = array();
		$lcQueryFields = array('`lc_accession`.`id`',
				'`lc_accession`.`accession_no`',
				//'`lc_accession`.`how_received`',
				//'`lc_accession`.`source_name`',
				//'`lc_accession`.`provenance`',
				//'`lc_accession`.`collector_name`',
				//'`lc_accession`.`collector_site`',
				//'`lc_accession`.`collector_subcountry_2`',
				//'`lc_accession`.`collector_subcountry_2_dsg`',
				//'`lc_accession`.`collector_subcountry_1`',
				//'`lc_accession`.`collector_subcountry_1_dsg`',
				//'`lc_accession`.`collector_country`',
				//'`lc_accession`.`sciname_id`',
				//'`lc_accession`.`no_received`',
				'NULL as image_thumb',
				'NULL as image_large'
			);
		$lcQueryNames = array('lc_id',
				'lc_accession_no',
				//'lc_how_received',
				//'lc_source_name',
				//'lc_provenance',
				//'lc_collector_name',
				//'lc_collector_site',
				//'lc_collector_subcountry_2',
				//'lc_collector_subcountry_2_dsg',
				//'lc_collector_subcountry_1',
				//'lc_collector_subcountry_1_dsg',
				//'lc_collector_country',
				//'lc_sciname_id',
				//'lc_no_received',
				'image_thumb',
				'image_large'
			);
		$lcQueryTables = array('lc_accession');
		$hrbqueryFields = array('`hrb_accession`.`id`',
				//'`hrb_accession`.`herb_nbr`',
				//'`hrb_accession`.`collector_primary`',
				'`hrb_accession`.`collector_primary_ln`',
				//'`hrb_accession`.`collector_addl`',
				//'`hrb_accession`.`collector_no`',
				'`hrb_accession`.`coll_date`',
				//'`hrb_accession`.`coll_date_acc`',
				//'`hrb_accession`.`site`',
				//'`hrb_accession`.`site_sensitive`',
				//'`hrb_accession`.`subctry2`',
				//'`hrb_accession`.`desig2`',
				//'`hrb_accession`.`subctry1`',
				//'`hrb_accession`.`desig1`',
				//'`hrb_accession`.`country`',
				//'`hrb_accession`.`lc_acc`',
				//'`hrb_accession`.`image_thumb`',
				//'`hrb_accession`.`image_large`'
			);
		$hrbQueryNames = array('herb_id',
				//'herb_herb_nbr',
				//'herb_collector_primary',
				'herb_collector_primary_ln',
				//'herb_collector_addl',
				//'herb_collector_no',
				'herb_coll_date',
				//'herb_coll_date_acc',
				//'herb_site',
				//'herb_site_sensitive',
				//'herb_subctry2',
				//'herb_desig2',
				//'herb_subctry1',
				//'herb_desig1',
				//'herb_country',
				//'herb_lc_acc',
				//'image_thumb',
				//'image_large'
			);
		$hrbQueryTables = array('hrb_accession');
		$queryFields = array('`sciname`.`id`',
				'`sciname`.`scientific_name_id`',
				'`sciname`.`sort_scientific_name`',
				//'`sciname`.`scientific_name`',
				//'`sciname`.`family_name`',
				//'`sciname`.`common_names`',
				//'`sciname`.`usda_zone_lo`',
				//'`sciname`.`usda_zone_hi`',
				//'`sciname`.`genus_designator`',
				//'`sciname`.`genus_name`',
				//'`sciname`.`species_designator`',
				//'`sciname`.`species_name`',
				//'`sciname`.`subspecies_name`',
				//'`sciname`.`variety_name`',
				//'`sciname`.`forma_name`',
				//'`sciname`.`cultivar_name`',
				//'`sciname`.`author_name`',
				//'`sciname`.`plant_patent_no`',
				//'`sciname`.`family_common_name`',
				//'`sciname`.`trademark`',
				//'`sciname`.`range`'
			);
		$queryNames = array('sciname_id',
				'sciname_scientific_name_id',
				'sciname_sort_scientific_name',
				//'sciname_scientific_name',
				//'sciname_family_name',
				//'sciname_common_names',
				//'sciname_usda_zone_lo',
				//'sciname_usda_zone_hi',
				//'sciname_genus_designator',
				//'sciname_genus_name',
				//'sciname_species_designator',
				//'sciname_species_name',
				//'sciname_subspecies_name',
				//'sciname_variety_name',
				//'sciname_forma_name',
				//'sciname_cultivar_name',
				//'sciname_author_name',
				//'sciname_plant_patent_no',
				//'sciname_family_common_name',
				//'sciname_trademark',
				//'sciname_range'
			);
		$queryTables = array('sciname');
		$hrbqueryAndConditions = array();
		$lcQueryAndConditions = array();
		if ($_REQUEST['collectionCombo'] != '') {
			$bindArrInfo[] = array('type'=>'s',
					'value'=>collectionNameForOption($_REQUEST['collectionCombo']),
					'which'=>'lc');
			if (!in_array('lc_plants', $lcQueryTables))
				$lcQueryTables[] = 'lc_plants';
			if (!in_array('lc_accession', $lcQueryTables))
				$lcQueryTables[] = 'lc_accession';
			$lcQueryAndConditions[] = '`lc_plants`.`collection_name`=?';
		}
		if ($_REQUEST['plantName'] != '') {
			$fieldsToSearch = array('`sciname`.`scientific_name`',
				'`sciname`.`common_names`',
				'`sciname`.`trademark`');
			$queryPrefix = '';
			$query = '';
			if (!in_array('sciname', $queryTables))
				$queryTables[] = 'sciname';
			foreach ($fieldsToSearch as $fieldToSearch) {
				$bindArrInfo[] = array('type'=>'s',
						'value'=>'%' . implode('\%', explode('%', $_REQUEST['plantName'])) . '%',
						'which'=>'both');
				$query .= $queryPrefix . '(' . $fieldToSearch . ' LIKE ?)';
				$queryPrefix = ' OR ';
			}
			$lcQueryAndConditions[] = '(' . $query . ')';
			$hrbqueryAndConditions[] = '(' . $query . ')';
		}
		if ($_REQUEST['collectionSite'] != '') {
			$collectionSite = '%' . implode('\%', explode('%', $_REQUEST['collectionSite'])) . '%';
			$lcFieldsToSearch = array('`lc_accession`.`collector_site`', '`lc_accession`.`collector_subcountry_2`',
					'`lc_accession`.`collector_subcountry_1`', '`lc_accession`.`collector_country`',
					'`lc_accession`.``collector_habitat');
			//$hrbFieldsToSearch = array('`hrb_accession`.`site`', '`hrb_accession`.`subctry2`', '`hrb_accession`.`subctry1`',
			//		'`hrb_accession`.`country`', '`hrb_accession`.`habitat`');
			if (!in_array('lc_accession', $lcQueryTables))
				$lcQueryTables[] = 'lc_accession';
			//if (!in_array('hrb_accession', $hrbQueryTables))
			//	$hrbQueryTables[] = 'hrb_accession';
			$queryPrefix = '';
			$query = '';
			foreach ($lcFieldsToSearch as $fieldToSearch) {
				$bindArrInfo[] = array('type'=>'s',
						'value'=>$collectionSite,
						'which'=>'lc');
				$query .= $queryPrefix . '(' . $fieldToSearch . ' LIKE ?)';
				$queryPrefix = ' OR ';
			}
			$lcQueryAndConditions[] = '(' . $query . ')';
			
			//$queryPrefix = '';
			//$query = '';
			//foreach ($hrbFieldsToSearch as $fieldToSearch) {
			//	$bindArrInfo[] = array('type'=>'s',
			//			'value'=>$collectionSite,
			//			'which'=>'hrb');
			//	$query .= $queryPrefix . '(' . $fieldToSearch . ' LIKE ?)';
			//	$queryPrefix = ' OR ';
			//}
			//$hrbqueryAndConditions[] = '(' . $query . ')';
		}
		$lcquery = 'SELECT DISTINCT ' . implode(', ', $queryFields);
		if (count($lcQueryFields) > 0) {
			$lcquery .= ', ' . implode(', ', $lcQueryFields);
		}
		$lcquery .= ' FROM ';
		$fromPrefix = '';
		if (count($queryTables) > 0) {
			$lcquery .= implode(', ', $queryTables);
			$fromPrefix = ', ';
		}
		if (count($lcQueryTables) > 0) {
			$lcquery .= $fromPrefix . implode(', ', $lcQueryTables);
		}
		
		$lcquery .= ' WHERE ' . implode(' AND ', $lcQueryAndConditions);
		if (in_array('lc_plants', $lcQueryTables)) {
			$lcquery .= ' AND lc_plants.accession_no = lc_accession.accession_no';
		}
		if (in_array('lc_accession', $lcQueryTables)) {
			$lcquery .= ' AND lc_accession.sciname_id = sciname.scientific_name_id';
		}

		$lcquery .= ' ORDER BY `sciname`.`sort_scientific_name`';
		
		if ((isset($debug_show_search) && $debug_show_search)
			|| ($_SERVER['SERVER_NAME'] == 'mortonarb.localhost')) {
			$_SERVER['LC_QUERY'] = $lcquery . '<br /><b>With:</b>';
			if ($_REQUEST['plantName']) {
				$fieldsToSearch = array('`sciname`.`scientific_name`',
					'`sciname`.`common_names`',
					'`sciname`.`trademark`');
				foreach ($fieldsToSearch as $fieldToSearch) {
					$_SERVER['LC_QUERY'] .= '<br />' . $fieldToSearch . ' = %' . implode('\%', explode('%', $_REQUEST['plantName'])) . '%';
				}
			}					
			if ($_REQUEST['collectionCombo'] != '') {
				$_SERVER['LC_QUERY'] .= '<br />`lc_plants`.`collection_name` = ' . collectionNameForOption($_REQUEST['collectionCombo']);
			}
			if ($_REQUEST['collectionSite'] != '') {
				$collectionSite = '%' . implode('\%', explode('%', $_REQUEST['collectionSite'])) . '%';
				$lcFieldsToSearch = array('`lc_accession`.`collector_site`', '`lc_accession`.`collector_subcountry_2`',
						'`lc_accession`.`collector_subcountry_1`', '`lc_accession`.`collector_country`',
						'`lc_accession`.``collector_habitat');
				foreach ($lcFieldsToSearch as $fieldToSearch) {
					$_SERVER['LC_QUERY'] .= '<br />' . $fieldToSearch . ' = ' . $collectionSite; 
				}
			}
		}
		
		if ($stmt = $link->prepare($lcquery)) {
			$bindArr = array('');
			$valueArr = array();
			foreach ($bindArrInfo as $bindInfo) {
				if (($bindInfo['which'] == 'lc') || ($bindInfo['which'] == 'both')) {
					$bindArr[0] .= $bindInfo['type'];
					$valueArr[] = $bindInfo['value'];
					$bindArr[] =& $valueArr[count($valueArr) - 1];
				}
			}
			
			call_user_func_array(array($stmt,'bind_param'), $bindArr);

			$stmt->execute();
			
			$result = array();
			$bindArr = array();
			foreach ($queryNames as $queryName) {
				$result[$queryName] = '';
				$bindArr[] =& $result[$queryName];
			}
			foreach ($lcQueryNames as $queryName) {
				$result[$queryName] = '';
				$bindArr[] =& $result[$queryName];
			}
			
			call_user_func_array(array($stmt, 'bind_result'), $bindArr);
			
			$keys = array();
			$keysSet = false;
			while ($stmt->fetch()) {
				if (!$keysSet) {
					foreach (array_keys($result) as $key) {
						$keys[] = array('key'=>$key, 'isSciname'=>(substr($key, 0, 8) == 'sciname_'), 'sci'=>substr($key, 8), 'isSciID'=>($key == 'sciname_id'));
					}
					$keysSet = true;
				}
				$results[] = array();
				$sciname = array();
				$scinameIdx = null;
				$resultEntry =& $results[count($results) - 1];
				$resultEntry['db'] =& $kDBLC;
				$resultEntry['lc_plant_count'] = 0;
				foreach ($keys as $key) {
					if ($key['isSciname']) {
						if ($key['isSciID']) {
							$resultEntry[$key['key']] = $result[$key['key']];
							$scinameIdx = $result[$key['key']];
						} else {
							$scinameKey = $key['sci'];
							$sciname[$scinameKey] = $result[$key['key']];
						}
					} else {
						if (isNull($result[$key['key']])) {
							$resultEntry[$key['key']] = null;
						} else {
							$resultEntry[$key['key']] = $result[$key['key']];
						}
					}
				}
				if (($scinameIdx != null) && !isset($scinames[$scinameIdx])) {
					$scinames[$scinameIdx] = $sciname;
				}
			}
			
			$stmt->close();
	
			if ($stmt = $link->prepare('SELECT count(no_grid) FROM lc_plants WHERE accession_no = ?')) {
				$accession_no = '';
				$stmt->bind_param('s', $accession_no);
				
				$plant_count = '';
				$stmt->bind_result($plant_count);
				
				$idx = 0;
				while ($idx < count($results)) {
					if ($results[$idx]['db'] == 'lc') {
						$accession_no = $results[$idx]['lc_accession_no'];
						$stmt->execute();
						if ($stmt->fetch()) {
							$results[$idx]['lc_plant_count'] = $plant_count;
						}
					}
					$idx++;
				}
				$stmt->close();
			}
		}
		
		// If the collection site is not set, then add the herbarium results.
		if($_REQUEST['collectionSite'] == '' || isNull($_REQUEST['collectionSite']))
		{
			$hrbquery = 'SELECT DISTINCT ' . implode(', ', $queryFields);
			if (count($hrbqueryFields) > 0) {
				$hrbquery .= ', ' . implode(', ', $hrbqueryFields);
			}
			$hrbquery .= ' FROM ';
			$fromPrefix = '';
			if (count($queryTables) > 0) {
				$hrbquery .= implode(', ', $queryTables);
				$fromPrefix = ', ';
			}
			if (count($hrbQueryTables) > 0) {
				$hrbquery .= $fromPrefix . implode(', ', $hrbQueryTables);
			}
			
			$hrbquery .= ' WHERE ' . implode(' AND ', $hrbqueryAndConditions);
	
			if (in_array('hrb_accession', $hrbQueryTables)) {
				$hrbquery .= ' AND hrb_accession.sciname_id = sciname.scientific_name_id';
			}
			
			$hrbquery .= ' ORDER BY `sciname`.`sort_scientific_name`';
			
			if ((isset($debug_show_search) && $debug_show_search)
				|| ($_SERVER['SERVER_NAME'] == 'mortonarb.localhost')) {
				$_SERVER['HRB_QUERY'] = $hrbquery . '<br /><b>With:</b>';;
				if ($_REQUEST['plantName']) {
					$fieldsToSearch = array('`sciname`.`scientific_name`',
						'`sciname`.`common_names`',
						'`sciname`.`trademark`');
					foreach ($fieldsToSearch as $fieldToSearch) {
						$_SERVER['HRB_QUERY'] .= '<br />' . $fieldToSearch . ' = %' . implode('\%', explode('%', $_REQUEST['plantName'])) . '%';
					}
				}					
				/*
				if ($_REQUEST['collectionSite'] != '') {
					$collectionSite = '%' . implode('\%', explode('%', $_REQUEST['collectionSite'])) . '%';
					$hrbFieldsToSearch = array('`hrb_accession`.`site`', '`hrb_accession`.`subctry2`', '`hrb_accession`.`subctry1`',
							'`hrb_accession`.`country`', '`hrb_accession`.`habitat`');
					foreach ($hrbFieldsToSearch as $fieldToSearch) {
						$_SERVER['HRB_QUERY'] .= '<br />' . $fieldToSearch . ' = ' . $collectionSite; 
					}
				}
				*/
			}
			
			if ($stmt = $link->prepare($hrbquery)) {
				$bindArr = array('');
				foreach ($bindArrInfo as $bindInfo) {
					if (($bindInfo['which'] == 'hrb') || ($bindInfo['which'] == 'both')) {
						$bindArr[0] .= $bindInfo['type'];
						$valueArr[] = $bindInfo['value'];
						$bindArr[] =& $valueArr[count($valueArr) - 1];
					}
				}
				
				call_user_func_array(array($stmt,'bind_param'), $bindArr);
	
				$stmt->execute();
				
				$result = array();
				$bindArr = array();
				foreach ($queryNames as $queryName) {
					$result[$queryName] = '';
					$bindArr[] =& $result[$queryName];
				}
				//foreach ($hrbQueryNames as $queryName) {
				//	$result[$queryName] = '';
				//	$bindArr[] =& $result[$queryName];
				//}
									
				call_user_func_array(array($stmt, 'bind_result'), $bindArr);
				
				$keys = array();
				$keysSet = false;
				while ($stmt->fetch()) {
					if (!$keysSet) {
						foreach (array_keys($result) as $key) {
							$keys[] = array('key'=>$key, 'isSciname'=>(substr($key, 0, 8) == 'sciname_'), 'sci'=>substr($key, 8), 'isSciID'=>($key == 'sciname_id'));
						}
						$keysSet = true;
					}
					$results[] = array();
					$sciname = array();
					$scinameIdx = null;
					$resultEntry =& $results[count($results) - 1];
					$resultEntry['db'] =& $kDBHRB;
					foreach ($keys as $key) {
						if ($key['isSciname']) {
							if ($key['isSciID']) {
								$resultEntry[$key['key']] = $result[$key['key']];
								$scinameIdx = $result[$key['key']];
							} else {
								$scinameKey = $key['sci'];
								$sciname[$scinameKey] = $result[$key['key']];
							}
						} else {
							if (isNull($result[$key['key']])) {
								$resultEntry[$key['key']] = null;
							} else {
								$resultEntry[$key['key']] = $result[$key['key']];
							}
						}
					}
					if (($scinameIdx != null) && !isset($scinames[$scinameIdx])) {
						$scinames[$scinameIdx] = $sciname;
					}
				}				
				$stmt->close();
			}
		}
		
		if ($stmt = $link->prepare('SELECT image_thumb, image_large FROM lc_images WHERE sciname_id = ?')) {
			$sciname_first_part = '';
			$stmt->bind_param('s', $sciname_first_part);
			
			$image_thumb = '';
			$image_large = '';
			$stmt->bind_result($image_thumb, $image_large);
			
			foreach (array_keys($scinames) as $idx) {
				$sciname_first_part = substr($scinames[$idx]['scientific_name_id'], 0, 14);  
				$stmt->execute();
				if ($stmt->fetch()) {
					$scinames[$idx]['image_thumb'] = $image_thumb;
					$scinames[$idx]['image_large'] = $image_large;
				}
			}
			$stmt->close();
		}
	
		$link->close();
	}
	
	function compElements($l, $r) {
		global $scinames;
		$retVal = strcmp($scinames[$l['sciname_id']]['sort_scientific_name'], $scinames[$r['sciname_id']]['sort_scientific_name']);
		
		if ($retVal == 0) {
			$retVal = -strcmp($l['db'], $r['db']);
		}
		
		if ($retVal == 0) {
			// Oldest to youngest.  The db values are the same at this point.
			if ($l['db'] == 'lc') {
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
			} else {
				if ($l['herb_coll_date'] < $r['herb_coll_date']) {
					$retVal = -1;
				} elseif ($l['herb_coll_date'] > $r['herb_coll_date']) {
					$retVal = 1;
				} else {
					$retVal = strcmp($l['herb_collector_primary_ln'], $r['herb_collector_primary_ln']);
				}
			}
		}
		return $retVal;
	}
	
	usort($results, 'compElements');
	
	if (isset($_REQUEST['download_file'])) {
		require $app_root . '/modules/downloadEntryRow.php';
		$num_Entry_Arr = dump_results('morton_search.csv', strtoupper($_REQUEST['download_file']), $results, $scinames);
	} else {
		require $app_root . '/modules/searchEntryRow.php';
		$num_Entry_Arr = dump_results($results, $scinames);
	}
	
	$numTaxa = $num_Entry_Arr['numTaxa'];
	$numAccessions = $num_Entry_Arr['numAccessions'];
	$numLivingColl = $num_Entry_Arr['numLivingColl'];
}

if (!isset($_REQUEST['download_file'])) {
	if ($numTaxa > 0) {
?>			<hr />
<?php
	}
?>			<b>Search Summary:</b> <?php echo $numTaxa; ?> tax<?php if ($numTaxa != 1) { echo 'a'; } else { echo 'o'; } ?>, <?php echo $numAccessions; ?> accession<?php if ($numAccessions != 1) echo 's'; ?>, <?php echo $numLivingColl; ?> plant<?php if ($numLivingColl != 1) echo 's'; ?>
<br />			<b>Memory used:</b> <?php echo memory_get_peak_usage(false); ?>
<?php
	require_once 'modules/footer_common.php';
?></body>
</html>
<?php
}
?>