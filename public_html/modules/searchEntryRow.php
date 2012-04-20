<?php

require_once $app_root . '/modules/sciname_common.php';

function dump_results($results, $scinames) {
	global $app_root;
	global $map_url;
	
	include $app_root . '/config.inc.php';
	
	if (!isset($camera_image_width)) {
		if (file_exists($app_root . '/images/AAA-IMAGE-ICON-cam.gif')) {
			$image_info = getimagesize($app_root . '/images/AAA-IMAGE-ICON-cam.gif');
			if (isset($image_info) && isset($image_info[0])) {
				$camera_image_width = $image_info[0];
				if ($camera_image_width == 0) {
					unset($camera_image_width);
				}
			}
		}
	}
	
	if (!isset($camera_image_width)) {
		// Use a default value
		$camera_image_width = 60;
	}
	
	$dbLink = new mysqli($dbHostName, $dbUserName, $dbUserPasswd, $dbName);
	$hasDBLink = true;

	if (mysqli_connect_error() != 0) {
		$hasDBLink = false;
	}
	
	$numTaxa = 0;
	$numAccessions = 0;
	$numLivingColl = 0;
	
	//sciname.sort_scientific_name, sciname.scientific_name, sciname.family_name, sciname.common_names, sciname.usda_zone_lo, sciname.usda_zone_hi, db
	//sciname_sortname, sciname_name, sciname_family_name, sciname_common_names, sciname_usda_zone_lo, sciname_usda_zone_hi, db
	
	$lcFieldKeys = array();
	$lcFetchAddlKeys = array();
	$lcFetchFlds = array();
	$lcFetchDBs = array();
	$lcRecords = array();
	$lcFields = array(
	 	array('key'=>'sciname_scientific_name_id', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'scientific_name', 'col'=>'scientific_name'),
		array('key'=>'sciname_sort_scientific_name', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'sort_scientific_name', 'col'=>'sort_scientific_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'family_name', 'col'=>'family_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'genus_designator', 'col'=>'genus_designator'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'genus_name', 'col'=>'genus_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'species_designator', 'col'=>'species_designator'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'species_name', 'col'=>'species_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'subspecies_name', 'col'=>'subspecies_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'variety_name', 'col'=>'variety_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'forma_name', 'col'=>'forma_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'cultivar_name', 'col'=>'cultivar_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'author_name', 'col'=>'author_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'common_names', 'col'=>'common_names'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'parentage', 'col'=>'parentage'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'usda_zone_lo', 'col'=>'usda_zone_lo'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'usda_zone_hi', 'col'=>'usda_zone_hi'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'range', 'col'=>'range'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'trademark', 'col'=>'trademark'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'plant_patent_no', 'col'=>'plant_patent_no'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'family_common_name', 'col'=>'family_common_name'),
		array('key'=>'lc_accession_no', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'accession_no', 'col'=>'lc_accession_no'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'how_received', 'col'=>'lc_how_received'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'no_received', 'col'=>'lc_no_received'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'original_source', 'col'=>'lc_original_source'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'provenance', 'col'=>'lc_provenance'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'received_sciname', 'col'=>'lc_received_sciname'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'source_name', 'col'=>'lc_source_name'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'source_no', 'col'=>'lc_source_no'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'source_collection', 'col'=>'lc_source_collection'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'source_location', 'col'=>'lc_source_location'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'source_annotation', 'col'=>'lc_source_annotation'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'understock', 'col'=>'lc_understock'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_date', 'col'=>'lc_collector_date'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_name', 'col'=>'lc_collector_name'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_no', 'col'=>'lc_collector_no'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_habitat', 'col'=>'lc_collector_habitat'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_site', 'col'=>'lc_collector_site'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_township_range', 'col'=>'lc_collector_township_range'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_subcountry_2', 'col'=>'lc_collector_subcountry_2'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_subcountry_2_dsg', 'col'=>'lc_collector_subcountry_2_dsg'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_subcountry_1', 'col'=>'lc_collector_subcountry_1'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_subcountry_1_dsg', 'col'=>'lc_collector_subcountry_1_dsg'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_country', 'col'=>'lc_collector_country'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_lat', 'col'=>'lc_collector_lat'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_lat_dsg', 'col'=>'lc_collector_lat_dsg'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_long', 'col'=>'lc_collector_long'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_long_dsg', 'col'=>'lc_collector_long_dsg'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_elevation_low', 'col'=>'lc_collector_elevation_low'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_elevation_high', 'col'=>'lc_collector_elevation_high'),
		array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_accession', 'fld'=>'collector_elevation_units', 'col'=>'lc_collector_elevation_units'),
		//array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_plants', 'fld'=>'plant_id', 'col'=>'lc_plant_id'),
		//array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_plants', 'fld'=>'annotation_date', 'col'=>'lc_annotation_date'),
		//array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_plants', 'fld'=>'annotator_name', 'col'=>'lc_annotator_name'),
		//array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_plants', 'fld'=>'annotation', 'col'=>'lc_annotation'),
		//array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_plants', 'fld'=>'collection_name', 'col'=>'lc_collection_name'),
		//array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_plants', 'fld'=>'grid_coord', 'col'=>'lc_grid_coord'),
		//array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_plants', 'fld'=>'no_grid', 'col'=>'lc_no_grid'),
		//array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_plants', 'fld'=>'row', 'col'=>'lc_row'),
		//array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_plants', 'fld'=>'structure', 'col'=>'lc_structure'),
		//array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_plants', 'fld'=>'subarea1', 'col'=>'lc_subarea1'),
		//array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_plants', 'fld'=>'subarea2', 'col'=>'lc_subarea2'),
		//array('key'=>'', 'tbl_as'=>'lc', 'tbl'=>'lc_plants', 'fld'=>'subarea3', 'col'=>'lc_subarea3')
	);
	foreach ($lcFields as $lcField) {
		if ($lcField['key'] != '') {
			$lcFieldKeys[] = $lcField['key'];
		} elseif ($hasDBLink) {
			if ($lcField['tbl'] == 'sciname') {
				$lcRecords[] = 'sciname';
				$lcFieldKey = $lcField['fld'];
			} else {
				$lcRecords[] = 'result';
				$lcFieldKey = $lcField['tbl_as'] . '_' . $lcField['fld'];
			}
			$lcFieldKeys[] = $lcFieldKey;
			$lcFetchAddlKeys[] = $lcFieldKey;
			$lcFetchFlds[] = '`' . $lcField['tbl'] . '`.`' . $lcField['fld'] . '`';
			if (!in_array($lcField['tbl'], $lcFetchDBs)) {
				$lcFetchDBs[] = $lcField['tbl'];
			}
		}
	}
	$lcIdxKey = 'lc_id';
	$lcIdxField = '`lc_accession`.`id`';
	$lcWhereCond = ' AND `sciname`.`scientific_name_id` = `lc_accession`.`sciname_id`';
	$lcDoDBQuery = false;
	if ($hasDBLink && isset($lcIdxField)) {
		// Query the un-fetched fields from the databases
		$lcQuery = 'SELECT ' . implode(',', $lcFetchFlds) . ' FROM ' . implode(',', $lcFetchDBs)
			. ' WHERE ' . $lcIdxField . '= ?' . $lcWhereCond;
		if ($lcStmt = $dbLink->prepare($lcQuery)) {
			$lcDoDBQuery = true;
		} else {
			$errStr = $dbLink->error;
		}
	}
	
	$hrbFieldKeys = array();
	$hrbFetchAddlKeys = array();
	$hrbFetchFlds = array();
	$hrbFetchDBs = array();
	$hrbRecords = array();
	$hrbFields = array(
	 	array('key'=>'sciname_scientific_name_id', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'scientific_name', 'col'=>'scientific_name'),
		array('key'=>'sciname_sort_scientific_name', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'sort_scientific_name', 'col'=>'sort_scientific_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'family_name', 'col'=>'family_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'genus_designator', 'col'=>'genus_designator'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'genus_name', 'col'=>'genus_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'species_designator', 'col'=>'species_designator'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'species_name', 'col'=>'species_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'subspecies_name', 'col'=>'subspecies_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'variety_name', 'col'=>'variety_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'forma_name', 'col'=>'forma_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'cultivar_name', 'col'=>'cultivar_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'author_name', 'col'=>'author_name'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'common_names', 'col'=>'common_names'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'parentage', 'col'=>'parentage'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'usda_zone_lo', 'col'=>'usda_zone_lo'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'usda_zone_hi', 'col'=>'usda_zone_hi'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'range', 'col'=>'range'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'trademark', 'col'=>'trademark'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'plant_patent_no', 'col'=>'plant_patent_no'),
		array('key'=>'', 'tbl_as'=>'sciname', 'tbl'=>'sciname', 'fld'=>'family_common_name', 'col'=>'family_common_name'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'herb_nbr', 'col'=>'hrb_herb_nbr'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'sciname_qual', 'col'=>'hrb_sciname_qual'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'spec_type', 'col'=>'hrb_spec_type'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'non_LC_acc_nbr', 'col'=>'hrb_non_LC_acc_nbr'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'folder', 'col'=>'hrb_folder'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'barcode', 'col'=>'hrb_barcode'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'associates', 'col'=>'hrb_associates'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'attributes', 'col'=>'hrb_attributes'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'fl', 'col'=>'hrb_fl'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'fr', 'col'=>'hrb_fr'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'veg', 'col'=>'hrb_veg'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'bud', 'col'=>'hrb_bud'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'lc_acc', 'col'=>'hrb_lc_acc'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'lc_plant', 'col'=>'hrb_lc_plant'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'orig_id', 'col'=>'hrb_orig_id'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'annot_by', 'col'=>'hrb_annot_by'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'annot_dt', 'col'=>'hrb_annot_dt'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'addl_annot_by', 'col'=>'hrb_addl_annot_by'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'annot_comments', 'col'=>'hrb_annot_comments'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'annot_date_acc', 'col'=>'hrb_annot_date_acc'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'collector_primary', 'col'=>'hrb_collector_primary'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'collector_primary_fn', 'col'=>'hrb_collector_primary_fn'),
		array('key'=>'herb_collector_primary_ln', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'collector_primary_ln', 'col'=>'hrb_collector_primary_ln'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'collector_addl', 'col'=>'hrb_collector_addl'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'collector_no', 'col'=>'hrb_collector_no'),
		array('key'=>'herb_coll_date', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'coll_date', 'col'=>'hrb_coll_date'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'coll_date_acc', 'col'=>'hrb_coll_date_acc'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'habitat', 'col'=>'hrb_habitat'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'site', 'col'=>'hrb_site'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'twp', 'col'=>'hrb_twp'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'twp_dir', 'col'=>'hrb_twp_dir'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'range', 'col'=>'hrb_range'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'range_dir', 'col'=>'hrb_range_dir'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'sect', 'col'=>'hrb_sect'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'sect_desc', 'col'=>'hrb_sect_desc'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'lat', 'col'=>'hrb_lat'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'lat_dir', 'col'=>'hrb_lat_dir'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'long', 'col'=>'hrb_long'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'long_dir', 'col'=>'hrb_long_dir'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'utm_zone', 'col'=>'hrb_utm_zone'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'utm_lat_band', 'col'=>'hrb_utm_lat_band'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'utm_easting', 'col'=>'hrb_utm_easting'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'utm_northing', 'col'=>'hrb_utm_northing'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'utm_hemisphere', 'col'=>'hrb_utm_hemisphere'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'country', 'col'=>'hrb_country'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'subctry1', 'col'=>'hrb_subctry1'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'subctry2', 'col'=>'hrb_subctry2'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'desig1', 'col'=>'hrb_desig1'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'desig2', 'col'=>'hrb_desig2'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'site_sensitive', 'col'=>'hrb_site_sensitive'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'project', 'col'=>'hrb_project'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'elev_low', 'col'=>'hrb_elev_low'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'elev_upper', 'col'=>'hrb_elev_upper'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'elev_units', 'col'=>'hrb_elev_units'),
		array('key'=>'', 'tbl_as'=>'', 'tbl'=>'hrb_accession', 'fld'=>'image_thumb', 'col'=>'hrb_image_thumb'),
		array('key'=>'', 'tbl_as'=>'', 'tbl'=>'hrb_accession', 'fld'=>'image_large', 'col'=>'hrb_image_large'),
		array('key'=>'', 'tbl_as'=>'herb', 'tbl'=>'hrb_accession', 'fld'=>'image_addl', 'col'=>'hrb_image_addl')
	);
	foreach ($hrbFields as $hrbField) {
		if ($hrbField['key'] != '') {
			$hrbFieldKeys[] = $hrbField['key'];
		} elseif ($hasDBLink) {
			if ($hrbField['tbl'] == 'sciname') {
				$hrbRecords[] = 'sciname';
				$hrbFieldKey = $hrbField['fld'];
			} else {
				$hrbRecords[] = 'result';
				if ($hrbField['tbl_as'] != '') {
					$hrbFieldKey = $hrbField['tbl_as'] . '_' . $hrbField['fld'];
				} else {
					$hrbFieldKey = $hrbField['fld'];
				}
			}
			$hrbFieldKeys[] = $hrbFieldKey;
			$hrbFetchAddlKeys[] = $hrbFieldKey;
			$hrbFetchFlds[] = '`' . $hrbField['tbl'] . '`.`' . $hrbField['fld'] . '`';
			if (!in_array($hrbField['tbl'], $hrbFetchDBs)) {
				$hrbFetchDBs[] = $hrbField['tbl'];
			}
		}
	}
	$hrbIdxKey = 'herb_id';
	$hrbIdxField = '`hrb_accession`.`id`';
	$hrbWhereCond = ' AND `sciname`.`scientific_name_id` = `hrb_accession`.`sciname_id`';
	$hrbDoDBQuery = false;
	if ($hasDBLink && isset($hrbIdxField)) {
		// Query the un-fetched fields from the databases
		$hrbQuery = 'SELECT ' . implode(',', $hrbFetchFlds) . ' FROM ' . implode(',', $hrbFetchDBs)
			. ' WHERE ' . $hrbIdxField . '= ?' . $hrbWhereCond;
		if ($hrbStmt = $dbLink->prepare($hrbQuery)) {
			$hrbDoDBQuery = true;
		} else {
			$errStr = $dbLink->error;
		}
	}
	
	$lastTaxaSortName = '';
	$rootURL = getRootUrl();
	echo '			<div class="search_results">' . "\n";
	if (isset($_SERVER['LC_QUERY'])) {
		echo '<b>LC Query:</b> ' . $_SERVER['LC_QUERY'] . '<br />' . "\n" . '<hr />';
	}
	if (isset($_SERVER['HRB_QUERY'])) {
		echo '<b>HRB Query:</b> ' . $_SERVER['HRB_QUERY'] . '<br />' . "\n" . '<hr />';
	}
	echo '			<table border="0" width="100%">' . "\n";
	echo '				<tr height="1px">' . "\n";
	echo '					<td width="<?php echo $camera_image_width; ?>px"></td>' . "\n";
	echo '					<td width="30px"></td>' . "\n";
	echo '					<td width="75px"></td>' . "\n";
	echo '					<td width="10px"></td>' . "\n";
	echo '					<td></td>' . "\n";
	echo '				</tr>' . "\n";
	$maxIdx = count($results);
	$idx = 0;
	while ($idx < $maxIdx) {
		$result = $results[$idx];
		$sciname = $scinames[$result['sciname_id']];
		$idx++;

		$doDBQuery = false;
		$stmt = null;
		$fetchAddlKeys = array();
		$records = array();
		$idxKey = '';
		
		switch (strtolower($result['db'])) {
			case 'lc':
				$doDBQuery = $lcDoDBQuery;
				$stmt = $lcStmt;
				$fetchAddlKeys = $lcFetchAddlKeys;
				$records = $lcRecords;
				$idxKey = $lcIdxKey;
				break;
				
			case 'hrb':
				$doDBQuery = $hrbDoDBQuery;
				$stmt = $hrbStmt;
				$fetchAddlKeys = $hrbFetchAddlKeys;
				$records = $hrbRecords;
				$idxKey = $hrbIdxKey;
				break;
		}
		
		if ($doDBQuery) {
			$stmt->bind_param('i', $result[$idxKey]);
			$stmt->execute();
			
			$bindResult = array();
			for ($fldIdx = 0; $fldIdx < count($fetchAddlKeys); $fldIdx++) {
				if ($records[$fldIdx] == 'sciname') {
					$sciname[$fetchAddlKeys[$fldIdx]] = '';
					$bindResult[] = &$sciname[$fetchAddlKeys[$fldIdx]];
				} else {
					$result[$fetchAddlKeys[$fldIdx]] = '';
					$bindResult[] = &$result[$fetchAddlKeys[$fldIdx]];
				}
			}
			call_user_func_array(array($stmt,'bind_result'), $bindResult);
			
			if ($stmt->fetch()) {
				if (!$stmt->reset()) {
					$errStr = $stmt->error;
				}
			} else {
				$errStr = $stmt->error;
				$dbErrStr = $dbLink->error;
			}
		}
		
		echo '				<tr valign="top">' . "\n";
		if ($sciname['sort_scientific_name'] != $lastTaxaSortName) {
			echo '					<td colspan="5">' . "\n";
			echo '						<br />' . "\n";
			echo '						<div class="search_taxa">' . "\n";
			echo '							';
			sciname_long($result, $sciname, true, ($result['db'] == 'lc'));
			if (isset($result['photo_url']) && ($result['photo_url'] != '')) {
				echo ' <a href="' . $result['photo_url'] . '"><img src="' . $rootURL . '/images/AAA-IMAGE-ICON-cam.gif" /></a>';
			}
			echo "\n";
			$lastTaxaSortName = $sciname['sort_scientific_name'];
			++$numTaxa;
			echo '						</div>' . "\n";
			echo '					</td>' . "\n";
			echo '				</tr>' . "\n";
			echo '				<tr valign="top">' . "\n";
		}
		echo '					<td>' . "\n";
		if (!isNull($result['image_thumb']) && !isNull($result['image_large'])) {
			echo '						<a href="';
			if ($result['db'] == 'hrb') {
				echo $result['image_large'];	// Herbarium images are full path.
			} else {
				// LC images are bring up the image browser for this sciname ID
				echo $rootURL . '/image_browser.php?id=' . $result['lc_accession_no'];
			}
			echo '"><img src="' . $rootURL . '/images/AAA-IMAGE-ICON-cam.gif" /></a>' . "\n";
		}
		echo '					</td>' . "\n";
		echo '					<td>' . "\n";
		echo '						';
		if ($result['db'] == 'hrb') {
			if (file_exists($app_root . '/images/herbarium.png')) {
				echo '<img src="' . $rootURL . '/images/herbarium.png" /> ';
			} else {
				echo '[H] ';
			}
			echo '					</td>' . "\n";
			echo '					<td align="right" nowrap>' . "\n";
			echo '						';
			echo '<a href="' . $rootURL . '/details_herbarium.php?id=' . $result['herb_herb_nbr'] . '">';
			echo $result['herb_herb_nbr'];
			echo '</a>'; 
		} elseif ($result['db'] == 'lc') {
			if (file_exists($app_root . '/images/living_coll.png')) {
				echo '<img src="' . $rootURL . '/images/living_coll.png" /> ';
			} else {
				echo '[LC] ';
			}
			echo '					</td>' . "\n";
			echo '					<td align="right" nowrap>' . "\n";
			echo '						';
			echo '<a href="' . $rootURL . '/details_living_coll.php?id=' . $result['lc_accession_no'] . '">';
			echo $result['lc_accession_no'];
			echo '</a>';
			$numLivingColl += $result['lc_plant_count'];
		}
		echo "\n";
		echo '					</td>' . "\n";
		echo '					<td></td>' . "\n";
		echo '					<td>' . "\n";
		echo '						';
		if ($result['db'] == 'hrb') {
			if ($result['herb_herb_nbr'] == '166477') {
				echo '';
			}
			$wherePrefix = '';
			if (!isNull($result['herb_coll_date']) && !isNull($result['herb_coll_date_acc'])) {
				$coll_date_parts = explode('-', sprintf('%s', $result['herb_coll_date']));
				switch ($result['herb_coll_date_acc']) {
					case 'D':
						$coll_date = date('d M Y', mktime(0, 0, 0, $coll_date_parts[1], $coll_date_parts[2], $coll_date_parts[0]));
						break;
						
					case 'M':
						$coll_date = date('M Y', mktime(0, 0, 0, $coll_date_parts[1], $coll_date_parts[2], $coll_date_parts[0]));
						break;
						
					case 'Y':
						$coll_date = date('Y', mktime(0, 0, 0, $coll_date_parts[1], $coll_date_parts[2], $coll_date_parts[0]));
						break;
				}
			}
			if (!isNull($result['herb_collector_primary'])) {
				$wherePrefix .= trim($result['herb_collector_primary']);
				if (!isNull($result['herb_collector_addl'])) {
					$wherePrefix .= ', ' . trim($result['herb_collector_addl']);
				}
			}
			if (!isNull($result['herb_collector_no'])) {
				$wherePrefix .= ' ' . trim($result['herb_collector_no']);
			}
			if (($wherePrefix != '') && (substr($wherePrefix, -1, 1) != '.')) {
				$wherePrefix .= '.';
			}
			if (isset($coll_date)) {
				$wherePrefix .= ' ' . $coll_date . '.';
			}
			if (!isNull($result['herb_site'])) {
				$wherePrefix .= ' ' . trim($result['herb_site']);
				if (substr($wherePrefix, -1, 1) != '.') {
					 $wherePrefix .= '.';
				}
			}
			if (!isNull($result['herb_subctry2'])) {
				$wherePrefix .= ' ' . trim($result['herb_subctry2']);
				
				if (!isNull($result['herb_desig2'])) {
					$wherePrefix .= ' ' . trim($result['herb_desig2']);
				}
				$wherePrefix .= ',';
			}
			if (!isNull($result['herb_subctry1'])) {
				$wherePrefix .= ' ' . trim($result['herb_subctry1']);

				if (!isNull($result['herb_desig1'])
					&& (strtolower($result['herb_desig1']) != 'state')) {
					$wherePrefix .= ' ' . trim($result['herb_desig1']);
				}
				$wherePrefix .= ',';
			}
			if (!isNull($result['herb_country'])) {
				$wherePrefix .= ' ' . trim($result['herb_country']);
			}
			if (($wherePrefix != '') && (substr($wherePrefix, -1, 1) != '.')) {
				$wherePrefix .= '.';
			}
			if (!isNull($result['herb_lc_acc'])) {
				$wherePrefix .= ' Associated living collections specimens: ' . trim($result['herb_lc_acc']);
			}
			echo $wherePrefix;			
		} elseif ($result['db'] == 'lc') {
			$outStr = array();
			if (!isNull($result['lc_how_received'])) {
				$outStr[] = strtolower($result['lc_how_received']);
			}
			if (!isNull($result['lc_no_received'])) {
				$outStr[] = '(' . $result['lc_no_received'] . ')';
			}
			if (!isNull($result['lc_source_name'])) {
				$outStr[] = 'from ' . $result['lc_source_name'];
			}
			if (count($outStr) > 0) {
				echo ucfirst(implode(' ', $outStr)) . '. ';
			}
			$wherePrefix = '';
			switch ($result['lc_provenance']) {
				case 'W':
				case 'Z':
					$wherePrefix = 'Wild collected ';
					break;
					
				default:
					$wherePrefix = 'Collected ';
					break;
			}
			$whereNothing = $wherePrefix;
			if (!isNull($result['lc_collector_name']) && (trim($result['lc_collector_name']) != '')) {
				$wherePrefix .= 'by ' . trim($result['lc_collector_name']);
			} else if (!isNull($result['lc_source_name'])) {
				$wherePrefix .= 'by ' . trim($result['lc_source_name']);
			}
			if (!isNull($result['lc_collector_site'])) {
				$wherePrefix .= '. Site: ' . trim($result['lc_collector_site']) . ',';
			}
			if (!isNull($result['lc_collector_subcountry_2']) && !isNull($result['lc_collector_subcountry_2_dsg'])) {
				$wherePrefix .= ' ' . trim($result['lc_collector_subcountry_2']) . ' ' . trim($result['lc_collector_subcountry_2_dsg']) . ',';
			}
			if (!isNull($result['lc_collector_subcountry_1'])) {
				$wherePrefix .= ' ' . trim($result['lc_collector_subcountry_1']);
			}
			if (!isNull($result['lc_collector_subcountry_1_dsg'])
					&& ($result['lc_collector_subcountry_1_dsg'] != 'State')) {
				$wherePrefix .= ' ' . trim($result['lc_collector_subcountry_1_dsg']);
			}
			if (!isNull($result['lc_collector_country']))
				$wherePrefix .= ', ' . trim($result['lc_collector_country']);
			if (($wherePrefix != '') && (substr($wherePrefix, -1, 1) != '.')) {
				$wherePrefix .= '.';
			}
			if ($plantStmt = $dbLink->prepare('SELECT plant_id FROM lc_plants WHERE accession_no = ?')) {
				$plantStmt->bind_param('s', $result['lc_accession_no']);
				$plantStmt->execute();
				$plantStmt->bind_result($lc_plant_id);
				$plantArray = array();
				
				while ($plantStmt->fetch()) {
				//	$plantArray[] = '<a href="' . $map_url . '&layer=plants&layer=photos&layer=highlight&plantid=' . $lc_plant_id . '&mode=browse" target="_blank">' . $lc_plant_id . '</a>';
					$plantArray[] = '<a href="http://www.plantconservation.us/zoomc.phtml?code=' . $lc_plant_id . '&minx=408887.656907&maxx=414151.074079&miny=4629090.818457&maxy=4631209.825644">' . $lc_plant_id. '</a>';
				}
				
				$plantStmt->close();
				
				if (count($plantArray) > 0) {
					if ($wherePrefix != $whereNothing) {
						$wherePrefix .= ' ';
					}
					$wherePrefix .= 'Map';
					if (count($plantArray) > 1) {
						$wherePrefix .= 's';
					}
					$wherePrefix .= ': ' . implode(', ', $plantArray);
				}
				
				unset($plantArray);
			}
			if ($wherePrefix != $whereNothing) {
				echo $wherePrefix;
			}
		}
		echo "\n";
		echo '					</td>' . "\n";
		echo '				</tr>' . "\n";
		++$numAccessions;
		unset($result);
		unset($sciname);
	}
	echo '			</table>' . "\n";
	echo '			</div>' . "\n";
	
	if ($hrbDoDBQuery) {
		$hrbStmt->close();
	}
	
	if ($lcDoDBQuery) {
		$lcStmt->close();
	}
	
	if ($hasDBLink) {
		$dbLink->close();
	}
		
	return array('numTaxa'=>$numTaxa, 'numAccessions'=>$numAccessions, 'numLivingColl'=>$numLivingColl);
}
