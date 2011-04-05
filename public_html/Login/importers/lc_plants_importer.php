<?php

include_once 'import_dir_common.php';

class lc_plants_importer extends ImportCommon {
	public static function getExportFileName() {
		return 'LC_PLANTS.txt';
	}
		
	public static function exportName() {
		return 'lc_plants';
	}
	
	protected function dbFieldInfo() {
		return array(
			array('name'=>'plant_id', 'def'=>'VARCHAR(16)'),
			array('name'=>'accession_no', 'def'=>'VARCHAR(16)'),
			array('name'=>'annotation_date', 'def'=>'DATE NULL'),
			array('name'=>'annotator_name', 'def'=>'VARCHAR(32)'),
			array('name'=>'annotation', 'def'=>'VARCHAR(16)'),
			array('name'=>'collection_name', 'def'=>'VARCHAR(32)'),
			array('name'=>'collection_preposition', 'def'=>'VARCHAR(16)'),
			array('name'=>'coord_loc', 'def'=>'VARCHAR(8)'),
			array('name'=>'grid_coord', 'def'=>'VARCHAR(16)'),
			array('name'=>'grid_loc', 'def'=>'VARCHAR(8)'),
			array('name'=>'hide_location', 'def'=>'CHAR(1)'),
			array('name'=>'no_grid', 'def'=>'INT(5)'),
			array('name'=>'row', 'def'=>'INT(5) NULL'),
			array('name'=>'structure', 'def'=>'VARCHAR(16)'),
			array('name'=>'subarea1', 'def'=>'VARCHAR(32)'),
			array('name'=>'subarea2', 'def'=>'VARCHAR(32)'),
			array('name'=>'subarea3', 'def'=>'VARCHAR(32)')
		);
	}
	
	function preColCountFilter($fields) {
		// Note: bug in exported data, subarea3 items appear to have an
		// extra pipe character prior to their data, so we need to account
		// for this and potentially drop item 17.
		// One row has a '1' in column 17.
		if ((count($fields) == 18)
			&& (($fields[16] == '') || ($fields[16] == '1'))) {
			$fields[16] = $fields[17];
			array_pop($fields);
		}
		return $fields;
	}
	
	protected function getAddlIndexesArray() {
		// return an array of array('name'=>'index_name', 'field'=>'field_to_index')
		return array(array('name'=>'accession_index', 'field'=>'accession_no'));
	}
}
?>