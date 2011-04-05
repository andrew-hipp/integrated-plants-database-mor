<?php

function exportResults($db, $fields, $results, $scinames, $filehdl) {
	global $app_root;
	include $app_root . '/config.inc.php';

	$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);
	$hasDBLink = true;

	if (mysqli_connect_error() != 0) {
		$hasDBLink = false;
	}
	
	$retVal = '';
	
	$fieldKeys = array();
	$exportKeys = array();
	$fetchAddlKeys = array();
	$fetchFlds = array();
	$fetchDBs = array();
	$idx = 0;
	while ($idx < count($fields)) {
		if ($fields[$idx]['key'] != '') {
			$fieldKeys[] = $fields[$idx]['key'];
			$exportKeys[] = $fields[$idx]['col'];
		} elseif ($hasDBLink) {
			switch ($fields[$idx]['fld']) {
				case 'image_thumb':
				case 'image_large':
				case 'image_addl':
					$fieldKey = $fields[$idx]['fld'];
					break;
					
				default:
					$fieldKey = $fields[$idx]['tbl'] . '_' . $fields[$idx]['fld'];
					break;
			}
			$fieldKeys[] = $fieldKey;
			$fetchAddlKeys[] = $fieldKey;
			$exportKeys[] = $fields[$idx]['col'];
			$fetchFlds[] = '`' . $fields[$idx]['tbl'] . '`.`' . $fields[$idx]['fld'] . '`';
			if (!in_array($fields[$idx]['tbl'], $fetchDBs)) {
				$fetchDBs[] = $fields[$idx]['tbl'];
			}
		}
		$idx++;
	}
	
	fprintf($filehdl, "%s\n", join(',', $exportKeys));
	
	if ($db == 'lc') {
		$idxKey = 'lc_id';
		$idxField = '`lc_accession`.`id`';
		$whereCond = ' AND `lc_plants`.`accession_no` = `lc_accession`.`accession_no` AND `sciname`.`scientific_name_id` = `lc_accession`.`sciname_id`';
	} elseif ($db == 'hrb') {
		$idxKey = 'herb_id';
		$idxField = '`hrb_accession`.`id`';
		$whereCond = ' AND `sciname`.`scientific_name_id` = `hrb_accession`.`sciname_id`';
	}
	
	$doDBQuery = false;
	if ($hasDBLink && isset($idxField)) {
		// Query the un-fetched fields from the databases
		$query = 'SELECT ' . implode(',', $fetchFlds) . ' FROM ' . implode(',', $fetchDBs)
			. ' WHERE ' . $idxField . '= ?' . $whereCond;
		if ($stmt = $dbLink->prepare($query)) {
			$doDBQuery = true;
		}
	}
	
	foreach ($results as $result) {
		if (strtolower($result['db']) == strtolower($db)) {
			if ($doDBQuery) {
				$stmt->bind_param('i', $result[$idxKey]);
				$stmt->execute();
				
				$bindResult = array();
				foreach ($fetchAddlKeys as $fetchAddlKey) {
					$result[$fetchAddlKey] = '';
					$bindResult[] = &$result[$fetchAddlKey];
				}
				call_user_func_array(array($stmt,'bind_result'), $bindResult);
				
				if ($stmt->fetch()) {
					;
				}
			}
			$expItems = array();
			$idx = 0;
			while ($idx < count($fieldKeys)) {
				$fieldKey = $fieldKeys[$idx];
				$record =& $result;
				
				if ((substr($fieldKey, 0, 8) == 'sciname_') && !isset($record[$fieldKey])) {
					$fieldKey = substr($fieldKey, 8);
					$record =& $scinames[$record['sciname_id']];
				}
				if (isset($record[$fieldKey]) && !isNull($record[$fieldKey])) {
					$expItems[] = '"' . implode('""', explode('"', $record[$fieldKey])) . '"';
				} else {
					$expItems[] = '""';
				}
				$idx++;
			}
			
			fprintf($filehdl, "%s\n", join(',', $expItems));
			
			$result = array();
		}
	}
	
	if ($doDBQuery) {
		$stmt->close();
	}
	
	if ($hasDBLink) {
		$dbLink->close();
	}
}

function date_fnString($aDate) {
	return date('Y-m-d-H-i');
}

function dump_results($basename, $whichDB, $results, $scinames) {
	global $app_root;

	$now = time();
	
	$lcFile = null;
	$hrbFile = null;
	if (strtolower($whichDB) == 'lc') {
		$fields = array(
		 	array('key'=>'sciname_scientific_name', 'tbl'=>'sciname', 'fld'=>'scientific_name', 'col'=>'scientific_name'),
			array('key'=>'sciname_sort_scientific_name', 'tbl'=>'sciname', 'fld'=>'sort_scientific_name', 'col'=>'sort_scientific_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'family_name', 'col'=>'family_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'genus_designator', 'col'=>'genus_designator'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'genus_name', 'col'=>'genus_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'species_designator', 'col'=>'species_designator'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'species_name', 'col'=>'species_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'subspecies_name', 'col'=>'subspecies_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'variety_name', 'col'=>'variety_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'forma_name', 'col'=>'forma_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'cultivar_name', 'col'=>'cultivar_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'author_name', 'col'=>'author_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'common_names', 'col'=>'common_names'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'parentage', 'col'=>'parentage'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'usda_zone_lo', 'col'=>'usda_zone_lo'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'usda_zone_hi', 'col'=>'usda_zone_hi'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'range', 'col'=>'range'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'trademark', 'col'=>'trademark'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'plant_patent_no', 'col'=>'plant_patent_no'),
			array('key'=>'lc_accession_no', 'tbl'=>'lc_accession', 'fld'=>'accession_no', 'col'=>'lc_accession_no'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'how_received', 'col'=>'lc_how_received'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'original_source', 'col'=>'lc_original_source'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'provenance', 'col'=>'lc_provenance'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'received_sciname', 'col'=>'lc_received_sciname'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'source_name', 'col'=>'lc_source_name'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'source_no', 'col'=>'lc_source_no'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'source_collection', 'col'=>'lc_source_collection'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'source_location', 'col'=>'lc_source_location'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'source_annotation', 'col'=>'lc_source_annotation'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'understock', 'col'=>'lc_understock'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_date', 'col'=>'lc_collector_date'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_name', 'col'=>'lc_collector_name'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_no', 'col'=>'lc_collector_no'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_habitat', 'col'=>'lc_collector_habitat'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_site', 'col'=>'lc_collector_site'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_township_range', 'col'=>'lc_collector_township_range'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_subcountry_2', 'col'=>'lc_collector_subcountry_2'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_subcountry_2_dsg', 'col'=>'lc_collector_subcountry_2_dsg'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_subcountry_1', 'col'=>'lc_collector_subcountry_1'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_subcountry_1_dsg', 'col'=>'lc_collector_subcountry_1_dsg'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_country', 'col'=>'lc_collector_country'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_lat', 'col'=>'lc_collector_lat'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_lat_dsg', 'col'=>'lc_collector_lat_dsg'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_long', 'col'=>'lc_collector_long'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_long_dsg', 'col'=>'lc_collector_long_dsg'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_elevation_low', 'col'=>'lc_collector_elevation_low'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_elevation_high', 'col'=>'lc_collector_elevation_high'),
			array('key'=>'', 'tbl'=>'lc_accession', 'fld'=>'collector_elevation_units', 'col'=>'lc_collector_elevation_units'),
			array('key'=>'', 'tbl'=>'lc_plants', 'fld'=>'plant_id', 'col'=>'lc_plant_id'),
			array('key'=>'', 'tbl'=>'lc_plants', 'fld'=>'annotation_date', 'col'=>'lc_annotation_date'),
			array('key'=>'', 'tbl'=>'lc_plants', 'fld'=>'annotator_name', 'col'=>'lc_annotator_name'),
			array('key'=>'', 'tbl'=>'lc_plants', 'fld'=>'annotation', 'col'=>'lc_annotation'),
			array('key'=>'', 'tbl'=>'lc_plants', 'fld'=>'collection_name', 'col'=>'lc_collection_name'),
			array('key'=>'', 'tbl'=>'lc_plants', 'fld'=>'grid_coord', 'col'=>'lc_grid_coord'),
			array('key'=>'', 'tbl'=>'lc_plants', 'fld'=>'no_grid', 'col'=>'lc_no_grid'),
			array('key'=>'', 'tbl'=>'lc_plants', 'fld'=>'row', 'col'=>'lc_row'),
			array('key'=>'', 'tbl'=>'lc_plants', 'fld'=>'structure', 'col'=>'lc_structure'),
			array('key'=>'', 'tbl'=>'lc_plants', 'fld'=>'subarea1', 'col'=>'lc_subarea1'),
			array('key'=>'', 'tbl'=>'lc_plants', 'fld'=>'subarea2', 'col'=>'lc_subarea2'),
			array('key'=>'', 'tbl'=>'lc_plants', 'fld'=>'subarea3', 'col'=>'lc_subarea3')
		);
		
		$lcFile = tmpfile();
		exportResults('lc', $fields, $results, $scinames, $lcFile);
	}
	elseif (strtolower($whichDB) == 'hrb') {
		$fields = array(
		 	array('key'=>'sciname_scientific_name', 'tbl'=>'sciname', 'fld'=>'scientific_name', 'col'=>'scientific_name'),
			array('key'=>'sciname_sort_scientific_name', 'tbl'=>'sciname', 'fld'=>'sort_scientific_name', 'col'=>'sort_scientific_name'),			
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'family_name', 'col'=>'family_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'genus_designator', 'col'=>'genus_designator'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'genus_name', 'col'=>'genus_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'species_designator', 'col'=>'species_designator'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'species_name', 'col'=>'species_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'subspecies_name', 'col'=>'subspecies_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'variety_name', 'col'=>'variety_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'forma_name', 'col'=>'forma_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'cultivar_name', 'col'=>'cultivar_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'author_name', 'col'=>'author_name'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'common_names', 'col'=>'common_names'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'parentage', 'col'=>'parentage'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'usda_zone_lo', 'col'=>'usda_zone_lo'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'usda_zone_hi', 'col'=>'usda_zone_hi'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'range', 'col'=>'range'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'trademark', 'col'=>'trademark'),
			array('key'=>'', 'tbl'=>'sciname', 'fld'=>'plant_patent_no', 'col'=>'plant_patent_no'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'herb_nbr', 'col'=>'hrb_herb_nbr'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'sciname_qual', 'col'=>'hrb_sciname_qual'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'spec_type', 'col'=>'hrb_spec_type'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'non_LC_acc_nbr', 'col'=>'hrb_non_LC_acc_nbr'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'folder', 'col'=>'hrb_folder'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'barcode', 'col'=>'hrb_barcode'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'associates', 'col'=>'hrb_associates'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'attributes', 'col'=>'hrb_attributes'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'fl', 'col'=>'hrb_fl'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'fr', 'col'=>'hrb_fr'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'veg', 'col'=>'hrb_veg'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'bud', 'col'=>'hrb_bud'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'lc_acc', 'col'=>'hrb_lc_acc'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'lc_plant', 'col'=>'hrb_lc_plant'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'orig_id', 'col'=>'hrb_orig_id'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'annot_by', 'col'=>'hrb_annot_by'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'annot_dt', 'col'=>'hrb_annot_dt'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'addl_annot_by', 'col'=>'hrb_addl_annot_by'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'annot_comments', 'col'=>'hrb_annot_comments'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'annot_date_acc', 'col'=>'hrb_annot_date_acc'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'collector_primary', 'col'=>'hrb_collector_primary'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'collector_primary_fn', 'col'=>'hrb_collector_primary_fn'),
			array('key'=>'herb_collector_primary_ln', 'tbl'=>'hrb_accession', 'fld'=>'collector_primary_ln', 'col'=>'hrb_collector_primary_ln'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'collector_addl', 'col'=>'hrb_collector_addl'),
			array('key'=>'herb_coll_date', 'tbl'=>'hrb_accession', 'fld'=>'coll_date', 'col'=>'hrb_coll_date'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'coll_date_acc', 'col'=>'hrb_coll_date_acc'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'habitat', 'col'=>'hrb_habitat'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'site', 'col'=>'hrb_site'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'twp', 'col'=>'hrb_twp'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'twp_dir', 'col'=>'hrb_twp_dir'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'range', 'col'=>'hrb_range'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'range_dir', 'col'=>'hrb_range_dir'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'sect', 'col'=>'hrb_sect'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'sect_desc', 'col'=>'hrb_sect_desc'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'lat', 'col'=>'hrb_lat'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'lat_dir', 'col'=>'hrb_lat_dir'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'long', 'col'=>'hrb_long'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'long_dir', 'col'=>'hrb_long_dir'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'utm_zone', 'col'=>'hrb_utm_zone'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'utm_lat_band', 'col'=>'hrb_utm_lat_band'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'utm_easting', 'col'=>'hrb_utm_easting'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'utm_northing', 'col'=>'hrb_utm_northing'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'utm_hemisphere', 'col'=>'hrb_utm_hemisphere'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'country', 'col'=>'hrb_country'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'subctry1', 'col'=>'hrb_subctry1'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'subctry2', 'col'=>'hrb_subctry2'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'desig1', 'col'=>'hrb_desig1'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'desig2', 'col'=>'hrb_desig2'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'site_sensitive', 'col'=>'hrb_site_sensitive'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'project', 'col'=>'hrb_project'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'elev_low', 'col'=>'hrb_elev_low'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'elev_upper', 'col'=>'hrb_elev_upper'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'elev_units', 'col'=>'hrb_elev_units'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'image_thumb', 'col'=>'hrb_image_thumb'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'image_large', 'col'=>'hrb_image_large'),
			array('key'=>'', 'tbl'=>'hrb_accession', 'fld'=>'image_addl', 'col'=>'hrb_image_addl')
		);
		
		$hrbFile = tmpfile();
		exportResults('hrb', $fields, $results, $scinames, $hrbFile);
	}
	
	$resultFileName = '';
	$contents = '';
	if ($lcFile != null) {
		$nameParts = explode('.', $basename);
		$nameParts[0] .= '-' . date_fnString($now) . '-LC';
		$resultFileName = implode('.', $nameParts);

		fseek($lcFile, 0);
		
		$contents = '';
		while (!feof($lcFile)) {
			$contents .= fread($lcFile, 4096);
		}
	} else if ($hrbFile != null) {
		$nameParts = explode('.', $basename);
		$nameParts[0] .= '-' . date_fnString($now) . '-HRB';
		fseek($hrbFile, 0);
		$resultFileName = implode('.', $nameParts);
		
		$contents = '';
		while (!feof($hrbFile)) {
			$contents .= fread($hrbFile, 4096);
		}
	}
	
	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename="' . $resultFileName . '"');
	echo $contents;
}