<?php
ini_set('memory_limit', '384M');
$app_root = realpath('.');
require_once $app_root . '/modules/page_intro_common.php';
require_once $app_root . '/modules/page_tab_Intro_common.php';
require_once $app_root . '/modules/collections_common.php';

$kMonthDayMonth = 0;
$kMonthDayDay = 1;

$kDBLC = 'lc';
$kDBHRB = 'hrb';

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

$requestFields = array();
$i=0;
$requestFields[$i]['field_name'] = 'search_type';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array();
$requestFields[$i]['db_field']['cond'] = '';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Search Type';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';
$requestFields[$i]['value_strings']['living'] = 'Living Collection (grounds)';
$requestFields[$i]['value_strings']['herbarium'] = 'Herbarium (dried plants)';
$requestFields[$i]['value_strings']['combined'] = 'Combined Search';

$i++;
$requestFields[$i]['field_name'] = 'coll_area';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_plants', 'field'=>'collection_name'));
$requestFields[$i]['db_field']['hrb'] = array();
$requestFields[$i]['db_field']['cond'] = '=';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = 'collectionNameForOption';
$requestFields[$i]['field_desc'] = 'Living Collection Area';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'access_nbr';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'accession_no', 'permit_wildcard'=>true, 'wildcard_regex'=>'/^\*\-[0-9]+$/'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'herb_nbr'));
$requestFields[$i]['db_field']['cond'] = '=';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Accession number';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_name_key';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'sciname', 'field'=>'scientific_name'),
		array('table'=>'sciname', 'field'=>'common_names'),
		array('table'=>'sciname', 'field'=>'trademark'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'sciname', 'field'=>'scientific_name'),
		array('table'=>'sciname', 'field'=>'common_names'),
		array('table'=>'sciname', 'field'=>'trademark'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Plant name keyword';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_order';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'sciname', 'field'=>'order_name'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'sciname', 'field'=>'order_name'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Plant order';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_fam';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'sciname', 'field'=>'family_name'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'sciname', 'field'=>'family_name'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Plant Family';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_genus';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'sciname', 'field'=>'genus_name'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'sciname', 'field'=>'genus_name'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Plant Genus';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_species';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'sciname', 'field'=>'species_name'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'sciname', 'field'=>'species_name'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Plant Species';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_subspecies';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'sciname', 'field'=>'subspecies_name'),
		array('table'=>'sciname', 'field'=>'variety_name'),
		array('table'=>'sciname', 'field'=>'forma_name'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'sciname', 'field'=>'subspecies_name'),
		array('table'=>'sciname', 'field'=>'variety_name'),
		array('table'=>'sciname', 'field'=>'forma_name'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Plant Subspecies';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_cult';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'sciname', 'field'=>'cultivar_name'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'sciname', 'field'=>'cultivar_name'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Plant Cultivar';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_trade_name';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'sciname', 'field'=>'scientific_name'),
		array('table'=>'sciname', 'field'=>'common_names'),
		array('table'=>'sciname', 'field'=>'trademark'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'sciname', 'field'=>'scientific_name'),
		array('table'=>'sciname', 'field'=>'common_names'),
		array('table'=>'sciname', 'field'=>'trademark'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Plant Common/Trademark Name';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_source';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'source_name'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'collector_primary'),
		array('table'=>'hrb_accession', 'field'=>'collector_addl'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Source institution/person';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_collector_lname';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_name'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'collector_primary_ln'),
		array('table'=>'hrb_accession', 'field'=>'collector_addl'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Collector last name';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_collector_fname';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_name'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'collector_primary_fn'),
		array('table'=>'hrb_accession', 'field'=>'collector_addl'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Collector first name';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_collector_project';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_name'),
		array('table'=>'lc_accession', 'field'=>'source_name'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'project'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Project/Expedition';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_collector_expnbr';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_no', 'permit_wildcard'=>true, 'wildcard_regex'=>'/^.+\*$/'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'project', 'permit_wildcard'=>true, 'wildcard_regex'=>'/^.+\*$/'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Collector/Expedition #';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

function lc_collector_dates($which) {
	global $bindArrInfo;
	global $lcQueryAndConditions;
	global $kMonthDayMonth;
	global $kMonthDayDay;
	switch ($which) {
		case 0:
			if (isset($_REQUEST['plant_collector_year']) && ($_REQUEST['plant_collector_year'] != '')) {
				if (isset($_REQUEST['plant_collector_monthday']) && ($_REQUEST['plant_collector_monthday'] != '')) {
					$dayMonthParts = explode('/', $_REQUEST['plant_collector_monthday']);
					if (count($dayMonthParts) == 2) {
						$bindArrInfo[] = array('type'=>'s',
											   'value'=>$_REQUEST['plant_collector_year'] . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayMonth]) . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayDay]),
											   'which'=>'lc');
						$lcQueryAndConditions[] = '`lc_accession`.`collector_date` >= ?';
					}
				} else {
					$bindArrInfo[] = array('type'=>'i',
										   'value'=>$_REQUEST['plant_collector_year'],
										   'which'=>'lc');
					$lcQueryAndConditions[] = 'YEAR(`lc_accession`.`collector_date`) >= ?';
				}
			}
			break;
			
		case 1:
			if (isset($_REQUEST['plant_collector_year']) && ($_REQUEST['plant_collector_year'] != '')) {
				if (isset($_REQUEST['plant_collector_monthday']) && ($_REQUEST['plant_collector_monthday'] != '')) {
					$dayMonthParts = explode('/', $_REQUEST['plant_collector_monthday']);
					if (count($dayMonthParts) == 2) {
						return $_REQUEST['plant_collector_year'] . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayMonth]) . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayDay]);
					}
				} else {
					return $_REQUEST['plant_collector_year'];
				}
			}
			break;
			
		case 2:
			if (isset($_REQUEST['plant_collector_year']) && ($_REQUEST['plant_collector_year'] != '')) {
				if (isset($_REQUEST['plant_collector_monthday']) && ($_REQUEST['plant_collector_monthday'] != '')) {
					$dayMonthParts = explode('/', $_REQUEST['plant_collector_monthday']);
					if (count($dayMonthParts) == 2) {
						return 'Collection date is on or after ' . date('d M Y', mktime(0, 0, 0, $dayMonthParts[$kMonthDayMonth], $dayMonthParts[$kMonthDayDay], $_REQUEST['plant_collector_year']));
					}
				} else {
					return 'Collection year is on or after ' . $_REQUEST['plant_collector_year'];
				}
			}
			break;
	}
}

function hrb_collector_dates($which) {
	global $bindArrInfo;
	global $hrbQueryAndConditions;
	global $kMonthDayMonth;
	global $kMonthDayDay;
	switch ($which) {
		case 0:
			if (isset($_REQUEST['plant_collector_year']) && ($_REQUEST['plant_collector_year'] != '')) {
				if (isset($_REQUEST['plant_collector_monthday']) && ($_REQUEST['plant_collector_monthday'] != '')) {
					$dayMonthParts = explode('/', $_REQUEST['plant_collector_monthday']);
					if (count($dayMonthParts) == 2) {
						$bindArrInfo[] = array('type'=>'s',
											   'value'=>$_REQUEST['plant_collector_year'] . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayMonth]) . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayDay]),
											   'which'=>'hrb');
						$hrbQueryAndConditions[] = '`hrb_accession`.`coll_date` >= ?';
					}
				} else {
					$bindArrInfo[] = array('type'=>'i',
										   'value'=>$_REQUEST['plant_collector_year'],
										   'which'=>'hrb');
					$hrbQueryAndConditions[] = 'YEAR(`hrb_accession`.`coll_date`) >= ?';
				}
			}
			break;
	
		case 1:
			if (isset($_REQUEST['plant_collector_year']) && ($_REQUEST['plant_collector_year'] != '')) {
				if (isset($_REQUEST['plant_collector_monthday']) && ($_REQUEST['plant_collector_monthday'] != '')) {
					$dayMonthParts = explode('/', $_REQUEST['plant_collector_monthday']);
					if (count($dayMonthParts) == 2) {
						return '`hrb_accession`.`coll_date` = ' . $_REQUEST['plant_collector_year'] . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayMonth]) . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayDay]);
					}
				} else {
					return '`hrb_accession`.`coll_date` = ' . $_REQUEST['plant_collector_year'];
				}
			}
			break;
			
		case 2:
			if (isset($_REQUEST['plant_collector_year']) && ($_REQUEST['plant_collector_year'] != '')) {
				if (isset($_REQUEST['plant_collector_monthday']) && ($_REQUEST['plant_collector_monthday'] != '')) {
					$dayMonthParts = explode('/', $_REQUEST['plant_collector_monthday']);
					if (count($dayMonthParts) == 2) {
						return 'Collection date is on or after ' . date('d M Y', mktime(0, 0, 0, $dayMonthParts[$kMonthDayMonth], $dayMonthParts[$kMonthDayDay], $_REQUEST['plant_collector_year']));
					}
				} else {
					return 'Collection year is on or after ' . $_REQUEST['plant_collector_year'];
				}
			}
			break;
	}
}
$i++;
$requestFields[$i]['field_name'] = 'plant_collector_year';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_date',
		'func'=>'lc_collector_dates'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'coll_date',
		'func'=>'hrb_collector_dates'));
$requestFields[$i]['db_field']['cond'] = '';
$requestFields[$i]['db_field']['bindType'] = '';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['fieldPre'] = '';
$requestFields[$i]['db_field']['fieldPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Collection year';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

function lc_collector_dates_end($which) {
	global $bindArrInfo;
	global $lcQueryAndConditions;
	global $kMonthDayMonth;
	global $kMonthDayDay;
	switch ($which) {
		case 0:
			if (isset($_REQUEST['plant_collector_year_end']) && ($_REQUEST['plant_collector_year_end'] != '')) {
				if (isset($_REQUEST['plant_collector_monthday_end']) && ($_REQUEST['plant_collector_monthday_end'] != '')) {
					$dayMonthParts = explode('/', $_REQUEST['plant_collector_monthday_end']);
					if (count($dayMonthParts) == 2) {
						$bindArrInfo[] = array('type'=>'s',
											   'value'=>$_REQUEST['plant_collector_year_end'] . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayMonth]) . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayDay]),
											   'which'=>'lc');
						$lcQueryAndConditions[] = '`lc_accession`.`collector_date` <= ?';
					}
				} else {
					$bindArrInfo[] = array('type'=>'i',
										   'value'=>$_REQUEST['plant_collector_year_end'],
										   'which'=>'lc');
					$lcQueryAndConditions[] = 'YEAR(`lc_accession`.`collector_date`) <= ?';
				}
			}
			break;
	
		case 1:
			if (isset($_REQUEST['plant_collector_year_end']) && ($_REQUEST['plant_collector_year_end'] != '')) {
				if (isset($_REQUEST['plant_collector_monthday_end']) && ($_REQUEST['plant_collector_monthday_end'] != '')) {
					$dayMonthParts = explode('/', $_REQUEST['plant_collector_monthday_end']);
					if (count($dayMonthParts) == 2) {
						return '`lc_accession`.`collector_date` = ' . $_REQUEST['plant_collector_year_end'] . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayMonth]) . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayDay]);
					}
				} else {
					return '`lc_accession`.`collector_date` = ' . $_REQUEST['plant_collector_year_end'];
				}
			}
			break;
			
		case 2:
			if (isset($_REQUEST['plant_collector_year_end']) && ($_REQUEST['plant_collector_year_end'] != '')) {
				if (isset($_REQUEST['plant_collector_monthday_end']) && ($_REQUEST['plant_collector_monthday_end'] != '')) {
					$dayMonthParts = explode('/', $_REQUEST['plant_collector_monthday_end']);
					if (count($dayMonthParts) == 2) {
						return 'Collection date is on or before ' . date('d M Y', mktime(0, 0, 0, $dayMonthParts[$kMonthDayMonth], $dayMonthParts[$kMonthDayDay], $_REQUEST['plant_collector_year_end']));
					}
				} else {
					return 'Collection year is on or before ' . $_REQUEST['plant_collector_year_end'];
				}
			}
			break;
	}
}

function hrb_collector_dates_end($which) {
	global $bindArrInfo;
	global $hrbQueryAndConditions;
	global $kMonthDayMonth;
	global $kMonthDayDay;
	switch ($which) {
		case 0:
			if (isset($_REQUEST['plant_collector_year_end']) && ($_REQUEST['plant_collector_year_end'] != '')) {
				if (isset($_REQUEST['plant_collector_monthday_end']) && ($_REQUEST['plant_collector_monthday_ed'] != '')) {
					$dayMonthParts = explode('/', $_REQUEST['plant_collector_monthday_end']);
					if (count($dayMonthParts) == 2) {
						$bindArrInfo[] = array('type'=>'s',
											   'value'=>$_REQUEST['plant_collector_year_end'] . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayMonth]) . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayDay]),
											   'which'=>'hrb');
						$hrbQueryAndConditions[] = '`hrb_accession`.`coll_date` <= ?';
					}
				} else {
					$bindArrInfo[] = array('type'=>'i',
										   'value'=>$_REQUEST['plant_collector_year_end'],
										   'which'=>'hrb');
					$hrbQueryAndConditions[] = 'YEAR(`hrb_accession`.`coll_date`) <= ?';
				}
			}
			break;
			
		case 1:
			if (isset($_REQUEST['plant_collector_year_end']) && ($_REQUEST['plant_collector_year_end'] != '')) {
				if (isset($_REQUEST['plant_collector_monthday_end']) && ($_REQUEST['plant_collector_monthday_ed'] != '')) {
					$dayMonthParts = explode('/', $_REQUEST['plant_collector_monthday_end']);
					if (count($dayMonthParts) == 2) {
						return '`hrb_accession`.`coll_date` = ' . $_REQUEST['plant_collector_year_end'] . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayMonth]) . '-' . sprintf('%02d', $dayMonthParts[$kMonthDayDay]);
					}
				} else {
					return '`hrb_accession`.`coll_date` = ' . $_REQUEST['plant_collector_year_end'];
				}
			}
			break;
			
		case 2:
			if (isset($_REQUEST['plant_collector_year_end']) && ($_REQUEST['plant_collector_year_end'] != '')) {
				if (isset($_REQUEST['plant_collector_monthday_end']) && ($_REQUEST['plant_collector_monthday_end'] != '')) {
					$dayMonthParts = explode('/', $_REQUEST['plant_collector_monthday_end']);
					if (count($dayMonthParts) == 2) {
						return 'Collection date is on or before ' . date('d M Y', mktime(0, 0, 0, $dayMonthParts[$kMonthDayMonth], $dayMonthParts[$kMonthDayDay], $_REQUEST['plant_collector_year_end']));
					}
				} else {
					return 'Collection year is on or before ' . $_REQUEST['plant_collector_year_end'];
				}
			}
			break;
	}
}
$i++;
$requestFields[$i]['field_name'] = 'plant_collector_year_end';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_date',
		'func'=>'lc_collector_dates_end'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'coll_date',
		'func'=>'hrb_collector_dates_end'));
$requestFields[$i]['db_field']['cond'] = '';
$requestFields[$i]['db_field']['bindType'] = '';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['fieldPre'] = '';
$requestFields[$i]['db_field']['fieldPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Collection year end';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_collector_monthday';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array();
$requestFields[$i]['db_field']['cond'] = '';
$requestFields[$i]['db_field']['bindType'] = '';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Collection month/day';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['ignore_status'] = true;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_collector_monthday_end';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array();
$requestFields[$i]['db_field']['cond'] = '';
$requestFields[$i]['db_field']['bindType'] = '';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Collection month/day end';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['ignore_status'] = true;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_herbarium_flower';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'fl'));
$requestFields[$i]['db_field']['cond'] = '= \'Y\'';
$requestFields[$i]['db_field']['bindType'] = '';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Plant Herbarium Flower type';
$requestFields[$i]['field_no_value'] = true;
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_herbarium_fruit';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'fr'));
$requestFields[$i]['db_field']['cond'] = '= \'Y\'';
$requestFields[$i]['db_field']['bindType'] = '';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Plant Herbarium Fruit type';
$requestFields[$i]['field_no_value'] = true;
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_herbarium_veg';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'veg'));
$requestFields[$i]['db_field']['cond'] = '= \'Y\'';
$requestFields[$i]['db_field']['bindType'] = '';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Plant Herbarium Vegetable type';
$requestFields[$i]['field_no_value'] = true;
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'plant_herbarium_bud';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'bud'));
$requestFields[$i]['db_field']['cond'] = '= \'Y\'';
$requestFields[$i]['db_field']['bindType'] = '';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Plant Herbarium Bud type';
$requestFields[$i]['field_no_value'] = true;
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

function lc_habitat($which) {
	global $bindArrInfo;
	global $lcQueryAndConditions;
	$conditionalStr = '';
	switch ($which) {
		case 0:
			if (isset($_REQUEST['hab_keyword']) && ($_REQUEST['hab_keyword'] != '')) {
				$wordList = explode(' ', $_REQUEST['hab_keyword']);
				$conditions = array();
				foreach ($wordList as $aWord) {
					$bindArrInfo[] = array('type'=>'s',
										   'value'=>'%' . implode('\%', explode('%', $aWord)) . '%',
										   'which'=>'lc');
					$conditions[] .= '( `lc_accession`.`collector_habitat` LIKE ?)';
				}
				if (count($conditions) > 1) {
					$conditionalStr = '(' . join(strtoupper($_REQUEST['hab_andor']), $conditions) . ')';
				} else {
					$conditionalStr = $conditions[0];
				}
			}
			break;
			
		case 1:
			if (isset($_REQUEST['hab_keyword']) && ($_REQUEST['hab_keyword'] != '')) {
				$wordList = explode(' ', $_REQUEST['hab_keyword']);
				$conditions = array();
				foreach ($wordList as $aWord) {
					$conditions[] .= '( `lc_accession`.`collector_habitat` = %' . implode('\%', explode('%', $aWord)) . '%)';
				}
				if (count($conditions) > 1) {
					$conditionalStr = '(' . join(strtoupper($_REQUEST['hab_andor']), $conditions) . ')';
				} else {
					$conditionalStr = $conditions[0];
				}
			}
			break;
			
		case 2:
			if (isset($_REQUEST['hab_keyword']) && ($_REQUEST['hab_keyword'] != '')) {
				$conditionalStr = "Habitat contains "
					. implode(strtoupper($_REQUEST['hab_andor']), explode(' ', $_REQUEST['hab_keyword']));
			}
			break;
	}
	if ($which == 0) {
		$lcQueryAndConditions[] = $conditionalStr;
	} else {
		return $conditionalStr;
	}
}

function hrb_habitat($which) {
	global $bindArrInfo;
	global $hrbQueryAndConditions;
	$conditionalStr = '';
	switch ($which) {
		case 0:
			if (isset($_REQUEST['hab_keyword']) && ($_REQUEST['hab_keyword'] != '')) {
				$wordList = explode(' ', $_REQUEST['hab_keyword']);
				$conditions = array();
				foreach ($wordList as $aWord) {
					$bindArrInfo[] = array('type'=>'s',
										   'value'=>'%' . implode('\%', explode('%', $aWord)) . '%',
										   'which'=>'hrb');
					$conditions[] .= '( `hrb_accession`.`habitat` LIKE ?)';
				}
				if (count($conditions) > 1) {
					$conditionalStr = '(' . join(strtoupper($_REQUEST['hab_andor']), $conditions) . ')';
				} else {
					$conditionalStr = $conditions[0];
				}
			}
			break;
			
		case 1:
			if (isset($_REQUEST['hab_keyword']) && ($_REQUEST['hab_keyword'] != '')) {
				$wordList = explode(' ', $_REQUEST['hab_keyword']);
				$conditions = array();
				foreach ($wordList as $aWord) {
					$conditions[] .= '( `hrb_accession`.`habitat` = %' . implode('\%', explode('%', $aWord)) . '%)';
				}
				if (count($conditions) > 1) {
					$conditionalStr = '(' . join(strtoupper($_REQUEST['hab_andor']), $conditions) . ')';
				} else {
					$conditionalStr = $conditions[0];
				}
			}
			break;
			
		case 2:
			if (isset($_REQUEST['hab_keyword']) && ($_REQUEST['hab_keyword'] != '')) {
				$conditionalStr = "Habitat contains "
					. implode(strtoupper($_REQUEST['hab_andor']), explode(' ', $_REQUEST['hab_keyword']));
			}
			break;
	}
	if ($which == 0) {
		$hrbQueryAndConditions[] = $conditionalStr;
	} else {
		return $conditionalStr;
	}
}
$i++;
$requestFields[$i]['field_name'] = 'hab_keyword';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_habitat',
		'func'=>'lc_habitat'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'habitat',
		'func'=>'hrb_habitat'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Habitat Keyword';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'hab_andor';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array();
$requestFields[$i]['db_field']['cond'] = '';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = '';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['ignore_status'] = true;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'country';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_country'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'country'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Country';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'subcountry1';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_subcountry_1'),
		array('table'=>'lc_accession', 'field'=>'collector_subcountry_2'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'subctry1'),
		array('table'=>'hrb_accession', 'field'=>'subctry2'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Subcountry';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'subcountry2';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_subcountry_1'),
		array('table'=>'lc_accession', 'field'=>'collector_subcountry_2'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'subctry1'),
		array('table'=>'hrb_accession', 'field'=>'subctry2'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Subcountry';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'township_name';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_township_range'));
$requestFields[$i]['db_field']['hrb'] = array();
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Township (name)';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'township_nbr';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_township_range'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'twp'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Township (number)';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'township_ns';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_township_range'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'twp_dir'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Range N/S';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'range_nbr';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_township_range'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'range'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Range (number)';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'township_ew';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_township_range'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'range_dir'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Section E/W';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'section';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_township_range'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'sect'),
		array('table'=>'hrb_accession', 'field'=>'sect_desc'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Section';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'latitude_max';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_lat'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'lat'));
$requestFields[$i]['db_field']['cond'] = '<=';
$requestFields[$i]['db_field']['bindType'] = 'd';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Latitude (max)';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'latitude_min';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_lat'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'lat'));
$requestFields[$i]['db_field']['cond'] = '>=';
$requestFields[$i]['db_field']['bindType'] = 'd';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Latitude (min)';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'longitude_max';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_long'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'long'));
$requestFields[$i]['db_field']['cond'] = '<=';
$requestFields[$i]['db_field']['bindType'] = 'd';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Longitude (max)';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'longitude_min';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_long'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'long'));
$requestFields[$i]['db_field']['cond'] = '>=';
$requestFields[$i]['db_field']['bindType'] = 'd';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Longitude (min)';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'utm_zone';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'utm_zone'));
$requestFields[$i]['db_field']['cond'] = '=';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'UTM zone';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'utm_zone_letter';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'utm_zone_letter'));
$requestFields[$i]['db_field']['cond'] = '=';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'UTM letter';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'utm_easting';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'utm_easting'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'UTM Easting';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'utm_northing';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'utm_northing'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'UTM Northing';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'utm_radius';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array();
$requestFields[$i]['db_field']['cond'] = '';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'UTM radius';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

function lc_site($which) {
	global $bindArrInfo;
	global $lcQueryAndConditions;
	$conditionalStr = '';
	switch ($which) {
		case 0:
			if (isset($_REQUEST['site_keyword']) && ($_REQUEST['site_keyword'] != '')) {
				$wordList = explode(' ', $_REQUEST['site_keyword']);
				$conditions = array();
				foreach ($wordList as $aWord) {
					$bindArrInfo[] = array('type'=>'s',
										   'value'=>'%' . implode('\%', explode('%', $aWord)) . '%',
										   'which'=>'lc');
					$conditions[] .= '( `lc_accession`.`collector_site` LIKE ?)';
				}
				if (count($conditions) > 1) {
					$conditionalStr = '(' . join(strtoupper($_REQUEST['site_andor']), $conditions) . ')';
				} else {
					$conditionalStr = $conditions[0];
				}
			}
			break;
			
		case 1:
			if (isset($_REQUEST['site_keyword']) && ($_REQUEST['site_keyword'] != '')) {
				$wordList = explode(' ', $_REQUEST['site_keyword']);
				$conditions = array();
				foreach ($wordList as $aWord) {
					$conditions[] .= '( `lc_accession`.`collector_site` = %' . implode('\%', explode('%', $aWord)) . '%)';
				}
				if (count($conditions) > 1) {
					$conditionalStr = '(' . join(strtoupper($_REQUEST['site_andor']), $conditions) . ')';
				} else {
					$conditionalStr = $conditions[0];
				}
			}
			break;
			
		case 2:
			if (isset($_REQUEST['site_keyword']) && ($_REQUEST['site_keyword'] != '')) {
				$conditionalStr = "Site contains "
					. implode(strtoupper($_REQUEST['site_andor']), explode(' ', $_REQUEST['hab_keyword']));
			}
			break;
	}
	if ($which == 0) {
		$lcQueryAndConditions[] = $conditionalStr;
	} else {
		return $conditionalStr;
	}
}

function hrb_site($which) {
	global $bindArrInfo;
	global $hrbQueryAndConditions;
	$conditionalStr = '';
	switch ($which) {
		case 0:
			if (isset($_REQUEST['site_keyword']) && ($_REQUEST['site_keyword'] != '')) {
				$wordList = explode(' ', $_REQUEST['site_keyword']);
				$conditions = array();
				foreach ($wordList as $aWord) {
					$bindArrInfo[] = array('type'=>'s',
										   'value'=>'%' . implode('\%', explode('%', $aWord)) . '%',
										   'which'=>'hrb');
					$conditions[] .= '( `hrb_accession`.`site` LIKE ?)';
				}
				if (count($conditions) > 1) {
					$conditionalStr = '(' . join(strtoupper($_REQUEST['site_andor']), $conditions) . ')';
				} else {
					$conditionalStr = $conditions[0];
				}
			}
			break;
			
		case 1:
			if (isset($_REQUEST['site_keyword']) && ($_REQUEST['site_keyword'] != '')) {
				$wordList = explode(' ', $_REQUEST['site_keyword']);
				$conditions = array();
				foreach ($wordList as $aWord) {
					$conditions[] .= '( `hrb_accession`.`site` = %' . implode('\%', explode('%', $aWord)) . '%)';
				}
				if (count($conditions) > 1) {
					$conditionalStr = '(' . join(strtoupper($_REQUEST['site_andor']), $conditions) . ')';
				} else {
					$conditionalStr = $conditions[0];
				}
			}
			break;
			
		case 2:
			if (isset($_REQUEST['site_keyword']) && ($_REQUEST['site_keyword'] != '')) {
				$conditionalStr = "Site contains "
					. implode(strtoupper($_REQUEST['site_andor']), explode(' ', $_REQUEST['hab_keyword']));
			}
			break;
	}
	if ($which == 0) {
		$hrbQueryAndConditions[] = $conditionalStr;
	} else {
		return $conditionalStr;
	}
}
$i++;
$requestFields[$i]['field_name'] = 'site_keyword';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'lc_accession', 'field'=>'collector_site',
		'func'=>'lc_site'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'hrb_accession', 'field'=>'site',
		'func'=>'hrb_site'));
$requestFields[$i]['db_field']['cond'] = 'LIKE';
$requestFields[$i]['db_field']['bindType'] = 's';
$requestFields[$i]['db_field']['modPre'] = '%';
$requestFields[$i]['db_field']['modPost'] = '%';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'Site Keyword';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

$i++;
$requestFields[$i]['field_name'] = 'site_andor';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array();
$requestFields[$i]['db_field']['cond'] = '';
$requestFields[$i]['db_field']['bindType'] = '';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = '';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['ignore_status'] = true;
$requestFields[$i]['field_req'] = '';

function lc_zoneFunc($which) {
	global $bindArrInfo;
	global $lcQueryAndConditions;
	$conditionalStr = '';
	switch ($which) {
		case 0:
			if (isset($_REQUEST['zone_min']) && ($_REQUEST['zone_min'] != '')) {
				$bindArrInfo[] = array('type'=>'i',
									   'value'=>$_REQUEST['zone_min'],
									   'which'=>'lc');
				if (isset($_REQUEST['zone_max']) && ($_REQUEST['zone_max'] != '')) {
					$bindArrInfo[] = array('type'=>'i',
										   'value'=>$_REQUEST['zone_max'],
										   'which'=>'lc');
					if ($_REQUEST['zone_min'] == $_REQUEST['zone_max']) {
						$conditionalStr = '( `sciname`.`usda_zone_lo` = ?)'
							. ' AND ((`sciname`.`usda_zone_hi` IS NULL) OR (`sciname`.`usda_zone_hi` = ?))';
					} else {
						$conditionalStr = '( `sciname`.`usda_zone_lo` >= ?)'
							. ' AND ((`sciname`.`usda_zone_hi` IS NULL) OR (`sciname`.`usda_zone_hi` <= ?))';
					}
				} else {
					$bindArrInfo[] = array('type'=>'i',
										   'value'=>$_REQUEST['zone_min'],
										   'which'=>'lc');
					$conditionalStr = '( `sciname`.`usda_zone_lo` <= ?)'
						. ' AND ((`sciname`.`usda_zone_hi` IS NULL) OR (`sciname`.`usda_zone_hi` >= ?))';
				}
			}
			break;
			
		case 1:
			if (isset($_REQUEST['zone_min']) && ($_REQUEST['zone_min'] != '')) {
				if (isset($_REQUEST['zone_max']) && ($_REQUEST['zone_max'] != '')) {
					if ($_REQUEST['zone_min'] == $_REQUEST['zone_max']) {
						$conditionalStr = '( `sciname`.`usda_zone_lo` = ' . $_REQUEST['zone_min'] . ')'
							. ' AND ((`sciname`.`usda_zone_hi` IS NULL) OR (`sciname`.`usda_zone_hi` = ' . $_REQUEST['zone_max'] . '))';
					} else {
						$conditionalStr = '( `sciname`.`usda_zone_lo` >= ' . $_REQUEST['zone_min'] . ')'
							. ' AND ((`sciname`.`usda_zone_hi` IS NULL) OR (`sciname`.`usda_zone_hi` <= ' . $_REQUEST['zone_max'] . '))';
					}
				} else {
					$conditionalStr = '( `sciname`.`usda_zone_lo` <= ' . $_REQUEST['zone_min'] . ')'
						. ' AND ((`sciname`.`usda_zone_hi` IS NULL) OR (`sciname`.`usda_zone_hi` >= ' . $_REQUEST['zone_min'] . '))';
				}
			}
			break;
			
		case 2:
			if (isset($_REQUEST['zone_min']) && ($_REQUEST['zone_min'] != '')) {
				$conditionalStr = 'USDA Zone is ';
				if (isset($_REQUEST['zone_max']) && ($_REQUEST['zone_max'] != '') && ($_REQUEST['zone_min'] != $_REQUEST['zone_max'])) {
					$conditionalStr .= ' between ' . $_REQUEST['zone_min'] . ' and ' . $_REQUEST['zone_max'];
				} else {
					$conditionalStr .= $_REQUEST['zone_min'];
				}
			}
			break;
	}
	if ($which == 0) {
		$lcQueryAndConditions[] = $conditionalStr;
	} else {
		return $conditionalStr;
	}
}
function hrb_zoneFunc($which) {
	global $bindArrInfo;
	global $hrbQueryAndConditions;
	$conditionalStr = '';
	switch ($which) {
		case 0:
			if (isset($_REQUEST['zone_min']) && ($_REQUEST['zone_min'] != '')) {
				$bindArrInfo[] = array('type'=>'i',
									   'value'=>$_REQUEST['zone_min'],
									   'which'=>'hrb');
				if (isset($_REQUEST['zone_max']) && ($_REQUEST['zone_max'] != '')) {
					$bindArrInfo[] = array('type'=>'i',
										   'value'=>$_REQUEST['zone_max'],
										   'which'=>'hrb');
					if ($_REQUEST['zone_min'] == $_REQUEST['zone_max']) {
						$conditionalStr = '( `sciname`.`usda_zone_lo` = ?)'
							. ' AND ((`sciname`.`usda_zone_hi` IS NULL) OR (`sciname`.`usda_zone_hi` = ?))';
					} else {
						$conditionalStr = '( `sciname`.`usda_zone_lo` >= ?)'
							. ' AND ((`sciname`.`usda_zone_hi` IS NULL) OR (`sciname`.`usda_zone_hi` <= ?))';
					}
				} else {
					$bindArrInfo[] = array('type'=>'i',
									   'value'=>$_REQUEST['zone_min'],
									   'which'=>'hrb');
					$conditionalStr = '(( `sciname`.`usda_zone_lo` <= ?) AND '
							. '((`sciname`.`usda_zone_hi` IS NULL) OR (`sciname`.`usda_zone_hi` >= ?)))';
				}
			}
			break;
			
		case 1:
			if (isset($_REQUEST['zone_min']) && ($_REQUEST['zone_min'] != '')) {
				if (isset($_REQUEST['zone_max']) && ($_REQUEST['zone_max'] != '')) {
					if ($_REQUEST['zone_min'] == $_REQUEST['zone_max']) {
						$conditionalStr = '( `sciname`.`usda_zone_lo` = ' . $_REQUEST['zone_min'] . ')'
							. ' AND ((`sciname`.`usda_zone_hi` IS NULL) OR (`sciname`.`usda_zone_hi` = ' . $_REQUEST['zone_max'] . '))';
					} else {
						$conditionalStr = '( `sciname`.`usda_zone_lo` >= ' . $_REQUEST['zone_min'] . ')'
							. ' AND ((`sciname`.`usda_zone_hi` IS NULL) OR (`sciname`.`usda_zone_hi` <= ' . $_REQUEST['zone_max'] . '))';
					}
				} else {
					$conditionalStr = '( `sciname`.`usda_zone_lo` <= ' . $_REQUEST['zone_min'] . ')'
						. ' AND ((`sciname`.`usda_zone_hi` IS NULL) OR (`sciname`.`usda_zone_hi` >= ' . $_REQUEST['zone_min'] . '))';
				}
			}
			break;
			
		case 2:
			if (isset($_REQUEST['zone_min']) && ($_REQUEST['zone_min'] != '')) {
				$conditionalStr = 'USDA Zone is ';
				if (isset($_REQUEST['zone_max']) && ($_REQUEST['zone_max'] != '') && ($_REQUEST['zone_min'] != $_REQUEST['zone_max'])) {
					$conditionalStr .= ' between ' . $_REQUEST['zone_min'] . ' and ' . $_REQUEST['zone_max'];
				} else {
					$conditionalStr .= $_REQUEST['zone_min'];
				}
			}
			break;
	}
	if ($which == 0) {
		$hrbQueryAndConditions[] = $conditionalStr;
	} else {
		return $conditionalStr;
	}
}
$i++;
$requestFields[$i]['field_name'] = 'zone_min';
$requestFields[$i]['db_field']['lc'] = array(array('table'=>'sciname', 'field'=>'usda_zone_lo',
		'func'=>'lc_zoneFunc'));
$requestFields[$i]['db_field']['hrb'] = array(array('table'=>'sciname', 'field'=>'usda_zone_lo',
		'func'=>'hrb_zoneFunc'));
$requestFields[$i]['db_field']['cond'] = '>=';
$requestFields[$i]['db_field']['bindType'] = 'i';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'USDA Zone Min';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';

/*$i++;
$requestFields[$i]['field_name'] = 'zone_max';
$requestFields[$i]['db_field']['lc'] = array();
$requestFields[$i]['db_field']['hrb'] = array();
$requestFields[$i]['db_field']['cond'] = '<=';
$requestFields[$i]['db_field']['bindType'] = 'i';
$requestFields[$i]['db_field']['modPre'] = '';
$requestFields[$i]['db_field']['modPost'] = '';
$requestFields[$i]['db_field']['xformFunc'] = '';
$requestFields[$i]['field_desc'] = 'USDA Zone Max';
$requestFields[$i]['field_cond'] = false;
$requestFields[$i]['field_req'] = '';*/


$numUsedFields = 0;
$numLCUsedFields = 0;
$numHrbUsedFields = 0;
$fieldErrors = array();
foreach ($requestFields as $requestField) {
	if (isset($_REQUEST[$requestField['field_name']]) && ($_REQUEST[$requestField['field_name']] != '')) {
		if (($requestField['field_req'] == '')
				|| (($requestField['field_req'] != '') && in_array($_REQUEST[$requestField['field_req']], $requestField['field_values']))) {
			++$numUsedFields;
			//
			// Change the search_type to "living" in the case where coll_area is set to something other than
			// blank.   This makes certain that only living specimens are returned when this is selected.
			// MORARB-24.  GC
			//
			if (!isset($_REQUEST['coll_area']) || $_REQUEST['coll_area'] != '') {
				$_REQUEST['search_type'] = 'living';
			}
			if ($requestField['field_name'] != 'search_type') {
				++$numLCUsedFields;
				++$numHrbUsedFields;
				$validField = array('lc'=>(($_REQUEST['search_type'] == 'living') || ($_REQUEST['search_type'] == 'combined')),
						'hrb'=>(($_REQUEST['search_type'] == 'herbarium') || ($_REQUEST['search_type'] == 'combined')));
				if ((($_REQUEST['search_type'] == 'living') || ($_REQUEST['search_type'] == 'combined'))
					&& isset($requestField['db_field']['lc'][0]['permit_wildcard'])
					&& (strpos($_REQUEST[$requestField['field_name']], '*') !== false)) {
					$permitWildcard = false;
					if (isset($requestField['db_field']['lc'][0]['permit_wildcard'])) {
						$permitWildcard = $requestField['db_field']['lc'][0]['permit_wildcard'];
					}
					$wildcardMatches = false;
					if ($permitWildcard && isset($requestField['db_field']['lc'][0]['wildcard_regex']) && ($requestField['db_field']['lc'][0]['wildcard_regex'] != '')) {
						$wildcardRegex = $requestField['db_field']['lc'][0]['wildcard_regex'];
						$fieldContents = $_REQUEST[$requestField['field_name']];
						$wildcardMatches = (preg_match($wildcardRegex, $fieldContents) != 0);
					}
					if ($permitWildcard && !$wildcardMatches) {
						$validField['lc'] = false;
						--$numLCUsedFields;
					} elseif (($_REQUEST['search_type'] == 'combined') && !isset($requestField['db_field']['hrb'][0]['permit_wildcard'])) {
						$validField['hrb'] = false;
						--$numHrbUsedFields;
					}
				} else if ((($_REQUEST['search_type'] == 'herbarium') || ($_REQUEST['search_type'] == 'combined'))
					&& isset($requestField['db_field']['hrb'][0]['permit_wildcard'])
					&& (strpos($_REQUEST[$requestField['field_name']], '*') !== false)) {
					$permitWildcard = false;
					if (isset($requestField['db_field']['hrb'][0]['permit_wildcard'])) {
						$permitWildcard = $requestField['db_field']['hrb'][0]['permit_wildcard'];
					}
					$wildcardMatches = false;
					if ($permitWildcard && isset($requestField['db_field']['hrb'][0]['wildcard_regex']) && ($requestField['db_field']['hrb'][0]['wildcard_regex'] != '')) {
						$wildcardRegex = $requestField['db_field']['hrb'][0]['wildcard_regex'];
						$fieldContents = $_REQUEST[$requestField['field_name']];
						$wildcardMatches = (preg_match($wildcardRegex, $fieldContents) != 0);
					}
					if ($permitWildcard && !$wildcardMatches) {
						$validField['hrb'] = false;
						--$numHrbUsedFields;
					}
				} else {
					$validField['lc'] = $validField['hrb'] = true;
				}
				if (!$validField['lc'] && !$validField['hrb']) {
					$numUsedFields--;
					if ($_REQUEST['search_type'] == 'combined') {
						$fieldErrors[] = 'Invalid wildcard expression for ' . $requestField['field_desc'];
					} elseif ($_REQUEST['search_type'] == 'living') {
						$fieldErrors[] = 'Invalid Living Collection wildcard expression for ' . $requestField['field_desc'];
					} else {
						$fieldErrors[] = 'Invalid Herbarium wildcard expression for ' . $requestField['field_desc'];
					}
					$_REQUEST[$requestField['field_req']] = '';
				}
			}
		}
	}
}
if (count($fieldErrors) > 0) {
	$numUsedFields = 0;
	unset($_REQUEST['download_file']);
}
if ($numUsedFields == 0) {
	$pageLocation = 'Advanced Search';
	unset($_REQUEST['download_file']);
} else
	$pageLocation = 'Search Results';
if (!isset($_REQUEST['download_file'])) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
      "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>Morton Arboretum</title>
  <link rel="stylesheet" type="text/css" href="libraries/main.php" />
  <?php 
  	$sUserAgent = $_SERVER['HTTP_USER_AGENT'];
  	$isKHTML = strpos($sUserAgent,"KHTML") != false
		|| strpos($sUserAgent,"Konqueror") != false
		|| strpos($sUserAgent,"AppleWebKit") != false;
	$isMoz = strpos($sUserAgent,"Gecko") != false
		&& $isKHTML == false;		
  	//echo '<!-- '. $sUserAgent .'-->';
	if($isMoz)
  	{
  	  	echo '<link rel="stylesheet" href="libraries/forms-moz.css"  type="text/css" />';  	  	
  	}
  	else
  	{
  	  	echo '<link rel="stylesheet" href="libraries/forms.css"  type="text/css" />';  	  	
   	}
  ?>
  <!-- link rel="stylesheet" href="libraries/forms.css"  type="text/css" / -->
  <script type="text/javascript" src="libraries/main.js"></script>
  <script type="text/javascript">
  </script>

  <!--[if IE 5]>
    <style>body {text-align: center;}#wrapper {text-align: left;}#column_wrapper {height: 1%;}</style>
  <![endif]-->
  <!--[if lte IE 7]>
    <style>#wrapper, #footer, #masthead, #column_wrapper {zoom: 1;}</style>
  <![endif]-->

</head>
<body>
	<form name="gotoPageForm" action="." method="post">
		<input type="hidden" name="page" value=""></input>
	</form>
<?php
	require_once $app_root . '/modules/header_common.php';
?>	<div id="body" class="body">
		<div id="content" class="content">
<?php
}
if (!isset($_REQUEST['download_file'])) {
	if ($numUsedFields > 0) {
		showTabs($pages_info, null, 'results');	
	} else {
		showTabs($pages_info, null, 'advanced_search');

		if (file_exists($app_root . '/pages/advanced_search_text.html')) {
			$fileContents = file_get_contents($app_root . '/pages/advanced_search_text.html');
			$bodyStartPos = stripos($fileContents, '<body>');
			$bodyEndPos = stripos($fileContents, '</body>');
			$advanced_search_text_contents = trim(substr($fileContents,
					$bodyStartPos + 6,
					$bodyEndPos - ($bodyStartPos + 6)));
			if ($advanced_search_text_contents == '') {
				unset($advanced_search_text_contents);
			}
		}
	}
}
if (!isset($_REQUEST['download_file']) && isset($advanced_search_text_contents) && ($advanced_search_text_contents != '')) {
?>			<div class="adv_search_text">
<?php
	$text_array = explode("\n", $advanced_search_text_contents);
	for ($idx = 0; $idx < count($text_array); ++$idx) {
		$text_array[$idx] = ltrim($text_array[$idx], "\t");
	}
?>				<?php echo implode("\n" . '				', $text_array); ?>
			</div>
<?php
}
if ($numUsedFields > 0) {
	if (!isset($_REQUEST['download_file'])) {
		echo "\t\t\t" . '<div class="results">' . "\n";
		echo '			<table width="100%" border="0">' . "\n";
		echo '			<tr valign="bottom"><td>' . "\n";
		echo '			<br />' . "\n" . '			<b><em>Search term' . (($numUsedFields > 1) ? 's' : '') . ":</em></b>\n";
		echo '			<ul>' . "\n";
		$conditionalStr = '';
		$fieldName = '';
		foreach ($requestFields as $requestField) {
			if (isset($_REQUEST[$requestField['field_name']]) && ($_REQUEST[$requestField['field_name']] != '')) {
				if (($requestField['field_req'] == '')
						|| (($requestField['field_req'] != '') && in_array($_REQUEST[$requestField['field_req']], $requestField['field_values']))) {
					if ($requestField['field_cond']) {
						if ($conditionalStr == '') {
							$conditionalStr = '				<li>';
							$fieldName = $requestField['field_desc'];
						}
						if ($requestField['field_desc'] == '') {
							$conditionalStr .= ' ' . strtoupper($_REQUEST[$requestField['field_name']]) . ' ';
						} else {
							$conditionalStr .= '"' . $_REQUEST[$requestField] . '"';
						}
					} elseif (isset($requestField['db_field']['lc'][0]['func'])) {
						echo '				<li>' . call_user_func($requestField['db_field']['lc'][0]['func'], 2) . '</li>' . "\n";
					} elseif (!isset($requestField['ignore_status']) || !$requestField['ignore_status']) {
						if ($conditionalStr != '') {
							if (substr_compare($conditionalStr, ' AND ', -5) == 0) {
								$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 5);
							}
							else if (substr_compare($conditionalStr, ' OR ', -4) == 0) {
								$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 4);
							}
							echo $conditionalStr . ' in ' . $fieldName . '</li>' . "\n";
							$conditionalStr = '';
							$fieldName = '';
						}
						if (isset($requestField['value_strings'])) {
							echo '				<li>"' . $requestField['value_strings'][$_REQUEST[$requestField['field_name']]] . '" in ' . $requestField['field_desc'] . '</li>' . "\n";
						} elseif (isset($requestField['field_no_value']) && $requestField['field_no_value']) {
							echo '				<li>' . $requestField['field_desc'] . '</li>' . "\n";
						} else {
							echo '				<li>"' . $_REQUEST[$requestField['field_name']] . '" in ' . $requestField['field_desc'] . '</li>' . "\n";
						}
					}
				}
			}
		}
		echo '			</ul>' . "\n";
		echo '			<hr />' . "\n";
		echo '			</td>' . "\n";
		echo '			<td>' . "\n";
		if (($_REQUEST['search_type'] == 'living') || ($_REQUEST['search_type'] == 'combined')) {
			echo '				<button name="download_file_lc" type="button" onclick="javascript:window.location=\'' . getRootUrl() . '/advanced_search.php?download_file=lc&' . $_SERVER['QUERY_STRING'] . '\';">Download Living Collection File</button>';
			if ($_REQUEST['search_type'] == 'combined') {
				echo '<br />';
			}
			echo "\n";
		}
		if (($_REQUEST['search_type'] == 'herbarium') || ($_REQUEST['search_type'] == 'combined')) {
			echo '				<button name="download_file_hrb" type="button" onclick="javascript:window.location=\'' . getRootUrl() . '/advanced_search.php?download_file=hrb&' . $_SERVER['QUERY_STRING'] . '\';">Download Herbarium File</button>' . "\n";
		}
		echo '			</td>' . "\n";
		echo '			</tr></table>' . "\n";
		$numTaxa = 0;
		$numAccessions = 0;
		$numLivingColl = 0;
	}
	
	if (!isset($_REQUEST['download_file'])) {
		flush();
	}
	
	require $app_root . '/config.inc.php';
	
	{
		$link = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);
		
		$results = array();
		$scinames = array();
		if (mysqli_connect_error() == 0) {
			$bindArrInfo = array();
			$lcQueryFields = array('`lc_accession`.`id`',
					'`lc_accession`.`accession_no`',
					//'`lc_accession`.`no_received`',
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
					'NULL as image_thumb',
					'NULL as image_large'
				);
			$lcQueryTables = array('lc_accession');
			$lcQueryNames = array('lc_id',
					'lc_accession_no',
					//'lc_no_received',
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
					'image_thumb',
					'image_large'
				);
			//from hrb_accession: collector_primary, collector_addl, coll_no, coll_date,
			//coll_date_acc, site, site_sensitive, desig1, subctry1, desig2, subctry2, country
			$hrbQueryFields = array('`hrb_accession`.`id`',
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
			$hrbQueryTables = array('hrb_accession');
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
				
			//echo "Here....";
			$queryFields = array('`sciname`.`id`',
					'`sciname`.`scientific_name_id`',
					'`sciname`.`sort_scientific_name`',
					'`sciname`.`scientific_name`',
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
					'sciname_scientific_name',
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
				
			//echo $queryFields;
			//echo $queryNames;
			$queryTables = array('sciname');
			$lcQueryAndConditions = array();
			$hrbQueryAndConditions = array();
			$allowHRB = (($_REQUEST['search_type'] == 'herbarium') || ($_REQUEST['search_type'] == 'combined'));
			
			// Do the LC search if LC or Combined search type
			if (($numLCUsedFields > 0) && (($_REQUEST['search_type'] == 'living') || ($_REQUEST['search_type'] == 'combined'))) {
				$conditionalStr = '';
				$fieldName = '';
				$conditionalCount = 0;
				foreach ($requestFields as $requestField) {
					if (isset($_REQUEST[$requestField['field_name']])
							&& ($_REQUEST[$requestField['field_name']] != '')
							&& (count($requestField['db_field']['lc']) != 0)) {
						if (($requestField['field_req'] == '')
							|| (($requestField['field_req'] != '') && in_array($_REQUEST[$requestField['field_req']], $requestField['field_values']))) {
							if ($requestField['field_cond']) {
								if ($fieldName == '') {
									if ($conditionalStr != '') {
										if (substr_compare($conditionalStr, ' AND ', -5) == 0) {
											$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 5);
											$condLoop = true;
										}
										else if (substr_compare($conditionalStr, ' OR ', -4) == 0) {
											$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 4);
											$condLoop = true;
										}
										if ($conditionalCount > 1) {
											$lcQueryAndConditions[] = '(' . $conditionalStr . ')';
										} else {
											$lcQueryAndConditions[] = $conditionalStr;
										}
										$conditionalStr = '';
										$conditionalCount = 0;
										$fieldName = '';
									}
									$fieldName = '`' . $requestField['db_field']['lc'][0]['table'] . '`.`' . $requestField['db_field']['lc'][0]['field'];
									if (!in_array($requestField['db_field']['lc'][0]['table'], $lcQueryTables)
										&& !in_array($requestField['db_field']['lc'][0]['table'], $queryTables)) {
										$lcQueryTables[] = $requestField['db_field']['lc'][0]['table'];
									}
								}
								if ($requestField['field_desc'] == '') {
									$conditionalStr .= ' ' . strtoupper($_REQUEST[$requestField['field_name']]) . ' ';
								} else {
									$bindArrInfo[] = array('type'=>$db_field['bindType'],
											'value'=>$_REQUEST[$requestField['field_name']],
											'which'=>'lc');
									$conditionalStr .= '(' . $fieldName . ' ' . $requestField['db_field']['cond'] . ' ?)';
									$conditionalCount++;
								}
							} else if (isset($requestField['db_field']['lc'][0]['func'])) {
								if ($conditionalStr != '') {
									if (substr_compare($conditionalStr, ' AND ', -5) == 0) {
										$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 5);
										$condLoop = true;
									}
									else if (substr_compare($conditionalStr, ' OR ', -4) == 0) {
										$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 4);
										$condLoop = true;
									}
									if ($conditionalCount > 1) {
										$lcQueryAndConditions[] = '(' . $conditionalStr . ')';
									} else {
										$lcQueryAndConditions[] = $conditionalStr;
									}
									$conditionalStr = '';
									$conditionalCount = 0;
									$fieldName = '';
								}
								call_user_func($requestField['db_field']['lc'][0]['func'], 0);
							} else {
								if ($conditionalStr != '') {
									if (substr_compare($conditionalStr, ' AND ', -5) == 0) {
										$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 5);
										$condLoop = true;
									}
									else if (substr_compare($conditionalStr, ' OR ', -4) == 0) {
										$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 4);
										$condLoop = true;
									}
									if ($conditionalCount > 1) {
										$lcQueryAndConditions[] = '(' . $conditionalStr . ')';
									} else {
										$lcQueryAndConditions[] = $conditionalStr;
									}
									$conditionalStr = '';
									$conditionalCount = 0;
									$fieldName = '';
								}
								$db_field = $requestField['db_field'];
								if (count($db_field['lc']) != 0) {
									$condOr = array();
									$conditional = $db_field['cond'];
									if ($db_field['bindType'] != '') {
										$value = implode('\%', explode('%', $_REQUEST[$requestField['field_name']]));
										$valueIsNotWildcard = (strpos($value, '*') === false);
										if ($db_field['xformFunc'] != '') {
											$xformFunc = $db_field['xformFunc'];
											$value = call_user_func($xformFunc, $value);
										}
										$permitWildcard = false;
										if (isset($db_field['lc'][0]['permit_wildcard'])) {
											$permitWildcard = $db_field['lc'][0]['permit_wildcard'];
										}
										$wildcardMatches = false;
										if ($permitWildcard && isset($db_field['lc'][0]['wildcard_regex']) && ($db_field['lc'][0]['wildcard_regex'] != '')) {
											$wildcardRegex = $db_field['lc'][0]['wildcard_regex'];
											$fieldContents = $_REQUEST[$requestField['field_name']];
											$wildcardMatches = (preg_match($wildcardRegex, $fieldContents) != 0);
										}
										if ($permitWildcard && $wildcardMatches) {
											$conditional = 'LIKE';
											$value = implode('%', explode('*', $value));
										}
										if (!$permitWildcard || $wildcardMatches || $valueIsNotWildcard) {
											foreach ($db_field['lc'] as $tableField) {
												if (!in_array($tableField['table'], $lcQueryTables)
													&& !in_array($tableField['table'], $queryTables)) {
													$lcQueryTables[] = $tableField['table'];
												}
												$bindArrInfo[] = array('type'=>$db_field['bindType'],
														'value'=>$db_field['modPre'] . $value . $db_field['modPost'],
														'which'=>'lc');
												if (isset($db_field['fieldPre']) && isset($db_field['fieldPost'])) {
													$condOr[] = $db_field['fieldPre'] . '`' . $tableField['table'] . '`.`' . $tableField['field'] . '`' . $db_field['fieldPost'] . ' ' . $conditional . ' ?';
												} else {
													$condOr[] = '`' . $tableField['table'] . '`.`' . $tableField['field'] . '` ' . $conditional . ' ?';
												}
											}
										}
									} else {
										foreach ($db_field['lc'] as $tableField) {
											if (!in_array($tableField['table'], $lcQueryTables)
												&& !in_array($tableField['table'], $queryTables)) {
												$lcQueryTables[] = $tableField['table'];
											}
											if (isset($db_field['fieldPre']) && isset($db_field['fieldPost'])) {
												$condOr[] = $db_field['fieldPre'] . '`' . $tableField['table'] . '`.`' . $tableField['field'] . '`' . $db_field['fieldPost'] . ' ' . $db_field['cond'];
											} else {
												$condOr[] = '`' . $tableField['table'] . '`.`' . $tableField['field'] . '` ' . $db_field['cond'];
											}
										}
									}
									if (count($condOr) > 0) {
										$lcQueryAndConditions[] = '(' . implode(' OR ', $condOr) . ')';
									}
								}
							}
							if ($conditionalStr != '') {
								if (substr_compare($conditionalStr, ' AND ', -5) == 0) {
									$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 5);
									$condLoop = true;
								}
								else if (substr_compare($conditionalStr, ' OR ', -4) == 0) {
									$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 4);
									$condLoop = true;
								}
								if ($conditionalCount > 1) {
									$lcQueryAndConditions[] = '(' . $conditionalStr . ')';
								} else {
									$lcQueryAndConditions[] = $conditionalStr;
								}
								$conditionalStr = '';
								$conditionalCount = 0;
								$fieldName = '';
							}
						}
					}
				}
				
				/*
				echo "LC Query fields ---";
				foreach($queryFields as $value) {
					echo $value;
				}
				echo "---";
				*/
				
				$query = 'SELECT DISTINCT ' . implode(', ', $queryFields);
				if (count($lcQueryFields) > 0) {
					$query .=  ', ' . implode(', ', $lcQueryFields);
				}
				$query .= ' FROM ';
				$fromPrefix = '';
				if (count($queryTables) > 0) {
					$query .= implode(', ', $queryTables);
					$fromPrefix = ', ';
				}
				if (count($lcQueryTables) > 0) {
					$query .= $fromPrefix . implode(', ', $lcQueryTables);
				}
				$query .= ' WHERE ' . implode(' AND ', $lcQueryAndConditions);
				if (in_array('lc_plants', $lcQueryTables)) {
					$query .= ' AND lc_plants.accession_no = lc_accession.accession_no';
				}
				if (in_array('lc_accession', $lcQueryTables)) {
					$query .= ' AND lc_accession.sciname_id = sciname.scientific_name_id';
				}
				
				$query .= ' ORDER BY `sciname`.`sort_scientific_name`';
				
				// echo "LC QUERY: ".$query;

				if ((isset($debug_show_search) && $debug_show_search)
					|| ($_SERVER['SERVER_NAME'] == 'mortonarb.localhost')) {
					$_SERVER['LC_QUERY'] = $query . '<br /><b>With:</b>';
					foreach ($requestFields as $requestField) {
						if (isset($_REQUEST[$requestField['field_name']])
								&& ($_REQUEST[$requestField['field_name']] != '')
								&& (count($requestField['db_field']['lc']) != 0)) {
							if (($requestField['field_req'] == '')
								|| (($requestField['field_req'] != '') && in_array($_REQUEST[$requestField['field_req']], $requestField['field_values']))) {
								if ($requestField['field_cond']) {
									if ($requestField['field_desc'] != '') {
										$_SERVER['LC_QUERY'] .= '<br />' . "\n" . $requestField['field_name'] . ' = "' . $_REQUEST[$requestField['field_name']] . '"';
									}
								} else if (isset($requestField['db_field']['lc'][0]['func'])) {
									$_SERVER['LC_QUERY'] .= '<br />' . "\n" . call_user_func($requestField['db_field']['lc'][0]['func'], 1);
								} else {
									$db_field = $requestField['db_field'];
									if (count($db_field['lc']) != 0) {
										if (($db_field['bindType'] != '') || isset($db_field['lc']['func'])) {
											$value = $_REQUEST[$requestField['field_name']];
											if ($db_field['xformFunc'] != '') {
												$xformFunc = $db_field['xformFunc'];
												$value = call_user_func($xformFunc, $value);
											}
											if (isset($requestField['wildcard_matches'])) {
												foreach($requestField['wildcard_matches'] as $oldChar => $newChar) {
													$value = implode($newChar, explode($oldChar, $value));
												}
											}
											foreach ($db_field['lc'] as $tableField) {
												$_SERVER['LC_QUERY'] .= '<br />' . "\n" . $tableField['table'] . '.' . $tableField['field'] . ' = "' . $db_field['modPre'] . $value . $db_field['modPost'] . '"';
											}
										}
									}
								}
							}
						}
					}					
				}
				
				if ($stmt = $link->prepare($query)) {
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
					unset($result);
					
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
			}
			
			if (($numHrbUsedFields > 0) && (($_REQUEST['search_type'] == 'herbarium') || ($_REQUEST['search_type'] == 'combined'))) {
				$conditionalStr = '';
				$conditionalCount = 0;
				$fieldName = '';
				foreach ($requestFields as $requestField) {
					if (isset($_REQUEST[$requestField['field_name']])
							&& ($_REQUEST[$requestField['field_name']] != '')
							&& ((count($requestField['db_field']['hrb']) != 0)
								|| isset($requestField['db_field']['condFunc']))) {
						if (($requestField['field_req'] == '')
							|| (($requestField['field_req'] != '') && in_array($_REQUEST[$requestField['field_req']], $requestField['field_values']))) {
							if ($requestField['field_cond']) {
								if ($fieldName == '') {
									if ($conditionalStr != '') {
										if (substr_compare($conditionalStr, ' AND ', -5) == 0) {
											$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 5);
											$condLoop = true;
										}
										else if (substr_compare($conditionalStr, ' OR ', -4) == 0) {
											$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 4);
											$condLoop = true;
										}
										if ($conditionalCount != 1) {
											$hrbQueryAndConditions[] = '(' . $conditionalStr . ')';
										} else {
											$hrbQueryAndConditions[] = $conditionalStr;
										}
										$conditionalCount = 0;
										$conditionalStr = '';
										$fieldName = '';
									}
									$fieldName = '`' . $requestField['db_field']['hrb'][0]['table'] . '`.`' . $requestField['db_field']['hrb'][0]['field'];
									if (!in_array($requestField['db_field']['hrb'][0]['table'], $hrbQueryTables)
										&& !in_array($requestField['db_field']['hrb'][0]['table'], $queryTables)) {
										$hrbQueryTables[] = $requestField['db_field']['hrb'][0]['table'];
									}
								}
								if ($requestField['field_desc'] == '') {
									$conditionalStr .= ' ' . strtoupper($_REQUEST[$requestField['field_name']]) . ' ';
								} else {
									$bindArrInfo[] = array('type'=>$db_field['bindType'],
											'value'=>$_REQUEST[$requestField['field_name']],
											'which'=>'hrb');
									$conditionalStr .= '(' . $fieldName . ' ' . $requestField['db_field']['cond'] . ' ?)';
								}
							} else if (isset($requestField['db_field']['hrb'][0]['func'])) {
								if ($conditionalStr != '') {
									if (substr_compare($conditionalStr, ' AND ', -5) == 0) {
										$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 5);
										$condLoop = true;
									}
									else if (substr_compare($conditionalStr, ' OR ', -4) == 0) {
										$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 4);
										$condLoop = true;
									}
									if ($conditionalCount != 1) {
										$hrbQueryAndConditions[] = '(' . $conditionalStr . ')';
									} else {
										$hrbQueryAndConditions[] = $conditionalStr;
									}
									$conditionalCount = 0;
									$conditionalStr = '';
									$fieldName = '';
								}
								call_user_func($requestField['db_field']['hrb'][0]['func'], 0);
							} else {
								if ($conditionalStr != '') {
									if (substr_compare($conditionalStr, ' AND ', -5) == 0) {
										$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 5);
										$condLoop = true;
									}
									else if (substr_compare($conditionalStr, ' OR ', -4) == 0) {
										$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 4);
										$condLoop = true;
									}
									if ($conditionalCount != 1) {
										$hrbQueryAndConditions[] = '(' . $conditionalStr . ')';
									} else {
										$hrbQueryAndConditions[] = $conditionalStr;
									}
									$conditionalCount = 0;
									$conditionalStr = '';
									$fieldName = '';
								}
								$db_field = $requestField['db_field'];
								if (isset($db_field['condFunc'])) {
									$result = call_user_func($db_field['condFunc'], $_REQUEST[$requestField['field_name']]);
									foreach ($result['tables'] as $resultTable) {
										if (!in_array($resultTable, $hrbQueryTables)
											&& !in_array($tableField['table'], $queryTables)) {
											$hrbQueryTables[] = $resultTable;
										}
									}
									$hrbQueryAndConditions[] = '(' . $result['cond'] . ')';
								} elseif (count($db_field['hrb']) != 0) {
									$condOr = array();
									$conditional = $db_field['cond'];
									if ($db_field['bindType'] != '') {
										$value = implode('\%', explode('%', $_REQUEST[$requestField['field_name']]));
										$valueIsNotWildcard = (strpos($value, '*') === false);
										if ($db_field['xformFunc'] != '') {
											$xformFunc = $db_field['xformFunc'];
											$value = call_user_func($xformFunc, $value);
										}
										$permitWildcard = false;
										if (isset($db_field['hrb'][0]['permit_wildcard'])) {
											$permitWildcard = $db_field['hrb'][0]['permit_wildcard'];
										}
										$wildcardMatches = false;
										if ($permitWildcard && isset($db_field['hrb'][0]['wildcard_regex']) && ($db_field['hrb'][0]['wildcard_regex'] != '')) {
											$wildcardRegex = $db_field['hrb'][0]['wildcard_regex'];
											$fieldContents = $_REQUEST[$requestField['field_name']];
											$wildcardMatches = (preg_match($wildcardRegex, $fieldContents) != 0);
										}
										if ($permitWildcard && $wildcardMatches) {
											$conditional = 'LIKE';
											$value = implode('%', explode('*', $value));
										}
										if (!$permitWildcard || $wildcardMatches || $valueIsNotWildcard) {
											foreach ($db_field['hrb'] as $tableField) {
												if (!in_array($tableField['table'], $hrbQueryTables)
													&& !in_array($tableField['table'], $queryTables)) {
													$hrbQueryTables[] = $tableField['table'];
												}
												$bindArrInfo[] = array('type'=>$db_field['bindType'],
														'value'=>$db_field['modPre'] . $value . $db_field['modPost'],
														'which'=>'hrb');
												if (isset($db_field['fieldPre']) && isset($db_field['fieldPost'])) {
													$condOr[] = $db_field['fieldPre'] . '`' . $tableField['table'] . '`.`' . $tableField['field'] . '`' . $db_field['fieldPost'] . ' ' . $conditional . ' ?';
												} else {
													$condOr[] = '`' . $tableField['table'] . '`.`' . $tableField['field'] . '` ' . $conditional . ' ?';
												}
											}
										}
									} else {
										foreach ($db_field['hrb'] as $tableField) {
											if (!in_array($tableField['table'], $hrbQueryTables)
												&& !in_array($tableField['table'], $queryTables)) {
												$hrbQueryTables[] = $tableField['table'];
											}
											if (isset($db_field['fieldPre']) && isset($db_field['fieldPost'])) {
												$condOr[] = $db_field['fieldPre'] . '`' . $tableField['table'] . '`.`' . $tableField['field'] . '`' . $db_field['fieldPost'] . ' ' . $db_field['cond'];
											} else {
												$condOr[] = '`' . $tableField['table'] . '`.`' . $tableField['field'] . '` ' . $db_field['cond'];
											}
										}
									}
									if (count($condOr) > 0) {
										$hrbQueryAndConditions[] = '(' . implode(' OR ', $condOr) . ')';
									}
								}
							}
							if ($conditionalStr != '') {
								if (substr_compare($conditionalStr, ' AND ', -5) == 0) {
									$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 5);
									$condLoop = true;
								}
								else if (substr_compare($conditionalStr, ' OR ', -4) == 0) {
									$conditionalStr = substr($conditionalStr, 0, strlen($conditionalStr) - 4);
									$condLoop = true;
								}
								if ($conditionalCount != 1) {
									$hrbQueryAndConditions[] = '(' . $conditionalStr . ')';
								} else {
									$hrbQueryAndConditions[] = $conditionalStr;
								}
								$conditionalCount = 0;
								$conditionalStr = '';
								$fieldName = '';
							}
						}
					}
				}
				
				/*
				echo "Query fields ---";
				foreach($queryFields as $value) {
					echo $value;
				}
				echo "---";
				*/
				
				//echo $queryFields;
				$query = 'SELECT DISTINCT ' . implode(', ', $queryFields);
				if (count($hrbQueryFields) > 0) {
					$query .= ', ' . implode(', ', $hrbQueryFields);
				}
				$query .= ' FROM ';
				$fromPrefix = '';
				if (count($queryTables) > 0) {
					$query .= implode(', ', $queryTables);
					$fromPrefix = ', ';
				}
				if (count($hrbQueryTables) > 0) {
					$query .= $fromPrefix . implode(', ', $hrbQueryTables);
				}
				$query .= ' WHERE ' . implode(' AND ', $hrbQueryAndConditions);
				if (in_array('hrb_accession', $hrbQueryTables)) {
					$query .= ' AND hrb_accession.sciname_id = sciname.scientific_name_id';
				}
				
				$query .= ' ORDER BY `sciname`.`sort_scientific_name`';
		
				// echo "HRB Query: ".$query;
				
				if ((isset($debug_show_search) && $debug_show_search)
					|| ($_SERVER['SERVER_NAME'] == 'mortonarb.localhost')) {
					$_SERVER['HRB_QUERY'] = $query . '<br /><b>With:</b>';;
					foreach ($requestFields as $requestField) {
						if (isset($_REQUEST[$requestField['field_name']])
								&& ($_REQUEST[$requestField['field_name']] != '')
								&& (count($requestField['db_field']['hrb']) != 0)) {
							if (($requestField['field_req'] == '')
								|| (($requestField['field_req'] != '') && in_array($_REQUEST[$requestField['field_req']], $requestField['field_values']))) {
								if ($requestField['field_cond']) {
									if ($requestField['field_desc'] != '') {
										$_SERVER['LC_QUERY'] .= '<br />' . "\n" . $requestField['field_name'] . ' = "' . $_REQUEST[$requestField['field_name']] . '"';
									}
								} else if (isset($requestField['db_field']['hrb'][0]['func'])) {
									$_SERVER['HRB_QUERY'] .= '<br />' . "\n" . call_user_func($requestField['db_field']['hrb'][0]['func'], 1);
								} else {
									$db_field = $requestField['db_field'];
									if (count($db_field['hrb']) != 0) {
										if (($db_field['bindType'] != '') || isset($db_field['hrb']['func'])) {
											$value = $_REQUEST[$requestField['field_name']];
											if ($db_field['xformFunc'] != '') {
												$xformFunc = $db_field['xformFunc'];
												$value = call_user_func($xformFunc, $value);
											}
											if (isset($requestField['wildcard_matches'])) {
												foreach($requestField['wildcard_matches'] as $oldChar => $newChar) {
													$value = implode($newChar, explode($oldChar, $value));
												}
											}
											foreach ($db_field['hrb'] as $tableField) {
												$_SERVER['HRB_QUERY'] .= '<br />' . "\n" . $tableField['table'] . '.' . $tableField['field'] . ' = "' . $db_field['modPre'] . $value . $db_field['modPost'] . '"';
											}
										}
									}
								}
							}
						}
					}
				}
				
				if ($stmt = $link->prepare($query)) {
					$bindArr = array('');
					$valueArr = array();
					foreach ($bindArrInfo as $bindInfo) {
						if (($bindInfo['which'] == 'hrb') || ($bindInfo['which'] == 'both')) {
							$bindArr[0] .= $bindInfo['type'];
							$valueArr[] = $bindInfo['value'];
							$bindArr[] =& $valueArr[count($valueArr) - 1];
						}
					}
					
					call_user_func_array(array($stmt,'bind_param'), $bindArr);
	
					$stmt->execute();
					
					$bindArr = array();
					foreach ($queryNames as $queryName) {
						$result[$queryName] = '';
						$bindArr[] =& $result[$queryName];
					}
					foreach ($hrbQueryNames as $queryName) {
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
					unset($result);
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
			$num_Entry_Arr = dump_results('morton_adv_search.csv', strtoupper($_REQUEST['download_file']), $results, $scinames);
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
		echo "\t\t\t" . '</div>' . "\n";
	} 
} else {
	if (count($fieldErrors)) {
		echo '			<font color="red"><ul><li>' . implode('</li><li>', $fieldErrors) . '</li></ul></font><br />' . "\n";
	}
?>			<form name="adv_search" action="<?php echo getRootUrl(); ?>/advanced_search.php" method="get">
			<fieldset class="adv_search_wrapper">
			<div class="search_form">
				<!--START FORMS PANEL-->
				<!--FORM LEFT COL-->
				
				<div class="column">  
				
				
				  <fieldset class="adv_search">
				  <legend class="adv_search"><span><a href="javascript:alert('<?php echo localize("SearchTypeHelp"); ?>');"><img src="images/question_mark.png" class="floatright"/></a>Collection Info</span></legend>
					<table width="99%" border="0" cellpadding="2" class="formtable">
				  <tr>
				    <td valign="top" align="right" width="50%">
				    Search Type
				    </td>
				    <td>
				    <select id="search_type" name="search_type" class="select">
				    <option value="living">Living Collection (grounds)</option>
				    <option value="herbarium">Herbarium (dried plants)</option>
				    <option value="combined" selected>Combined Search</option>
				    </select>
					</td>
				  </tr>
				  <tr>
				    <td valign="top" align="right">
					Living Collection Area
					</td>
					<td>
					<select name="coll_area" class="select">
<?php
buildCollectionsOptionsList("\t\t\t\t\t");
?>					</select>
					</td>
				  </tr>
				  <tr>
					<td valign="top" align="right">
					Accession number
					</td>
					<td>
					<input name="access_nbr" class="input" type="text"  />
					</td>
				  </tr>
				</table>
				  </fieldset>
				 
				<fieldset class="adv_search">
				  <legend class="adv_search"><span><a href="javascript:alert('<?php echo localize("TaxonomicNameHelp"); ?>');"><img src="images/question_mark.png" class="floatright"/></a>Taxonomic Name</span></legend>
					<table width="99%" border="0" cellpadding="2" class="formtable">
				  <tr>
				    <td valign="top" align="right" width="50%">Plant name keyword</td>
				    <td valign="top"><input name="plant_name_key" class="input" type="text"  /></td>
				  </tr>
				  <tr>
				    <td valign="top" align="right" width="50%">Order</td>
				    <td valign="top"><input name="plant_order" class="input" type="text"  /></td>
				  </tr>
				  <tr>
				    <td valign="top" align="right" width="50%">Family</td>
				    <td valign="top"><input name="plant_fam" class="input" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Genus</td>
				    <td valign="top"><input name="plant_genus" class="input" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Species epithet</td>
				    <td valign="top"><input name="plant_species" class="input" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Subspecies/variety/forma</td>
				    <td valign="top"><input name="plant_subspecies" class="input" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Cultivar</td>
				    <td valign="top"><input name="plant_cult" class="input" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Common/Trade name</td>
				    <td valign="top"><input name="plant_trade_name" class="input" type="text"  /></td>
				  </tr>
				</table>
				  </fieldset>
				 
				 
				<fieldset class="adv_search">
				  <legend class="adv_search"><span><a href="javascript:alert('<?php echo localize("SourceCollectorHelp"); ?>');"><img src="images/question_mark.png" class="floatright"/></a>Source / Collector</span></legend>
					<table width="99%" border="0" cellpadding="2" class="formtable">
				  <tr>
				    <td valign="top" align="right" width="50%">Source institution/person</td>
				    <td valign="top"><input name="plant_source" class="input" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right" width="50%">Collector last name</td>
				    <td valign="top"><input name="plant_collector_lname" class="input" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Collector first name</td>
				    <td valign="top"><input name="plant_collector_fname" class="input_med" type="text"  />&nbsp;<!-- &nbsp;&nbsp;MI&nbsp;<input name="" class="input_small" type="text"  /> --></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Project/Expedition</td>
				    <td valign="top"><input name="plant_collector_project" class="input" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Collector/Expedition #</td>
				    <td valign="top"><input name="plant_collector_expnbr" class="input" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Collection year</td>
				    <td valign="top">
				    	<input name="plant_collector_year" class="input_small" type="text"  />&nbsp;
						End of range&nbsp;<input name="plant_collector_year_end" class="input_small" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Collection month/day (M/D)<br /><i>Only used if Collection year range entered</i></td>
				    <td valign="top">
				    	<input name="plant_collector_monthday" class="input_small" type="text"  />&nbsp;
						End of range&nbsp;<input name="plant_collector_monthday_end" class="input_small" type="text"  /></td>
				  </tr>
				   <tr>
				    <td valign="top" align="right">Herbarium only:</td>
				    <td valign="top">
				    	<input name="plant_herbarium_flower" type="checkbox" />Flower<br />
				    	<input name="plant_herbarium_fruit" type="checkbox" />Fruit<br />
				    	<input name="plant_herbarium_veg" type="checkbox" />Vegetative<br />
				    	<input name="plant_herbarium_bud" type="checkbox" />Bud<br />
					</td>
				  </tr>
				</table>
				  </fieldset>
				
				</div>
				
				<!--END LEFT COL-->
				
				
				<!--FORM RIGHT COL-->
				
				<div class="column last">
								
				<fieldset class="adv_search">
				  <legend class="adv_search"><span><a href="javascript:alert('<?php echo localize("HabitatHelp"); ?>');"><img src="images/question_mark.png" class="floatright"/></a>Habitat</span></legend>
					<table width="99%" border="0" cellpadding="2" class="formtable">
				  <tr valign="top">
				    <td align="right" width="30%">Keyword(s)</td>
				    <td>
				    	<input name="hab_keyword" class="input_lrg" type="text"  /><br />
				    	<input name="hab_andor" class="radio" type="radio" value="and" checked/>&nbsp;Require all keywords<br />
				    	<input name="hab_andor" class="radio" type="radio" value="or" />&nbsp;Require at least one keyword
				    </td>
				</table>
				  </fieldset>
				
				
				<fieldset class="adv_search">
				  <legend class="adv_search"><span><a href="javascript:alert('<?php echo localize("GeopoliticalHelp"); ?>');"><img src="images/question_mark.png" class="floatright"/></a>Geopolitical</span></legend>
					<table width="99%" border="0" cellpadding="2" class="formtable">
				  <tr>
				    <td valign="top" align="right" width="50%">Country</td>
				    <td valign="top"><input name="country" class="input" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right" width="50%">State (Subcountry1)</td>
				    <td valign="top"><input name="subcountry1" class="input" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">County (Subcountry2)</td>
				    <td valign="top"><input name="subcountry2" class="input" type="text"  /><p></p></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Township (name)</td>
				    <td valign="top"><input name="township_name" class="input" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Township (number)</td>
				    <td valign="top"><input name="township_nbr" class="input_small" type="text"  />&nbsp; N or S:&nbsp;<input name="township_ns" class="input_small" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right">Range (number)</td>
				    <td valign="top"><input name="range_nbr" class="input_small" type="text"  />&nbsp; E or W:&nbsp;<input name="township_ew" class="input_small" type="text"  /></td>
				  </tr>
				   <tr>
				    <td valign="top" align="right">Section</td>
				    <td valign="top"><input name="section" class="input_small" type="text"  /></td>
				  </tr>
				</table>
				  </fieldset>
				
				<fieldset class="adv_search">
				  <legend class="adv_search"><span><a href="javascript:alert('<?php echo localize("GeopoliticalCoordHelp"); ?>');"><img src="images/question_mark.png" class="floatright"/></a>Geopolitical coordinates</span></legend>
					<table width="99%" border="0" cellpadding="2" class="formtable">
				  <tr>
				    <td valign="top" align="right" width="40%">Latitude (deg.decimal)</td>
				    <td valign="top">Min:&nbsp;<input name="latitude_min" class="input_small" type="text"  />&nbsp; Max:&nbsp;<input name="latitude_max" class="input_small" type="text"  /></td>
				  </tr>
				  <tr>
				    <td valign="top" align="right">Longitude (deg.decimal)</td>
				    <td valign="top">Min:&nbsp;<input name="longitude_min" class="input_small" type="text"  />&nbsp; Max:&nbsp;<input name="longitude_max" class="input_small" type="text"  /><p></p></td>
				  </tr>
				  <tr>
				    <td valign="top" align="right">UTM Center:</td>
				    <td valign="top">Zone #&nbsp;&nbsp;&nbsp;<input name="utm_zone" class="input_small" type="text"  />&nbsp; Zone letter&nbsp;<input name="utm_zone_letter" class="input_small" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right"></td>
				    <td valign="top">Easting&nbsp;&nbsp;&nbsp;<input name="utm_easting" class="input_med2" type="text"  /></td>
				  </tr>
				    <tr>
				    <td valign="top" align="right"></td>
				    <td valign="top">Northing&nbsp;<input name="utm_northing" class="input_med2" type="text"  /><p></p></td>
				  </tr>
				</table>
				  </fieldset>
				
				<fieldset class="adv_search">
				  <legend class="adv_search"><span><a href="javascript:alert('<?php echo localize("SiteLocalityHelp"); ?>');"><img src="images/question_mark.png" class="floatright"/></a>Site / locality</span></legend>
					<table width="99%" border="0" cellpadding="2" class="formtable">
				  <tr valign="top">
				    <td align="right" width="30%">Keyword(s)</td>
				    <td>
				    	<input name="site_keyword" class="input_lrg" type="text"  /><br />
				    	<input name="site_andor" class="radio" type="radio" value="and" checked/>&nbsp;Require all keywords<br />
				    	<input name="site_andor" class="radio" type="radio" value="or" />&nbsp;Require at least one keyword
				    </td>
				  </tr>
				</table>
				  </fieldset>
				
				<fieldset class="adv_search">
				  <legend class="adv_search"><span><a href="javascript:alert('<?php echo localize("USDAZoneHelp"); ?>');"><img src="images/question_mark.png" class="floatright"/></a>USDA Zone</span></legend>
					<table width="99%" border="0" cellpadding="2" class="formtable">
				    <tr>
				    <td valign="top" align="right">Zone minimum</td>
				    <td valign="top"><input name="zone_min" class="input_small" type="text"  />&nbsp; Zone maximum&nbsp;<input name="zone_max" class="input_small" type="text"  /></td>
				  </tr>
				</table>
				  </fieldset>
				
				
				</div>
				
				<!--END RIGHT COL-->
				
				<!--END FORMS PANEL-->
				<br style="clear:both; font-size; 1px;" />
				
			</div>
			<div class="floatRight" style="margin-top: 4px;"><input type="submit" value="Search" /></div>
			</fieldset>
			</form>	
<?php
}
if (!isset($_REQUEST['download_file'])) {
?>		</div>
<?php
	require_once 'modules/footer_common.php';
?>	</div>
</body>
</html>
<?php
}
?>