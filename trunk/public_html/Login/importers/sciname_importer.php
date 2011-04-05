<?php

include_once 'import_dir_common.php';

class sciname_importer extends ImportCommon {
	public static function getExportFileName() {
		return 'SCINAME.txt';
	}
		
	public static function exportName() {
		return 'sciname';
	}
	
	protected function dbFieldInfo() {
		return array(
			array('name'=>'scientific_name_id', 'def'=>'VARCHAR(48)'),
			array('name'=>'scientific_name', 'def'=>'VARCHAR(128)'),
			array('name'=>'sort_scientific_name', 'def'=>'VARCHAR(128)'),
			array('name'=>'order_name', 'def'=>'VARCHAR(32)'),
			array('name'=>'family_name', 'def'=>'VARCHAR(32)'),
			array('name'=>'family_common_name', 'def'=>'VARCHAR(32)'),
			array('name'=>'genus_designator', 'def'=>'VARCHAR(8)'),
			array('name'=>'genus_name', 'def'=>'VARCHAR(32)'),
			array('name'=>'species_designator', 'def'=>'VARCHAR(8)'),
			array('name'=>'species_name', 'def'=>'VARCHAR(64)'),
			array('name'=>'subspecies_name', 'def'=>'VARCHAR(32)'),
			array('name'=>'variety_name', 'def'=>'VARCHAR(96)'),
			array('name'=>'forma_name', 'def'=>'VARCHAR(32)'),
			array('name'=>'cultivar_name', 'def'=>'VARCHAR(64)'),
			array('name'=>'author_name', 'def'=>'VARCHAR(96)'),
			array('name'=>'accepted_name_id', 'def'=>'VARCHAR(32)'),
			array('name'=>'accepted_name', 'def'=>'VARCHAR(96)'),
			array('name'=>'common_names', 'def'=>'VARCHAR(512)'),
			array('name'=>'parentage', 'def'=>'VARCHAR(128)'),
			array('name'=>'usda_zone_lo', 'def'=>'INT(5)'),
			array('name'=>'usda_zone_hi', 'def'=>'INT(5)'),
			array('name'=>'range', 'def'=>'VARCHAR(64)'),
			array('name'=>'trademark', 'def'=>'VARCHAR(64)'),
			array('name'=>'plant_patent_no', 'def'=>'VARCHAR(16)'),
			array('name'=>'growth_form', 'def'=>'VARCHAR(8)'),
			array('name'=>'woody_herbaceous', 'def'=>'VARCHAR(8)'),
			array('name'=>'usda_code', 'def'=>'VARCHAR(16)'),
			array('name'=>'vplant_no', 'def'=>'VARCHAR(16)'),
			array('name'=>'name_status', 'def'=>'VARCHAR(8)'),
			array('name'=>'name_type', 'def'=>'VARCHAR(8)')
		);
	}

	protected function getAddlIndexesArray() {
		// return an array of array('name'=>'index_name', 'field'=>'field_to_index')
		return array(array('name'=>'scientific_index', 'field'=>'scientific_name_id'),
				array('name'=>'sort_scientific_name_index', 'field'=>'sort_scientific_name'));
	}
}
?>