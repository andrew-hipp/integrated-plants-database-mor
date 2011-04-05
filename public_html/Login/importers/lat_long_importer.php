<?php

include_once 'import_dir_common.php';

class lat_long_importer extends ImportCommon {
	public static function getExportFileName() {
		return 'Lat_Long.txt';
	}
		
	public static function exportName() {
		return 'lat_long';
	}
	
	protected function dbFieldInfo() {
		return array(
			array('name'=>'plant_id', 'def'=>'VARCHAR(16)'),
			array('name'=>'latitude', 'def'=>'REAL(10,6)'),
			array('name'=>'longitude', 'def'=>'REAL(11,6)')
		);
	}
	
	protected function getAddlIndexesArray() {
		// return an array of array('name'=>'index_name', 'field'=>'field_to_index')
		return array(array('name'=>'plant_index', 'field'=>'plant_id'));
	}
}
?>